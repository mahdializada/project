export const SET_ITEMS = "SET_ITEMS";
export const SET_ALL_ITEMS = "SET_ALL_ITEMS";
export const INSERT_ITEM = "INSERT_ITEM";
export const UPDATE_ITEM = "UPDATE_ITEM";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const SEARCH_IN_ITEMS = "SEARCH_IN_ITEMS";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_NECESSARY_ITEMS = "SET_NECESSARY_ITEMS";
export const SET_COUNTRIES = "SET_COUNTRIES";
export const SET_NATIONALS = "SET_NATIONALS";
export const SET_TEAMS = "SET_TEAMS";
export const SET_PROJECTS = "SET_PROJECTS";
export const SET_ROLES = "SET_ROLES";
export const SET_DEPARTMENTS = "SET_DEPARTMENTS";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const SET_NEW_USER = "SET_NEW_USER";
export const PROFILE_USER = "PROFILE_USER";
export const UPDATE_USER = "UPDATE_USER";
export const CHANGE_RECORD_STATUS = "CHANGE_RECORD_STATUS";
export const DELETE_USER = "DELETE_USER";
export const FETCH_NEW_REAL_TIME = "FETCH_NEW_REAL_TIME";
export const SET_ONLINE_USERS_DATA = "SET_ONLINE_USERS_DATA";
export const ADD_ONLINE_USER = "ADD_ONLINE_USER";
export const REMOVE_ONLINE_USER = "REMOVE_ONLINE_USER";

export default {
	[FETCH_NEW_REAL_TIME](state, response) {
		state.items = [...state.items, ...response.data.data];
		state.extraData = response?.data;
	},
	//delete user
	[DELETE_USER](state, { tabKey, item }) {
		if (tabKey == "removed") {
			state.items = state.items.filter((user) => {
				if (user.id !== item) {
					return user;
				}
			});
		}
		state.extraData.removedTotal = state.extraData.removedTotal - 1;
		state.extraData.allTotal = state.extraData.allTotal - 1;
	},
	// change record status
	[CHANGE_RECORD_STATUS](state, { data, item, tabKey }) {
		if (tabKey == data.current_status && tabKey !== "all") {
			state.items = state.items.filter((user) => {
				if (user.id !== item.id) {
					return user;
				}
			});
		} else if (tabKey == data.new_status) {
			state.items.unshift(item);
		} else {
			state.items = state.items.map((user) => {
				if (user.id == item.id) {
					return {
						...user,
						status: data.new_status,
					};
				}
				return user;
			});
		}
	},
	//update user
	[UPDATE_USER](state, user) {
		state.items = state.items?.map((item) => {
			if (item?.id === user?.id) {
				return user;
			}
			return item;
		});
	},
	// set new user inserted
	[SET_NEW_USER](state, { new_user, tabKey }) {
		if (tabKey == "pending" || tabKey == "all") {
			state.items.unshift(new_user);
		}
		state.extraData.pendingTotal = state.extraData.pendingTotal + 1;
		state.extraData.allTotal = state.extraData.allTotal + 1;
	},
	// set all items into store
	[SET_ITEMS](state, data) {
		state.items = data;
	},

	[SET_ALL_ITEMS](state, data) {
		state.allItems = data;
	},

	[SET_ONLINE_USERS_DATA](state, data) {
		state.onlineUsers = data;
	},

	[ADD_ONLINE_USER](state, user) {
		state.onlineUsers.push(user);
	},

	[REMOVE_ONLINE_USER](state, user) {
		state.onlineUsers = state.onlineUsers.filter((item) => {
			if (item.id !== user.id) {
				return item;
			}
		});
	},

	// set all necessary data
	[SET_NECESSARY_ITEMS](state, data) {
		state.projects = data?.projects;
		state.countries = data?.countries;
		state.departments = data?.departments;
		state.roles = data?.roles;
		state.teams = data?.teams;
		state.nationals = data?.nationals;
	},

	// insert new item to the store
	[INSERT_ITEM](state, newItem) {
		state.items?.unshift(newItem);
		state.extraData.total = state?.extraData?.total + 1;
	},

	// update edited item from store
	[UPDATE_ITEM](state, updatedItem) {
		state.items = state.items?.map((item) => {
			if (item?.id === updatedItem?.id) {
				return updatedItem;
			}
			return item;
		});
	},

	// set the extra data of items in to store
	[SET_EXTRA_DATA](state, extraData) {
		state.extraData = extraData;
	},
	[REMOVE_ITEM](state, userId) {
		state.items = state.items.filter((user) => user?.id !== userId);
		state.extraData.total = state.extraData?.total - 1;
	},
	removeItem(state, userId) {
		state.items = state.items.filter((user) => user?.id !== userId);
		state.extraData.total = state.extraData?.total - 1;
	},

	// toggle api
	[TOGGLE_API_CALLING](state, value) {
		state.isApiCalling = value;
	},

	//user profile edit dialog
	[PROFILE_USER](state, value) {
		state.profileUser = value;
	},

	changeItemTotals(state, value) {
		if (value > 0) {
			state.extraData.total = state.extraData?.total + value;
		} else {
			if (state.extraData?.total > 0)
				state.extraData.total = state.extraData?.total - 1;
		}
	},
	changeActiveTotals(state, value) {
		if (value > 0) {
			state.extraData.activeTotal = state.extraData?.activeTotal + value;
		} else {
			if (state.extraData?.activeTotal > 0)
				state.extraData.activeTotal = state.extraData?.activeTotal - 1;
		}
	},

	changeInactiveTotals(state, value) {
		if (value > 0) {
			state.extraData.inactiveTotal = state.extraData?.inactiveTotal + value;
		} else {
			if (state.extraData?.inactiveTotal > 0)
				state.extraData.inactiveTotal = state.extraData?.inactiveTotal - 1;
		}
	},

	changePendingTotals(state, value) {
		if (value > 0) {
			state.extraData.pendingTotal = state.extraData?.pendingTotal + value;
		} else {
			if (state.extraData?.pendingTotal > 0)
				state.extraData.pendingTotal = state.extraData?.pendingTotal - 1;
		}
	},

	changeWarningTotals(state, value) {
		if (value > 0) {
			state.extraData.warningTotal = state.extraData?.warningTotal + value;
		} else {
			if (state.extraData?.warningTotal > 0)
				state.extraData.warningTotal = state.extraData?.warningTotal - 1;
		}
	},

	changeDeletedTotals(state, value) {
		if (value > 0) {
			state.extraData.removedTotal = state.extraData?.removedTotal + value;
		} else {
			if (state.extraData?.removedTotal > 0)
				state.extraData.removedTotal = state.extraData?.removedTotal - 1;
		}
	},

	changeTracedTotals(state, value) {
		if (value > 0) {
			state.extraData.tracedTotal = state.extraData?.tracedTotal + value;
		} else {
			if (state.extraData?.tracedTotal > 0)
				state.extraData.tracedTotal = state.extraData?.tracedTotal - 1;
		}
	},
	[SET_COUNTRIES](state, data) {
		state.countries = data;
	},
	[SET_NATIONALS](state, data) {
		state.nationals = data;
	},
	[SET_TEAMS](state, data) {
		state.teams = data;
	},
	[SET_PROJECTS](state, data) {
		state.projects = data;
	},
	[SET_DEPARTMENTS](state, data) {
		state.departments = data;
	},
	[SET_ROLES](state, data) {
		state.roles = data;
	},
};
