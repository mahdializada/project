import moment from "moment-timezone";

export default (context, inject) => {
	const formatDateTime = (date) => {
		return moment(date)
			.locale(context.$vuetify.lang.current)
			.format("YYYY-MM-DD h:mm: A");
	};

	const isInScope = (scope, checkall) => {
		let userScopes = context.$auth.$state.user.scopes;
		if (userScopes) {
			if (Array.isArray(scope)) {
				if (!checkall) {
					for (let sc of scope) {
						return userScopes.includes(sc);
					}
				} else {
					let res = true;
					for (let sc of scope) {
						return res && userScopes.includes(sc);
					}
				}
			} else if (typeof scope === "string") {
				return userScopes.includes(scope);
			}
		}
	};

	const tr = (phrase, ...params) => {
		let str = context.store.getters["translations/getTranslation"](phrase);
		for (let x = 1; x <= params.length; x++) {
			str = str.replace(`{${x}}`, params[x - 1]);
		}
		return str;
	};

	const isObjectEmpty = (obj) => {
		return obj &&
			Object.keys(obj).length === 0 &&
			Object.getPrototypeOf(obj) === Object.prototype
			? true
			: false;
	};

	const sortStatusTransformationData = (data) => {
		let statusTransformation = [];
		const len = data?.reasons?.length;
		for (let index = 0; index < len; index++) {
			statusTransformation.push({
				status: data.reasons[index].pivot.status,
				applied_by:
					data.changed_by[index].firstname +
					" " +
					data.changed_by[index].lastname,
				reason: data.reasons[index].title,
				date: moment(data.reasons[index].pivot.created_at)
					.locale(context.$vuetify.lang.current)
					.format("YYYY-MM-DD h:mm: A"),
			});
		}
		return statusTransformation;
	};
	let tabItems = [
		{
			text: "all",
			icon: "fa-table",
			isSelected: true,
			key: "all",
		},
		{
			text: "active",
			icon: "fa-thumbs-up",
			isSelected: false,
			key: "active",
		},
		{
			text: "inactive",
			icon: "fa-ban",
			isSelected: false,
			key: "inactive",
		},
		{
			text: "approved",
			icon: "fa-thumbs-up",
			isSelected: false,
			key: "approved",
		},
		{
			text: "rejected",
			icon: "fa-times",
			isSelected: 0,
			key: "rejected",
		},
		{
			text: "blocked",
			icon: "fa-times-circle",
			isSelected: false,
			key: "blocked",
		},
		{
			text: "pending",
			icon: "mdi mdi-pencil",
			isSelected: false,
			key: "pending",
		},

		{
			text: "assigned",
			icon: "fas fa-user-plus",
			isSelected: 0,
			key: "assigned",
		},
		{
			text: "not_assigned",
			icon: "fas fa-user-minus",
			isSelected: 0,
			key: "not assigned",
		},
		{
			text: "waiting",
			icon: "fas fa-spinner",
			isSelected: 0,
			key: "waiting",
		},
		{
			text: "in_sourcing",
			icon: "fas fa-tasks",
			isSelected: 0,
			key: "in sourcing",
		},
		{
			text: "in_hold",
			icon: "fas fa-pause",
			isSelected: 0,
			key: "in hold",
		},
		{
			text: "product_found",
			icon: "fas fa-check-double",
			isSelected: 0,
			key: "product found",
		},
		{
			text: "sourcing_failed",
			icon: "fas fa-exclamation-triangle",
			isSelected: 0,
			key: "sourcing failed",
		},
		{
			text: "cancelled",
			icon: "fa-times",
			isSelected: 0,
			key: "cancelled",
		},
		{
			text: "removed",
			icon: "fa-trash",
			isSelected: false,
			key: "removed",
		},
		{
			text: "personal",
			icon: "fa-thumbs-up",
			isSelected: 0,
			key: "personal",
		},
		{
			text: "share",
			icon: "fa-ban",
			isSelected: 0,
			key: "share",
		},
	];

	const getTabItems = (tabs) => {
		let result = [];
		tabItems.forEach((ele) => {
			if (tabs.includes(ele.key)) result.push(ele);
		});
		return result;
	};

	const fetchOptions = (
		tabKey = "all",
		filterData = {},
		options = {},
		searchContent = "",
		searchId = "",
	) => {
		let data = {
			key: tabKey,
		};

		if (!isObjectEmpty(filterData)) Object.assign(data, filterData);
		if (!isObjectEmpty(options)) Object.assign(data, options);
		if (searchContent != "") data.searchContent = searchContent;
		if (searchId != "") data.code = searchId;

		return data;
	};

	// Inject $hello(msg) in Vue, context and store.
	inject("getTabItems", getTabItems);
	inject("isInScope", isInScope);
	inject("tr", tr);
	inject("formatDateTime", formatDateTime);
	inject("isObjectEmpty", isObjectEmpty);
	inject("fetchOptions", fetchOptions);


	inject("sortStatusTransformationData", sortStatusTransformationData);
	// For Nuxt <= 2.12, also add 👇
	context.$getTabItems = getTabItems;
	context.$isInScope = isInScope;
	context.$fetchOptions = fetchOptions;
	context.$isObjectEmpty = isObjectEmpty;
	context.$sortStatusTransformationData = sortStatusTransformationData;
	context.$tr = tr;

	context.$formatDateTime = formatDateTime;
};
