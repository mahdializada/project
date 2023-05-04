import Icons from "./menuIcons";

export default {
	text: "File Management Systems",
	key: "file_management_system",
	icon: Icons.personal_file_management,
	items: [
		{
			text: "file_management",
			key: "file_management",
			link: "/file-management-system",
			exact: true,
			scope: "advv",
			icon: Icons.my_drive,
		},
	],
};
