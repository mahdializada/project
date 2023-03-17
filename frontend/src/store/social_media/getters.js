export default {
  getItems: (state) => {
    return state.items;
  },
  getAllItems: (state) => {
    return state.allItems;
  },

  isApiCalling: (state) => state.isApiCalling,

  getTotal: (state) => (tabKey) => {
    switch (tabKey) {
      case 'removed':
        return state.extraData?.removedTotal || 0;
      case 'inactive':
        return state.extraData?.inactiveTotal || 0;
      case 'blocked':
        return state.extraData?.blockedTotal || 0;
      case 'active':
        return state.extraData?.activeTotal || 0;
      case 'pending':
        return state.extraData?.pendingTotal || 0;
      case 'all':
      default:
        return state.extraData?.allTotal || 0;
    }
  },
};
