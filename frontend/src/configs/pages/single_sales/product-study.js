import menuIcons from "../../menus/menuIcons";

export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      { text: "dashboard", exact: true, to: "/" },
      {
        text: "product_study",
        disabled: true,
        to: "",
        icon: menuIcons.product_study,
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
        text: "assigned",
        icon: "fa-user-check",
        isSelected: 0,
        key: "assigned",
      },
      {
        text: "not_assigned",
        icon: "fa-user-times",
        isSelected: 0,
        key: "not assigned",
      },
      {
        text: "removed",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "removed",
      },
    ],
    tabItemsOrder: [
      {
        text: "all",
        icon: "fa-table",
        isSelected: 1,
        key: "all",
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
        text: "removed",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "removed",
      },
    ],
    // table headers
    
  };
}
