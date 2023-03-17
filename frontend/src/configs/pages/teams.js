import Icons from "~/configs/menus/menuIcons";

export default function (appContext) {
  return {
  
    steppers: [
      {
        icon: "fa-info",
        title: "general",
        slotName: "step1",
      },
      {
        icon: "fa-lock",
        title: "team",
        slotName: "step2",
      },
      {
        icon: "fa-user-shield",
        title: "permissions",
        slotName: "step3",
      },
      {
        icon: "fa-users",
        title: "members",
        slotName: "step4",
      },
      {
        icon: "fa-comment-dots",
        title: "remarks",
        slotName: "step5",
      },
      {
        icon: "fa-thumbs-up",
        title: "done",
        slotName: "step6",
      },
    ],

    // breadcrumb
    breadcrumb: [
      
			{
				text: "user_management_system",
				disabled: true,
				to: "",
				icon: Icons.user_list,
			},
      {
        text: "team_list",
        disabled: true,
        to: "",
        icon: Icons.team_list,
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
        text: "warning",
        icon: "fa-exclamation-triangle",
        isSelected: 0,
        key: "warning",
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
