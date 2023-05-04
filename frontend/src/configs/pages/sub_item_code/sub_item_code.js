import Icons from "~/configs/menus/menuIcons.js";
import tableIcons from "~/configs/menus/advertisementsTableIcons";

export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      {
        text: "advertisement_management_system",
        disabled: true,
        to: "",
        icon: Icons.advertisement_management_system,
      },
      {
        text: "sub_item_list",
        disabled: true,
        to: "",
        icon: Icons.advertisement_management_system,
      },
    ],
    // tab items
    tabItems: [
      {
        text: "country",
        name: "Country",
        icon: tableIcons.country,
        isSelected: 1,
        key: "country",
        selectedItems: [],
        count: 0,
        id_selector: 'country_id'
      },
      {
        text: "company",
        name: "Company",
        icon: tableIcons.company,
        isSelected: 0,
        key: "company",
        selectedItems: [],
        count: 0,
        id_selector: 'company_id'
      },
      {
        text: "project",
        name: "Project",
        icon: tableIcons.company,
        isSelected: 0,
        key: "project",
        selectedItems: [],
        count: 0,
        id_selector: 'project_id'

      },
      {
        text: "sales_type",
        name: "sales_type",
        icon: tableIcons.company,
        isSelected: 0,
        key: "sales_type",
        selectedItems: [],
        count: 0,
        id_selector: 'sales_type'

      },
      {
        text: "store_code",
        name: "Store Code",
        icon: tableIcons.item_code,
        isSelected: 0,
        key: "store_code",
        selectedItems: [],
        count: 0,
        id_selector: 'id'
      },
      {
        text: "sub_item_code",
        name: "Sub Item Code",
        icon: tableIcons.item_code,
        isSelected: 0,
        key: "sub_item_code",
        selectedItems: [],
        count: 0,
        id_selector: 'id'
      },
    ],
  };
}
