export default {
    namespaced: true,
    state: () => ({
        notifications: [],
    }),
    mutations: {
        setNotifications(state, notifications) {
            state.notifications = notifications;
        },
    },
    actions: {
        updateNotifications({commit}, payload) {
            commit('setNotifications', payload)
        },

        async checkNewNotifications({commit},userId) {

            try {
                const response = await fetch(`/api/getNotificationsByUserId/${userId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });
                const notifications = await response.json();
    
                if (response.ok) {
                    console.log('New Notifications:', notifications);
                    commit('setNotifications',notifications)
                } else {
                    console.error('Error fetching notifications:', notifications.error);
                }
            } catch (error) {
                console.error('Fetch error:', error);
            }
        }
    
        
    },
    getters: {
        getNotifications:(state) => state.notifications,
        getNotificationsLength:(state) => state.notifications.length
    },
};
