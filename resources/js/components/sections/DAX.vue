<template>
    <!-- DAX Button to open the modal -->
    <button type="button" @click="openModal" v-if="page !== 'Model' && page !== 'GigReport'"
        class="bg-white min-h-[100px] rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-center justify-center text-center transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">

        <div class="flex items-center justify-center space-x-2">
            <i class="fas fa-headphones-simple text-lg text-[#171A1FFF]"></i>
            <span class="text-xl font-medium text-[#666666FF]">
                DAX
            </span>
        </div>
    </button>

    <div v-else-if="page === 'GigReport'" @click="openModal"
        class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start cursor-pointer
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
        <i class="fas fa-headset text-2xl text-gray-700"></i>
        <p class="text-sm font-medium mt-2">DAX</p>
    </div>

    <div @click="openModal"
        class="bg-white shadow-md rounded-lg p-4 flex items-center justify-center mt-6 cursor-pointer" v-else>
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
            recording ? 'bg-red-500 pulse-active' : 'bg-blue-500 hover:scale-110']" :style="{
                transform: recording
                    ? `scale(${1 + volume * 1.5})`
                    : 'scale(1)'
            }">
            <i :class="recording ? 'fas fa-stop' : 'fas fa-microphone'"></i>
        </button>
    </div>
</template>

<script>
import axios from "axios";
import { reactive } from 'vue';




// Define a shared reactive event bus (global, minimal)
export const bus = reactive({
    callbacks: {},
    on(event, callback) {
        this.callbacks[event] = this.callbacks[event] || [];
        this.callbacks[event].push(callback);
    },
    emit(event, data) {
        if (this.callbacks[event]) {
            this.callbacks[event].forEach(cb => cb(data));
        }
    },
    off(event, callback) {
        this.callbacks[event] = (this.callbacks[event] || []).filter(cb => cb !== callback);
    }
});

