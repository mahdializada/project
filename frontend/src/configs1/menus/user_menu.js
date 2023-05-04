import Icons from "../menus/menuIcons";

export default {
	text: "Users Management Systems",
	key: "user_management_system",
	icon: Icons.user_management_system,
	items: [
		{
			text: "Users List",
			key: "user_list",
			link: "/users",
			exact: true,
			scope: "uv",
			icon: Icons.user_list,
		},
		{
			text: "Teams List",
			key: "team_list",
			link: "/users/teams",
			exact: true,
			scope: "tv",
			icon: Icons.team_list,
		},
		{
			text: "Roles List",
			key: "role_list",
			link: "/users/roles",
			exact: true,
			scope: "rv",
			icon: Icons.role_list,
		},
		{
			text: "Settings",
			key: "settings",
			subLink: "user-settings",
			scope: ["luv", "rnuv", "seuv"],
			icon: Icons.settings,
			items: [
				{
					text: "Status Event List",
					key: "status_event_list",
					link: "/status_management/status_event/user",
					exact: true,
					scope: "seuv",
					icon: Icons.status_event_list,
				},
				{
					text: "Reasons List",
					key: "reason_list",
					link: "/status_management/reasons/user",
					exact: true,
					scope: "rnuv",
					icon: Icons.reason_list,
				},
				{
					text: "Labels",
					key: "labels",
					link: "/labels/user",
					exact: true,
					scope: "luv",
					icon: Icons.labels,
				},
			],
		},
		{
			text: "Users Activity",
			key: "user_activity",
			link: "/logs/users",
			exact: true,
			scope: "uav",
			icon: Icons.activity,
		},
	],
};
