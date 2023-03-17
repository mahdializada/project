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
				text: "applications",
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
				text: "expired",
				icon: "fa-ban",
				isSelected: 0,
				key: "token_expired",
			},
			{
				text: "removed",
				icon: "fa-ban",
				isSelected: 0,
				key: "removed",
			},
		],
	};
}
