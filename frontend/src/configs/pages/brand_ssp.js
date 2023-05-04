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
        icon: menuIcons.brand,
        text: "Brand",
        disabled: true,
        to: "/single-sales/brand",
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
        text: "active",
        icon: "fa-thumbs-up",
        isSelected: 0,
        key: "active",
      },
      {
        text: "inactive",
        icon: "fa-ban",
        isSelected: 0,
        key: "inactive",
      },
      {
        text: "canceled",
        icon: "fa-ban",
        isSelected: 0,
        key: "canceled",
      },
      {
        text: "removed",
        icon: "fa-ban",
        isSelected: 0,
        key: "removed",
      },
    ],
  };
}
