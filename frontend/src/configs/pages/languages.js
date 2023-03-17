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
				text: "language_setting",
				disabled: true,
				to: "",
				icon: Icons.language_setting,
			},
			{
				text: "languages",
				disabled: true,
				to: "",
				icon: Icons.languages,
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
				text: "pending",
				icon: "fa-ban",
				isSelected: 0,
				key: "pending",
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
		],

		// table headers
	};
}
