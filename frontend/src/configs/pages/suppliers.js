import Icons from "../menus/menuIcons";
export default function (appContext) {
  return {
    category: [
      { text: "all_caps", value: 0, category_id: 0, selected: true },
      { text: "general_info_caps", value: 0, category_id: 1, selected: false },
      {
        text: "supplier_information",
        value: 0,
        category_id: 2,
        selected: false,
      },
      { text: "location_caps", value: 0, category_id: 3, selected: false },
      { text: "datewise_caps", value: 0, category_id: 4, selected: false },
    ],
    steppers: [
      {
        icon: "fa-info-circle",
        title: "general",
        slotName: "step1",
      },
      {
        icon: "fa-user",
        title: "supplier_information",
        slotName: "step2",
      },
      {
        icon: "fa-warehouse",
        title: "location",
        slotName: "step3",
      },
      { icon: "fa-edit", title: "note", slotName: "step4" },
      {
        icon: "fa-thumbs-up",
        title: "done",
        slotName: "step5",
      },
    ],
    // breadcrumb
    breadcrumb: [
      {
        text: "supplier_management_system",
        disabled: true,
        to: "/",
      },
      {
        text: "suppliers_list",
        disabled: true,
        to: "/supplier",
        icon: Icons.supplier_management_system,
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
        text: "active",
        icon: "fa-thumbs-up",
        isSelected: 0,
        key: "active",
      },
      {
        text: "inactive",
        icon: "fa-ban",
        isSelected: 0,
        key: "inactive",
      },
      {
        text: "blocked",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "blocked",
      },
      {
        text: "removed",
        icon: "fa-trash",
        isSelected: 0,
        key: "removed",
      },
      {
        text: "pending",
        icon: "fa-user-slash",
        isSelected: 0,
        key: "pending",
      },
    ],

    // Table headers
    headers: [
      {
        text: "id",
        value: "code",
        select: false,
        id: 1,
        category_id: 1,
        sortable: false,
      },
      {
        text: "logo",
        value: "supplierLogo",
        select: false,
        id: 2,
        category_id: 1,
      },
      {
        text: "supplier_name",
        value: "supplier_name",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "supplier_trading_name",
        value: "supplier_trading_name",
        select: false,
        id: 4,
        category_id: 1,
      },
      {
        text: "email",
        value: "email",
        select: false,
        id: 5,
        category_id: 2,
      },
      {
        text: "bank_account",
        value: "bank_account",
        select: false,
        id: 21,
        category_id: 4,
      },
      {
        text: "location",
        value: "supp_location",
        select: false,
        id: 22,
        category_id: 4,
      },
      {
        text: "main_phone",
        value: "main_phone",
        select: false,
        id: 6,
        category_id: 2,
      },
      {
        text: "purchase_order_phone",
        value: "purchase_order_phone",
        select: false,
        id: 7,
        category_id: 2,
      },
      {
        text: "supply_type",
        value: "supply_type",
        select: false,
        id: 8,
        category_id: 2,
      },
      {
        text: "commercial_type",
        value: "commercial_type",
        select: false,
        id: 10,
        category_id: 2,
      },
      {
        text: "status",
        value: "status",
        select: false,
        id: 9,
        category_id: 2,
      },
      {
        text: "website",
        value: "website",
        select: false,
        id: 11,
        category_id: 2,
      },
      {
        text: "prepration_period",
        value: "prepration_period",
        select: false,
        id: 12,
        category_id: 4,
      },
      {
        text: "legal_type",
        value: "legal_type",
        select: false,
        id: 13,
        category_id: 2,
      },
      {
        text: "country_type",
        value: "country_type",
        select: false,
        id: 14,
        category_id: 3,
      },
      {
        text: "companies",
        value: "companies",
        select: false,
        id: 15,
        category_id: 2,
      },
      {
        text: "sourcers",
        value: "sourcers",
        select: false,
        id: 16,
        category_id: 2,
      },
      {
        text: "note",
        value: "note",
        select: false,
        id: 17,
        category_id: 1,
      },
      {
        text: "created_by",
        value: "created_by",
        select: false,
        id: 18,
        category_id: 4,
      },
      {
        text: "updated_by",
        value: "updated_by",
        select: false,
        id: 19,
        category_id: 4,
      },
      {
        text: "created_at",
        value: "created_at",
        select: false,
        id: 20,
        category_id: 4,
      },
      
    ],
  };
}
