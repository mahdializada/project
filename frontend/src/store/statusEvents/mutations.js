export const SET_DATA     = 'SET_DATA'
export const SET_TOTAL    = 'SET_TOTAL'
export const REMOVE_ITEM = 'REMOVE_ITEM';
export const INSERT_ITEM = 'INSERT_ITEM';

const mutations = {
   // for remove an item from datatable
   [REMOVE_ITEM](state, depsId) {
    state.status_event = state.status_event.filter((deps) => deps?.id !== depsId);
    
  },
   // insert new item to the store
   [INSERT_ITEM](state, newItem) {
    state.status_event?.unshift(newItem);
     
  },

  [SET_DATA](state, data) {
    state.status_event = data;

  },
  [SET_TOTAL](state,data){
    state.total = data;
  },


}

export default mutations
