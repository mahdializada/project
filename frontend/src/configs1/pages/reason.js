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
        text: "reasons", 
        disabled: true, 
        to: "", 
        icon: Icons.reason_list 
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
        text: "reasons", 
        disabled: true, 
        to: "", 
        icon: Icons.reason_list 
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
        text: "reasons", 
        disabled: true, 
        to: "", 
        icon: Icons.reason_list 
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
        text: "reasons", 
        disabled: true, 
        to: "", 
        icon: Icons.reason_list 
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
        text: "reasons", 
        disabled: true, 
        to: "", 
        icon: Icons.reason_list 
      },
		],


		// tab items
		tabItems: [{ text: "all", icon: "fa-table", isSelected: 1, key: "all" }],

		// table headers
		headers: [
			{
				text: "id",
				value: "code",
				select: false,
				id: 1,
				sortable: false,
				category_id: 1,
			},
			{ text: "title", value: "title", select: false, id: 2, category_id: 1 },
			{
				text: "systems",
				value: "system.name",
				select: false,
				id: 3,
				category_id: 1,
			},
			{
				text: "created_at",
				value: "created_at",
				select: false,
				id: 4,
				category_id: 1,
			},
		],
	};
}
