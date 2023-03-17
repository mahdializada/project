export const SET_ITEMS = "SET_ITEMS";
export const INSERT_ITEM = "INSERT_ITEM";
export const UPDATE_ITEMS_TOTAL = "UPDATE_ITEMS_TOTAL";
export const UPDATE_ITEM = "UPDATE_ITEM";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const SET_ITEMS_TOTAL = "SET_ITEMS_TOTAL";
export const SEARCH_IN_ITEMS = "SEARCH_IN_ITEMS";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";

// Deleted Section
export const INSERT_DELETED_ITEM = "INSERT_DELETED_ITEM";
export const REMOVE_DELETED_ITEM = "REMOVE_DELETED_ITEM";
export const SET_DELETED_ITEMS_TOTAL = "SET_DELETED_ITEMS_TOTAL";

export const SET_NEW_ROLE = "SET_NEW_ROLE";
export const UPDATE_ROLE = "UPDATE_ROLE";
export const CHANGE_RECORD_STATUS = "CHANGE_RECORD_STATUS";
export const DELETE_ROLE = "DELETE_ROLE";
export const FETCH_NEW_REAL_TIME = "FETCH_NEW_REAL_TIME";

export default {
	[FETCH_NEW_REAL_TIME](state, response) {
		state.items = [...state.items, ...response.data.data];
		state.extraData = response?.data;
	},
	//delete team
	[DELETE_ROLE](state, { tabKey, item }) {
		if (tabKey == "removed") {
			state.items = state.items.filter((role) => {
				if (role.id !== item) {
					return role;
				}
			});
		}
		state.extraData.removedTotal = state.extraData.removedTotal - 1;
		state.extraData.allTotal = state.extraData.allTotal - 1;
	},
	// change record status
	[CHANGE_RECORD_STATUS](state, { data, item, tabKey }) {
		if (tabKey == data.current_status && tabKey !== "all") {
			state.items = state.items.filter((role) => {
				if (role.id !== item.id) {
					return role;
				}
			});
		} else if (tabKey == data.new_status) {
			state.items.unshift(item);
		} else {
			state.items = state.items.map((role) => {
				if (role.id == item.id) {
					return {
						...role,
						status: data.new_status,
					};
				}
				return role;
			});
		}
	},
	//update team
	[UPDATE_ROLE](state, company) {
		state.items = state.items?.map((item) => {
			if (item?.id === company?.id) {
				return company;
			}
			return item;
		});
	},
	// set new team inserted
	[SET_NEW_ROLE](state, { new_role, tabKey }) {
		if (tabKey == "pending" || tabKey == "all") {
			state.items.unshift(new_role);
		}
		state.extraData.pendingTotal = state.extraData.pendingTotal + 1;
		state.extraData.allTotal = state.extraData.allTotal + 1;
	},

	// set all items into store
	[SET_ITEMS](state, data) {
		state.items = data;
	},
	[SET_EXTRA_DATA](state, data) {
		state.extraData = data;
	},
	// insert new item to the store
	[INSERT_ITEM](state, newItem) {
		state.items?.unshift(newItem);
		state.itemsTotal = state.itemsTotal + 1;
	},

	[UPDATE_ITEMS_TOTAL](state) {
		state.extraData.pendingTotal += 1;
		state.extraData.total += 1;
	},

	// remove deleted item from store
	[REMOVE_ITEM](state, removedId) {
		state.items = state.items?.filter((items) => items?.id !== removedId);
		state.itemsTotal = state.itemsTotal - 1;
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

	[SET_EXTRA_DATA](state, extraData) {
		state.extraData = extraData;
	},

	// set the total number of items in to store
	[SET_ITEMS_TOTAL](state, total) {
		state.itemsTotal = total;
	},

	// toggle api
	[TOGGLE_API_CALLING](state, value) {
		state.isApiCalling = value;
	},

	// ==========>   Deleted Section   <========= //

	// insert deleted item to the store deleted items state
	[INSERT_DELETED_ITEM](state, newItem) {
		state.items?.unshift(newItem);
		state.extraData.deletedTotals = state.extraData.removedTotal + 1;
	},

	// remove deleted item from store from deleted state
	[REMOVE_DELETED_ITEM](state, removedId) {
		state.deletedItems = state.deletedItems?.filter(
			(items) => items?.id !== removedId
		);
		state.deletedItemsTotal = state.deletedItemsTotal - 1;
	},

	// set the total number of deleted items in to store of deleted state
	[SET_DELETED_ITEMS_TOTAL](state, total) {
		state.deletedItemsTotal = total;
	},
};
