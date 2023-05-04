export default function (appContext) {
  return {
   

    // breadcrumb
    breadcrumb: [
      { text: "dashboard", exact: true, to: "/" },
      {
        text: "quantity_reservation",
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
        text: "rejected",
        icon: "fa-ban",
        isSelected: 0,
        key: "rejected",
      },
      {
        text: "in_process",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "in process",
      },
      {
        text: "not_possible",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "not possible",
      },
      {
        text: "order_made",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "order made",
      },
      {
        text: "order_received",
        icon: "fa-times-circle",
        isSelected: 0,
        key: "order received",
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
    ]
  };
}
