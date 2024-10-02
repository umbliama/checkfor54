export default {
    namespaced: true,
    state: () => ({
        createdContragentID: null,
        formData: {
            agentTypeLegal: "",
            country: "",
            name: "",
            fullname: "",
            inn: "",
            kpp: "",
            ogrn: "",
            reason: "",
            notes: "",
            commentary: "",
            group: "",
            bankname: "",
            bank_bik: "",
            bank_inn: "",
            bank_rs: "",
            bank_ca: "",
            bank_commnetary: "",
            supplier: false,
            customer: false,
            address: "",
            site: "",
            phone: "",
            email: "",
            contact_person: "",
            contact_person_phone: "",
            contact_person_email: "",
            contact_person_notes: "",
            contact_person_commentary: "",
            status: "active",
            avatar: "",
        },
        activeTab: "profile",
        activeMenuLink: "all",
        openDropdown:false
    }),
    mutations: {
        setOpenDropdown:(state,value) => {
            state.openDropdown = value
        },
        setActiveMenuLink: (state, link) => {
            state.activeMenuLink = link;
        },
        setCreatedContragentID: (state, contrAgentId) => {
            state.createdContragentID = contrAgentId;
        },
        setActiveTab: (state, tab) => {
            state.activeTab = tab;
        },
        setFormData: (state, payload) => {
            state.formData = { ...state.formData, ...payload };
        },
    },
    actions: {
        updateOpenDropdown({commit}, payload) {
            commit('setOpenDropdown',payload)
        },
        updateActiveMenuLink({ commit }, payload) {
            commit("setActiveMenuLink", payload);
        },
        updateFormData({ commit }, payload) {
            commit("setFormData", payload);
        },
        updateActiveTab({ commit }, tab) {
            commit("setActiveTab", tab);
        },
        updateCreatedContragentID({ commit }, contrAgentId) {
            commit("setCreatedContragentID", contrAgentId);
        },
    },
    getters: {
        getActiveMenuLink: (state) => state.activeMenuLink,
        getOpenDropdown: (state) => state.openDropdown,
        getActiveTab: (state) => state.activeTab,
        getFormData: (state) => state.formData,
        getCreatedContragentID: (state) => state.createdContragentID,
    },
};
