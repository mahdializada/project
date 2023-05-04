
import Icons from "~/configs/menus/menuIcons";


export default function (appContext) {
  return {
    // Category
 
    // Steppers
    steppers: [
      {
        icon: "fa-info-circle",
        title: "general",
        slotName: "step1",
      },
      {
        icon: "mdi-list-status",
        title: "sub_system",
        slotName: "step2",
      },
      {
        icon: "fa-thumbs-up",
        title: "done",
        slotName: "step3",
      },
    ],
    // breadcrumb
    breadcrumb: [
      
			{
				text: "master_management_system",
				disabled: true,
				to: "",
				icon: Icons.master_management_system,
			},
      {
        text: "social_media",
        disabled: true,
        to: "",
        icon: Icons.social_media,
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
      {
        text: "deleted",
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
    // table headers
  };
}
