export const UPDATE_PROJECTS = "UPDATE_PROJECTS";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const INSERT_ITEM = "INSERT_ITEM";

export default {
  // deleted items
  [REMOVE_ITEM](state, depsId) {
    state.projects = state.projects.filter((deps) => deps?.id !== depsId);
    state.extraData.total = state.extraData?.total - 1;
  },
  // insert new item to the store
  [INSERT_ITEM](state, newItem) {
    state.projects?.unshift(newItem);
    // state.extraData.total = state?.extraData?.total + 1;
  },
  // set all items into store
  [UPDATE_PROJECTS](state, data) {
    state.projects = data;
  },
  // set the extra data of items in to store
  [SET_EXTRA_DATA](state, { extraData, key }) {
    if (key === "all") {
      state.extraData.total = extraData.total;
    }
    state.extraData.activeTotal = extraData.activeTotal;
    state.extraData.inactiveTotal = extraData.inactiveTotal;
    state.extraData.removedTotal = extraData.removedTotal;
    state.extraData.blockedTotal = extraData.blockedTotal;
    state.extraData.pendingTotal = extraData.pendingTotal;
  },
  // toggle api
  [TOGGLE_API_CALLING](state, value) {
    state.isApiCalling = value;
  },
};
