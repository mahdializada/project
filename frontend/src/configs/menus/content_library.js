import Icons from "./menuIcons";
export default {
  color: "",
  text: "Content Library Management System",
  key: "content_library_management_system",
  icon: Icons.library,
  items: [
    {
      text: "content_library",
      key: "content_library",
      link: "/content-library/media",
      exact: true,
      scope: "clv",
      icon: Icons.library,
    },
    {
      text: "favorites",
      key: "favorites",
      link: "/content-library/favorite",
      exact: true,
      scope: "clv",
      icon: Icons.favorite,
    },
    {
      text: "Recent Files",
      key: "recent_files",
      link: "/content-library/history",
      exact: true,
      scope: "clv",
      icon: Icons.history,
    },
    {
      text: "Settings",
      key: "settings",
      subLink: "supplier-settings",
      scope: ["clv"],
      icon: Icons.settings,
      items: [
        {
          text: "Labels",
          key: "labels",
          link: "/labels/content_library",
          exact: true,
          scope: "clv",
          icon: Icons.labels,
        },
        {
          text: "Status Event List",
          key: "status_event_list",
          link: "/status_management/status_event/content_library",
          exact: true,
          scope: "clv",
          icon: Icons.status_event_list,
        },
        {
          text: "Reasons List",
          key: "reason_list",
          link: "/status_management/reasons/content_library",
          exact: true,
          scope: "clv",
          icon: Icons.reason_list,
        },

      ],
    },
  ],
};
