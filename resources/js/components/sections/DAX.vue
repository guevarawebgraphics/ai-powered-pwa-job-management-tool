<template>
    <!-- DAX Button to open the modal -->
    <button type="button" @click="openModal" v-if="page !== 'Model'"
        class="bg-white min-h-[100px] rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-center justify-center text-center transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">

        <div class="flex items-center justify-center space-x-2">
            <i class="fas fa-headphones-simple text-lg text-[#171A1FFF]"></i>
            <span class="text-xl font-medium text-[#666666FF]">
                DAX
            </span>
        </div>
    </button>

    <div @click="openModal" class="bg-white shadow-md rounded-lg p-4 flex items-center justify-center mt-6" v-else>
        <i class="fas fa-headset text-3xl text-gray-700"></i>
        <p class="text-sm font-medium ml-2">DAX</p>
    </div>

    <!-- Modal -->
    <div v-if="showModal"
        class="fixed inset-0 z-50 bg-[#fff] flex flex-col items-center justify-center text-white px-4 py-6">

        <!-- Exit Button -->
        <button @click="closeModal" class="absolute top-5 right-5 text-[#333] text-2xl">✖</button>

        <!-- Dynamic Conversation State Heading -->
        <div class="text-gray-800 mb-4 text-center">
            <template v-if="recording">
                <h2 class="text-4xl font-semibold leading-tight">Listening...</h2>
                <p class="text-4xl text-gray-500 mt-1">Speak now</p>
            </template>
            <template v-else>
                <h2 class="text-4xl font-semibold leading-tight">Talk to DAX</h2>
                <p class="text-2xl text-gray-500 mt-1">Press the button to start talking</p>
            </template>
        </div>

        <!-- Scrollable Transcript Log -->
        <div ref="transcriptContainer"
            class="w-full max-w-2xl h-80 overflow-y-auto px-4 py-2 mb-6 text-center space-y-2">
            <div v-for="(msg, index) in chatHistory" :key="index" @mouseover="hoveredIndex = index"
                @mouseleave="hoveredIndex = null" :class="[
                    'leading-snug transition-all duration-300 cursor-default',
                    hoveredIndex === index
                        ? 'text-base font-semibold opacity-100'
                        : index === chatHistory.length - 1 && hoveredIndex === null
                            ? 'text-base font-semibold opacity-100'
                            : 'text-lg opacity-40',
                    msg.role === 'user' ? 'text-[#232850FF]' : 'text-blue-300'
                ]">
                <span class="mr-2">{{ msg.role === 'user' ? 'You' : 'DAX' }}:</span>
                <span class="text-[#333]">{{ msg.content }}</span>
            </div>

            <!-- Typing (live transcript) effect -->
            <div v-if="typingReply" class="leading-snug text-base font-semibold text-blue-300">
                <span class="mr-2">DAX:</span>
                <span class="text-[#333]">{{ typingReply }}</span>
            </div>
        </div>

        <!-- Toggle Recording Button -->
        <button @click="recording ? stopRecording() : startRecording()" :class="['relative w-20 h-20 rounded-full shadow-lg flex items-center justify-center text-white text-3xl transition',
            recording ? 'bg-red-500 pulse-active' : 'bg-blue-500 hover:scale-110']">
            <i :class="recording ? 'fas fa-stop' : 'fas fa-microphone'"></i>
        </button>
    </div>
</template>

