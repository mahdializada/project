export const UPDATE_DEPARTMENTS = "UPDATE_DEPARTMENTS";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const INSERT_ITEM = "INSERT_ITEM";
export const SET_COUNTRIES = "SET_COUNTRIES";
export const SET_COMPANIES = "SET_COMPANIES";
export const SET_ROLES = "SET_ROLES";
// real time
export const SET_NEW_DEPARTMENT = "SET_NEW_DEPARTMENT";
export const UPDATE_DEPARTMENT = "UPDATE_DEPARTMENT";
export const CHANGE_RECORD_STATUS = "CHANGE_RECORD_STATUS";
export const DELETE_DEPARTMENT = "DELETE_DEPARTMENT";
export const FETCH_NEW_REAL_TIME = "FETCH_NEW_REAL_TIME";

export default {
	[FETCH_NEW_REAL_TIME](state, response) {
		state.departments = [...state.departments, ...response.data.data];
		state.extraData = response?.data;
	},
	//delete deparrment
	[DELETE_DEPARTMENT](state, { tabKey, id }) {
		if (tabKey == "removed") {
			state.departments = state.departments.filter((item) => {
				if (item.id !== id) {
					return item;
				}
			});
		}
		state.extraData.removedTotal = state.extraData.removedTotal - 1;
		state.extraData.allTotal = state.extraData.allTotal - 1;
	},
	// change record status
	[CHANGE_RECORD_STATUS](state, { data, item, tabKey }) {
		if (tabKey == data.current_status && tabKey !== "all") {
			state.departments = state.departments.filter((department) => {
				if (department.id !== item.id) {
					return department;
				}
			});
		} else if (tabKey == data.new_status) {
			state.departments.unshift(item);
		} else {
			state.departments = state.departments.map((department) => {
				if (department.id == item.id) {
					return {
						...department,
						status: data.new_status,
					};
				}
				return department;
			});
		}
	},
	//update department
	[UPDATE_DEPARTMENT](state, department) {
		state.departments = state.departments?.map((item) => {
			if (item?.id === department?.id) {
				return department;
			}
			return item;
		});
	},
	// set new department inserted
	[SET_NEW_DEPARTMENT](state, { new_department, tabKey }) {
		if (tabKey == "pending" || tabKey == "all") {
			state.departments.unshift(new_department);
		}
		state.extraData.pendingTotal = state.extraData.pendingTotal + 1;
		state.extraData.allTotal = state.extraData.allTotal + 1;
	},
	//
	[SET_ROLES](state, roles) {
		state.roles = roles;
	},

	//set countries
	[SET_COUNTRIES](state, countries) {
		state.countries = countries;
	},
	//set companies
	[SET_COMPANIES](state, companies) {
		state.companies = companies;
	},
	// deleted items
	[REMOVE_ITEM](state, depsId) {
		state.departments = state.departments.filter((deps) => deps?.id !== depsId);
		state.extraData.total = state.extraData?.total - 1;
	},
	// insert new item to the store
	[INSERT_ITEM](state, newItem) {
		state.departments?.unshift(newItem);
		state.extraData.total = state?.extraData?.total + 1;
	},
	// set all items into store
	[UPDATE_DEPARTMENTS](state, data) {
		state.departments = data;
	},
	// set the extra data of items in to store
	[SET_EXTRA_DATA](state, data) {
		state.extraData = data;
	},
	// toggle api
	[TOGGLE_API_CALLING](state, value) {
		state.isApiCalling = value;
	},
};
