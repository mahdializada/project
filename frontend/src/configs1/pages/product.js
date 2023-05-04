import Icons from "~/configs/menus/menuIcons";

export default function (appContext) {
	return {
		// breadcrumb
		breadcrumb: [
			{
				text: "dashboard",
				exact: true,
				to: "/",
			},
			// {
			// 	icon: "mdi-professional-hexagon",
			// 	text: "products",
			// 	disabled: true,
			// 	to: "/products",
			// },
			{
				icon: Icons.products,

				text: "Products",
				disabled: true,
				to: "/products",
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
				text: "now",
				icon: "fa-thumbs-up",
				isSelected: 0,
				key: "now",
			},
			{
				text: "comming_soon",
				icon: "fa-ban",
				isSelected: 0,
				key: "comming soon",
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
				icon: "fa-ban",
				isSelected: 0,
				key: "removed",
			},
		],
		// table headers
	};
}
