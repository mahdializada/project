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
				text: "status_event_list", 
				disabled: true, 
				to: "", 
				icon: Icons.status_event_list 
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
				text: "status_event_list", 
				disabled: true, 
				to: "", 
				icon: Icons.status_event_list 
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
				text: "status_event_list", 
				disabled: true, 
				to: "", 
				icon: Icons.status_event_list 
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
				text: "status_event_list", 
				disabled: true, 
				to: "", 
				icon: Icons.status_event_list 
			},
		],
		breadcrumb_advertisement:[
			
			{
				text: "advertisement_management_system",
				disabled: true,
				to: "",
				icon: Icons.advertisement_management_system,
			},
			{ 
				text: "status_event_list", 
				disabled: true, 
				to: "", 
				icon: Icons.status_event_list 
      },
		],

		// tab items
		tabItems: [{ text: "all", icon: "fa-table", isSelected: 1, key: "all" }],

		// table headers
	};
}
