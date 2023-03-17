import {
  INSERT_DELETED_ITEM,
  INSERT_ITEM,
  REMOVE_DELETED_ITEM,
  REMOVE_ITEM,
  SET_DELETED_ITEMS,
  SET_DELETED_ITEMS_TOTAL,
  SET_ITEMS,
  SET_ITEMS_TOTAL,
  TOGGLE_API_CALLING,
  SET_EXTRA_DATA,
  UPDATE_ITEM,
  UPDATE_ITEMS_TOTAL,
} from "./mutations";

export default {
  async fetchTeams({ commit }, data) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(`teams`, {
        params: data,
      });
      if (response?.status === 200) {
        commit(TOGGLE_API_CALLING, false);
        commit(SET_ITEMS, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_ITEMS_TOTAL, response?.data?.total);
        commit(SET_EXTRA_DATA, extraData);
      }
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },

  async insertTeamDb({ commit }, {data, vm}) {
    try {
      const response = await this.$axios.post("teams", data, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      if (response?.data?.result) {
        vm.canNext = true;
        await vm.$refs.teamRoleStepper.next();
        vm.resetForm();
      } else {
        // vm.$toastr.e(vm.$tr("something_went_wrong"));
				vm.$toasterNA("red",vm.$tr("something_went_wrong"));

      }
    } catch (error) {}
  },

  async updateTeamDb({ commit }, {formData, id, vm}) {
    try {
      const response =  await this.$axios.post(
        `teams/update/${id}`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );
      if (response?.data?.result) {
        vm.$refs.teamEditStepper.nextForce();
      } else {
        // vm.$toastr.e(vm.$tr("something_went_wrong"));
				vm.$toasterNA("red",vm.$tr("something_went_wrong"));

      }
    } catch (error) {}
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
  removeItem({ commit }, itemId) {
    commit(REMOVE_ITEM, itemId);
  },
  updateItem({ commit }, updatedItem) {
    commit(UPDATE_ITEM, updatedItem);
  },
  // ==========>   Deleted Section   <========= //

  async fetchDeletedItems({ commit }, baseUrl) {
    try {
      const response = await this.$axios.get(baseUrl);
      if (response?.status === 200) {
        commit(SET_DELETED_ITEMS, response?.data?.data);
        commit(SET_DELETED_ITEMS_TOTAL, response?.data?.total);
      }
      return response;
    } catch (error) {
      return error;
    }
  },
  async paginateDeletedItems({ commit }, options) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(options?.baseUrl, {
        params: options,
      });
      if (response?.status === 200) {
        commit(SET_DELETED_ITEMS, response?.data?.data);
        commit(SET_DELETED_ITEMS_TOTAL, response?.data?.total);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },
  insertDeletedItem({ commit }, newItem) {
    commit(INSERT_DELETED_ITEM, newItem);
  },
  removeDeletedItem({ commit }, itemId) {
    commit(REMOVE_DELETED_ITEM, itemId);
  },
};
