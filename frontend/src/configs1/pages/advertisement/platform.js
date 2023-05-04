import menuIcons from "../../menus/menuIcons";

export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      // {
			// 	text: "dashboard",
			// 	exact: true,
			// 	to: "/",
			// 	icon: menuIcons.dashbourd,
			// },
			{
				text: "advertisement_management_system",
				disabled: true,
				to: "",
				icon: menuIcons.advertisement_management_system,
			},
      {
        text: "platforms",
        disabled: true,
        to: "",
        icon: menuIcons.platforms,
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
        icon: "fa-table",
        isSelected: 0,
        key: "current",
      },
      // {
      //   text: "removed",
      //   icon: "fa-times-circle",
      //   isSelected: 0,
      //   key: "removed",
      // },
    ],
    // table headers
  };
}
