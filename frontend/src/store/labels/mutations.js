export const SET_DATA     = 'SET_DATA'
export const INSERT_DATA  = 'INSERT_DATA'
export const UPDATE_DATA  = 'UPDATE_DATA'
export const SET_TOTAL    = 'SET_TOTAL'
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const REMOVE_ITEM = 'REMOVE_ITEM';
export const INSERT_ITEM = 'INSERT_ITEM';

const mutations = {
  // for remove an item from datatable
  [REMOVE_ITEM](state, depsId) {
    state.labels = state.labels.filter((deps) => deps?.id !== depsId);

  },
   // insert new item to the store
   [INSERT_ITEM](state, newItem) {
    state.labels?.unshift(newItem);

  },

  // set total label from DB into store
  [SET_TOTAL](state,data){
    state.total = data;
  },

  // set all label from DB into store
  [SET_DATA](state, data) {
    state.labels = data;
  },

  // insert label from DB into store
  [INSERT_DATA](state,data){
    state.labels?.unshift(data)
    state.extraData.allTotal = state.extraData?.allTotal + 1;
    data?.status === 'live' ? state.extraData.liveTotal = state.extraData?.liveTotal + 1 : state.extraData.archiveTotal = state.extraData?.archiveTotal + 1;
  },

  // update label from DB into store
  [UPDATE_DATA](state, updatedItem){
    state.labels = state.labels?.map((item) => {
      if (item?.id === updatedItem?.id) {
        return updatedItem;
      }
      return item;
    });
  },


  [SET_EXTRA_DATA](state, extraData) {
    state.extraData = extraData;
  },

  [TOGGLE_API_CALLING](state, value) {
    state.isApiCalling = value;
  },
}

export default mutations
