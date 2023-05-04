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
        text: "manual_upload",
        disabled: true,
        to: "",
        icon: Icons.advertisement_management_system,
      },
    ],
    // tab items
    tabItems: [
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
