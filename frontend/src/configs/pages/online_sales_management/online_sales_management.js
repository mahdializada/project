import Icons from "~/configs/menus/menuIcons.js";
import tableIcons from "~/configs/menus/advertisementsTableIcons";

export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      {
        text: "online_sales_management_system",
        disabled: true,
        to: "",
        icon: Icons.products,
      },
      {
        text: "online_sales",
        disabled: true,
        to: "",
        icon: Icons.products,
      },
    ],
    // tab items
    itemTabs: [
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
        text: "item_code",
        name: "Item Code",
        icon: tableIcons.item_code,
        isSelected: 0,
        key: "item_code",
        selectedItems: [],
        count: 0,
        id_selector: 'id'
      },
    ],
  };
}
