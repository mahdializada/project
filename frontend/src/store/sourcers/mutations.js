export const SET_SOURCERS = "SET_SOURCERS";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";

export default {
  [SET_SOURCERS](state, data) {
    state.sourcers = data;
  },
  [TOGGLE_API_CALLING](state, value) {
    state.isApiCalling = value;
  },
};
