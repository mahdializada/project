const getters = {
  getLabels: (state) => [...state.labels],

  getTotal: (state) => (tabKey) => {
    switch (tabKey) {
      case "archive":
        return state.extraData?.archiveTotal || 0;
      case "live":
        return state.extraData?.liveTotal || 0;
      case "all":
        return state.extraData?.allTotal || 0;
      case "removed":
        return state.extraData?.removedTotal || 0;
      default: return state.extraData?.allTotal || 0;

    }
  },
  isApiCalling: (state) => state.isApiCalling,

}

export default getters
