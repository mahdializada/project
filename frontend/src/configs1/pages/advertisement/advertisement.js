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
        text: "advertisement",
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
      },
      {
        text: "company",
        name: "Company",
        icon: tableIcons.company,
        isSelected: 0,
        key: "company",
        selectedItems: [],
        count: 0,
      },
      {
        text: "project",
        name: "Project",
        icon: tableIcons.company,
        isSelected: 0,
        key: "project",
        selectedItems: [],
        count: 0,
      },
      {
        text: "sales_type",
        name: "sales_type",
        icon: tableIcons.company,
        isSelected: 0,
        key: "sales_type",
        selectedItems: [],
        count: 0,
      },
      {
        text: "item_code",
        name: "Item Code",
        icon: tableIcons.item_code,
        isSelected: 0,
        key: "item_code",
        selectedItems: [],
        count: 0,
      },
      {
        text: "ispp_code",
        name: "Ispp Code",
        icon: tableIcons.ispp,
        isSelected: 0,
        key: "ispp_code",
        selectedItems: [],
        count: 0,
      },
      {
        text: "platform",
        name: "Platform",
        icon: tableIcons.platform,
        isSelected: 0,
        key: "platform",
        selectedItems: [],
        count: 0,
      },
      {
        text: "organization",
        name: "organization",
        icon: tableIcons.organization,
        isSelected: 0,
        key: "organization",
        selectedItems: [],
        count: 0,
      },
      {
        text: "ad_account",
        name: "Ad Account",
        icon: tableIcons.ad_account,
        isSelected: 0,
        key: "ad_account",
        selectedItems: [],
        count: 0,
      },
      {
        text: "campaign",
        name: "campaign",
        icon: tableIcons.compaign,
        isSelected: 0,
        key: "campaign",
        selectedItems: [],
        count: 0,
      },
      {
        text: "ad_set",
        name: "Adset",
        icon: tableIcons.ad_set,
        isSelected: 0,
        key: "ad_set",
        selectedItems: [],
        count: 0,
      },
      {
        text: "ad",
        name: "ad",
        icon: tableIcons.ad,
        isSelected: 0,
        key: "ad",
        selectedItems: [],
        count: 0,
      },
    ],
    productTab: [
      {
        text: "country",
        name: "Country",
        icon: tableIcons.country,
        isSelected: 1,
        key: "country",
        selectedItems: [],
        count: 0,
      },
      {
        text: "company",
        name: "Company",
        icon: tableIcons.company,
        isSelected: 0,
        key: "company",
        selectedItems: [],
        count: 0,
      },
      {
        text: "project",
        name: "Project",
        icon: tableIcons.company,
        isSelected: 0,
        key: "project",
        selectedItems: [],
        count: 0,
      },
      {
        text: "sales_type",
        name: "sales_type",
        icon: tableIcons.company,
        isSelected: 0,
        key: "sales_type",
        selectedItems: [],
        count: 0,
      },
      {
        text: "item_code",
        name: "Item Code",
        icon: tableIcons.item_code,
        isSelected: 0,
        key: "item_code",
        selectedItems: [],
        count: 0,
      },
    ],
    upload_tabItem: [
      {
        text: "campaign",
        name: "campaign",
        icon: tableIcons.compaign,
        isSelected: 0,
        key: "campaign",
        selectedItems: [],
        count: 0,
      },
      {
        text: "ad_set",
        name: "Adset",
        icon: tableIcons.ad_set,
        isSelected: 0,
        key: "ad_set",
        selectedItems: [],
        count: 0,
      },
      {
        text: "ad",
        name: "ad",
        icon: tableIcons.ad,
        isSelected: 0,
        key: "ad",
        selectedItems: [],
        count: 0,
      },
    ],
  };
}
