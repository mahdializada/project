export const SET_ITEMS = "SET_ITEMS";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const INSERT_ITEM = "INSERT_ITEM";
export const UPDATE_ITEM = "UPDATE_ITEM";
export const UPDATE_HAS_FILE = 'UPDATE_HAS_FILE';

export default {
	[UPDATE_HAS_FILE](state, flag){
		state.has_file = flag;
	},
	// deleted items
	[REMOVE_ITEM](state, depsId) {
		state.items = state.items.filter((deps) => deps?.id !== depsId);
		state.extraData.total = state.extraData?.total - 1;
	},
	// insert new item to the store
	[INSERT_ITEM](state, newItem) {
		state.items?.unshift(newItem);
		state.extraData.allTotal = state.extraData?.allTotal + 1;
		state.extraData.pendingTotal = state.extraData?.pendingTotal + 1;
	},
	// set all items into store
	[SET_ITEMS](state, data) {
		state.items = data;
	},
	// update edited item from store
	[UPDATE_ITEM](state, updatedItem) {
		state.items = state.items?.map((value) => {
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
};
