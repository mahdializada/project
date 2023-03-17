import {SET_DATA, SET_CATEGORIES,SET_API_CALL} from "./mutations";

const actions = {
  // fetch all users from database
  async fetchUsers({commit}) {
    try {
      commit(SET_API_CALL, true);
      const response = await this.$axios.get('users_chart');
      if (response?.status === 200) {
        commit(SET_DATA,       response?.data?.data);
        commit(SET_CATEGORIES, response?.data?.categories);
        commit(SET_API_CALL, false);

      }
    } catch (error) {
      commit(SET_API_CALL, false);
    }
  },

  // weekly filter
  async weekFilter({commit},data) {

    try {
      commit(SET_API_CALL, true);
      const response = await this.$axios.get('users_chart_week',{params:{date: data}});
      if (response?.status === 200) {

        commit(SET_DATA,       response?.data?.data);
        commit(SET_CATEGORIES, response?.data?.categories);
        commit(SET_API_CALL, false);
      }

    } catch (error) {
      commit(SET_API_CALL, false);
    }
  },

  // monthly filter
  async monthFilter({commit},data) {
    try {
      commit(SET_API_CALL, true);
      const response = await this.$axios.get('users_chart_month',{params:{date: data}});
      if (response?.status === 200) {
        commit(SET_DATA,       response?.data?.data);
        commit(SET_CATEGORIES, response?.data?.categories);
      commit(SET_API_CALL, false);
      }
    } catch (error) {
      commit(SET_API_CALL, false);

    }
  },

  async yearFilter({commit},data) {
    try {
      commit(SET_API_CALL, true);
      const response = await this.$axios.get('users_chart_year',{params:{date: data}});
      if (response?.status === 200) {
        commit(SET_DATA,       response?.data?.data);
        commit(SET_CATEGORIES, response?.data?.categories);
      commit(SET_API_CALL, false);
      }
    } catch (error) {
      commit(SET_API_CALL, false);

    }
  },



}


export default actions
