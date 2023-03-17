import Alert from "~/helpers/alert";
import {
	TOGGLE_API_CALLING,
	UPDATE_COUNTRIES,
	SET_EXTRA_DATA,
	UPDATE_COUNTRIES_TOTAL_WHEN_STATUS_CHANGED,
	UPDATE_COUNTRIES_TOTAL_WHEN_BLOCKED,
	UPDATE_COUNTRIES_TOTAL_WHEN_DELETED,
	SET_COUTRIES_WITH_COMPANIES,
	SET_ASC_COUNTRIES,
	INSERT_ITEM,
	REMOVE_ITEM,
} from "./mutations";

const BASE_URL = "countries";

export default {
	async fetchCountries({ commit }, data) {
		try {
			commit(TOGGLE_API_CALLING, true);
			const response = await this.$axios.get("countries", {
				params: data,
			});
			if (response?.status === 200) {
				commit(TOGGLE_API_CALLING, false);
				commit(UPDATE_COUNTRIES, response?.data?.data);
				const { data, ...extraData } = response?.data;
				commit(SET_EXTRA_DATA, extraData);
			}
			return response;
		} catch (error) {
			commit(TOGGLE_API_CALLING, false);
			return error;
		}
	},
	// get countries by tab change's key
	async getCountriesByKey({ commit }, options) {
		try {
			commit(UPDATE_COUNTRIES, []);
			commit(TOGGLE_API_CALLING, true);
			const response = await this.$axios.get("countries", {
				params: options,
			});
			if (response?.status === 200) {
				commit(UPDATE_COUNTRIES, response?.data?.data);
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
	// change record status to active, blocked, inactive and does deletion
	async changeRecordStatus({ commit }, { selectedStatus, payload }) {
		payload.toggleShowProgress();
		const id = payload.selectedItems[0]?.id;
		try {
			const response = await payload.$axios.post(
				`countries/change-record-status/${id}`,
				{
					status: selectedStatus,
					key: payload.tabItems[payload.tabIndex]?.key,
				}
			);
			if (response?.status === 200 && response?.data?.result) {
				commit(UPDATE_COUNTRIES_TOTAL_WHEN_STATUS_CHANGED, {
					country: response?.data?.data,
					value: selectedStatus,
					key: payload.tabItems[payload.tabIndex]?.key,
				});
				Alert.successAlert(
					payload,
					payload.$tr("record_status_changed_success_title")
				);
				selectedStatus = null;
			} else {
				// payload.$toastr.e(payload.$tr("something_went_wrong"));
				payload.$toasterNA("red",payload.$tr("something_went_wrong"));

			}
		} catch (error) {
			HandleException.handleApiException(payload, error);
		}
		payload.toggleShowProgress();
	},
	//block record
	async blockRecord({ commit }, payload) {
		payload.toggleShowProgress();
		const id = payload.selectedItems[0]?.id;
		try {
			const response = await payload.$axios.post(
				`countries/change-record-status/${id}`,
				{ status: "blocked" }
			);
			if (response?.status === 200 && response?.data?.result) {
				commit(UPDATE_COUNTRIES_TOTAL_WHEN_BLOCKED, {
					country_id: id,
					key: payload.tabItems[payload.tabIndex].key,
				});
				Alert.blockedSuccessAlert(payload, payload.$tr("successfully_blocked"));
			} else {
				// payload.$toastr.e(payload.$tr("something_went_wrong"));
				payload.$toasterNA("red",payload.$tr("something_went_wrong"));

			}
		} catch (error) {
			HandleException.handleApiException(payload, error);
		}
		payload.toggleShowProgress();
	},
	//delete a record
	async deleteRecord({ commit }, payload) {
		payload.toggleShowProgress();
		const ids = payload.selectedItems[0].id;
		try {
			const response = await payload.$axios.delete(`countries/${ids}`);
			if (response?.status === 200 && response?.data?.result) {
				commit(UPDATE_COUNTRIES_TOTAL_WHEN_DELETED, {
					country_id: ids,
					key: payload.tabItems[payload.tabIndex].key,
				});
				Alert.removedSuccessAlert(payload, payload.$tr("deleted"));
			} else {
				// payload.$toastr.e(payload.$tr("something_went_wrong"));
				payload.$toasterNA("red",payload.$tr("something_went_wrong"));

			}
		} catch (error) {
			HandleException.handleApiException(payload, error);
		}
		payload.toggleShowProgress();
	},

	async fetchCountriesWithCompanies({ commit }) {
		try {
			const response = await this.$axios.get(
				"countries?itemsPerPage=-1&sortBy[]=name&sortDesc[]=false&withProject=true"
			);
			if (response?.status === 200) {
				commit(SET_COUTRIES_WITH_COMPANIES, response?.data);
			}
			return response;
		} catch (error) {
			return error;
		}
	},

	async fetchAscCountries({ commit, state }) {
		if (state.ascCountries.length == 0) {
			try {
				const response = await this.$axios.get(
					"countries?key=all&sortBy[]=name&sortDesc[]=false&itemsPerPage=-1&for_select=true"
				);
				if (response?.status === 200) {
					commit(SET_ASC_COUNTRIES, response?.data?.data);
				}
				return response;
			} catch (error) {
				return error;
			}
		}
	},
	insertNewItem({ commit }, item) {
		commit(INSERT_ITEM, item);
	},
	deleteItem({ commit }, item) {
		commit(REMOVE_ITEM, item);
	},
};
