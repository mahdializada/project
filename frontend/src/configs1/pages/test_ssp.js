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
		],
		// breadcrumb
		breadcrumb: [
			{
				text: "dashboard",
				exact: true,
				to: "/",
			},
			{
				icon: "mdi-gesture-double-tap",
				text: "Actions",
				disabled: true,
				to: "/actions",
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
				text: "inprocess",
				icon: "fa-chalkboard-teacher",
				isSelected: 0,
				key: "inprocess",
			},
			{
				text: "archived",
				icon: "fa-archive",
				isSelected: 0,
				key: "archived",
			},
			{
				text: "cancelled",
				icon: "fa-ban",
				isSelected: 0,
				key: "cancelled",
			},
			{
				text: "done",
				icon: "fa-check-circle",
				isSelected: 0,
				key: "done",
			},
			{
				text: "failed",
				icon: "fa-ban",
				isSelected: 0,
				key: "failed",
			},
			{
				text: "removed",
				icon: "fa-trash",
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
				category_id: 1,
				sortable: false,
			},
			{
				text: "goals",
				value: "goals",
				select: false,
				id: 2,
				category_id: 1,
				sortable: false,
			},
			{
				text: "priority",
				value: "priority",
				select: false,
				id: 3,
				category_id: 1,
			},
			{
				text: "created_by",
				value: "created_by",
				select: false,
				id: 4,
				category_id: 1,
			},
			{
				text: "done_by",
				value: "done_by",
				select: false,
				id: 5,
				category_id: 1,
			},
			{
				text: "status",
				value: "status",
				select: false,
				id: 7,
				category_id: 1,
			},
			{
				text: "done_date",
				value: "done_date",
				select: false,
				id: 8,
				category_id: 1,
			},
			{
				text: "created at",
				value: "created_at",
				select: false,
				id: 9,
				category_id: 1,
			},
			{
				text: "updated_at",
				value: "updated_at",
				select: false,
				id: 10,
				category_id: 1,
			},
		],
	};
}
