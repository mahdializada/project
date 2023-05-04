import menuIcons from "../../menus/menuIcons";

export default function (appContext) {
	return {
		// breadcrumb
		breadcrumb: [
			// {
			// 	text: "dashboard",
			// 	exact: true,
			// 	to: "/",
			// 	icon: menuIcons.dashbourd,
			// },
			{
				text: "advertisement_management_system",
				disabled: true,
				to: "",
				icon: menuIcons.advertisement_management_system,
			},
			{
				text: "currency",
				disabled: true,
				to: "",
				icon: menuIcons.applications,
			},
		],

		// tab items
		tabItems: [
			{
				text: "all",
				icon: "fa-table",
				isSelected: true,
				key: "all",
			},
			{
				text: "active",
				icon: "fa-thumbs-up",
				isSelected: false,
				key: "active",
			},
			{
				text: "pending",
				icon: "fa-ban",
				isSelected: false,
				key: "pending",
			},
			{
				text: "inactive",
				icon: "fa-ban",
				isSelected: false,
				key: "inactive",
			},
			{
				text: "removed",
				icon: "fa-ban",
				isSelected: false,
				key: "removed",
			},
		],
	};
}
