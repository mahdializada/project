import {
  SET_DATA,
  INSERT_DATA,
  UPDATE_DATA,
  SET_TOTAL,
  TOGGLE_API_CALLING,
  SET_EXTRA_DATA,
  REMOVE_ITEM,
  INSERT_ITEM,
} from "./mutations";

const actions = {
  async fetchLabels({ commit }, data) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(
        `labels?key=${data.key}&system_name=${data.slug}`,
        {
          params: data.options,
        }
      );
      if (response?.status === 200) {
        commit(TOGGLE_API_CALLING, false);
        commit(SET_DATA, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, extraData);
      }
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },
  // search by Id
  async fetchLabelsByID({ commit }, data) {
    try {
      
      const response = await this.$axios.get(
        `labels?key=${data.key}&system_name=${data.slug}`,
        {
          params: data.options,
        }
      );
      if (response?.status === 200) {

        if(response.data.data[0] ){

          commit(REMOVE_ITEM, response?.data?.data[0].id);

          commit(INSERT_ITEM, response?.data?.data[0]);
          return response?.data?.data[0];
        }
      }
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },

  insertNewItem({ commit }, item) {
    commit(INSERT_DATA, item);
  },

  updateItem({ commit }, item) {
    commit(UPDATE_DATA, item);
  },
};

export default actions;
