import Icons from "../menus/menuIcons";
export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      {
        text: "advertisement_management_system",
        disabled: true,
        to: "",
        icon: Icons.advertisement_management_system,
      },
      {
        text: "account_pixel",
        disabled: true,
        to: "",
        icon: Icons.advertisement_management_system,
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
        text: "removed",
        icon: "fa-ban",
        isSelected: 0,
        key: "removed",
      },
    ],
  };
}
