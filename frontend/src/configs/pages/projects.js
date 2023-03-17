
import Icons from "~/configs/menus/menuIcons";




export default function (appContext) {
  return {
   

    // Register Company Steppers
    steppers: [
      {
        icon: "fa-question-circle",
        title: "general",
        slotName: "step1",
      },
      {
        icon: "fa-lock",
        title: "company",
        slotName: "step2",
      },
      {
        icon: "fa-icons",
        title: "social_media",
        slotName: "step3",
      },
      {
        icon: "fa-comment-dots",
        title: "remarks",
        slotName: "step4",
      },
      {
        icon: "fa-thumbs-up",
        title: "done",
        slotName: "step5",
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
        text: "companies",
        disabled: true,
        to: "",
        icon: Icons.companies,
      },
    ],

    //  tabItem
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
