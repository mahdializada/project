
import Icons from '~/configs/menus/menuIcons.js'

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
        text: "company_information",
        value: 0,
        category_id: 2,
        selected: false,
      },
      {
        value: 0,
        text: "location_caps",
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
				text: "content_management_system",
				disabled: true,
				to: "",
				icon: Icons.content_management_system,
			},
      {
        text: "order",
        disabled: true,
        to: "",
        icon: Icons.my_orders,
      },
    ],

    //  tabItem
    tabItems: [
      {
        text: "all",
        icon: "fa-table",
        isSelected: 1,
        key: "all",
      },
      {
        text: "in_storyboard",
        icon: "fa-hourglass-start",
        isSelected: 0,
        key: "in storyboard",
      },
      {
        text: "storyboard_review",
        icon: "fa-tasks",
        isSelected: 0,
        key: "storyboard review",
      },
      {
        text: "in_design",
        icon: "fa-drafting-compass",
        isSelected: 0,
        key: "in design",
      },
      {
        text: "design_review",
        icon: "fa-tasks",
        isSelected: 0,
        key: "design review",
      },

      {
        text: "in_programming",
        icon: "fa-code",
        isSelected: 0,
        key: "in programming",
      },
      {
        text: "programming_review",
        icon: "fa-user-slash",
        isSelected: 0,
        key: "programming review",
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
        text: "content_type",
        value: "content_type",
        select: false,
        id: 2,
        category_id: 2,
      },
      {
        text: "company",
        value: "company",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "template",
        value: "template",
        select: false,
        id: 4,
        sortable: false,
        category_id: 1,
      },
      {
        text: "order_type",
        value: "order_type",
        select: false,
        id: 5,
        sortable: false,
        category_id: 1,
      },
      {
        text: "product_code",
        value: "product_code",
        select: false,
        id: 6,
        category_id: 1,
      },
      {
        text: "product_name",
        value: "product_name",
        select: false,
        id: 7,
        category_id: 1,
      },
      {
        text: "priority",
        value: "priority",
        select: false,
        id: 8,
        sortable: false,
        category_id: 1,
      },
      {
        text: "percentage",
        value: "percentage",
        select: false,
        id: 9,
        sortable: false,
        category_id: 1,
      },
      {
        text: "total_time_spent",
        value: "total_time_spent",
        select: false,
        id: 10,
        sortable: false,
        category_id: 1,
      },
      {
        text: "status",
        value: "status",
        select: false,
        id: 11,
        sortable: false,
        category_id: 1,
      },
      {
        text: "total_storyboard_rejected",
        value: "storyboard_reject_count",
        select: false,
        id: 12,
        sortable: false,
        category_id: 1,
      },
      {
        text: "total_design_rejected",
        value: "design_reject_count",
        select: false,
        id: 13,
        sortable: false,
        category_id: 1,
      },
      {
        text: "label",
        value: "label",
        select: false,
        id: 14,
        sortable: false,
        category_id: 1,
      },
      {
        text: "updated_by",
        // text: "systems",
        value: "updatedBy",
        select: false,
        id: 15,
        sortable: false,
        category_id: 1,
      },
      {
        text: "created_at",
        // text: "note",
        value: "created_at",
        select: false,
        id: 16,
        sortable: false,
        category_id: 1,
      },
    ],
  };
}
