const state = {
    currentPage: 1,
    totalPages: 1,
    perPage: 10
  };
  
  const mutations = {
    SET_CURRENT_PAGE(state, page) {
      state.currentPage = page;
    },
    SET_TOTAL_PAGES(state, totalPages) {
      state.totalPages = totalPages;
    },
    SET_PER_PAGE(state, perPage) {
      state.perPage = perPage
    }
  };
  
  const actions = {
    updatePagination({ commit }, { currentPage, totalPages }) {
      commit('SET_CURRENT_PAGE', currentPage);
      commit('SET_TOTAL_PAGES', totalPages);
    },
    updateCurrentPage({commit}, currentPage) {
      commit('SET_CURRENT_PAGE', currentPage)
    },
    updatePerPage({commit}, perPage) {
      commit('SET_PER_PAGE', perPage)
    }
  };
  
  const getters = {
    currentPage: state => state.currentPage,
    totalPages: state => state.totalPages,
    perPage: state => state.perPage
  };
  
  export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
  };
  