<template>
        <!-- DAX Button to open the modal -->
        <button type="button" @click="openModal" v-if="page !== 'Model'"
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
                <!-- <template v-else-if="speaking">
                    <h2 class="text-4xl font-semibold leading-tight">Responding...</h2>
                    <p class="text-2xl text-gray-500 mt-1">Please wait while the AI responds</p>
                </template> -->
                <template v-else>
                    <h2 class="text-4xl font-semibold leading-tight">Talk to DAX</h2>
                    <p class="text-2xl text-gray-500 mt-1">Press the button to start talking</p>
                </template>
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
        }
    },
    data() {
        return {
            showModal: false,
            recording: false,
            speaking: false,
            peerConnection: null,
            localStream: null,
            dataChannel: null,
        };
    },
    methods: {
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.recording = false;
            this.speaking = false;
            if (this.peerConnection) {
                this.peerConnection.close();
                this.peerConnection = null;
            }
        },
        async startRecording() {
            this.recording = true;
            this.speaking = false;
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
                    const msg = JSON.parse(ev.data);
                    console.log("Received message:", msg);
                    // Here you can process responses from the AI (e.g. conversation responses)
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


                // 7. Send the SDP offer to the OpenAI Realtime endpoint.
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
            this.speaking = true;
            // Optionally close the connection if you wish to end the session.
            if (this.peerConnection) {
                this.peerConnection.close();
                this.peerConnection = null;
            }
        },
        configureData() {
            // Send a session update on the data channel (if desired)
            // For a simple conversation, you might only need to notify the API of supported modalities.
            if (this.dataChannel && this.dataChannel.readyState === "open") {
                const event = {
                    type: "session.update",
                    session: {
                        modalities: ["text", "audio"]
                    },
                };
                this.dataChannel.send(JSON.stringify(event));
                console.log("Sent session.update:", event);
            }
        },
    },
};
</script>

<style scoped>
/* Add your component-specific styles here */
</style>
