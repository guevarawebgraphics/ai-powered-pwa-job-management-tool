
let recognition = null;
let listening = false;
let onResult = () => {};

export const MicManager = {
  start(callback) {
    if (listening) return;
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
      console.warn("MicManager: SpeechRecognition not supported");
      return;
    }
    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = false;
    recognition.lang = 'en-US';

    onResult = callback;
    recognition.onresult = (event) => {
      if (!listening) return;
      for (let i = event.resultIndex; i < event.results.length; i++) {
        if (event.results[i].isFinal) {
          const text = event.results[i][0].transcript.trim();
          if (text) onResult(text);
        }
      }
    };
    recognition.onerror = (e) => console.warn('MicManager error:', e.error);
    recognition.onend = () => {
      if (listening) recognition.start();
    };
    recognition.start();
    listening = true;
    console.log('MicManager: started');
  },

  stop() {
    if (!listening) return;
    try {
      recognition.abort();
      recognition.onresult = null;
      recognition.onerror = null;
      recognition.onend = null;
      console.log('MicManager: stopped');
    } catch (e) {
      console.warn('MicManager stop error:', e);
    }
    recognition = null;
    listening = false;
    onResult = () => {};
  },

  isActive() {
    return listening;
  }
};

