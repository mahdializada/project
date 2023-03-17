import { UPDATE_ITEM } from "../permissions/mutations";
import {
  INSERT_ITEM,
  SET_ITEMS,
  SET_ITEMS_TOTAL,
  TOGGLE_API_CALLING,
} from "./mutations";

export default {
  async fetchItems({ commit }, data) {
    try {
      const response = await this.$axios.get("systems", {
        params: data,
      });
      if (response?.status === 200) {
        commit(SET_ITEMS, response?.data);
        commit(SET_ITEMS_TOTAL, response?.data.length);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async paginateItems({ commit }, options) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(options?.baseUrl, {
        params: options,
      });
      if (response?.status === 200) {
        commit(SET_ITEMS, response?.data?.data);
        commit(SET_ITEMS_TOTAL, response?.data?.total);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },

  insertNewItem({ commit }, item) {
    commit(INSERT_ITEM, item);
  },

  updateItem({ commit }, item) {
    commit(UPDATE_ITEM, item);
  },
};
