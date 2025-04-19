<template>

    <div class="mt-20 mb-20">
        <NavBar />
        <div class="max-w-lg mx-auto p-6 text-center">
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
            <div class="relative">

                <!-- Toggle Recording Button -->
                <button @click="recording ? stopRecording() : startRecording()" :class="['absolute left-1/2 transform -translate-x-1/2 -translate-y-full w-20 h-20 rounded-full shadow-lg flex items-center justify-center text-white text-3xl transition',
                    recording ? 'bg-red-500 pulse-active' : 'bg-blue-500 hover:scale-110']">
                    <i :class="recording ? 'fas fa-stop' : 'fas fa-microphone'"></i>
                </button>


            </div>
            <BottomNav />
        </div>
    </div>
</template>

<script>

import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import axios from "axios";

export default {

    components: { NavBar, BottomNav },
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
            recording: false,
            speaking: false,
            chatHistory: [],
            typingReply: '', // Holds the current text being typed out
            hoveredIndex: null,
            peerConnection: null,
            localStream: null,
            dataChannel: null,
            speechRecognition: null,
            userTranscript: '',

        };
    },
    async created() {
        const fileIDs = await this.fetchFileIDs();
        this.$store.commit("setVectorIDs", fileIDs);
    },
    methods: {
        getPageContent() {
            const element = document.querySelector(this.contentSelector);
            return element ? element.innerText : '';
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

                    if (msg.type === 'response.function_call_arguments.done') {
                        if (msg.name === "query_about_machine") {
                            const args = JSON.parse(msg.arguments);
                            console.log(`🔍 Argument:`, args.user_query);
                            this.runFileSearchTool(args.user_query)
                                .then((data) => {
                                    const outputText = data.output?.[1]?.content?.[0]?.text || "";

                                    if (outputText) {
                                        const argument_event = {
                                            type: "response.create",
                                            response: {
                                                modalities: ["text", "audio"],
                                                voice: "ash",
                                                instructions: `Tell the user the following information from the documents: ${outputText}`
                                            },
                                        };

                                        this.dataChannel.send(JSON.stringify(argument_event));


                                        this.chatHistory.push({
                                            role: "assistant",
                                            content: outputText
                                        });
                                        this.typingReply = "";

                                    }



                                }).catch((err) => {
                                    console.error("❌ API error:", err);

                                    if (err.response) {
                                        // HTTP error from the server
                                        console.error("🔍 Response data:", err.response.data);
                                        console.error("📦 Status code:", err.response.status);
                                        console.error("📋 Headers:", err.response.headers);
                                    } else if (err.request) {
                                        // No response received
                                        console.error("📭 No response received:", err.request);
                                    } else {
                                        // Other types of errors (e.g., bad syntax)
                                        console.error("⚠️ Error message:", err.message);
                                    }

                                    // Optional: full stack trace
                                    console.error("🧵 Stack trace:", err.stack);
                                });
                        }
                    }

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

                    if (msg.type === "tool_call" && msg.tool_name === "file_search") {
                        const resultText = msg.result?.content?.[0]?.text;

                        if (resultText) {
                            this.chatHistory.push({
                                role: "assistant",
                                content: resultText
                            });
                            this.typingReply = '';
                        }
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

                console.log(`Appended files: `, this.$store.state.vectoreIDs);

                const event = {
                    type: "session.update",
                    session: {
                        modalities: ["text", "audio"],
                        voice: "ash",
                        tools: [
                            {
                                type: "function",
                                name: "query_about_machine",
                                description: "call this function when the user is wanting to know information about the appliance",
                                parameters: {
                                    type: "object",
                                    strict: true,
                                    properties: {
                                        user_query: {
                                            type: "string",
                                            description: "Query from the user about what information they are looking for. Phrase it in a way that it forms a question and ends in a question mark.",
                                        },
                                    },
                                    required: ["user_query"],
                                },
                            },
                        ],
                        tool_choice: "auto",
                        instructions: `
                            You are provided with additional context derived from the current webpage:
                            "${pageContent.trim()}"

                            Additionally, you may reference the contents of the uploaded PDF files to support your answers.

                            And you are assisting trained appliance repair professionals in the field. These technicians are knowledgeable and experienced, often using tech sheets, wiring diagrams, and diagnostic tools. Respond to their questions with concise, technical, and accurate guidance focused on diagnostics, error codes, mechanical functions, and repairs. Assume they understand appliance mechanics and only need help narrowing down issues or verifying steps. Avoid oversimplifying or providing basic definitions unless asked.
                            
                            When responding to user queries, consider this context only if there is a clear, relevant connection. Otherwise, answer using your standard knowledge.

                            If a question requires looking into documentation or PDFs, you may use the 'file_search' tool.
                        `,
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
        },
        async fetchFileIDs() {
            const token = localStorage.getItem("token");
            if (!token) {
                console.error("No token found in localStorage");
                return [];
            }

            try {
                const response = await axios.get("/api/dax/openai/files", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                });

                const fileIDs = response.data;
                console.log("✅ Retrieved file IDs:", fileIDs);
                return fileIDs;

            } catch (error) {
                console.error("❌ Error fetching file IDs:", error);
                return [];
            }
        },
        async runFileSearchTool(user_query) {
            try {
                const apiKey = import.meta.env.VITE_API_OPENAI_API_KEY;
                const response = await fetch("https://api.openai.com/v1/responses", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${apiKey}`,
                        "OpenAI-Beta": "assistants=v2"  // ✅ Required for file_search tools
                    },
                    body: JSON.stringify({
                        model: "gpt-4o-mini",
                        input: `${user_query} Only use data from my documents`,
                        tools: [
                            {
                                type: "file_search",
                                vector_store_ids: ["vs_67fa7f0abec48191adc1594c4e2641dc"]
                            }
                        ]
                    })
                });

                console.log("ended fetch");
                const data = await response.json();
                console.log(`⚠️ responseAPI: `, data);
                return data;

            } catch (err) {
                console.error("File search error:", err);
                return null;
            }
        },

        async getVectorIds() {
            try {
                const token = localStorage.getItem("token");

                const response = await axios.get("/api/openai/vector_stores", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                });

                console.log("Vector Stores:", response);
            } catch (err) {
                console.error("Error fetching vector stores:", err.response?.data || err.message);
            }
        },
        async updateVectorName(vectorID, newName) {
            const token = localStorage.getItem("token");

            try {
                const response = await axios.post(
                    `/api/openai/vector_stores/${vectorID}`,
                    {
                        name: newName
                    },
                    {
                        headers: {
                            "Authorization": `Bearer ${token}`,
                            "OpenAI-Beta": "assistants=v2",
                            "Content-Type": "application/json"
                        }
                    }
                );

                console.log("Updated Vector Store:", response.data);
                return response.data;
            } catch (error) {
                console.error("Error updating vector store:", error.response?.data || error.message);
            }
        },

    },
};
</script>
<style scoped>
/* Add your component-specific styles here */
#remoteAudio {
    display: none;
}
</style>