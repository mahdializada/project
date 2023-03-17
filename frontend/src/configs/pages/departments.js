import Icons from "~/configs/menus/menuIcons"

export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      
      {
        text: "master_management_system",
        disabled: true,
        to: "",
        icon : Icons.master_management_system
      },
      {
        text: "departments",
        disabled: true,
        to: "",
        icon: Icons.departments,
      },
    ],
    steppers: [
      {
        icon: "fa-info-circle",
        title: "general",
        slotName: "general",
      },
      {
        icon: "fa-lock",
        title: "department",
        slotName: "department",
      },
      {
        icon: "fa-comment-dots",
        title: "remarks",
        slotName: "remark",
      },
      {
        icon: "fa-thumbs-up",
        title: "done",
        slotName: "done",
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
  };
}
