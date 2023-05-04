import Icons from "~/configs/menus/menuIcons";
export default function (appContext) {
	return {
		// breadcrumb
		breadcrumb: [
			{
				text: "online_sales_management_system",
				disabled: true,
				to: "",
				icon: Icons.my_orders,
			},
			{
				icon: Icons.sourcing_orders,

				text: "sales_category",
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
			}
		],
	};
}
