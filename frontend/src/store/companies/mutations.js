export const SET_ITEMS = "SET_ITEMS";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const INSERT_ITEM = "INSERT_ITEM";
export const UPDATE_ITEM = "UPDATE_ITEM";
export const SET_ASC_COMPANIES = "SET_ASC_COMPANIES";

export const SET_NEW_COMPANY = "SET_NEW_COMPANY";
export const UPDATE_COMPANY = "UPDATE_COMPANY";
export const CHANGE_RECORD_STATUS = "CHANGE_RECORD_STATUS";
export const DELETE_COMPANY = "DELETE_COMPANY";
export const FETCH_NEW_REAL_TIME = "FETCH_NEW_REAL_TIME";

export default {
	[FETCH_NEW_REAL_TIME](state, response) {
		state.companies = [...state.companies, ...response.data.data];
		state.extraData = response?.data;
	},
	//delete company
	[DELETE_COMPANY](state, { tabKey, item }) {
		if (tabKey == "removed") {
			state.companies = state.companies.filter((company) => {
				if (company.id !== item) {
					return company;
				}
			});
		}
		state.extraData.removedTotal = state.extraData.removedTotal - 1;
		state.extraData.total = state.extraData.total - 1;
	},
	// change record status
	[CHANGE_RECORD_STATUS](state, { data, item, tabKey }) {
		if (tabKey == data.current_status && tabKey !== "all") {
			state.companies = state.companies.filter((company) => {
				if (company.id !== item.id) {
					return company;
				}
			});
		} else if (tabKey == data.new_status) {
			state.companies.unshift(item);
		} else {
			state.companies = state.companies.map((company) => {
				if (company.id == item.id) {
					return {
						...company,
						status: data.new_status,
					};
				}
				return company;
			});
		}
	},
	//update company
	[UPDATE_COMPANY](state, company) {
		state.companies = state.companies?.map((item) => {
			if (item?.id === company?.id) {
				return company;
			}
			return item;
		});
	},
	// set new company inserted
	[SET_NEW_COMPANY](state, { new_company, tabKey }) {
		if (tabKey == "pending" || tabKey == "all") {
			state.companies.unshift(new_company);
		}
		state.extraData.pendingTotal = state.extraData.pendingTotal + 1;
		state.extraData.allTotal = state.extraData.allTotal + 1;
	},
	// deleted items
	[REMOVE_ITEM](state, depsId) {
		state.companies = state.companies.filter((deps) => deps?.id !== depsId);
		state.extraData.total = state.extraData?.total - 1;
	},
	// insert new item to the store
	[INSERT_ITEM](state, newItem) {
		state.companies?.unshift(newItem);
		state.extraData.total = state.extraData?.total + 1;
		state.extraData.pendingTotal = state.extraData?.pendingTotal + 1;
	},
	// set all items into store
	[SET_ITEMS](state, data) {
		state.companies = data;
	},
	// update edited item from store
	[UPDATE_ITEM](state, updatedItem) {
		state.companies = state.companies?.map((value) => {
			if (updatedItem.id == value.id) {
				value = updatedItem;
			}
			return value;
		});
	},
	// set the extra data of items in to store
	[SET_EXTRA_DATA](state, extraData) {
		state.extraData = extraData;
	},
	// toggle api
	[TOGGLE_API_CALLING](state, value) {
		state.isApiCalling = value;
	},
	[SET_ASC_COMPANIES](state, value) {
		state.ascCompanies = value;
	},
};
