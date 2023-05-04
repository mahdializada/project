
import Icons from "~/configs/menus/menuIcons";

export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb_items_user: [
      
			{
				text: "user_management_system",
				disabled: true,
				to: "",
				icon: Icons.user_management_system,
			},
      {
        text: "user_activity",
        disabled: true,
        to: "/users/logs",
        icon: Icons.activity,
      },
    ],

    breadcrumb_items_master: [
      
			{
				text: "master_management_system",
				disabled: true,
				to: "",
				icon: Icons.master_management_system,
			},
      {
        text: "master_activity",
        disabled: true,
        to: "/masters/logs",
        icon: Icons.activity,
      },
    ],

    breadcrumb_items_landing: [
      
			{
				text: "content_management_system",
				disabled: true,
				to: "",
				icon: Icons.content_management_system,
			},
      {
        text: appContext.$tr("landing_page_activity"),
        disabled: true,
        to: "/design-request/logs",
        icon: Icons.activity,
      },
    ],

    // tab items user
    tabItems_user: [
      {
        text: "all",
        icon: "fa fa-table",
        isSelected: 1,
        key: "all",
      },
      {
        text: "user",
        icon: "fa fa-user",
        isSelected: 0,
        key: "user",
      },
      {
        text: "team",
        icon: "mdi-account-group ",
        isSelected: 0,
        key: "team",
      },
      {
        text: "roles",
        icon: "mdi-shield-account",
        isSelected: 0,
        key: "role",
      },
    ],

    category: [
      { text: "all_caps", value: 0, category_id: 0, selected: true },
      {
        text: "general_info_caps",
        value: 0,
        category_id: 1,
        selected: false,
      },
      {
        text: "activity_information",
        value: 0,
        category_id: 2,
        selected: false,
      },
      { text: "location_caps", value: 0, category_id: 3, selected: false },
      { text: "datewise_caps", value: 0, category_id: 4, selected: false },
    ],

    // tab items master
    tabItems_master: [
      {
        text: "all",
        icon: "fa fa-table",
        isSelected: 1,
        key: "all",
      },
      {
        text: "country",
        icon: "mdi-earth",
        isSelected: 0,
        key: "country",
      },
      {
        text: "department",
        icon: "mdi-school ",
        isSelected: 0,
        key: "department",
      },
      {
        text: "company",
        icon: "mdi-domain",
        isSelected: 0,
        key: "company",
      },
      {
        text: "system",
        icon: "mdi-domain",
        isSelected: 0,
        key: "system",
      },
      {
        text: "business_locations",
        icon: "mdi-domain",
        isSelected: 0,
        key: "business_location",
      },
      {
        text: "language",
        icon: "mdi-domain",
        isSelected: 0,
        key: "language",
      },
      {
        text: "translation",
        icon: "mdi-domain",
        isSelected: 0,
        key: "translation",
      },
    ],

    tabItems_landing: [
      {
        text: "all",
        icon: "fa fa-table",
        isSelected: 1,
        key: "all",
      },
      // {
      //   text: "design_request",
      //   icon: "fa fa-table",
      //   isSelected: 0,
      //   key: "design_request",
      // },
    ],

    // table headers
    headers: [
      {
        text: "id",
        value: "user_code",
        align: "left",
        select: false,
        id: 1,
        category_id: 1,
      },
      {
        text: "username",
        value: "username",
        align: "left",
        select: false,
        id: 2,
        category_id: 1,
      },
      {
        text: "location",
        width: "300px",
        value: "location",
        align: "left",
        select: false,
        id: 3,
        category_id: 3,
      },
      {
        text: "event",
        value: "event",
        width: "120px",
        align: "left",
        select: false,
        id: 4,
        category_id: 2,
      },
      {
        text: "content",
        value: "content",
        align: "left",
        select: false,
        id: 5,
        category_id: 2,
      },
      {
        text: "page",
        value: "page",
        align: "left",
        select: false,
        id: 6,
        category_id: 2,
      },
      {
        text: "date",
        width: "200px",
        value: "date",
        align: "left",
        select: false,
        id: 7,
        category_id: 4,
      },
      {
        text: "time",
        width: "200px",
        value: "time",
        align: "left",
        select: false,
        id: 8,
        category_id: 4,
      },
    ],
  };
}
