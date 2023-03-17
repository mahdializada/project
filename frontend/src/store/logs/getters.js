let key = "";
let totalKey = "";
const getters = {
  get_logs: (state) => [...state.logs],
  get_file_name: (state) => state.file_name,

  // return only user logs according to tab changes
  filterLogs: (state) => (tabKey, system) => {
    key = tabKey;
    if (system == "users") {
      switch (key) {
        case "user":
          return state.logs.filter(
            (log) => log?.page == "user" && log?.system == "users"
          );
        case "team":
          return state.logs.filter(
            (log) => log?.page == "team" && log?.system == "users"
          );
        case "role":
          return state.logs.filter(
            (log) => log?.page == "role" && log?.system == "users"
          );
        case "reason":
          return state.logs.filter(
            (log) => log?.page == "reason" && log?.system == "users"
          );
        case "label":
          return state.logs.filter(
            (log) => log?.page == "label" && log?.system == "users"
          );
        case "status_event":
          return state.logs.filter(
            (log) => log?.page == "status_event" && log?.system == "users"
          );

        case "all":
          return state.logs.filter((log) => log?.system == "users");
      }
    } else if (system == "masters") {
      switch (key) {
        case "company":
          return state.logs.filter(
            (log) => log?.page == "company" && log?.system == "masters"
          );
        case "country":
          return state.logs.filter(
            (log) => log?.page == "country" && log?.system == "masters"
          );
        case "department":
          return state.logs.filter(
            (log) => log?.page == "department" && log?.system == "masters"
          );

        case "label":
          return state.logs.filter(
            (log) => log?.page == "label" && log?.system == "masters"
          );
        case "system":
          return state.logs.filter(
            (log) => log?.page == "system" && log?.system == "masters"
          );
        case "language":
          return state.logs.filter(
            (log) => log?.page == "language" && log?.system == "masters"
          );
        case "status_event":
          return state.logs.filter(
            (log) => log?.page == "status_event" && log?.system == "masters"
          );
        case "business_location":
          return state.logs.filter(
            (log) =>
              log?.page == "business_location" && log?.system == "masters"
          );
        case "language":
          return state.logs.filter(
            (log) => log?.page == "language" && log?.system == "masters"
          );
        case "translation":
          return state.logs.filter(
            (log) => log?.page == "translation" && log?.system == "masters"
          );

        case "all":
          return state.logs.filter((log) => log?.system == "masters");
      }
    } else if (system == "landing_page") {
      switch (key) {
        case "design_request":
          return state.logs.filter(
            (log) =>
              log?.page == "design_request" && log?.system == "landing_page"
          );
        case "status_event":
          return state.logs.filter(
            (log) =>
              log?.page == "status_event" && log?.system == "landing_page"
          );

        case "all":
          return state.logs.filter((log) => log?.system == "landing_page");
      }
    }
  },

  // return only totals  according to tab changes
  getTotal: (state) => (tabKey, system) => {
    totalKey = tabKey;
    if (system == "users") {
      switch (totalKey) {
        case "user":
          return state.logs.filter(
            (log) => log?.page == "user" && log?.system == "users"
          ).length;
        case "team":
          return state.logs.filter(
            (log) => log?.page == "team" && log?.system == "users"
          ).length;
        case "role":
          return state.logs.filter(
            (log) => log?.page == "role" && log?.system == "users"
          ).length;
        case "reason":
          return state.logs.filter(
            (log) => log?.page == "reason" && log?.system == "users"
          ).length;
        case "label":
          return state.logs.filter(
            (log) => log?.page == "label" && log?.system == "users"
          ).length;
        case "status_event":
          return state.logs.filter(
            (log) => log?.page == "status_event" && log?.system == "users"
          ).length;

        case "all":
          return state.logs.filter((log) => log?.system == "users").length;
      }
    } else if (system == "masters") {
      switch (totalKey) {
        case "company":
          return state.logs.filter(
            (log) => log?.page == "company" && log?.system == "masters"
          ).length;
        case "country":
          return state.logs.filter(
            (log) => log?.page == "country" && log?.system == "masters"
          ).length;
        case "department":
          return state.logs.filter(
            (log) => log?.page == "department" && log?.system == "masters"
          ).length;

        case "label":
          return state.logs.filter(
            (log) => log?.page == "label" && log?.system == "masters"
          ).length;
        case "language":
          return state.logs.filter(
            (log) => log?.page == "language" && log?.system == "masters"
          ).length;
        case "status_event":
          return state.logs.filter(
            (log) => log?.page == "status_event" && log?.system == "masters"
          ).length;
        case "business_location":
          return state.logs.filter(
            (log) =>
              log?.page == "business_location" && log?.system == "masters"
          ).length;
        case "language":
          return state.logs.filter(
            (log) => log?.page == "language" && log?.system == "masters"
          ).length;
        case "translation":
          return state.logs.filter(
            (log) => log?.page == "translation" && log?.system == "masters"
          ).length;
        case "system":
          return state.logs.filter(
            (log) => log?.page == "system" && log?.system == "masters"
          ).length;

        case "all":
          return state.logs.filter((log) => log?.system == "masters").length;
      }
    } else if (system == "landing_page") {
      switch (totalKey) {
        case "design_request":
          return state.logs.filter(
            (log) =>
              log?.page == "design_request" && log?.system == "landing_page"
          ).length;
        case "status_event":
          return state.logs.filter(
            (log) =>
              log?.page == "status_event" && log?.system == "landing_page"
          ).length;

        case "all":
          return state.logs.filter((log) => log?.system == "landing_page")
            .length;
      }
    }
  },
};

export default getters;
