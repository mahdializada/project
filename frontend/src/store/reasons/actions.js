import {
  SET_DATA,
  SET_DATA_STATUS_EVENT,
  INSERT_DATA_STATUS_EVENT,
  UPDATE_DATA_STATUS_EVENT,
  INSERT_DATA,
  UPDATE_DATA,
  SET_TOTAL,
  SET_TOTAL_STATAUS_EVENT,
  INSERT_ITEM,
  REMOVE_ITEM,
} from "./mutations";

const actions = {
  // fetch all users from database
  async fetchReasons({ commit }, data) {
    try {
      const response = await this.$axios.get("reasons", { params: data });

      if (response?.status === 200) {
        commit(SET_DATA, response?.data?.data);
        commit(SET_TOTAL, response?.data?.total);
      }
    } catch (error) {
    }
  },

  async searchReasons({ commit }, data) {
    try {
      const response = await this.$axios.post("searchReason",  data );
       
      if (response.data.data) {
        commit(REMOVE_ITEM, response.data.data[0].id);
        commit(INSERT_ITEM, response.data.data[0]);
        return response.data.data[0];
      }
    } catch (error) {
    }
  },

  async fetchReasonAccordingToSystemEvent({ commit }, data) {
    try {
      const response = await this.$axios.get("reasons_system_event", {
        params: data,
      });

      if (response?.status === 200) {
        // commit(SET_DATA, response?.data);
      }
    } catch (error) {
    }
  },

  async fetchReasonPaginate({ commit }, data) {
    // try {
    //   const response = await this.$axios.get('paginate_reasons', { params: data });
    //   if (response?.status === 200) {
    //     commit(SET_DATA, response?.data?.reasons.data);
    //     commit(SET_TOTAL, response.data.reasons.total);
    //   }
    // } catch (error) {
    // }
  },

  async filterReason({ commit }, data) {
    // try {
    //   const response = await this.$axios.get('filter_reasons', { params: data });
    //   if (response?.status === 200) {
    //     commit(SET_DATA, response?.data?.reasons.data);
    //     commit(SET_TOTAL, response.data.reasons.total);
    //   }
    // } catch (error) {
    // }
  },

  async insertReason({ commit }, data) {
    const response = await this.$axios.post("reasons", data);
  },
  updateReason({ commit }, item) {
    commit(UPDATE_DATA_STATUS_EVENT, item);
  },

  insertNewItem({ commit }, item) {
    commit(INSERT_DATA, item);
  },
  updateItem({ commit }, updatedItem) {
    commit(UPDATE_DATA, updatedItem);
  },
  deleteItem({ commit }, item) {
    // commit(REMOVE_DATA, item);
  },
};

export default actions;
