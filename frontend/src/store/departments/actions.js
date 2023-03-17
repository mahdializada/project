import Alert from "~/helpers/alert";
import {
  TOGGLE_API_CALLING,
  UPDATE_DEPARTMENTS,
  SET_EXTRA_DATA,
  SET_COUNTRIES,
  INSERT_ITEM,
  REMOVE_ITEM,
  SET_COMPANIES,
  SET_ROLES,
} from "./mutations";
export default {
  async fetchDepartments({ commit }, data) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(`departments`, {
        params: data,
      });
      if (response?.status === 200) {
        commit(TOGGLE_API_CALLING, false);
        commit(UPDATE_DEPARTMENTS, response?.data?.data);
        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, extraData);
      }
      return response;
    } catch (error) {
      return error;
    }
  },
  // get departments by tab change's key
  async getDepartmentsByKey({ commit }, options) {
    try {
      commit(UPDATE_DEPARTMENTS, []);
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get("departments", {
        params: options,
      });
      if (response?.status === 200) {
        commit(UPDATE_DEPARTMENTS, response?.data?.data);
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

async paginateDepartments({ commit }, options) {
	try {
		const response = await this.$axios.get("departments", {
			params: options,
		});
		if (response?.status === 200) {
			commit(UPDATE_DEPARTMENTS, response?.data?.data);
			const { data, ...extraData } = response?.data;
			commit(SET_EXTRA_DATA, { extraData, key: options });
		}
		return response;
	} catch (error) {
		return error;
	}
},
async fetchCountries({ commit }) {
	try {
		const response = await this.$axios.get(
			"countries?key=all&itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
		);
		if (response?.status === 200) {
			commit(SET_COUNTRIES, response?.data);
		}
		return response;
	} catch (error) {
		return error;
	}
},

  async fetchAsDepartments({ commit }) {
    try {
      const response = await this.$axios.get(
        "departments?key=all&itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
      );
      if (response?.status === 200) {
        commit(UPDATE_DEPARTMENTS, response?.data?.data);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchCompaniesOfCountries({ commit }, country_ids) {
    country_ids = Array.from(country_ids);
    try {
      const response = await this.$axios.get("companies?itemsPerPage=-1", {
        params: { country_ids },
      });
      if (response?.status === 200) {
        commit(SET_COMPANIES, response?.data?.companies);
      }
      return response;
    } catch (error) {
      return error;
    }
  },

  async fetchCompanies({ commit }) {
	try {
		const response = await this.$axios.get(
			"companies?key=all&itemsPerPage=-1&sortBy[]=name&sortDesc[]=false"
		);
		if (response?.status === 200) {
			commit(SET_COMPANIES, response?.data?.companies);
		}
		return response;
	} catch (error) {
		return error;
	}
  },
  async fetchRoles({ commit }) {
    try {
      const response = await this.$axios.get("common/roles");
      if (response?.status === 200) {
        commit(SET_ROLES, response?.data?.roles);
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
        `departments/change-record-status/${id}`,
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
      const response = await this_object.$axios.delete(`departments/${ids}`);
      if (response?.status === 200 && response?.data?.result) {
        Alert.removedSuccessAlert(this_object, this_object.$tr("deleted"));
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
};
