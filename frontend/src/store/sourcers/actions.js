import { SET_SOURCERS , TOGGLE_API_CALLING} from "./mutations";

const actions = {
  
  async fetchSourcers({ commit }) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(`sourcers`);
      if (response?.status === 200) {
        commit(TOGGLE_API_CALLING, false);
        commit(SET_SOURCERS, response?.data?.data ?? []);
      }
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },
}

export default actions
