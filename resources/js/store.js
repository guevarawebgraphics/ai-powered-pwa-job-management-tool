import { createStore } from 'vuex';

const store = createStore({
  state: {
    notificationData: [],
    chatMessages: [],
    unseenNotification: 0,
    isLoading: false,
    vectoreIDs: [],
    pdfFiles: [],
    gigHistory: [],
    isDaxActive: false
  },
  mutations: {
    setNotificationData(state, payload) {
      state.notificationData = payload;
    },
    setChatMessages(state, payload) {
      state.chatMessages = payload;
    },
    setUnseenNotification(state, payload) {
      state.unseenNotification = payload;
    },
    addChatMessage(state, message) {
      state.chatMessages.push(message);
    },
    // Mutation to update loading state
    setLoading(state, payload) {
      state.isLoading = payload;
    },
    setVectorIDs(state, payload) {
      state.vectoreIDs = payload;
    },
    setPdfFiles(state, payload) {
      state.pdfFiles = payload;
    },
    setGigHistory(state, payload) {
      state.gigHistory = payload;
    },
    setDaxActive(state, payload) {
      state.isDaxActive = payload;
    }
  },
  actions: {
    updateNotificationData({ commit }, payload) {
      commit('setNotificationData', payload);
    },
    updateChatMessages({ commit }, payload) {
      commit('setChatMessages', payload);
    },
    updateUnseenNotification({ commit }, payload) {
      commit('setUnseenNotification', payload);
    },
    addChatMessage({ commit }, message) {
      commit('addChatMessage', message);
    },
    // Actions to show or hide the loader
    showLoader({ commit }) {
      commit('setLoading', true);
    },
    hideLoader({ commit }) {
      commit('setLoading', false);
    },
    setVectorIDs({ commit }, message) {
      commit('setVectorIDs', message);
    },
    setPdfFiles({ commit }, message) {
      commit('setPdfFiles', message);
    },
    setGigHistory({ commit }, message) {
      commit('setGigHistory', message);
    },
    setDaxActive({ commit }, message) {
      commit('setDaxActive', message);
    },
  }
});

export default store;
