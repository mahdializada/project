import state from "./state";

export default {
  getProjects: (state) => (tabKey) => {
    switch (tabKey) {
      case "deleted":
        return [
          ...state.projects.filter(
            (project) => project?.deleted_at !== null
          ),
        ];
      case "inactive":
        return [
          ...state.projects.filter(
            (project) =>
              project?.status === "inactive" &&
              project?.deleted_at === null
          ),
        ];
      case "active":
        return [
          ...state.projects.filter(
            (project) =>
              project?.status === "active" && project?.deleted_at === null
          ),
        ];
      case "blocked":
        return [
          ...state.projects.filter(
            (project) =>
              project?.status === "blocked" &&
              project?.deleted_at === null
          ),
        ];
      case "pending":
        return [
          ...state.projects.filter(
            (project) =>
              project?.status === "pending" &&
              project?.deleted_at === null
          ),
        ];
      case "all":
      default:
        return [...state.projects];
    }
  },

  // return only totals  according to tab changes
  getTotal: (state) => (tabKey) => {
    switch (tabKey) {
      case "deleted":
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
      default:
        return state.extraData?.total || 0;
    }
  },
  // return api calling status
  isApiCalling: (state) => state.isApiCalling,
  
};
