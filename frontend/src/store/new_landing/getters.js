import state from "./state";

export default {
  isApiCalling: (state) => state.isApiCalling,

  getCategories: (state) => state.categories,

  getProducts: (state) => state.products,

  subCategories: (state) => state.subCategories,

  items: (state) => state.items, 

  allCategories: (state) => state.allCategories,
  // return only totals  according to tab changes
  getTotal: (state) => (tabKey) => {
    switch (tabKey) {
      case "active":
        return state.extraData?.activeTotal || 0;
      case "inactive":
        return state.extraData?.inactiveTotal || 0;
      case "pending":
        return state.extraData?.pendingTotal || 0;
      case "removed":
        return state.extraData?.removedTotal || 0;
      case "all":
      default:
        return state.extraData?.allTotal || 0;
    }
  },
};
