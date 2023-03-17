export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      { text: "dashboard", exact: true, to: "/" },
      {
        text: "sourcer",
        disabled: true,
        to: "",
        icon: "mdi-alpha-s-circle",
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
        text: "pending",
        icon: "fa-thumbs-up",
        isSelected: 0,
        key: "pending",
      },
      {
        text: "assigned",
        icon: "fa-user-check",
        isSelected: 0,
        key: "assigned",
      },
      {
        text: "not_assigned",
        icon: "fa-user-times",
        isSelected: 0,
        key: "not assigned",
      },
      // {
        //   text: "rejected",
      //   icon: "fa-ban",
      //   isSelected: 0,
      //   key: "rejected",
      // },
      {
        text: "in_sourcing",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "in sourcing",
      },
      {
        text: "in_hold",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "in hold",
      },
      {
        text: "found",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "found",
      },
      {
        text: "not_found",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "not found",
      },
      {
        text: "cancelled",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "cancelled",
      },

      {
        text: "removed",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "removed",
      },

    ],
    tabItemsOrder: [
      {
        text: "all",
        icon: "fa-table",
        isSelected: 1,
        key: "all",
      },
      {
        text: "in_sourcing",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "in sourcing",
      },
      {
        text: "in_hold",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "in hold",
      },
      {
        text: "found",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "found",
      },
      {
        text: "not_found",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "not found",
      },
      {
        text: "cancelled",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "cancelled",
      },

      {
        text: "removed",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "removed",
      },

    ],
  };
}
