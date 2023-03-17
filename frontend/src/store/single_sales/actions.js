import {
  SET_CATEGORIES,
  TOGGLE_API_CALLING,
  SET_EXTRA_DATA,
  REMOVE_CATEGORY,
  INSERT_CATEGORY,
  REMOVE_ITEM,
  INSERT_ITEM,
  SET_ITEMS,
  SET_SUB_CATEGORIES,
  REMOVE_SUBCATEGORY,
  INSERT_SUBCATEGORY,
  SET_ALL_CATEGORIES,
} from "./mutations";

export default {
  // fetch all Categories
  async fetchAllCategories({commit}, {url, data}) {
    try {
      const response = await this.$axios.get(url, {
        params: {
          status: data.status,
        },
      });
      if (response?.status === 200) {
        commit(SET_ALL_CATEGORIES, response?.data?.categories);
      }
    } catch (error) {
      handleException.handleApiException(this, error);
    }
  },
  // fetch master tables
  async fetchItems({ commit }, { data, url }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(url, {
        params: { key: data.key },
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
  // fetch sub categories
  async fetchSubCategories({ commit }, { data, url }) {
    try {
      const response = await this.$axios.get(url + `?key=${data.key}`, {
        params: data,
      });
      if (response?.status === 200) {
        commit(SET_SUB_CATEGORIES, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, extraData);
      }
      return response;
    } catch (error) {
      return error;
    }
  },
  // fetch data  Categories
  async fetchCategories({ commit }, { data, url }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get( url , {
        params: data,
      });
      if (response?.status === 200) {
        commit(TOGGLE_API_CALLING, false);
        commit(SET_CATEGORIES, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, extraData);
      }
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },
  
  // search categories by id
  async searchCategory({ commit }, {data,url}) {
    try {
      const response = await this.$axios.get("landing"+url, {
        params: data,
      });
      if (response.data) {
        commit(REMOVE_CATEGORY, response.data.code);
        commit(INSERT_CATEGORY, response.data);
        return response?.data;
      } else {
        return 0;
      }
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },

  // search items by id
    async searchItems({ commit }, {data,url}) {
      try {
        const response = await this.$axios.get("landing"+url, {
          params: data,
        });
        if (response.data) {
          commit(REMOVE_ITEM, response.data.code);
          commit(INSERT_ITEM, response.data);
          return response?.data;
        } else {
          return 0;
        }
      } catch (error) {
        commit(TOGGLE_API_CALLING, false);
        return error;
      }
    },

    // search sub categories by id
    async searchSubCategory({ commit }, {data,url}) {
      try {
        const response = await this.$axios.get("landing"+url, {
          params: data,
        });
        if (response.data) {
          commit(REMOVE_SUBCATEGORY, response.data.code);
          commit(INSERT_SUBCATEGORY, response.data);
          return response?.data;
        } else {
          return 0;
        }
      } catch (error) {
        commit(TOGGLE_API_CALLING, false);
        return error;
      }
    },

    


};
