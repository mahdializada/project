export default {
  // return all users
  getItems: (state) => [...state.items],
  itemsTotal: (state) => state.extraData?.total || 0,

  // return only online users
  onlineUsers: (state) => [...state.onlineUsers],
  getAllItems: (state) => [...state.allItems],

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
      case "tracing":
        return state.extraData?.tracingTotal || 0;
      case "all":
        return state.extraData?.allTotal || 0;
      case "warning":
        return state.extraData?.warningTotal || 0;
      case "deleted":
        return state.extraData?.removedTotal || 0;
    }
  },

  // getters for registering users
  teams: (state) => state.teams,
  countries: (state) => state.countries,
  nationals: (state) => state.nationals,
  projects: (state) => state.projects,
  departments: (state) => state.departments,
  roles: (state) => state.roles,

  // return api calling status
  isApiCalling: (state) => state.isApiCalling,

  //return user profile edit dialog
  profileUser: (state) => state.profileUser,
};
