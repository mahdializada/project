export const SET_ITEMS = "SET_ITEMS";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const INSERT_ITEM = "INSERT_ITEM";

export default {
  // set all items into store
  [SET_ITEMS](state, data) {
    state.items = data;
  },

  // set the extra data of items in to store
  [SET_EXTRA_DATA](state, extraData) {
    state.extraData = extraData;
  },

  // toggle api
  [TOGGLE_API_CALLING](state, value) {
    state.isApiCalling = value;
  },

  // deleted items
  [REMOVE_ITEM](state, depsId) {
    state.items = state.items.filter((deps) => deps?.id !== depsId);
    state.extraData.total = state.extraData?.total - 1;
  },
  // insert new item to the store
  [INSERT_ITEM](state, newItem) {
    state.items?.unshift(newItem);
    state.extraData.allTotal = state.extraData?.allTotal + 1;
    state.extraData.pendingTotal = state.extraData?.pendingTotal + 1;
  },
};
