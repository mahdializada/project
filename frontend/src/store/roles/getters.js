const active = "active";
const inactive = "inactive";
const pending = "pending";
const blocked = "blocked";
const deleted = "deleted";
const traced = "traced";

export default {
	getItems: (state) => {
		return state.items;
	},
	itemsTotal: (state) => state.itemsTotal,

	// search in items
	filterItems: (state) => (searchQuery) => {
		if (searchQuery && searchQuery.toString().trim()) {
			return state.items.filter((item) =>
				item?.name?.toLowerCase().includes(searchQuery?.toLowerCase())
			);
		}
		return [...state.items];
	},

	// search in deleted items
	filterDeletedItems: (state) => (searchQuery) => {
		if (searchQuery && searchQuery.toString().trim()) {
			return state.deletedItems.filter((item) =>
				item?.name?.toLowerCase().includes(searchQuery?.toLowerCase())
			);
		}
		return [...state.deletedItems];
	},

	isApiCalling: (state) => state.isApiCalling,

	getTotal: (state) => (tabKey) => {
		switch (tabKey) {
			case "removed":
				return state.extraData?.removedTotal || 0;
			case "inactive":
				return state.extraData?.inactiveTotal || 0;
			case "blocked":
				return state.extraData?.blockedTotal || 0;
			case "active":
				return state.extraData?.activeTotal || 0;
			case "pending":
				return state.extraData?.pendingTotal || 0;
			case "all":
				return state.extraData?.allTotal || 0;
			case "warning":
			default:
				return state.extraData?.warningTotal || 0;
		}
	},
};
