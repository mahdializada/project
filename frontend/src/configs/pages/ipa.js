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
				icon: menuIcons.ipa,
				text: "IPA",
				disabled: true,
				to: "/ipa",
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
				text: "pending",
				icon: "fa-thumbs-up",
				isSelected: 0,
				key: "pending",
			},
			{
				text: "in_review",
				icon: "fa-eye",
				isSelected: 0,
				key: "in review",
			},
			{
				text: "rejected",
				icon: "fa-ban",
				isSelected: 0,
				key: "rejected",
			},
			{
				text: "in_creation",
				icon: "fa-plus",
				isSelected: 0,
				key: "in creation",
			},
			{
				text: "completed",
				icon: "fa-check",
				isSelected: 0,
				key: "completed",
			},
			{
				text: "in_testing",
				icon: "fa-trash",
				isSelected: 0,
				key: "in testing",
			},
			{
				text: "sales_moving",
				icon: "fa-trash",
				isSelected: 0,
				key: "sales moving",
			},
			{
				text: "sales_unstable",
				icon: "fa-trash",
				isSelected: 0,
				key: "sales unstable",
			},

			{
				text: "stopped",
				icon: "fa-trash",
				isSelected: 0,
				key: "stopped",
			},
			{
				text: "failed",
				icon: "fa-trash",
				isSelected: 0,
				key: "failed",
			},
			{
				text: "cancelled",
				icon: "fa-trash",
				isSelected: 0,
				key: "cancelled",
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
