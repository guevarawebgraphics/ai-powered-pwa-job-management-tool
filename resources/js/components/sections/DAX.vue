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
            speakQueue: [],
            isSpeaking: false,
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

            if (this.recording && this.dataChannel?.readyState === "open") {
                // Just reconfigure tools and instructions
                this.configureData();
            } else if (this.$store.state.isDaxActive) {
                // If recording was lost during route change, recover it
                this.startRecording();
            }
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
            // this.recording = false;
            // this.speaking = false;
            // this.chatHistory = [];
            // this.typingReply = '';
            // if (this.peerConnection) {
            //     this.peerConnection.close();
            //     this.peerConnection = null;
            // }
            // this.stopRecording();
        },
        async startRecording() {
            if (this.recording) return; // ✅ Don't re-init if already active
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

                    if (msg.type === 'response.function_call_arguments.done') {

                        const msg = JSON.parse(ev.data)
                        const args = JSON.parse(msg.arguments)

                        // Now this is fine:
                        const result = await this.handleFunctionCall(msg.name, args);

                        if (result.message) {
                            // 1) send function_call_output
                            this.dataChannel.send(JSON.stringify({
                                type: 'conversation.item.create',
                                item: {
                                    type: 'function_call_output',
                                    call_id: msg.call_id,
                                    output: JSON.stringify(result.message)
                                }
                            }));

                            // 2) send response.create
                            this.dataChannel.send(JSON.stringify({
                                type: 'response.create',
                                response: {
                                    modalities: ['text', 'audio'],
                                    voice: 'ash',
                                    instructions: result.message
                                }
                            }));
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
                    name: "navigate_to_global_page",
                    description: `Use this tool to navigate to a main page in the technician app.

Supported global destinations include:
- 'dashboard' → Home page (main landing screen)
- 'calendar' or 'schedules' → Schedule view (same destination)
- 'set-schedule' → Availability setup
- 'analytics' or 'stats' → Gig performance dashboard
- 'profile' or 'settings' → Technician's profile page
- 'notification' → Alerts and status updates
- 'guild-profile' or 'forum' → Guild achievements and community page

Use either the main keyword or its synonym.`,
                    parameters: {
                        type: "object",
                        properties: {
                            destination: {
                                type: "string",
                                description: "Destination keyword (e.g., 'calendar', 'dashboard', 'profile', 'guild-profile')"
                            }
                        },
                        required: ["destination"]
                    }
                });

                tools.push({
                    type: "function",
                    name: "navigate_to_gig_context",
                    description: `Use this tool to navigate within gig-related contexts.

                    Supported destinations:
                    1. **Gigs:** Associate to a gig ID with job identifiers like:
                    - Cryptic codes (e.g., 'GIG123')
                    - Time-based references (e.g., 'the 2 PM job', 'my 11 AM gig')
                    - Associated client details (e.g., 'Robert Mack's gig')

                    2. **Gig Views** (requires active gig context):
                    - 'Client Page' → View client details
                    - 'Machine Page' → View appliance information

                    3. **Machine Documents** (requires gig + machine context):
                    - Document types: 'tech sheet', 'service manual', 'parts list', 'service pointer'
                    - Optional query (e.g., 'noisy operation') for narrowing results.`,
                    parameters: {
                        type: "object",
                        properties: {
                            target_type: {
                                type: "string",
                                enum: ["gig", "gig_client_page", "gig_machine_page", "machine_document"],
                                description: "Type of navigation: a gig, its client page, its machine page, or a specific document"
                            },
                            // identifier: {
                            //     type: "string",
                            //     description: "Gig code (e.g., 'GIG123'), time reference (e.g., '2 PM job'), or document type"
                            // },
                            identifier: {
                                type: "string",
                                description: "Gig ID based on correlated information to your <knowledge>"
                            },
                            document_query: {
                                type: "string",
                                description: "Optional. Search term for filtering document content (e.g., 'leaking issue')"
                            }
                        },
                        required: ["target_type"]
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
                    2.  **Sending SMS:** Send text messages. Use 'template' type for status updates like 'behind-schedule', 'on-time', 'arriving-early' (typically from Gig page context). Use 'blank' type for a standard message composer (typically from Client page context).
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
                                enum: ["behind-schedule", "on-time", "arriving-early"], // Match your exact template keys
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

                // console.log(`DAX Gigs: ${JSON.stringify(this.$store.state.gigOpenAIObject) }`);
                // --- Session Update Event ---
                const event = {
                    type: "session.update",
                    session: {
                        modalities: ["text", "audio"],
                        voice: "ash", // Or your preferred voice
                        tools: tools, // Use the new consolidated tools array
                        tool_choice: "auto", // Let OpenAI decide which tool (and args) fit the user's request
                        // Update instructions to mention synonyms and context sensitivity
                        instructions: `
                        <bio>You are DAX. Your name is DAX, a voice technicial assistant for professional appliance repair technicians. You are embedded in their work app.</bio>

                        <knowledge>
                        Current Page Context: ${currentPage}
                        Page Content Context: "${pageContent.trim()}"
                        Here is a list of the gigs with the ID that corresponds to the info in the object
                        ${JSON.stringify(this.$store.state.gigOpenAIObject)}
                        </knowledge>

                        <important_rules>
                        - Help navigate the app using the 'navigate' tool. Recognize synonyms: Calendar/Schedule, Notification, Analytics/Stats, Profile/Settings, Gigs/Jobs. Handle navigation requests like "show me", "pull up", "go to".
                        - Perform actions related to gigs (call, SMS, map, query machine) using the 'perform_action' tool. Understand context: template SMS are for status updates (usually from gig list/details), blank SMS for custom messages (usually from client page).
                        - Use the 'file_search_tool' to answer questions requiring information from uploaded documents (PDFs, manuals).
                        - Handle time-based gig requests (e.g., "pull up my 2 o'clock job") by using the 'navigate' tool with a time identifier.
                        - Be concise, technical, and assume user expertise. Focus on diagnostics, codes, parts, and procedures.
                        - Use available tools when appropriate based on the user request and current context.
                        </important_rules>
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
            console.log(`Handling function call: ${name}`, args);
            let result = { success: false, message: "Action could not be completed." };

            try {
                switch (name) {

                    // 🎯 Global Page Navigation
                    case 'navigate_to_global_page': {
                        const { destination } = args;
                        const map = {
                            profile: "/profile",
                            settings: "/profile",
                            "set-schedule": "/set-schedule",
                            "guild-profile": "/guild-profile",
                            forum: "/guild-profile",
                            notification: "/notification",
                            dashboard: "/dashboard",
                            home: "/dashboard",
                            calendar: "/schedules",
                            schedules: "/schedules",
                            analytics: "/analytics",
                            stats: "/analytics"
                        };
                        const path = map[destination.toLowerCase()];
                        result = path
                            ? (this.$router.push(path), { success: true, message: `Navigating to "${destination}"` })
                            : { success: false, message: `Sorry, I don't recognize the page "${destination}"` };
                        break;
                    }

                    // 🧭 Gig-related Navigation (including gig views and documents)
                    case 'navigate_to_gig_context': {
                        const { target_type, identifier, document_query } = args;

                        switch (target_type) {

                            case 'gig': {
                                const gigs = this.$store.state.gigOpenAIObject || [];
                                const identifierText = identifier.trim().toLowerCase();
                                const gig = gigs.find(g => g.gigCryptic.toUpperCase() === identifierText.toUpperCase());

                                let matchedGigs = [];

                                if (gig) {
                                    if (gig) matchedGigs = [gig];
                                } else {
                                    // Match by 12-hour formatted time like "12:00 am"
                                    const byTime = gigs.find(g => g.gigTime === identifierText);
                                    if (byTime) matchedGigs = [byTime];

                                    // Optional: support matching by client name (e.g. "stacey tate")
                                    if (!byTime) {
                                        matchedGigs = gigs.filter(g => g.gigClient.toLowerCase().includes(identifierText));
                                    }
                                }

                                if (matchedGigs.length === 0) {
                                    const list = gigs.slice(0, 5).map(g =>
                                        `${g.gigCryptic} (${g.gigTime}) for ${g.gigClient}`
                                    ).join('\n');
                                    result = {
                                        success: false,
                                        message: `I couldn't find a gig matching "${identifier}". Here are your upcoming gigs:\n${list}`
                                    };
                                } else if (matchedGigs.length > 1) {
                                    const list = matchedGigs.slice(0, 3).map(g =>
                                        `${g.gigCryptic} (${g.gigTime}) for ${g.gigClient}`
                                    ).join('\n');
                                    result = {
                                        success: false,
                                        message: `I found multiple gigs that match:\n${list}\nCan you specify the time or name more clearly?`
                                    };
                                } else {
                                    const gig = matchedGigs[0];
                                    this.$router.push(`/gig/${gig.gigID}`);
                                    result = {
                                        success: true,
                                        message: `Sure, opening Gig ${gig.gigCryptic} for ${gig.gigClient}`
                                    };
                                }

                                break;
                            }



                            case 'gig_client_page': {
                                const gig = this.$store.state.gigData;
                                if (gig?.client_id) {
                                    this.$router.push(`/customer/${gig.client_id}/gig/${gig.gig_id}`);
                                    result = { success: true, message: `Opening client page.` };
                                } else {
                                    result = { success: false, message: `Sorry, I couldn't find the client's page.` };
                                }
                                break;
                            }

                            case 'gig_machine_page': {
                                const gig = this.$store.state.gigData;
                                if (gig?.machine?.model_number) {
                                    this.$router.push(`/model/${gig.machine.model_number}/gig/${gig.gig_id}`);
                                    result = { success: true, message: `Opening model page for ${gig.machine.model_number}` };
                                } else {
                                    result = { success: false, message: `I couldn't find a matching machine page.` };
                                }
                                break;
                            }

                            case 'machine_document': {
                                result = { success: true, message: `Searching documents for: ${identifier}` };
                                await this.speak(result.message);
                                const data = await this.runFileSearchTool(document_query || identifier);
                                const snippet = data.output?.[1]?.content?.[0]?.text || "No relevant document content found.";
                                result.message = snippet;
                                break;
                            }

                            default:
                                result.message = `Unknown gig context target: ${target_type}`;
                        }
                        break;
                    }

                    // 🛠️ Other Actions
                    case 'perform_action': {
                        const { action_type, sms_type, sms_template, query_text } = args;
                        switch (action_type) {
                            case 'call_client': {
                                if (!args.confirm_call) return "";
                                const gig = this.$store.state.gigData;
                                if (gig?.client_phone_number) {
                                    window.open(`tel:${gig.client_phone_number}`, "_blank");
                                    // result = { success: true, message: `Sure, calling ${gig.client_name} now.` };
                                    result = { success: true, message: `` };
                                } else {
                                    result = { success: false, message: `Sorry, I couldn't find the client's phone number.` };
                                }
                                break;
                            }

                            case 'send_sms': {
                                if (sms_type === 'template') {
                                    if (!sms_template) {
                                        result = {
                                            success: false,
                                            message: `Template SMS type requires a specific status like "arriving-early", "on-time", or "behind-schedule".`
                                        };
                                    } else {
                                        bus.emit("trigger-send-status", sms_template); // Send the exact template
                                        result = {
                                            success: true,
                                            // message: `Sending "${sms_template.replace(/-/g, ' ')}" message to the client now.`
                                            message: ``
                                        };
                                    }
                                } else if (sms_type === 'blank') {
                                    bus.emit("trigger-send-status", "blank");
                                    result = {
                                        success: true,
                                        // message: `Opening a custom message composer for the client.`
                                        message: ``
                                    };
                                } else {
                                    result = {
                                        success: false,
                                        message: `Unknown sms_type: "${sms_type}". It must be either "template" or "blank".`
                                    };
                                }
                                break;
                            }
                            case 'open_map': {
                                bus.emit("trigger-open-map");
                                result = { success: true, message: `` };
                                break;
                            }
                            case 'query_machine_info': {
                                result = { success: true, message: `Looking up information for: ${query_text}` };
                                await this.speak(result.message);
                                const data = await this.runFileSearchTool(query_text);
                                const snippet = data.output?.[1]?.content?.[0]?.text || "No relevant document content found.";
                                result.message = snippet;
                                break;
                            }
                            default:
                                result.message = `Unknown action type: ${action_type}`;
                        }
                        break;
                    }

                    // 🗂️ File Search Tool (fallback)
                    case 'file_search_tool': {
                        const { search_query } = args;
                        result = { success: true, message: `Searching documents for: ${search_query}. I will provide the results shortly.` };
                        await this.speak(result.message);
                        const data = await this.runFileSearchTool(search_query);
                        const snippet = data.output?.[1]?.content?.[0]?.text || "No relevant document content found.";
                        result.message = snippet;
                        break;
                    }

                    default: {
                        console.warn(`Function call received for unknown tool: ${name}`);
                        result.message = `Tool '${name}' is not implemented.`;
                    }
                }
            } catch (error) {
                console.error(`Error handling function call ${name}:`, error);
                result.message = `An error occurred while trying to ${name}.`;
            }

            return result;
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
        },
        parseTimeReference(reference) {
            const match = reference.match(/(\d{1,2})(?::(\d{2}))?\s*(AM|PM)?/i);
            if (!match) return null;

            let [_, hour, minute = "00", meridiem] = match;
            hour = parseInt(hour);
            minute = parseInt(minute);

            if (meridiem) {
                meridiem = meridiem.toUpperCase();
                if (meridiem === "PM" && hour < 12) hour += 12;
                if (meridiem === "AM" && hour === 12) hour = 0;
            }

            return { hour, minute };
        },
        formatGigInfo(g) {
            let t = g.start_datetime;
            if (t.indexOf("T") === -1) t = t.replace(" ", "T") + "Z";
            const gigTime = new Date(t);

            const time = gigTime.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
                timeZone: 'UTC'
            });
            const date = gigTime.toLocaleDateString('en-US', { timeZone: 'UTC' });

            const model = g.machine?.model_number || 'unknown model';
            const mtype = g.machine?.machine_type || 'unknown machine type';
            const mbrand = g.machine?.brand_name || 'unknown brand';
            const modelv = `${model} ${mtype} ${mbrand}`;

            return `• ${date} ${time} – ${modelv}`;
        },
        speak(text, delayMs = null) {
            return new Promise(resolve => {
                this.speakQueue.push({ text, delayMs, resolve });
                this.processSpeakQueue();
            });
        },


        processSpeakQueue() {
            if (this.isSpeaking || !this.speakQueue.length) return;

            const { text, delayMs, resolve } = this.speakQueue.shift();
            this.isSpeaking = true;

            this.dataChannel?.send(JSON.stringify({
                type: 'response.create',
                response: {
                    modalities: ['text', 'audio'],
                    voice: 'ash',
                    instructions: text
                }
            }));

            const estimatedDelay = delayMs || this.estimateSpeechTime(text);

            setTimeout(() => {
                this.isSpeaking = false;
                resolve(); // ✅ Now this resolves the promise
                this.processSpeakQueue();
            }, estimatedDelay);
        },

        estimateSpeechTime(text) {
            const words = text.trim().split(/\s+/).length;
            return words * 250;
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
