import Icons from "../menus/menuIcons";
export default function (appContext) {

  return {
    // breadcrumb
    breadcrumb: [
      {
        text: "supplier_management_system",
        disabled: true,
        to: "/",
        icon: Icons.supplier_management_system,
      },
      {
        text: "product_list",
        disabled: true,
        to: "/product-list",
        icon: Icons.products,
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
        text: "with_supplier",
        icon: "fa-users",
        isSelected: 0,
        key: "with supplier",
      },
      {
        text: "without_supplier",
        icon: "fa-user",
        isSelected: 0,
        key: "without supplier",
      },
    ],
    headers: [
      {
        text: "id",
        value: "code",
        select: false,
        id: 1,
        category_id: 1,
        sortable: true,
      },
      {
        text: "image",
        value: "product_image",
        select: false,
        id: 2,
        category_id: 1,
      },
      {
        text: "product_name",
        value: "product_name",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "product_code",
        value: "product_code",
        select: false,
        id: 4,
        category_id: 1,
      },
      {
        text: "suppliers",
        value: "suppliers",
        select: false,
        id: 5,
        category_id: 1,
        sortable: false,
      },
    ],
  };
}
