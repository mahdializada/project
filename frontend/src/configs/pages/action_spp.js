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
				icon: menuIcons.actions,
				text: "Actions",
				disabled: true,
				to: "/actions",
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
				text: "inprocess",
				icon: "fa-chalkboard-teacher",
				isSelected: 0,
				key: "inprocess",
			},
			{
				text: "archived",
				icon: "fa-archive",
				isSelected: 0,
				key: "archived",
			},
			{
				text: "cancelled",
				icon: "fa-ban",
				isSelected: 0,
				key: "cancelled",
			},
			{
				text: "done",
				icon: "fa-check-circle",
				isSelected: 0,
				key: "done",
			},
			{
				text: "failed",
				icon: "fa-ban",
				isSelected: 0,
				key: "failed",
			},
			{
				text: "removed",
				icon: "fa-trash",
				isSelected: 0,
				key: "removed",
			},
		],
	};
}
