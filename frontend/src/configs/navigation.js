import UserMenu from "./menus/user_menu";
import MasterMenu from "./menus/master_menu";
import LandingMenu from "./menus/landing_menu";
import FileMenu from "./menus/file_menu";
import single_salse_menu from "./menus/single_salse_menu";
import adversitement_menu from "./menus/adversitement_menu";
import online_sales_management from "./menus/online_sales_management";
import Icons from "./menus/menuIcons";
// import ProductMenu from "./menus/product_menu";
import SupplierMunu from "./menus/supplier_menu";
import OrdersMenu from "./menus/crm_orders_menu";
import content_library from "./menus/content_library";
import file_management from "./menus/file_management";

export default {
	// main navigation - side menu
	menu: [
		{
			text: "Dashboard",
			key: "dashboard",
			link: "/",
			showForAll: true,
			icon: Icons.dashbourd,
		},
		MasterMenu,
		UserMenu,
		...FileMenu,
		LandingMenu,
		// ProductMenu,
		SupplierMunu,
		single_salse_menu,
		adversitement_menu,
		online_sales_management,
		OrdersMenu,
		content_library,
		file_management
	],
};
