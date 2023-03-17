
import Icons from "~/configs/menus/menuIcons"

export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      
      {
        text: "master_management_system",
        disabled: true,
        to: "",
        icon : Icons.master_management_system
      },
      {
        text: "countries",
        disabled: true,
        to: "",
        icon: Icons.countries,
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
        text: "blocked",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "blocked",
      },
    ],
    // table headers
    
  };
}
