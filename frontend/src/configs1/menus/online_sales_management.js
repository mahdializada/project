import Icons from "./menuIcons";
export default {
  color: "",
  text: "Online Sales Management",
  key: "online_sales_management_system",
  icon: Icons.products,
  items: [
    {
      text: "online_sales",
      key: "online_sales",
      link: "/online-sales-management-system/online-sales/",
      exact: true,
      scope: "osv",
      icon: Icons.products,
    },

    {
      text: "Settings",
      key: "settings",
      subLink: "online-sales-management",
      scope: ["losv"],
      icon: Icons.settings,
      items: [
        {
          text: "Labels",
          key: "labels",
          link: "/labels/online_sales",
          exact: true,
          scope: "losv",
          icon: Icons.labels,
        },
        {
          text: "Status Event List",
          key: "status_event_list",
          link: "/status_management/status_event/online_sales",
          exact: true,
          scope: "seosv",
          icon: Icons.status_event_list,
        },
        {
          text: "Reasons List",
          key: "reason_list",
          link: "/status_management/reasons/online_sales",
          exact: true,
          scope: "rnosv",
          icon: Icons.reason_list,
        },
      ],
    },
  ],
};
