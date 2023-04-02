import Icons from "../menus/menuIcons";
export default {
	text: "Single Sales Management System",
	key: "single_sales_management_system",
	icon: Icons.single_sales_management_system,
	items: [
		{
			text: "Attributes",
			key: "attributes",
			link: "/single_sales_management_system/attributes",
			exact: true,
			scope: "advv",
			icon: Icons.attributes,
		},
		{
			text: "Products",
			key: "products",
			link: "/single_sales_management_system/products",
			exact: true,
			scope: "advv",
			icon: Icons.products,
		},
		{
			text: "product_study",
			key: "product_study",
			link: "/single_sales_management_system/product-study",
			exact: true,
			scope: "advvHA",
			icon: Icons.product_study,
		},
		{
			text: "ISPP",
			key: "ispp",
			link: "/single_sales_management_system/ispp",
			exact: true,
			scope: "advvMA",
			icon: Icons.ispp,
		},
		{
			text: "IPA",
			key: "ipa",
			link: "/single_sales_management_system/ipa",
			exact: true,
			scope: "advvMA",
			icon: Icons.ipa,
		},
		{
			text: "Actions",
			key: "actions",
			link: "/single_sales_management_system/actions",
			exact: true,
			scope: "advvMA",
			icon: Icons.actions,
		},
		{
			text: "brand",
			key: "brand",
			link: "/single_sales_management_system/brand",
			exact: true,
			scope: "advv",
			icon: Icons.brand,
		},
		{
			text: "Catogories",
			key: "catogories",
			link: "/single_sales_management_system/categories",
			exact: true,
			scope: "advv",
			icon: Icons.catogories,
		},

		{
			text: "my_orders",
			key: "my_orders",
			subLink: "file-settings",
			scope: ["advv", "advv"],
			icon: Icons.sourcing_orders,
			items: [
				{
					text: "product_study_orders",
					key: "product_study_orders",
					link: "/single_sales_management_system/orders/product_study",
					exact: true,
					scope: "advv",
					icon: Icons.my_orders,
				},
			],
		},
		{
			text: "Settings",
			key: "settings",
			subLink: "file-settings",
			scope: ["advv", "advv"],
			icon: Icons.settings,
			items: [
				{
					text: "Status Event List",
					key: "status_event_list",
					link: "/status_management/status_event/single_sale",
					exact: true,
					scope: "advv",
					icon: Icons.status_event_list,
				},
				{
					text: "Reasons List",
					key: "reason_list",
					link: "/status_management/reasons/single_sale",
					exact: true,
					scope: "advv",
					icon: Icons.reason_list,
				},
			],
		},
	],
};