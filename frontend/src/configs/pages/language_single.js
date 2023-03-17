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
        text: "language_info_caps",
        value: 0,
        category_id: 2,
        selected: false,
      },
      {
        text: "location_caps",
        value: 0,
        category_id: 3,
        selected: false,
      },
      {
        text: "datewise_caps",
        value: 0,
        category_id: 4,
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
				text: "language_setting",
				disabled: true,
				to: "",
				icon: Icons.language_setting,
			},
      {
        text: "translation",
        exact: true,
        to: "/languages/translation",
        icon: Icons.translation,
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
        text: "translated",
        icon: "fa-thumbs-up",
        isSelected: 0,
        key: "translated",
      },
      {
        text: "not_translated",
        icon: "fa-thumbs-down",
        isSelected: 0,
        key: "notTranslated",
      },
    ],

    // table headers
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
        text: "phrase",
        value: "phrase",
        select: false,
        id: 2,
        category_id: 1,
      },
      {
        text: "base_language",
        value: "base_lang_word.translation",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "translation",
        value: "lang_word.translation",
        select: false,
        id: 4,
        category_id: 1,
      },
      {
        text: "word_type",
        value: "word_type",
        select: false,
        id: 5,
        category_id: 1,
      },
      {
        text: "created_by",
        value: "created_by",
        select: false,
        id: 6,
        category_id: 1,
      },
      {
        text: "updated_by",
        value: "updated_by",
        select: false,
        id: 7,
        category_id: 1,
      },
      {
        text: "created_at",
        value: "lang_word.created_at",
        select: false,
        id: 8,
        category_id: 4,
      },
    ],
  };
}
