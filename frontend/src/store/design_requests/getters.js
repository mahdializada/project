import state from "./state";

export default {
	getCompanies: (state) => [...state.items],
	// return only totals  according to tab changes
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

			case "warning":
				return state.extraData?.warningTotal || 0;
			case "all":
			default:
				return state.extraData?.allTotal || 0;
		}
	},
	// return api calling status
	isApiCalling: (state) => state.isApiCalling,
	hasFile: (state) => state.has_file,
};
