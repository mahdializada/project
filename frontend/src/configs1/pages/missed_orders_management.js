import Icons from "~/configs/menus/menuIcons";
export default function (appContext) {
	return {
		// breadcrumb
		breadcrumb: [
			{
				text: "order_management_system",
				disabled: true,
				to: "",
				icon: Icons.my_orders,
			},
			{
				icon: Icons.sourcing_orders,

				text: "missed_orders",
				disabled: true,
				
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
				text: "process",
				icon: "fa-chalkboard-teacher",
				isSelected: 0,
				key: "process",
			},
			{
				text: "pendding",
				icon: "fa-archive",
				isSelected: 0,
				key: "pendding",
			},
		],
	};
}
