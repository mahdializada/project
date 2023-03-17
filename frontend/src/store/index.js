/* ================================> States <================================*/
export const state = () => ({
     oredohUnAuthorized: false
});

/* ================================> Getters <================================*/

export const getters = {
     getoredohUnAuthorized: state => state.oredohUnAuthorized
};

/* ================================>Mutations <================================*/

export const mutations = {
     setAuthorization(state, data) {
          state.oredohUnAuthorized = data;
     }
};

/* ================================> Actions <================================*/

export const actions = {

     ChangeAuthorization({ commit }, payload) {
          commit('setAuthorization', payload);
     }
};
