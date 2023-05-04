import menuIcons from "../menus/menuIcons";

export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      {
        text: "dashboard",
        exact: true,
        to: "/",
      },
      {
        icon: menuIcons.ispp,
        text: "ISPP",
        disabled: true,
        to: "/ispp",
      },
    ],
    // tab items
    tabItems: [
      {
        text: "all",
        icon: "fa-table",
        isSelected: 1,
        key: "all",
      },
      {
        text: "pending",
        icon: "fa-thumbs-up",
        isSelected: 0,
        key: "pending",
      },
      {
        text: "in review",
        icon: "fa-ban",
        isSelected: 0,
        key: "in review",
      },
      {
        text: "in preparation",
        icon: "fa-trash",
        isSelected: 0,
        key: "in preparation",
      },
      {
        text: "in hold",
        icon: "fa-trash",
        isSelected: 0,
        key: "in hold",
      },
      {
        text: "completed",
        icon: "fa-trash",
        isSelected: 0,
        key: "completed",
      },
      {
        text: "failed",
        icon: "fa-trash",
        isSelected: 0,
        key: "failed",
      },
      {
        text: "cancelled",
        icon: "fa-trash",
        isSelected: 0,
        key: "cancelled",
      },
      {
        text: "removed",
        icon: "fa-trash",
        isSelected: 0,
        key: "removed",
      },
    ]
  };
}
