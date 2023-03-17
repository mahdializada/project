export const SET_DATA     = 'SET_DATA'
export const INSERT_DATA  = 'INSERT_DATA'
export const UPDATE_DATA  = 'UPDATE_DATA'
export const SET_TOTAL    = 'SET_TOTAL'
export const REMOVE_ITEM = 'REMOVE_ITEM';
export const INSERT_ITEM = 'INSERT_ITEM';

export const SET_DATA_STATUS_EVENT     = 'SET_DATA_STATUS_EVENT'
export const INSERT_DATA_STATUS_EVENT     = 'INSERT_DATA_STATUS_EVENT'
export const UPDATE_DATA_STATUS_EVENT     = 'UPDATE_DATA_STATUS_EVENT'
export const SET_TOTAL_STATAUS_EVENT    = 'SET_TOTAL_STATAUS_EVENT'

const mutations = {

  // set all users from DB into store
  [SET_TOTAL](state,data){
    state.total = data;
  },

  [SET_TOTAL_STATAUS_EVENT](state,data){
    state.total_status_event = data;
  },

  [SET_DATA](state, data) {
    state.reasons = data;

  },

  [SET_DATA_STATUS_EVENT](state, data) {
    state.status_event = data;

  },

  [INSERT_DATA_STATUS_EVENT](state,data){
    state.status_event?.unshift(data)
  },

  [INSERT_DATA](state,data){
    state.reason?.unshift(data)
  },

  [UPDATE_DATA](state, updatedItem){
    state.reason = state.reason?.map((item) => {
      if (item?.id === updatedItem?.id) {
        return updatedItem;
      }
      return item;
    });
  },


  [UPDATE_DATA_STATUS_EVENT](state, updatedItem){
    state.status_event = state.status_event?.map((item) => {
      if (item?.id === updatedItem?.id) {
        return updatedItem;
      }
      return item;
    });
  },

  [REMOVE_ITEM](state, ids) {
    state.reasons = state.reasons.filter((ele) => ele?.id !== ids);
},
[INSERT_ITEM](state, newItem) {
  state.reasons?.unshift(newItem)
  state.total = state.total + 1
},



}

export default mutations
