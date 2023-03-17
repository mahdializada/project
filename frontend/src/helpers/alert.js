export default {
	async removeDialogAlert(
		appContext,
		confirmHandler,
		isForceDelete = "",
		confirmBtnText = "yes_delete"
	) {
		await appContext
			.$swal({
				icon: "warning",
				title: appContext.$tr("are_you_sure"),
				text: appContext.$tr(isForceDelete),
				showCancelButton: true,
				confirmButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.error
					: appContext.$vuetify.theme.themes.light.error,
				cancelButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.primary
					: appContext.$vuetify.theme.themes.light.primary,
				confirmButtonText: appContext.$tr(confirmBtnText),
				cancelButtonText: appContext.$tr("cancel"),
			})
			.then((result) => {
				if (result.value) {
					confirmHandler();
				}
			});
	},
	async removeDialogAlertWithInput(
		appContext,
		confirmHandler,
		isForceDelete = "",
		confirmBtnText = "yes_delete"
	) {
		const inputValue = await appContext
			.$swal({
				icon: "warning",
				title: appContext.$tr("are_you_sure"),
				input: "checkbox",
				inputValue: 0,
				inputPlaceholder: "<p>Delete Label with Category!</p> ",
				footer:
					'<p style="color:red">If delete with category all realted labels will delete?</p>',
				text: appContext.$tr(isForceDelete),

				showCancelButton: true,
				confirmButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.error
					: appContext.$vuetify.theme.themes.light.error,
				cancelButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.primary
					: appContext.$vuetify.theme.themes.light.primary,
				confirmButtonText: appContext.$tr(confirmBtnText),
				cancelButtonText: appContext.$tr("cancel"),
			})
			.then((result) => {
				confirmHandler(result.value);
			});
	},

	async confirmAlert(
		appContext,
		confirmHandler,
		isForceDelete = "",
		swalType = "warning",
		text = "are_you_sure"
	) {
		await appContext
			.$swal({
				icon: swalType,
				title: appContext.$tr(text),
				text: appContext.$tr(isForceDelete),
				showCancelButton: true,
				confirmButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.error
					: appContext.$vuetify.theme.themes.light.error,
				cancelButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.primary
					: appContext.$vuetify.theme.themes.light.primary,
				confirmButtonText: appContext.$tr("yes"),
				cancelButtonText: appContext.$tr("cancel"),
			})
			.then((result) => {
				if (result.value) {
					confirmHandler();
				}
			});
	},
	async removeLimitAlert(
		appContext,
		text = "cant_delete_more_item",
		title = "cant_delete_more_item"
	) {
		await appContext.$swal({
			icon: "warning",
			title: appContext.$tr(title),
			text: appContext.$tr(text),
			confirmButtonColor: "red",
			confirmButtonText: appContext.$tr("close"),
		});
	},

	async blockChangeStatusAlert(
		appContext,
		text = "cant_delete_more_item",
		title = "cant_delete_more_item"
	) {
		return await appContext.$swal({
			icon: "warning",
			title: appContext.$tr(title),
			text: appContext.$tr(text),
			confirmButtonColor: "red",
			confirmButtonText: appContext.$tr("close"),
		});
	},

	async customAlert(appContext, text, title) {
		await appContext.$swal({
			icon: "warning",
			title: appContext.$tr(title),
			text: appContext.$tr(text),
			confirmButtonColor: "red",
			confirmButtonText: appContext.$tr("close"),
		});
	},

	async blockLimitAlert(appContext) {
		await appContext.$swal({
			icon: "warning",
			title: appContext.$tr("item_limited", $tr("block")),
			text: appContext.$tr("cant_block_more_than_one_item"),
			confirmButtonColor: "red",
			confirmButtonText: appContext.$tr("close"),
		});
	},

	async updateLimitAlert(appContext) {
		await appContext.$swal({
			icon: "warning",
			title: appContext.$tr("item_limited", $tr("update")),
			text: appContext.$tr("cant_update_more_than_one_item"),
			confirmButtonColor: "red",
			confirmButtonText: appContext.$tr("close"),
		});
	},

	// shows if user does not select any status item
	async chooseStatus(appContext) {
		await appContext.$swal({
			icon: "warning",
			title: appContext.$tr("status_not_selected"),
			text: appContext.$tr("please_status_first"),
			confirmButtonColor: "red",
			confirmButtonText: appContext.$tr("close"),
		});
	},
	// shows if user tries to force delete an item that is not deletable
	async cannotForceDeleteItem(appContext) {
		await appContext.$swal({
			icon: "warning",
			title: appContext.$tr("cant_force_delete_an_item"),
			confirmButtonColor: "red",
			confirmButtonText: appContext.$tr("close"),
		});
	},
	// shows if user does not select any status item
	async selectItem(appContext) {
		await appContext.$swal({
			icon: "warning",
			title: appContext.$tr("item_not_selected"),
			text: appContext.$tr("please_select_an_item_first"),
			confirmButtonColor: "red",
			confirmButtonText: appContext.$tr("close"),
		});
	},

	async restoreDialogAlert(appContext, confirmHandler) {
		await appContext
			.$swal({
				icon: "info",
				title: appContext.$tr("are_you_sure"),
				text: appContext.$tr("swal_restore_text"),
				showCancelButton: true,
				confirmButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.error
					: appContext.$vuetify.theme.themes.light.error,
				cancelButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.primary
					: appContext.$vuetify.theme.themes.light.primary,
				confirmButtonText: appContext.$tr("yes_restore"),
				cancelButtonText: appContext.$tr("cancel"),
			})
			.then((result) => {
				if (result.value) {
					confirmHandler();
				}
			});
	},

	async blockDialogAlert(appContext, confirmHandler) {
		await appContext
			.$swal({
				icon: "info",
				title: appContext.$tr("are_you_sure"),
				text: appContext.$tr("do_you_want_to_block_selected"),
				showCancelButton: true,
				confirmButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.error
					: appContext.$vuetify.theme.themes.light.error,
				cancelButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.primary
					: appContext.$vuetify.theme.themes.light.primary,
				confirmButtonText: appContext.$tr("yes_block"),
				cancelButtonText: appContext.$tr("cancel"),
			})
			.then((result) => {
				if (result.value) {
					confirmHandler();
				}
			});
	},

	async logoutDialogAlert(appContext, confirmHandler) {
		await appContext
			.$swal({
				icon: "info",
				title: appContext.$tr("are_you_sure"),
				text: appContext.$tr("exit_from_system"),
				showCancelButton: true,
				confirmButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.error
					: appContext.$vuetify.theme.themes.light.error,
				cancelButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.primary
					: appContext.$vuetify.theme.themes.light.primary,
				confirmButtonText: appContext.$tr("yes"),
				cancelButtonText: appContext.$tr("cancel"),
			})
			.then((result) => {
				if (result.value) {
					confirmHandler();
				}
			});
	},

	successAlert(appContext, title) {
		appContext.$swal({
			icon: "success",
			title: appContext.$tr(title),
			confirmButtonText: appContext.$tr("close"),
			confirmButtonColor: appContext.$vuetify.theme.themes.dark.primary,
		});
	},

	removedSuccessAlert(appContext, title) {
		appContext.$swal({
			icon: "success",
			title: appContext.$tr(title),
			confirmButtonText: appContext.$tr("close"),
		});
	},

	restoredSuccessAlert(appContext, title) {
		appContext.$swal({
			icon: "success",
			title: appContext.$tr(title),
			confirmButtonText: appContext.$tr("close"),
		});
	},

	blockedSuccessAlert(appContext, title) {
		appContext.$swal({
			icon: "success",
			title: appContext.$tr(title),
			confirmButtonText: appContext.$tr("close"),
		});
	},

	async shareRemoveAlert(
		appContext,
		title = "",
		text = "",
		confirmHandler,
		denyHandler,
		confirmBtnText = "delete_my_files",
		denyBtnText = "yes_delete",
		showDeny = true
	) {
		await appContext
			.$swal({
				icon: "warning",
				title: appContext.$tr(title),
				html: appContext.$tr(text),
				showDenyButton: showDeny,
				showCancelButton: true,
				confirmButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.primary
					: appContext.$vuetify.theme.themes.light.primary,
				cancelButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.info
					: appContext.$vuetify.theme.themes.light.info,
				denyButtonColor: appContext.$vuetify.theme.isDark
					? appContext.$vuetify.theme.themes.dark.error
					: appContext.$vuetify.theme.themes.light.error,
				confirmButtonText: appContext.$tr(confirmBtnText),
				denyButtonText: appContext.$tr(denyBtnText),
				cancelButtonText: appContext.$tr("cancel"),
			})
			.then((result) => {
				if (result.isConfirmed) {
					confirmHandler();
				} else if (result.isDenied) {
					denyHandler();
				}
			});
	},
};
