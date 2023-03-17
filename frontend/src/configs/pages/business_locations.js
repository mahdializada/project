
import Icons from "~/configs/menus/menuIcons";


export default function (appContext) {
  return {
 
    // breadcrumb
    breadcrumb: [
      
			{
				text: "master_management_system",
				disabled: true,
				to: "",
				icon: Icons.master_management_system,
			},
      {
        text: "business_locations",
        disabled: true,
        to: "/users",
        icon: Icons.business_locations,
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
        isSelected: 0,
        key: "blocked",
      },
      {
        text: "removed",
        icon: "fa-trash",
        isSelected: 0,
        key: "removed",
      },
      {
        text: "pending",
        icon: "fa-user-slash",
        isSelected: 0,
        key: "pending",
      },
    ],

  };
}
