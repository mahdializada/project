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

				text: "orders",
				disabled: true,
				to: "/orders",
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

		headers: [
			{
				text: "id",
				value: "code",
				select: false,
				id: 1,
				sortable: false,
				category_id: 1,
			},
			{
				text: "content_type",
				value: "content_type",
				select: false,
				id: 2,
				category_id: 2,
			},
		],
	};
}
