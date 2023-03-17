import state from "./state";




export default {
  getCountries: (state) => {
    return state.countries;
  },


  // return api calling status
  isApiCalling: (state) => state.isApiCalling,

  getCountriesWithCompanies: (state) => state.countriesWithCompanies,
  getAscCountries: (state) => state.ascCountries,
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
      case "all":
        return state.extraData?.allTotal || 0;
      case "warning":
        return state.extraData?.warningTotal || 0;
    }
  },
};
