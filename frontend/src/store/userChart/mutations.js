export const SET_DATA         = 'SET_DATA'
export const SET_CATEGORIES   = 'SET_CATEGORIES'
export const SET_API_CALL     = 'SET_API_CALL'

const mutations = {

  [SET_DATA](state, data) {
    state.data = data
  },

  [SET_CATEGORIES](state,data){
    state.categories =data;
  },

  [SET_API_CALL](state,data){
    state.api_call =data;
  },

}

export default mutations
