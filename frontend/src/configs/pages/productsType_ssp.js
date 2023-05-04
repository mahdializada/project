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
        icon: menuIcons.products,
        text: "Products Type",
        disabled: true,
        to: "/single-sales/productsType",
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
        icon: "fa-user-slash",
        isSelected: 0,
        key: "pending",
      },
      {
        text: "Attribute Setting",
        icon: "fa-drafting-compass",
        isSelected: 0,
        key: "in_attribute_setting",
      },
      {
        text: "ready",
        icon: "fa-thumbs-up",
        isSelected: 0,
        key: "ready",
      },
      {
        text: "cancel",
        icon: "fa-ban",
        isSelected: 0,
        key: "cancel",
      },
      {
        text: "removed",
        icon: "fa-trash",
        isSelected: 0,
        key: "removed",
      },
    ],
  };
}
