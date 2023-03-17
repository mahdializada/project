import Icons from "~/configs/menus/menuIcons";



export default function (appContext) {
  return {
    category: [
      {
        text: "all_caps",
        value: 0,
        category_id: 0,
        selected: true,
      },
      {
        text: "general_info_caps",
        value: 0,
        category_id: 1,
        selected: false,
      },
      {
        text: "country_info_caps",
        value: 0,
        category_id: 2,
        selected: false,
      },
    ],

    // breadcrumb
    breadcrumb: [
      
			{
				text: "master_management_system",
				disabled: true,
				to: "",
				icon: Icons.master_management_system,
			},
			{
				text: "settings",
				disabled: true,
				to: "",
				icon: Icons.settings,
			},
      {
        text: "column_category",
        disabled: true,
        to: "",
        icon: Icons.column_category,
      },
    ],

    // table headers
    headers: [
      {
        text: "id",
        value: "id",
        select: false,
        id: 1,
        sortable: false,
        category_id: 1,
      },
      {
        text: "name",
        value: "category_name",
        select: false,
        id: 2,
        category_id: 1,
      },
      {
        text: "sub_system",
        value: "sub_systems",
        select: false,
        id: 2,
        category_id: 1,
      },

      {
        text: "created_by",
        value: "user",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "created_at",
        value: "created_at",
        select: false,
        id: 3,
        category_id: 1,
      },
    ],
  };
}
