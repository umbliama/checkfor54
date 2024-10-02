const state = {
    currentPage: 1,
    totalPages: 1
  };
  
  const mutations = {
    SET_CURRENT_PAGE(state, page) {
      state.currentPage = page;
    },
    SET_TOTAL_PAGES(state, totalPages) {
      state.totalPages = totalPages;
    }
  };
  
  const actions = {
    updatePagination({ commit }, { currentPage, totalPages }) {
      commit('SET_CURRENT_PAGE', currentPage);
      commit('SET_TOTAL_PAGES', totalPages);
    }
  };
  
  const getters = {
    currentPage: state => state.currentPage,
    totalPages: state => state.totalPages
  };
  
  export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
  };
  