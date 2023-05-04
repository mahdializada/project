export default function (appContext) {
  return {
    // Category
    
    // Steppers
    steppers: [
      {
        icon: "fa-info-circle",
        title: "general",
        slotName: "step1",
      },
      {
        icon: "mdi-list-status",
        title: "sub_system",
        slotName: "step2",
      },
      {
        icon: "fa-thumbs-up",
        title: "done",
        slotName: "step3",
      },
    ],
    // breadcrumb
    breadcrumb: [
      {
        text: "dashboard",
        exact: true,
        to: "/",
      },
      {
        text: "systems",
        disabled: true,
        to: "",
        icon: "mdi-cogs",
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
    ],
    // table headers
  
  };
}
