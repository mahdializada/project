
import Icons from "~/configs/menus/menuIcons";


export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb_user: [
			
			{
				text: "user_management_system",
				disabled: true,
				to: "",
				icon: Icons.user_management_system,
			},
			{
        text: "labels",
        disabled: true,
        to: "",
        icon: Icons.labels,
      },
		],
		
		breadcrumb_master: [
			
			{
				text: "master_management_system",
				disabled: true,
				to: "",
				icon: Icons.master_management_system,
			},
			{
        text: "labels",
        disabled: true,
        to: "",
        icon: Icons.labels,
      },
		],
		
		breadcrumb_landing: [
			
			{
				text: "content_management_system",
				disabled: true,
				to: "",
				icon: Icons.content_management_system,
			},
			{
        text: "labels",
        disabled: true,
        to: "",
        icon: Icons.labels,
      },
		],

    breadcrumb_personal: [
			
			{
				text: "personal_file_management",
				disabled: true,
				to: "",
				icon: Icons.personal_file_management,
			},
			{
        text: "labels",
        disabled: true,
        to: "",
        icon: Icons.labels,
      },
		],

    breadcrumb_advertisement: [
			
			{
				text: "advertisement_management_system",
				disabled: true,
				to: "",
				icon: Icons.advertisement_management_system,
			},
			{
        text: "labels",
        disabled: true,
        to: "",
        icon: Icons.labels,
      },
		],


    // tab items
    tabItems: [{
          text: "all",
          icon: "fa-table",
          isSelected: 1,
          key: "all"
        },
        {
          text: "archive",
          icon: "fa-thumbs-up",
          isSelected: 0,
          key: "archive"
        },
        {
          text: "live",
          icon: "fa-ban",
          isSelected: 0,
          key: "live"
        },
        {
          text: "removed",
          icon: "fa-trash",
          isSelected: 0,
          key: "removed"
        },
    ],

    // table headers
    
  };
}