<script>
export default {
    name: "DAX",
    props: {
        page: {
            type: String,
            default: 'Guest'
        },
        contentSelector: { type: String, default: 'body' },
    },
    data() {
        return {
            showModal: false,
            recording: false,
            speaking: false,
            chatHistory: [],
            typingReply: '', // Holds the current text being typed out
            hoveredIndex: null,
            peerConnection: null,
            localStream: null,
            dataChannel: null,
            speechRecognition: null,
            userTranscript: ''
        };
    },
    methods: {
        getPageContent() {
            const element = document.querySelector(this.contentSelector);
            return element ? element.innerText : '';
        },
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.recording = false;
            this.speaking = false;
            this.chatHistory = [];
            this.typingReply = '';
            if (this.peerConnection) {
                this.peerConnection.close();
                this.peerConnection = null;
            }
        },
        async startRecording() {
            this.recording = true;

            // Start browser speech recognition (user transcript)
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (SpeechRecognition) {
                this.speechRecognition = new SpeechRecognition();
                // Enable continuous mode for multiple utterances
                this.speechRecognition.continuous = true;
                this.speechRecognition.interimResults = false;
                this.speechRecognition.lang = 'en-US';

                this.speechRecognition.onresult = (event) => {
                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        if (event.results[i].isFinal) {
                            const transcript = event.results[i][0].transcript.trim();

                            // ✅ Only add if it's not empty
                            if (transcript.length > 0) {
                                this.userTranscript = transcript;
                                this.chatHistory.push({
                                    role: 'user',
                                    content: transcript
                                });
                                this.scrollToBottom();
                            }
                        }
                    }
                };


                this.speechRecognition.onerror = (event) => {
                    console.warn('Speech recognition error:', event.error);
                };

                // If the recognition stops unexpectedly, restart it.
                this.speechRecognition.onend = () => {
                    if (this.recording) {
                        this.speechRecognition.start();
                    }
                };

                this.speechRecognition.start();
            }

            this.speaking = false;
            this.chatHistory = [];
            this.typingReply = '';
            try {
                // 1. Create a new RTCPeerConnection with a STUN server.
                this.peerConnection = new RTCPeerConnection({
                    iceServers: [{ urls: "stun:stun.l.google.com:19302" }]
                });

                // 2. Setup ontrack event to handle inbound audio.
                this.peerConnection.ontrack = (event) => {
                    let remoteAudio = document.getElementById("remoteAudio");
                    if (!remoteAudio) {
                        remoteAudio = document.createElement("audio");
                        remoteAudio.id = "remoteAudio";
                        remoteAudio.autoplay = true;
                        remoteAudio.controls = true;
                        // Hide the audio element
                        remoteAudio.style.display = 'none';
                        document.body.appendChild(remoteAudio);
                    }
                    remoteAudio.srcObject = event.streams[0];
                };

                // 3. Create a data channel for exchanging signaling and session configuration.
                this.dataChannel = this.peerConnection.createDataChannel("ai-signaling");
                this.dataChannel.addEventListener("open", () => {
                    console.log("Data channel open");
                    this.configureData();
                });

                this.dataChannel.addEventListener("message", (ev) => {
                    let msg;
                    try {
                        msg = JSON.parse(ev.data);
                    } catch (error) {
                        console.error("Unable to parse message data:", ev.data);
                        return;
                    }

                    console.log(`Transcript: `, msg);

                    // 🔄 Live transcription while AI is speaking
                    if (msg.type === "response.audio_transcript.delta" && msg.delta) {
                        this.typingReply += msg.delta;
                        this.scrollToBottom();
                    }

                    // ✅ When AI is done speaking, finalize transcript
                    if (msg.type === "response.audio_transcript.done" && msg.transcript) {
                        this.chatHistory.push({
                            role: "assistant",
                            content: msg.transcript
                        });
                        this.typingReply = "";
                    }

                    // ✅ Final user transcript (from your voice)
                    if (
                        msg.type === "response.item.done" &&
                        msg.item?.role === "user" &&
                        msg.item?.content?.[0]?.text
                    ) {
                        const userText = msg.item.content[0].text;

                        this.chatHistory.push({
                            role: "user",
                            content: userText
                        });

                        this.scrollToBottom();
                    }
                });


                // 4. Capture microphone audio.
                this.localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                // Add local audio tracks to the connection.
                this.localStream.getTracks().forEach(track => {
                    this.peerConnection.addTrack(track, this.localStream);
                });

                // 5. Create an SDP offer.
                const offer = await this.peerConnection.createOffer();
                await this.peerConnection.setLocalDescription(offer);

                // 6. Send the SDP offer to the OpenAI Realtime endpoint.
                const baseUrl = 'https://api.openai.com/v1/realtime';
                const model = 'gpt-4o-realtime-preview-2024-12-17';
                const sdpResponse = await fetch(`${baseUrl}?model=${model}`, {
                    method: 'POST',
                    body: offer.sdp,
                    headers: {
                        Authorization: `Bearer ${import.meta.env.VITE_API_OPENAI_API_KEY}`,
                        'Content-Type': 'application/sdp',
                    },
                });
                const answerSdp = await sdpResponse.text();
                await this.peerConnection.setRemoteDescription({
                    type: "answer",
                    sdp: answerSdp,
                });
            } catch (error) {
                console.error("Error establishing WebRTC connection:", error);
            }
        },
        async stopRecording() {
            this.recording = false;
            if (this.speechRecognition) {
                this.speechRecognition.stop();
                this.speechRecognition = null;
            }
            this.speaking = true;
            if (this.peerConnection) {
                this.peerConnection.close();
                this.peerConnection = null;
            }
        },
        configureData() {
            if (this.dataChannel && this.dataChannel.readyState === "open") {

                const pageContent = document.querySelector(this.contentSelector)?.innerText || '';

                console.log(`Appended files: `, this.$store.state.pdfFiles);

                const event = {
                    type: "session.update",
                    session: {
                        modalities: ["text", "audio"],
                        voice: "ash",
                        instructions: `
                            You are provided with additional context derived from the current webpage:
                            "${pageContent.trim()}"

                            And you are assisting trained appliance repair professionals in the field. These technicians are knowledgeable and experienced, often using tech sheets, wiring diagrams, and diagnostic tools. Respond to their questions with concise, technical, and accurate guidance focused on diagnostics, error codes, mechanical functions, and repairs. Assume they understand appliance mechanics and only need help narrowing down issues or verifying steps. Avoid oversimplifying or providing basic definitions unless asked.
                            
                            When responding to user queries, consider this context only if there is a clear, relevant connection. Otherwise, answer using your standard knowledge.
                        `

                    },
                };
                this.dataChannel.send(JSON.stringify(event));
                console.log("Sent session.update:", event);
            }
        },
        animateText(fullText) {
            // Clear any previous typing
            this.typingReply = "";
            let charIndex = 0;
            const intervalSpeed = 50; // milliseconds per character

            const interval = setInterval(() => {
                if (charIndex < fullText.length) {
                    this.typingReply += fullText[charIndex];
                    charIndex++;

                    // Optionally, scroll to the bottom of the transcript container
                    this.$nextTick(() => {
                        const container = this.$refs.transcriptContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                } else {
                    clearInterval(interval);
                    // When done, push to chatHistory and clear typingReply
                    this.chatHistory.push({
                        role: 'assistant',
                        content: this.typingReply
                    });
                    this.typingReply = '';
                }
            }, intervalSpeed);
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.transcriptContainer;
                if (container) container.scrollTop = container.scrollHeight;
            });
        }
    },
};
</script>

<style scoped>
/* Add your component-specific styles here */
#remoteAudio {
    display: none;
}
</style>
