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
                const currentPage = this.page; // Get current page context

                console.log(`Configuring tools for page: ${currentPage}`);
                console.log(`Appended files: `, this.$store.state.vectoreIDs); // Assuming you still need this

                const tools = [];

                // --- Consolidated Navigation Tool ---
                tools.push({
                    type: "function",
                    name: "navigate",
                    description: `Handles navigation within the technician app. Use this tool to go to:
1.  **Global Pages:** Use page names like 'Home', 'Calendar' (or 'Schedule', 'Upcoming jobs'), 'Analytics' (or 'Stats'), 'Guild' (or 'Forum'), 'Profile' (or 'Settings').
2.  **Specific Gigs:** Use identifiers like cryptic codes (e.g., 'GIG123') or time references (e.g., 'my 2 o'clock job', 'the job at 11 AM'). Requires context like Home or Calendar page.
3.  **Gig-Specific Views:** When inside a gig context, use targets like 'Client Page' or 'Machine Page'.
4.  **Machine Documents:** When inside a gig/machine context, specify document types like 'Tech Sheet', 'Service Manual', 'Parts List', or 'Service Pointer'. You can include a query for specific document content.`,
                    parameters: {
                        type: "object",
                        properties: {
                            target_type: {
                                type: "string",
                                enum: ["global_page", "gig", "gig_client_page", "gig_machine_page", "machine_document"],
                                description: "The type of destination: a main app page, a specific gig, the client page within a gig, the machine page within a gig, or a specific document related to the machine."
                            },
                            identifier: {
                                type: "string",
                                description: "Identifier for the target. For 'global_page', use page name (e.g., 'calendar', 'profile'). For 'gig', use cryptic code (e.g., 'GIG123') or time reference (e.g., '2 PM job', '11 o'clock'). For 'machine_document', use document type/name (e.g., 'tech sheet', 'parts list'). Can be omitted for client/machine pages if context is clear."
                            },
                            document_query: {
                                type: "string",
                                description: "Optional specific query for machine documents, e.g., 'noisy operation' when asking for a 'service pointer'."
                            }
                        },
                        required: ["target_type"] // Identifier might be handled via context lookups (e.g., time) or required based on target_type in handling logic.
                    }
                });

                // --- Consolidated Action Tool ---
                // This tool might be primarily useful within a Gig context, but we define it globally.
                // The LLM should use context (current page, conversation) to call it appropriately.
                tools.push({
                    type: "function",
                    name: "perform_action",
                    description: `Performs specific actions, usually related to a gig context. Actions include:
1.  **Calling Client:** Initiate a voice call to the client associated with the current gig.
2.  **Sending SMS:** Send text messages. Use 'template' type for status updates like 'arriving_early', 'on_time', 'arriving_late' (typically from Gig page context). Use 'blank' type for a standard message composer (typically from Client page context).
3.  **Opening Map:** Show directions to the client's address for the current gig using Google Maps.
4.  **Querying Machine Info:** Ask for technical details or search documents related to the machine in the current gig (distinct from navigating to a specific document type).`,
                    parameters: {
                        type: "object",
                        properties: {
                            action_type: {
                                type: "string",
                                enum: ["call_client", "send_sms", "open_map", "query_machine_info"],
                                description: "The specific action to perform."
                            },
                            sms_type: {
                                type: "string",
                                enum: ["template", "blank"],
                                description: "Required only if action_type is 'send_sms'. Specifies 'template' (for early/on_time/late status) or 'blank' (for custom message)."
                            },
                            sms_template: {
                                type: "string",
                                enum: ["arriving_early", "on_time", "arriving_late"], // Match your exact template keys
                                description: "Required only if sms_type is 'template'. Specifies which status update template to use."
                            },
                            query_text: {
                                 type: "string",
                                 description: "Required only if action_type is 'query_machine_info'. The user's technical question about the appliance/machine."
                            }
                        },
                        required: ["action_type"] // Other args like sms_type, sms_template, query_text become required based on the chosen action_type. Your handler validates this.
                    }
                });

                 // --- Optional: Keep File Search Tool if needed ---
                 // This seems more related to querying vector stores/documents than core navigation/actions.
                 // Decide if this should be separate or integrated into 'query_machine_info'.
                 // For now, keeping it separate if your `runFileSearchTool` logic is distinct.
                 // Consider renaming 'query_about_machine' if you keep it separate.
                 tools.push({
                        type: "function",
                        name: "file_search_tool", // Renamed from query_about_machine for clarity if it specifically uses vector search
                        description: "Use this function to search uploaded documents (PDFs, tech sheets etc.) when the user asks a question that likely requires information from specific files.",
                        parameters: {
                            type: "object",
                            strict: true, // Assuming you want strict validation
                            properties: {
                                search_query: { // Renamed from user_query for clarity
                                    type: "string",
                                    description: "The user's question or search term to look up in the documents. Phrase it clearly."
                                }
                            },
                            required: ["search_query"]
                        }
                    });


                // --- Session Update Event ---
                const event = {
                    type: "session.update",
                    session: {
                        modalities: ["text", "audio"],
                        voice: "ash", // Or your preferred voice
                        tools: tools, // Use the new consolidated tools array
                        tool_choice: "auto", // Let OpenAI decide which tool (and args) fit the user's request
                        // Update instructions to mention synonyms and context sensitivity
                        instructions: `You are DAX, a voice assistant for appliance repair technicians. You are embedded in their work app.
Current Page Context: ${currentPage}
Page Content Context: "${pageContent.trim()}"

Key Tasks & Rules:
- Help navigate the app using the 'navigate' tool. Recognize synonyms: Calendar/Schedule, Analytics/Stats, Profile/Settings, Gigs/Jobs. Handle navigation requests like "show me", "pull up", "go to".
- Perform actions related to gigs (call, SMS, map, query machine) using the 'perform_action' tool. Understand context: template SMS are for status updates (usually from gig list/details), blank SMS for custom messages (usually from client page).
- Use the 'file_search_tool' to answer questions requiring information from uploaded documents (PDFs, manuals).
- Handle time-based gig requests (e.g., "pull up my 2 o'clock job") by using the 'navigate' tool with a time identifier.
- Be concise, technical, and assume user expertise. Focus on diagnostics, codes, parts, and procedures.
- Use available tools when appropriate based on the user request and current context.
`,
                    },
                };
                this.dataChannel.send(JSON.stringify(event));
                console.log("Sent session.update with consolidated tools:", event);
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
            console.log(`Handling function call: ${name}`, args);
            let result = { success: false, message: "Action could not be completed." }; // Default result

            try {
                if (name === 'navigate') {
                    // --- Logic for the 'navigate' tool ---
                    const { target_type, identifier, document_query } = args;

                    // TODO: Implement navigation based on target_type
                    if (target_type === 'global_page') {
                        console.log(`Navigating to global page: ${identifier}`);
                        // Example: this.$router.push({ name: mapIdentifierToRouteName(identifier) });
                        result = { success: true, message: `Navigating to ${identifier}.` };
                    } else if (target_type === 'gig') {
                         console.log(`Finding gig with identifier: ${identifier}`);
                         // TODO: Look up gig by code or time
                         // Example: const gig = await findGig(identifier); this.$router.push({ name: 'GigDetail', params: { id: gig.id } });
                         result = { success: true, message: `Opening gig: ${identifier}.` };
                    } else if (target_type === 'gig_client_page') {
                         // TODO: Ensure in gig context, navigate to client sub-page
                         console.log(`Navigating to client page for current gig.`);
                         result = { success: true, message: `Opening client page.` };
                    } else if (target_type === 'gig_machine_page') {
                         // TODO: Ensure in gig context, navigate to machine sub-page
                         console.log(`Navigating to machine page for current gig.`);
                          result = { success: true, message: `Opening machine page.` };
                    } else if (target_type === 'machine_document') {
                         // TODO: Ensure in gig context, find and potentially display document
                         console.log(`Opening machine document: ${identifier}`, Query: ${document_query || 'N/A'});
                         result = { success: true, message: `Opening document: ${identifier}.` };
                    } else {
                         result.message = `Unknown navigation target type: ${target_type}`;
                    }

                } else if (name === 'perform_action') {
                    // --- Logic for the 'perform_action' tool ---
                    const { action_type, sms_type, sms_template, query_text } = args;

                    // TODO: Implement action based on action_type
                    if (action_type === 'call_client') {
                        console.log(`Initiating call to client...`);
                        // Example: bus.emit('call-client', currentGigId);
                        result = { success: true, message: `Calling the client.` };
                    } else if (action_type === 'send_sms') {
                         console.log(`Sending SMS. Type: ${sms_type}`, Template: ${sms_template || 'N/A'});
                         // TODO: Differentiate between template and blank SMS logic
                         // Example: bus.emit('send-sms', { type: sms_type, template: sms_template, gigId: currentGigId });
                         result = { success: true, message: `SMS action initiated.` };
                    } else if (action_type === 'open_map') {
                         console.log(`Opening map for client address...`);
                         // Example: bus.emit('open-map', currentGigAddress);
                         result = { success: true, message: `Opening map directions.` };
                         // Handle potential popup blocker feedback
                         // You might need a way for the map component to signal back if blocked
                    } else if (action_type === 'query_machine_info') {
                         console.log(`Querying machine info: ${query_text}`);
                         runFileSearchTool(query_text);
                         // This might overlap with file_search_tool - decide how to handle
                         // Example: const info = await queryMachineBackend(query_text, currentGigId);
                         result = { success: true, message: `Looking up information for: ${query_text}` };
                    } else {
                         result.message = `Unknown action type: ${action_type}`;
                    }

                } else if (name === 'file_search_tool') {
                    // --- Logic for file search (if kept separate) ---
                    const { search_query } = args;
                    console.log(`Performing file search for: ${search_query}`);
                    // Example: result = await this.runFileSearchTool(search_query);
                    // The result from runFileSearchTool might need processing to fit the expected output format.
                    // For now, just confirming the call was received.
                    result = { success: true, message: `Searching documents for: ${search_query}. I will provide the results shortly.` };
                    // Note: The actual results might come asynchronously via the API response, not necessarily in this direct function output.
                    // You might need to adjust how you handle the output vs. the live response stream.

                } else {
                    console.warn(`Function call received for unknown tool: ${name}`);
                    result.message = `Tool '${name}' is not implemented.`;
                }

            } catch (error) {
                console.error(`Error handling function call ${name}:`, error);
                result.message = `An error occurred while trying to ${name}.`;
            }

            // Return the result to be sent back to OpenAI
            return result;
        }

    } // End methods
};
</script>

<style scoped>
/* Add your component-specific styles here */
#remoteAudio {
    display: none;
}
</style>
