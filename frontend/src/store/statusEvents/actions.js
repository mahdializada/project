import { SET_DATA, SET_TOTAL, REMOVE_ITEM, INSERT_ITEM } from "./mutations";

const actions = {
  async fetchStatusEvent({ commit }, data) {
    try {
      const response = await this.$axios.get("status_events", { params: data });
      if (response?.status === 200) {
        commit(SET_DATA, response?.data?.data);
        commit(SET_TOTAL, response?.data?.total);
      }
    } catch (error) {
    }
  },
  async fetchStatusEventByID({ commit }, data) {
    try {
      const response = await this.$axios.post("status_events_search",data);

      if (response?.status === 200) {
        commit(REMOVE_ITEM, response?.data?.data[0].id);
        commit(INSERT_ITEM, response?.data?.data[0]);
        return response?.data?.data[0];
      }
    } catch (error) {
    }
  },

  async fetchReasonPaginate({ commit }, data) {},

  async filterReason({ commit }, data) {},

  async insertReason({ commit }, data) {
    // const response = await this.$axios.post("reasons", data);
  },

  updateReason({ commit }, data) {},

  async fetchSubSystemTypes({ commit }, data) {
    const response = await this.$axios.get("subsystems", { params: data });
  },
};

export default actions;
