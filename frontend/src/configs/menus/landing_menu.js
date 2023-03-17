import Icons from "../menus/menuIcons";
export default {
  text: "Content Management System",
  // key: "content_management_system",
  icon: Icons.content_management_system,
  items: [
    {
      text: "Design Request",
      key: "design_request",
      link: "/landing-page/design-request",
      exact: true,
      scope: "drvHM",
      icon: Icons.design_request,
    },
    {
      text: "My Orders",
      key: "my_orders",
      link: "/landing-page/orders",
      exact: true,
      scope: "drovHM",
      icon: Icons.my_orders,
    },
    {
      text: "History",
      key: "history",
      link: "/landing-page/histories",
      exact: true,
      scope: "drovHM",
      icon: Icons.history,
    },
    {
      text: "Settings",
      key: "settings",
      scope: ["rncvHM", "secvHM"],
      subLink: "landing-settings",
      icon: Icons.settings,
      items: [
        {
          text: "Status Event List",
          key: "status_event_list",
          link: "/status_management/status_event/content",
          exact: true,
          scope: "secvHM",
          icon: Icons.status_event_list,
        },
        {
          text: "Reasons List",
          key: "reason_list",
          link: "/status_management/reasons/content",
          exact: true,
          scope: "rncvHM",
          icon: Icons.reason_list,
        },
        {
          text: "Labels List",
          key: "labels",
          link: "/labels/content",
          exact: true,
          scope: "lcvHM",
          icon: Icons.labels,
        },
      ],
    },
    {
      text: "Activities List",
      key: "landing_page_activity",
      link: "/logs/landing_page",
      exact: true,
      scope: "lpavHM",
      icon: Icons.activity,
    },
  ],
};
