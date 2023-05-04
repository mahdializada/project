import Icons from "./menuIcons";
export default {
  color: "",
  text: "Supplier Management System",
  key: "supplier_management_system",
  icon: Icons.advertisement_management_system,
  items: [
    {
      key: "suppliers",
      text: "suppliers",
      link: "/supplier-management/supplier",
      exact: true,
      scope: "advv",
      icon: Icons.sourcer,
    },
    {
      key: "sourcers",
      text: "sourcers",
      link: "/supplier-management/sourcers",
      exact: true,
      scope: "advv",
      icon: Icons.sourcer,
    },
    {
      key: "product_list",
      text: "product_list",
      link: "/supplier-management/product-list",
      exact: true,
      scope: "advvHa",
      // scope: "prlv",
      icon: Icons.products,
    },
    {
      text: "quantity_reservation",
      key: "quantity_reservation",
      link: "/supplier-management/quantity-reservation",
      exact: true,
      scope: "qrrvHa",
      icon: Icons.quantity_reservation,
    },

    {
      text: "my_orders",
      key: "my_orders",
      subLink: "my-order-supplier",
      scope: ["sov_Ha"],
      icon: Icons.sourcing_orders,
      items: [
        {
          text: "sourcing_oders",
          key: "sourcing_orders",
          link: "/supplier-management/orders/sourcing",
          exact: true,
          scope: "sov_Ha",
          icon: Icons.my_orders,
        },
      ],
    },
    {
      text: "Settings",
      key: "settings",
      subLink: "supplier-settings",
      scope: ["lsupv"],
      icon: Icons.settings,
      items: [
				{
					text: "Status Event List",
					key: "status_event_list",
					link: "/status_management/status_event/supplier",
					exact: true,
					scope: "sesupv",
					icon: Icons.status_event_list,
				},
				{
					text: "Reasons List",
					key: "reason_list",
					link: "/status_management/reasons/supplier",
					exact: true,
					scope: "rnsupv",
					icon: Icons.reason_list,
				},
        {
          text: "Labels",
          key: "labels",
          link: "/labels/supplier",
          exact: true,
          scope: "lsupv",
          icon: Icons.labels,
        },
				
			],
    },
  ],
};
