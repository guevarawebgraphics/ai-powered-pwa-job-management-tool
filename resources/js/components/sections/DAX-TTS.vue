<template>
    <!-- DAX BUTTON -->
    <button type="button" @click="openModal" v-if="page != 'Model'"
        class="bg-white min-h-[100px] rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
        <div class="flex items-center space-x-2">
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

    <!-- MODAL -->
    <div v-if="showModal"
        class="fixed inset-0 z-50 bg-[#fff] flex flex-col items-center justify-center text-white px-4 py-6">

        <!-- Exit -->
        <button @click="closeModal" class="absolute top-5 right-5 text-[#333] text-2xl">✖</button>


        <!-- Dynamic Assistant State Heading -->
        <div class="text-gray-800 mb-4 text-center">
            <template v-if="isRecording">
                <h2 class="text-4xl font-semibold leading-tight">Listening...</h2>
                <p class="text-4xl text-gray-500 mt-1">Speak now</p>
            </template>

            <template v-else-if="isSpeaking">
                <h2 class="text-4xl font-semibold leading-tight">Responding...</h2>
                <p class="text-2xl text-gray-500 mt-1">DAX is talking, please wait</p>
            </template>

            <template v-else>
                <h2 class="text-4xl font-semibold leading-tight">Talk to DAX</h2>
                <p class="text-2xl text-gray-500 mt-1">Press audio button to start talking</p>
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

            <!-- AI Typing -->
            <div v-if="typingReply" class="text-sm text-purple-300 font-semibold">
                DAX: <span class="text-[#333] ml-2">{{ typingReply }}</span><span class="animate-pulse">|</span>
            </div>

            <!-- Live Transcript -->
            <div v-if="liveTranscript" class="text-sm text-[#333] font-semibold mt-2">
                You: <span class="italic">{{ liveTranscript }}</span>
            </div>
        </div>


        <!-- Animated Waveform -->
        <!-- <div v-if="isSpeaking" class="flex items-end justify-center h-32 mb-10">
            <div v-for="i in 32" :key="i"
                class="w-[3px] bg-gradient-to-b from-cyan-400 to-purple-500 mx-[1px] animate-wave" :style="getStyle(i)">
            </div>
        </div> -->

        <!-- Mic Button -->
        <!-- <button @click="toggleRecording" :class="[
            'relative w-20 h-20 rounded-full shadow-lg flex items-center justify-center text-white text-3xl transition',
            isRecording ? 'bg-blue-500 pulse-active' : 'bg-blue-500 hover:scale-110'
        ]">
            <i class="fas fa-microphone"></i>
        </button> -->

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
            required: false,
            default: 'Guest'
        }
    },
    data() {
        return {
            showModal: false,
            isRecording: false,
            isSpeaking: false,
            liveTranscript: '',
            recognition: null,
            chatHistory: [],
            typingReply: '',
            hoveredIndex: null,
            selectedVoice: null,
            recording: false,
            audioChunks: [],
            mediaRecorder: null,
        };
    },
    // mounted() {
    //     // const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    //     // this.recognition = new SpeechRecognition();
    //     // this.recognition.lang = 'en-US';
    //     // this.recognition.interimResults = true;

    //     // window.speechSynthesis.onvoiceschanged = () => {
    //     //     const voices = window.speechSynthesis.getVoices();
    //     //     this.selectedVoice = voices.find(v => v.lang === 'en-US' || v.name.includes('Samantha')) || voices[0];
    //     // };

    //     // this.recognition.onresult = async (event) => {
    //     //     let finalTranscript = '';
    //     //     for (let i = event.resultIndex; i < event.results.length; ++i) {
    //     //         const result = event.results[i];
    //     //         const transcript = result[0].transcript;
    //     //         if (result.isFinal) {
    //     //             finalTranscript += transcript;
    //     //             this.chatHistory.push({ role: 'user', content: transcript });
    //     //             this.liveTranscript = '';
    //     //             this.scrollToBottom();
    //     //             // await this.getAIResponse(transcript);
    //     //         } else {
    //     //             this.liveTranscript = transcript;
    //     //             this.scrollToBottom();
    //     //         }
    //     //     }
    //     // };

    //     // this.recognition.onend = () => {
    //     //     this.isRecording = false;
    //     // };
    // },
    mounted() {
        // Initialize the SpeechRecognition API
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SpeechRecognition();
        this.recognition.lang = 'en-US';
        this.recognition.interimResults = true;

        // Handle speech recognition results
        this.recognition.onresult = (event) => {
            let finalTranscript = '';
            // Process all results from the event
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const result = event.results[i];
                const transcript = result[0].transcript;
                if (result.isFinal) {
                    finalTranscript += transcript;
                    this.chatHistory.push({ role: 'user', content: transcript });
                    this.liveTranscript = '';
                    this.scrollToBottom();
                    // await this.getAIResponse(transcript);
                } else {
                    this.liveTranscript = transcript;
                    this.scrollToBottom();
                }
            }
            // If there's a final transcript, add it to your chatHistory and clear liveTranscript
            if (finalTranscript) {
                this.chatHistory.push({ role: 'user', content: finalTranscript });
                this.liveTranscript = '';
                this.scrollToBottom();
            }
        };

        // When speech recognition ends, update the state
        this.recognition.onend = () => {
            this.isRecording = false;
        };
    },

    methods: {
        async startRecording() {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            this.mediaRecorder = new MediaRecorder(stream);
            this.audioChunks = [];

            this.mediaRecorder.ondataavailable = (e) => {
                this.audioChunks.push(e.data);
            };

            this.mediaRecorder.onstop = this.sendAudioToServer;
            this.mediaRecorder.start();
            this.recording = true;
            this.isRecording = true;
            this.isSpeaking = false;
        },
        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                this.mediaRecorder.stop();
            }
            this.recording = false;
            this.isRecording = false;
            this.isSpeaking = true;
        },
        async sendAudioToServer() {
            const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
            const formData = new FormData();
            formData.append('audio', audioBlob, 'voice.webm'); // name must be 'audio'

            const token = localStorage.getItem('token');

            const response = await fetch('/api/dax/voice-chat', {
                method: 'POST',
                body: formData,
                headers: {
                    Authorization: `Bearer ${token}`
                    // Do not set Content-Type when using FormData
                },
            });

            const data = await response.json();

            this.isRecording = false;
            this.isSpeaking = false;

            // Optionally play the returned audio
            if (data.audio_url) {
                const audio = new Audio(data.audio_url);
                audio.play();
            }

            // Display the AI reply with a typewriter effect
            if (data.reply) {
                this.typingReply = '';
                // Loop over each character in the reply text with a short delay
                for (let i = 0; i < data.reply.length; i++) {
                    this.typingReply += data.reply[i];
                    this.scrollToBottom(); // Ensure transcript scrolls as text animates
                    await new Promise(resolve => setTimeout(resolve, 20));
                }
                // Once the animated reply is complete, push it into chatHistory
                this.chatHistory.push({ role: 'assistant', content: data.reply });
                this.typingReply = ''; // Clear the typing effect placeholder
            }
        },
        
        openModal() {
            this.showModal = true;
            this.$nextTick(() => {
                this.scrollToBottom();
            });
        },
        toggleRecording() {
            if (this.isRecording) {
                this.recognition.stop();
                this.isRecording = false;
            } else {
                this.recognition.start();
                this.isRecording = true;
            }
        },
        closeModal() {
            this.recognition.stop();
            this.isRecording = false;
            this.showModal = false;
            this.liveTranscript = '';
            if (this.peerConnection) {
        this.peerConnection.close();
        this.peerConnection = null;
      }
        },
        async getAIResponse(text) {
            try {
                this.isSpeaking = true;
                const token = localStorage.getItem('token');

                const response = await fetch('/api/dax/chat', {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ messages: this.chatHistory }),
                });

                const data = await response.json();
                const reply = data.reply;

                // Start speaking first (don’t wait)
                this.speak(reply);

                // Animate typing simultaneously
                this.typingReply = '';
                for (let i = 0; i < reply.length; i++) {
                    this.typingReply += reply[i];
                    this.scrollToBottom();
                    await new Promise(resolve => setTimeout(resolve, 20)); // still animating while speaking
                }

                // Once typed, save to chatHistory
                this.chatHistory.push({ role: 'assistant', content: reply });
                this.typingReply = '';
                this.scrollToBottom();

            } catch (e) {
                console.error(e);
            }
        },
        speak(text) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';
            if (this.selectedVoice) utterance.voice = this.selectedVoice;
            utterance.onend = () => {
                this.isSpeaking = false;
            };
            if (typeof window !== "undefined" && window.speechSynthesis) {
                window.speechSynthesis.speak(utterance);
            }
        },
        getStyle(i) {
            const height = Math.floor(Math.random() * 60 + 20); // 20–80px
            const delay = (i % 8) * 0.1;
            return {
                height: `${height}px`,
                animationDelay: `${delay}s`,
                animationDuration: `1s`,
            };
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.transcriptContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    },
};
</script>

<style scoped>
@keyframes wave {

    0%,
    100% {
        transform: scaleY(1);
    }

    50% {
        transform: scaleY(2.2);
    }
}

.animate-wave {
    animation-name: wave;
    animation-iteration-count: infinite;
    animation-timing-function: ease-in-out;
    transform-origin: bottom center;
}


@keyframes pulse-ring {
    0% {
        transform: scale(1);
        opacity: 0.6;
    }

    50% {
        transform: scale(1.4);
        opacity: 0.2;
    }

    100% {
        transform: scale(1);
        opacity: 0.6;
    }
}

.pulse-active::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: #3b82f6;
    /* Tailwind blue-500 */
    border-radius: 9999px;
    animation: pulse-ring 1.5s infinite;
    z-index: -1;
}
</style>
