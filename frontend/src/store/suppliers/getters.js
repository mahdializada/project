export default {
  getSuppliers: (state) => {
    return state.suppliers;
  },
  getSuccessInsert:(state)=>state.insertItem,

  // return only totals  according to tab changes
  getTotal: (state) => (tabKey) => {
    switch (tabKey) {
      case "removed":
        return state.extraData?.deletedTotal || 0;
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
        return state.extraData?.warningTotal || 0;
      default: return
    }
  },
  isApiCalling: (state) => state.isApiCalling,

};