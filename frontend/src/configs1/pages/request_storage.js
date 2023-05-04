import Icons from "~/configs/menus/menuIcons";
export default function (appContext) {
  return {
    // breadcrumb
    breadcrumb: [
      
			{
				text: "personal_file_management",
				disabled: true,
				to: "",
				icon: Icons.personal_file_management,
			},
      {
        text: "storage_requests",
        disabled: true,
        to: "",
        icon: Icons.storage_requests,
      },
    ],
  };
}
