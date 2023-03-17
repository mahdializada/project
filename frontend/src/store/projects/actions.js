import Alert from "~/helpers/alert";
import {
  TOGGLE_API_CALLING,
  UPDATE_PROJECTS,
  SET_EXTRA_DATA,
  INSERT_ITEM,
  REMOVE_ITEM,
} from "./mutations";
export default {
  async fetchProjects({ commit }, { key, searchData }) {
    try {
      const response = await this.$axios.get(`projects?key=${key}`, {
        params: searchData,
      });
      if (response?.status === 200) {
        commit(UPDATE_PROJECTS, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, { extraData, key: key });
      }
      return response;
    } catch (error) {
      return error;
    }
  },
  // get projects by tab change's key
  async getProjectsByKey({ commit }, options) {
    try {
      commit(UPDATE_PROJECTS, []);
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get("projects", {
        params: options,
      });
      if (response?.status === 200) {
        commit(UPDATE_PROJECTS, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, { extraData, key: options });
      }
      commit(TOGGLE_API_CALLING, false);
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return error;
    }
  },

  async paginateProjects({ commit }, options) {
    try {
      const response = await this.$axios.get("projects", {
        params: options,
      });
      if (response?.status === 200) {
        commit(UPDATE_PROJECTS, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, { extraData, key: options });
      }
      return response;
    } catch (error) {
      return error;
    }
  },
  // change record status to active, blocked, inactive and does deletion
  async changeRecordStatus(
    { commit, dispatch },
    { selectedStatus, this_object }
  ) {
    const id = this_object.selectedItems[0]?.id;
    const key = this_object.tabItems[this_object.tabIndex].key;
    try {
      this_object.toggleShowProgress();
      const response = await this_object.$axios.post(
        `projects/change-record-status/${id}`,
        {
          status: selectedStatus,
          key: key,
        }
      );
      if (response?.status === 200 && response?.data?.result) {
        this_object.toggleShowProgress();
        Alert.successAlert(
          this_object,
          this_object.$tr("record_status_changed_success_title")
        );
        commit(TOGGLE_API_CALLING, true);
        dispatch("paginateDepartments", { key: key });
        commit(TOGGLE_API_CALLING, false);
        selectedStatus = null;
      } else {
        // this_object.$toastr.e(this_object.$tr("something_went_wrong"));
				this_object.$toasterNA("red",this_object.$tr("something_went_wrong"));

      }
    } catch (error) {
      HandleException.handleApiException(this_object, error);
    }
  },
  //delete a record
  async deleteRecord({ commit, dispatch }, this_object) {
    this_object.toggleShowProgress();
    const ids = this_object.selectedItems[0].id;
    const key = this_object.tabItems[this_object.tabIndex].key;
    try {
      const response = await this_object.$axios.delete(`projects/${ids}`);
      if (response?.status === 200 && response?.data?.result) {
        Alert.removedSuccessAlert(
          this_object,
          this_object.$tr("deleted")
        );
        this_object.toggleShowProgress();
        commit(TOGGLE_API_CALLING, true);
        dispatch("paginateDepartments", { key: key });
        commit(TOGGLE_API_CALLING, false);
      } else {
        // this_object.$toastr.e(this_object.$tr("something_went_wrong"));
				this_object.$toasterNA("red",this_object.$tr("something_went_wrong"));

      }
    } catch (error) {
      HandleException.handleApiException(this_object, error);
    }
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

  async insertProject({ commit }, data) {
    try {
      const response = await this.$axios.post("projects", data, {
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
    } catch (error) {
      // if (error.response.status == 422) {
      //   // return server errors
      //   const errors = error.response.data.errors;
      //   for (let error in errors) {
      //     for (let er in errors[error]) {
      //       this.$toastr.e(errors[error][er]);
      //     }
      //   }
      // } else if (error.response.status === 401) {
      //   await this.$router.push("/admin/login");
      //   this.$toastr.e(error.response.statusText);
      // } else {
      //   this.$toastr.e(error.response.statusText);
      // }
      return false
    }
  },
};
