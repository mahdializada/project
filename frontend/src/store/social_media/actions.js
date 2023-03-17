import {
  INSERT_DELETED_ITEM,
  INSERT_ITEM,
  REMOVE_DELETED_ITEM,
  REMOVE_ITEM,
  SET_DELETED_ITEMS,
  SET_DELETED_ITEMS_TOTAL,
  SET_ITEMS,
  SET_EXTRA_DATA,
  SET_ITEMS_TOTAL,
  TOGGLE_API_CALLING,
  UPDATE_ITEM,
  SET_ALL_ITEMS,
}  from "./mutations";

export default {

  async fetchItems({commit}, data) {
    try {
      commit(TOGGLE_API_CALLING,true);
      const response = await this.$axios.get(`social_media`, {
        params: data,
      })
      if (response?.status === 200) {
      commit(TOGGLE_API_CALLING,false);
        const { data, ...extraData } = response?.data;
        commit(SET_ITEMS, response?.data?.data);
        commit(SET_EXTRA_DATA, extraData);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING,false);
      return error
    }
  },
  async fetchAllSocialMedia({ commit }) {
    try {
      const response = await this.$axios.get(
        "social_media?key=active&itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
      );
      if (response?.status === 200) {
        commit(SET_ALL_ITEMS, response?.data?.data);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async insertRoleDb({ commit }, data) {
    try {
      const response = await this.$axios.post("social_media", data, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      if (response?.status === 201) {
        commit(INSERT_ITEM, response?.data?.data);
        return true;
      } else {
        return false;
      }
    } catch (error) {}
  },

  async updateRoleDb({ commit }, obj) {
    try {
      const response = await this.$axios.post(`roles/update/${obj.id}`, obj.formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      if (response?.status === 200) {
        commit(UPDATE_ITEM, response?.data?.data);
        return true;
      } else {
        return false;
      }
    } catch (error) {}
  },


  insertNewItem({ commit }, item) {
    commit(INSERT_ITEM, item);
  },
  removeItem({ commit }, itemId) {
    commit(REMOVE_ITEM, itemId);
  },
  updateItem({ commit }, updatedItem) {
    commit(UPDATE_ITEM, updatedItem);
  },
  // ==========>   Deleted Section   <========= //



  insertDeletedItem({commit}, newItem) {
    commit(INSERT_DELETED_ITEM, newItem)
  },
  removeDeletedItem({ commit }, itemId) {
    commit(REMOVE_DELETED_ITEM, itemId);
  },
};
