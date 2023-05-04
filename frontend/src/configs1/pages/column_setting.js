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
        text: "column_setting",
        disabled: true,
        to: "",
        icon: Icons.column_setting,
      },
    ],

    // table headers
    headers_system: [
      {
        text: "id",
        value: "id",
        select: false,
        id: 1,
        sortable: false,
        category_id: 1,
      },
      {
        text: "logo",
        value: "system_logo",
        select: false,
        id: 2,
        category_id: 1,
      },
      {
        text: "name",
        value: "name",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "updated_at",
        value: "updated_at",
        select: false,
        id: 4,
        category_id: 1,
      },
      {
        text: "created_at",
        value: "created_at",
        select: false,
        id: 5,
        category_id: 1,
      },
    ],

    headers_sub_system: [
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
        value: "name",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "updated_at",
        value: "updated_at",
        select: false,
        id: 4,
        category_id: 1,
      },
      {
        text: "created_at",
        value: "created_at",
        select: false,
        id: 5,
        category_id: 1,
      },
    ],
    headers_columns: [
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
        value: "text",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "tooltip",
        value: "tooltip",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "category",
        value: "category_id",
        select: false,
        id: 4,
        category_id: 1,
      },
      {
        text: "created_at",
        value: "created_at",
        select: false,
        id: 5,
        category_id: 1,
      },
    ],
  };
}
