import menuIcons from "../menus/menuIcons";

export default function (appContext) {
	return {
	
		// breadcrumb
		breadcrumb: [
			{
				text: "dashboard",
				exact: true,
				to: "/",
			},
			{
				icon: menuIcons.sourcer,
				text: "Sourcers",
				disabled: true,
				to: "/sourcers",
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
				text: "removed",
				icon: "fa-trash",
				isSelected: 0,
				key: "removed",
			},
		],

		// table headers
		
	};
}
