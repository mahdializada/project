import Icons from "~/configs/menus/menuIcons";



export default function (appContext) {
  return {
   
    // breadcrumb
    breadcrumb: [
      
			{
				text: "user_management_system",
				disabled: true,
				to: "",
				icon: Icons.user_list,
			},
      {
        icon: Icons.user_list,
        text: "user_list",
        disabled: true,
        to: "/users",
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
        text: "warning",
        icon: "fa-exclamation-triangle",
        isSelected: 0,
        key: "warning",
      },
      {
        text: "blocked",
        icon: "fa-times-circle",
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
      {
        text: "tracing",
        icon: "fa-user-shield",
        isSelected: 0,
        key: "tracing",
      },
    ],

    // table headers
  
    // edit steppers
    editSteppers: [
      {
        icon: "fa-user",
        title: "general",
        slotName: "step1",
      },
      {
        icon: "fa-user",
        title: "account",
        slotName: "step2",
      },
      {
        icon: "fa-user",
        title: "personal",
        slotName: "step3",
      },
      {
        icon: "fa-user-shield",
        title: "permission",
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
    autoEditSteppers: [
      {
        icon: "fa-user",
        title: "general",
        slotName: "step1",
      },
      {
        icon: "fa-user",
        title: "account",
        slotName: "step2",
      },
      {
        icon: "fa-user",
        title: "personal",
        slotName: "step3",
      },
      {
        icon: "fa-user-shield",
        title: "permission",
        slotName: "step4",
      },
      {
        icon: "fa-comment-dots",
        title: "remarks",
        slotName: "step5",
      },
    ],
  };
}
