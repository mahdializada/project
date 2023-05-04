export default function (appContext) {
  return {
    category: [
      {
        text: "all_caps",
        value: 0,
        category_id: 0,
        selected: true,
      },
      {
        text: "general_info_caps",
        value: 0,
        category_id: 1,
        selected: false,
      },
      {
        text: "user_info_caps",
        value: 0,
        category_id: 2,
        selected: false,
      },
      {
        text: "location_caps",
        value: 0,
        category_id: 3,
        selected: false,
      },
      {
        text: "datewise_caps",
        value: 0,
        category_id: 4,
        selected: false,
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
        icon: "mdi-account-outline",
        text: "Products",
        disabled: true,
        to: "/categories",
      },
    ],
     //product category breadcrumb
     CategoryBreadcrumb: [
      {
        text: "dashboard",
        exact: true,
        to: "/",
      },
      {
        icon: "mdi-account-outline",
        text: "Products Category",
        disabled: true,
        to: "/categories",
      },
    ],

     //product subcategory breadcrumb
     SubCategoryBreadcrumb: [
      {
        text: "dashboard",
        exact: true,
        to: "/",
      },
      {
        icon: "mdi-account-outline",
        text: "Products SubCategory",
        disabled: true,
        to: "/subcategories",
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

    // table headers
    headers: [
      {
        text: "id",
        value: "code",
        select: false,
        id: 1,
        category_id: 1,
        sortable: false,
      },
      {
        text: "icon",
        value: "icon",
        select: false,
        id: 2,
        category_id: 1,
        sortable: false,
      },
      {
        text: "name",
        value: "name",
        select: false,
        id: 3,
        category_id: 1,
      },
      {
        text: "description",
        value: "description",
        select: false,
        id: 4,
        category_id: 1,
      },
     
    ],

   
    
  };
}
