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
  UPDATE_ITEMS_TOTAL,
} from "./mutations";

export default {
  async fetchItems({ commit }, data) {
    try {
      commit(TOGGLE_API_CALLING, true);

      const response = await this.$axios.get(`roles`, {
        params: data,
      });
      if (response?.status === 200) {
        commit(TOGGLE_API_CALLING, false);
        const { data, ...extraData } = response?.data;
        commit(SET_ITEMS, response?.data?.data);
        commit(SET_EXTRA_DATA, extraData);
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },

  async insertRoleDb({ commit }, { data, vm }) {
    try {
      const response = await this.$axios.post("roles", data, {
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

  async updateRoleDb({ commit }, { formData, id, vm }) {
    try {
      const response = await this.$axios.post(`roles/update/${id}`, formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      if (response?.data?.result) {
        vm.$refs.teamEditStepper.nextForce();
      } else {
        // vm.$toastr.e(vm.$tr("something_went_wrong"));
				vm.$toasterNA("red",vm.$tr("something_went_wrong"));
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

  insertDeletedItem({ commit }, newItem) {
    commit(INSERT_DELETED_ITEM, newItem);
  },
  removeDeletedItem({ commit }, itemId) {
    commit(REMOVE_DELETED_ITEM, itemId);
  },
};