export default {
    name: "DAX",
    props: {
        page: {
            type: String,
            default: 'Guest'
        },
        user_id: {
            type: [String, Number],
            default: ''
        },
        vector_id: {
            type: [String, Number],
            default: ''
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
            userTranscript: '',
            vectorIDs: [],
            volume: 0,
            audioContext: null,
            analyser: null,
            dataArray: null,
        };
    },
    created() {
        bus.on("open-dax", this.openModal);
        bus.on("dax-map-popup-blocked", this.notifyMapPopupBlocked);
        this.restoreDaxState();
    },
    beforeUnmount() {
        bus.off("open-dax", this.openModal);
        bus.off("dax-map-popup-blocked", this.notifyMapPopupBlocked);
    },
    mounted() {

    },
    watch: {
        $route() {
            this.restoreDaxState();
        }
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
            this.stopRecording();
        },
        async startRecording() {
            this.recording = true;
            this.$store.commit("setDaxActive", true);

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

                this.dataChannel.addEventListener("message", async (ev) => {
                    let msg;
                    try {
                        msg = JSON.parse(ev.data);
                    } catch (error) {
                        console.error("Unable to parse message data:", ev.data);
                        return;
                    }

                    console.log(`Transcript: `, msg);

                    if (msg.type === 'response.function_call_arguments.done') {

                        const msg = JSON.parse(ev.data)
                        const args = JSON.parse(msg.arguments)

                        // Now this is fine:
                        const result = await this.handleFunctionCall(msg.name, args);

                        // 1) send function_call_output
                        this.dataChannel.send(JSON.stringify({
                            type: 'conversation.item.create',
                            item: {
                                type: 'function_call_output',
                                call_id: msg.call_id,
                                output: JSON.stringify(result)
                            }
                        }));

                        // 2) send response.create
                        this.dataChannel.send(JSON.stringify({
                            type: 'response.create',
                            response: {
                                modalities: ['text', 'audio'],
                                voice: 'ash',
                                instructions: result
                            }
                        }));


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



                this.audioContext = this.audioContext || new AudioContext();

                // 2. create analyser, hook up the mic stream
                this.analyser = this.audioContext.createAnalyser();
                this.analyser.fftSize = 256;
                const source = this.audioContext.createMediaStreamSource(this.localStream);
                source.connect(this.analyser);

                // 3. prepare a buffer to read time‑domain data
                this.dataArray = new Uint8Array(this.analyser.frequencyBinCount);

                // 4. start a loop to update `this.volume`
                const updateVolume = () => {
                    this.analyser.getByteTimeDomainData(this.dataArray);
                    let sum = 0;
                    for (let i = 0; i < this.dataArray.length; i++) {
                        const v = (this.dataArray[i] - 128) / 128;
                        sum += v * v;
                    }
                    const rms = Math.sqrt(sum / this.dataArray.length);
                    this.volume = rms;    // roughly 0–1

                    if (this.recording) {
                        requestAnimationFrame(updateVolume);
                    } else {
                        this.volume = 0;
                    }
                };
                updateVolume();






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
            this.$store.commit("setDaxActive", false);
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

                const tools = [];

                // ✅ Add only if page is "Model"
                if (this.page === "Model" || this.page === "GigIndex" || this.page === "GigReport") {
                    tools.push({
                        type: "function",
                        name: "query_about_machine",
                        description: "Call this function when the user is wanting to know information about the appliance.",
                        parameters: {
                            type: "object",
                            strict: true,
                            properties: {
                                user_query: {
                                    type: "string",
                                    description: "Query from the user about what information they are looking for. Phrase it as a question ending with a question mark."
                                }
                            },
                            required: ["user_query"]
                        }
                    });
                }

                if (this.page === "GigIndex" || this.page === "CustomerUI") {
                    tools.push(
                        {
                            type: "function",
                            name: "call_client_by_voice",
                            description: "Use this tool if the technician says they want to call the client.",
                            parameters: {
                                type: "object",
                                properties: {
                                    confirm_call: {
                                        type: "boolean",
                                        description: "Whether the technician confirmed calling the client. Always true."
                                    }
                                },
                                required: ["confirm_call"]
                            }
                        },
                        {
                            type: "function",
                            name: "send_email",
                            description: "Call this when the technician wants to send an email to the client",
                            parameters: {
                                type: "object",
                                properties: {}
                            }
                        },
                        {
                            type: "function",
                            name: "open_google_map",
                            description: "Call this when the technician asks for directions or wants to open the client location in Google Maps.",
                            parameters: {
                                type: "object",
                                properties: {}
                            }
                        }

                    );

                    const statusEnum =
                        this.page === "GigIndex"
                            ? ["arriving-early", "on-time", "behind-schedule"]
                            : ["send-message"];

                    tools.push({
                        type: "function",
                        name: "send_status_message",
                        description:
                            this.page === "CustomerUI"
                                ? "Call this when the technician wants to send a message to the client from the client or customer page."
                                : "Call this when the technician wants to send a status update to the client, like arriving early, on time, or behind schedule.",
                        parameters: {
                            type: "object",
                            properties: {
                                status: {
                                    type: "string",
                                    enum: statusEnum,
                                    description:
                                        this.page === "CustomerUI"
                                            ? "Only use enum 'send-message' when on the Customer Page."
                                            : "Status message options: 'arriving-early', 'on-time', or 'behind-schedule'."
                                }
                            },
                            required: ["status"]
                        }
                    });
                }

                // ✅ Add shared tools regardless of page
                tools.push(
                    {
                        type: "function",
                        name: "open_gig_from_voice",
                        description: "Call this when the user asks to open a gig by number or cryptic code.",
                        parameters: {
                            type: "object",
                            properties: {
                                gig_cryptic: {
                                    type: "string",
                                    description: "The spoken gig code or number, like 'GIG12345' or '12345'."
                                }
                            },
                            required: ["gig_cryptic"]
                        }
                    },
                    {
                        type: "function",
                        name: "navigate_to_page",
                        description: "Call this when the user wants to navigate to a specific technician page like profile, dashboard (this is the same with home page), schedules, calendar(calendar is just the same with schedules page), guild-profile, notification, set-schedule etc.",
                        parameters: {
                            type: "object",
                            properties: {
                                destination: {
                                    type: "string",
                                    description: "The destination keyword like 'profile', 'analytics', 'dashboard', 'set-schedule', 'schedules', 'notification', 'guild-profile'"
                                }
                            },
                            required: ["destination"]
                        }
                    },
                    {
                        type: "function",
                        name: "open_model_page",
                        description: "Call this when the technician wants to view the model or machine details for a gig. If no gig is provided, use the most recent one.",
                        parameters: {
                            type: "object",
                            properties: {
                                gig_cryptic: {
                                    type: "string",
                                    description: "The gig's cryptic code (optional)"
                                }
                            }
                            // 🚫 No 'required' key — now it's optional
                        }
                    }
                );

                const event = {
                    type: "session.update",
                    session: {
                        modalities: ["text", "audio"],
                        voice: "ash",
                        tools: tools,
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
                                vector_store_ids: [`${this.vector_id}`]
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

                const vectorStores = response.data || [];

                // 👇 Extract only the "id" values
                this.vectorIDs = vectorStores.map(store => store.id);

                console.log("Vector IDs:", this.vectorIDs);

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

        async handleFunctionCall(name, args) {
            switch (name) {
                case 'query_about_machine': {
                    const data = await this.runFileSearchTool(args.user_query)
                    const snippet = data.output?.[1]?.content?.[0]?.text || ''
                    return snippet
                        ? `Here’s what I found in the docs: ${snippet}`
                        : `Sorry, I couldn't find anything on that.`
                }

                case 'call_client_by_voice': {
                    if (!args.confirm_call) return "";
                    const gigId = this.$route?.params?.id;
                    const gig = this.$store.state.gigHistory.find(g => g.gig_id == gigId);
                    if (gig?.client_phone_number) {
                        window.open(`tel:${gig.client_phone_number}`, "_self");
                        return `Sure, calling ${gig.client_name} now.`;
                    } else {
                        return `Sorry, I couldn't find the client's phone number.`;
                    }
                }

                case 'send_status_message': {
                    bus.emit("trigger-send-status", args.status);
                    return `Sending "${args.status.replace(/-/g, ' ')}" message to the client now.`;
                }

                case 'open_google_map': {
                    bus.emit("trigger-open-map");
                    return `Opening Google Maps to the client's address now.`;
                }

                case 'send_email': {
                    bus.emit("trigger-send-email");
                    return `Opening your Mail App`;
                }

                case 'open_gig_from_voice': {
                    const code = args.gig_cryptic?.toUpperCase();
                    const gig = this.$store.state.gigHistory.find(g => g.gig_cryptic.toUpperCase() === code);
                    if (!gig) return `Sorry, I couldn't find a gig matching ${code}`;
                    this.$router.push(`/gig/${gig.gig_id}`);
                    return `Sure, opening Gig ${gig.gig_cryptic}`;
                }

                case 'navigate_to_page': {
                    const map = {
                        profile: "/profile",
                        "set-schedule": "/set-schedule",
                        "guild-profile": "/guild-profile",
                        notification: "/notification",
                        dashboard: "/dashboard",
                        schedules: "/schedules",
                        analytics: "/analytics",
                    };
                    const path = map[args.destination];
                    if (!path) return `Sorry, I don't recognize the page "${args.destination}"`;

                    this.$router.push(path);

                    return `Navigating to your ${args.destination.replace(/-/g, ' ')} page.`;
                }

                case 'open_model_page': {
                    const gigs = this.$store.state.gigHistory || [];
                    let gig = args.gig_cryptic
                        ? gigs.find(g => g.gig_cryptic.toUpperCase() === args.gig_cryptic.toUpperCase())
                        : gigs[0];
                    if (!gig || !gig.machine) return `I couldn't find a model page to open.`;
                    const { model_number } = gig.machine;
                    const id = gig.gig_id;
                    if (!id) return `You don’t have a gig associated with this machine.`;
                    this.$router.push(`/model/${model_number}/gig/${id}`);
                    return `Opening model page for ${model_number}`;
                }

                default:
                    return `Sorry, I don't recognize the action "${name}"`;
            }
        },
        notifyMapPopupBlocked() {
            const message = "It looks like your browser or app blocked the Google Maps window. Please check your popup settings or permissions.";

            if (this.dataChannel?.readyState === "open") {
                this.dataChannel.send(JSON.stringify({
                    type: "response.create",
                    response: {
                        modalities: ["text", "audio"],
                        voice: "ash",
                        instructions: message
                    }
                }));
            }
        },
        restoreDaxState() {
            if (this.$store.state.isDaxActive) {
                this.recording = true;
                if (!this.peerConnection || !this.dataChannel) {
                    this.startRecording(); // Only if disconnected
                }
            }
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
