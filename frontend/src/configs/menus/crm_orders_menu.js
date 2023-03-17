import Icons from "./menuIcons";

export default {
	text: "Orders Management Systems",
	key: "order_management_system",
	icon: Icons.sourcing_orders,
	items: [
		{
			text: "orders",
			key: "crm_orders",
			link: "/crm-orders",
			exact: true,
			scope: "crm_ordersv",
			icon: Icons.my_orders,
		},

	],
};
