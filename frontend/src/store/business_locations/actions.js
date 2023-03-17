import { SET_ITEMS, SET_EXTRA_DATA, TOGGLE_API_CALLING, REMOVE_ITEM, INSERT_ITEM } from './mutations';

export default {
  async fetchItems({ commit }, data) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get("business-locations", {
				params: data,
			});
      if (response?.status === 200) {
        commit(TOGGLE_API_CALLING, false);
        commit(SET_ITEMS, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, extraData);
      }
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },
  insertNewItem({ commit }, item) {
    commit(INSERT_ITEM, item);
  },
  deleteItem({ commit }, item) {
    commit(REMOVE_ITEM, item);
  },
};
