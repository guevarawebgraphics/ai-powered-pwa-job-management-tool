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
        class="fixed inset-0 z-50 bg-[#fff] flex flex-col items-center justify-between text-white px-4 py-6">

        <!-- Exit -->
        <button @click="closeModal" class="absolute top-5 right-5 text-[#333] text-2xl">✖</button>

        <!-- Dynamic Assistant State Heading -->
        <div class="text-gray-800 mb-4 text-center mt-10">
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

        <!-- Mic Button -->
        <div class="mb-20">
            <button @click="toggleRecording" :class="[
                'relative w-20 h-20 rounded-full shadow-lg flex items-center justify-center text-white text-3xl transition',
                isRecording || isSpeaking ? 'bg-blue-500 pulse-active' : 'bg-blue-500 hover:scale-110'
            ]">
                <i class="fas fa-microphone"></i>
            </button>
        </div>

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
            voice: null,
        };
    },
    mounted() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SpeechRecognition();
        this.recognition.lang = 'en-US';
        this.recognition.interimResults = true;

        window.speechSynthesis.onvoiceschanged = () => {
            const voices = window.speechSynthesis.getVoices();
            this.voice = voices.find(v => v.name.includes('Samantha') || v.lang === 'en-US');
        };

        this.recognition.onresult = async (event) => {
            let finalTranscript = '';
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                const result = event.results[i];
                const transcript = result[0].transcript;
                if (result.isFinal) {
                    finalTranscript += transcript;
                    this.chatHistory.push({ role: 'user', content: transcript });
                    this.liveTranscript = '';
                    this.scrollToBottom();
                    await this.getAIResponse(transcript);
                } else {
                    this.liveTranscript = transcript;
                    this.scrollToBottom();
                }
            }
        };

        this.recognition.onend = () => {
            this.isRecording = false;
        };
    },
    methods: {
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

                this.speak(reply);

                this.typingReply = '';
                for (let i = 0; i < reply.length; i++) {
                    this.typingReply += reply[i];
                    this.scrollToBottom();
                    await new Promise(resolve => setTimeout(resolve, 20));
                }

                this.chatHistory.push({ role: 'assistant', content: reply });
                this.typingReply = '';
                this.scrollToBottom();

            } catch (e) {
                console.error(e);
            }
        },
        speak(text) {
            if (!window.speechSynthesis) return;

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';
            if (this.voice) utterance.voice = this.voice;

            utterance.onend = () => {
                this.isSpeaking = false;
            };

            window.speechSynthesis.speak(utterance);
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
    border-radius: 9999px;
    animation: pulse-ring 1.5s infinite;
    z-index: -1;
}
</style>
