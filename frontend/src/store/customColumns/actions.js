import { split } from "lodash";
import {
  SET_PERSONAL_SETTING,
  SET_SCOPE,
  SET_PREV_SETTING,
  SET_SETTING_NAME,
  SET_SHARED_SETTING,
  SET_VIEW_ID,
  SET_DEFAULT_COLUMNS,
} from "./mutations";

export default {
  async fetchCustomColumns({ commit }, data) {
    try {
      const response = await this.$axios.get("view_name", { params: data });
      // reset vuex
      commit(SET_PERSONAL_SETTING, []);
      commit(SET_DEFAULT_COLUMNS, []);
      commit(SET_SETTING_NAME, "");
      commit(SET_PREV_SETTING, false);
      commit(SET_SHARED_SETTING, []);
      if (response?.status === 200 && response.data.personal_data.length > 0) {
        let personal_data = response.data.personal_data;
        if (personal_data.length > 0) {
          personal_data.forEach((data) => {
            data.columns = JSON.parse(data.columns);
          });
        }
        commit(SET_DEFAULT_COLUMNS, personal_data[0].columns);
        commit(SET_PERSONAL_SETTING, personal_data);

        let sharedData = response.data.shared_data;
        if (sharedData.length > 0) {
          sharedData.forEach((data) => {
            data.columns = JSON.parse(data.columns);
          });
        }
        commit(SET_SHARED_SETTING, sharedData);
      } else {
        let sharedData = response.data.shared_data;
        if (sharedData.length > 0) {
          sharedData = sharedData.forEach((data) => {
            data.columns = JSON.parse(data.columns);
          });
        }
        commit(SET_SHARED_SETTING, response.data.shared_data);
      }
    } catch (error) {
      return error;
    }
  },
  changePrevSetting() {
    commit(SET_PREV_SETTING, false);
  },
  async deleteSetting({ commit }, id) {
    try {
      const response = await this.$axios.delete("view_name/" + id);
      return response.data;
    } catch (error) {}
  },
};
