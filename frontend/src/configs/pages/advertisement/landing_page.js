import menuIcons from "../../menus/menuIcons";

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
                text: "country_info_caps",
                value: 0,
                category_id: 2,
                selected: false,
            },
        ],

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
                text: "Landing Page",
                disabled: true,
                to: "",
                icon: menuIcons.landing_page
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
                text: "publish",
                icon: "fa-table",
                isSelected: 0,
                key: "current",
            },
            {
                text: "published",
                icon: "fa-table",
                isSelected: 0,
                key: "current",
            },

            {
                text: "removed",
                icon: "fa-times-circle",
                isSelected: 0,
                key: "removed",
            },
        ],
        // table headers
        headers: [
            {
                text: "id",
                value: "code",
                select: false,
                id: 1,
                sortable: false,
                category_id: 1,
            },
            {
                text: "name",
                value: "name",
                select: false,
                id: 2,
                category_id: 1,
            },

            {
                text: "domain",
                value: "domain",
                select: false,
                id: 4,
                category_id: 1,
            },
            {
                text: "countries",
                value: "countries",
                select: false,
                id: 5,
                category_id: 1,
            },

            {
                text: "companies",
                value: "companies",
                select: false,
                id: 9,
                category_id: 1,
            },
            {
                text: "created_at",
                value: "created_at",
                select: false,
                id: 11,
                category_id: 1,
            },
        ],
    };
}
