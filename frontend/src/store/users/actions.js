import {
  INSERT_ITEM,
  SET_ITEMS,
  SET_NECESSARY_ITEMS,
  SET_ONLINE_USERS_DATA,
  TOGGLE_API_CALLING,
  UPDATE_ITEM,
  SET_EXTRA_DATA,
  SET_ALL_ITEMS,
  SET_COUNTRIES,
  SET_NATIONALS,
  SET_TEAMS,
  SET_PROJECTS,
  SET_DEPARTMENTS,
  SET_ROLES,
  REMOVE_ITEM,
  PROFILE_USER,
} from "./mutations";

const ONLINE_USERS_URL = "online/users";

export default {
  seProfileUser({ commit }, data) {
    commit(PROFILE_USER, data);
  },
  async fetchItems({ commit }, data) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(`users`, {
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

  async fetchItemsNew({ commit }, { tableTabKey, searchData }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(
        `users?tableTabKey=${tableTabKey}`,
        { params: searchData }
      );
      if (response?.status === 200) {
        commit(SET_ITEMS, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, extraData);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },

  async fetchAllUsers({ commit }, baseUrl) {
    try {
      const response = await this.$axios.get(baseUrl);

      if (response?.status === 200) {
        commit(SET_ALL_ITEMS, response?.data);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchNecessaryItems({ commit }) {
    try {
      const response = await this.$axios.get("users/?items=necessary");
      if (response?.status === 200) {
        commit(SET_NECESSARY_ITEMS, response?.data);
      }
    } catch (error) {}
  },

  async fetchOnlineUsers({ commit }) {
    // fetch online users from database
    try {
      const response = await this.$axios.get(ONLINE_USERS_URL);
      if (response?.status === 200) {
        commit(SET_ONLINE_USERS_DATA, response?.data?.data);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  filterUsers({ commit }, item) {
    commit(SET_ITEMS, item);
  },
  insertNewItem({ commit }, item) {
    commit(INSERT_ITEM, item);
  },
  updateItem({ commit }, updatedItem) {
    commit(UPDATE_ITEM, updatedItem);
  },
  deleteItem({ commit }, item) {
    commit(REMOVE_ITEM, item);
  },

  async fetchCountries({ commit }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(
        "countries?itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
      );
      if (response?.status === 200) {
        commit(SET_COUNTRIES, response?.data?.data);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      return error;
    }
  },
  async fetchNationals({ commit }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(
        "nationals?itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
      );
      if (response?.status === 200) {
        commit(SET_NATIONALS, response?.data?.data);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      return error;
    }
  },
  async fetchTeams({ commit }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(
        "teams?itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
      );
      if (response?.status === 200) {
        commit(SET_TEAMS, response?.data?.data);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      return error;
    }
  },
  async fetchProjects({ commit }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(
        "projects?itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
      );
      if (response?.status === 200) {
        commit(SET_PROJECTS, response?.data?.data);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      return error;
    }
  },
  async fetchDepartments({ commit }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(
        "departments?itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
      );
      if (response?.status === 200) {
        commit(SET_DEPARTMENTS, response?.data?.data);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      return error;
    }
  },
  async fetchRoles({ commit }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get("common/roles");
      if (response?.status === 200) {
        commit(SET_ROLES, response?.data?.data);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      return error;
    }
  },
};
