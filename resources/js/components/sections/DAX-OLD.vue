<template>
    <!-- DAX BUTTON -->
    <button type="button" @click="openModal"
        class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
        <div class="flex items-center space-x-2">
            <i class="fas fa-headphones-simple text-lg text-[#171A1FFF]"></i>
            <span class="text-xl font-medium text-[#666666FF]">
                DAX
            </span>
        </div>
    </button>


</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";

export default {
    name: "DAX",
    data() {
        return {
            chatHistory: [],
            isRecording: false,
            recognition: null,
        };
    },

    mounted() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SpeechRecognition();
        this.recognition.lang = 'en-US';
        this.recognition.interimResults = false;

        this.recognition.onresult = async (event) => {
            const transcript = event.results[0][0].transcript;
            this.chatHistory.push({ role: 'user', content: transcript });
            await this.getAIResponse(transcript);
        };

        this.recognition.onend = () => {
            this.isRecording = false;
        };
    },

    computed: {

    },

    methods: {
        toggleRecording() {
            if (this.isRecording) {
                this.recognition.stop();
            } else {
                this.recognition.start();
            }
            this.isRecording = !this.isRecording;
        },

        async getAIResponse(userInput) {
            try {

                const token = localStorage.getItem("token");

                const response = await fetch('/api/dax/chat', {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        messages: this.chatHistory,
                    }),
                });

                const data = await response.json();
                const aiReply = data.reply;

                this.chatHistory.push({ role: 'assistant', content: aiReply });
                this.speak(aiReply);

                console.log(data);

            } catch (error) {
                console.error("Fetch error:", error);
                Swal.fire("Error", "Something went wrong talking to DAX.", "error");
            }
        },

        speak(text) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';
            window.speechSynthesis.speak(utterance);
        },
    },
};
</script>
