export const SET_DATA  = 'SET_DATA'
export const SET_NAME  = 'SET_NAME'


const mutations = {

  // set all users from DB into store
  [SET_DATA](state, data) {
    state.logs = data

  },
  [SET_NAME](state,data){
    state.file_name =data;
  }




}

export default mutations
