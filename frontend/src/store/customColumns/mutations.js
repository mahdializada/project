export const SET_DEFAULT_COLUMNS = "SET_DEFAULT_COLUMNS";
export const SET_PERSONAL_SETTING = "SET_PERSONAL_SETTING";
export const SET_PREV_SETTING = "SET_PREV_SETTING";
export const SET_SHARED_SETTING = "SET_SHARED_SETTING";
export const SET_SETTING_NAME = "SET_SETTING_NAME";
export const SET_VIEW_ID = "SET_VIEW_ID";
export const SET_SCOPE = "SET_SCOPE";

const mutations = {
  // set data to columns state
  [SET_DEFAULT_COLUMNS](state, data) {
    state.custom_columns = data;
  },
  [SET_PERSONAL_SETTING](state, data) {
    state.personal_setting = data;
  },
  [SET_SCOPE](state, data) {
    state.view_scope = data;
  },
  [SET_SHARED_SETTING](state, data) {
    state.shared_setting = data;
  },
  [SET_PREV_SETTING](state, data) {
    state.prev_setting = data;
  },
  [SET_SETTING_NAME](state, data) {
    state.setting_name = data;
  },
  [SET_VIEW_ID](state, data) {
    state.view_id = data;
  },
};

export default mutations;
