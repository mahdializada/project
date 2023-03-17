<template>
	<div
		class="folder__container elevation-1"
		:class="{
			selected__folder: folder.isSelected,
			'hide-details': hideDetails,
			'd-flex align-center': renameFolder,
			'folder-create-error': folderNameErrorMessage,
		}"
		tabindex="0"
		ref="folder"
		@click.stop="onFolderClicked"
		@dblclick.stop="onFolderDoubleClicked"
		@contextmenu.stop="onRightClick"
	>
		<div
			v-if="parent_id == null && isShare && folder.seen == false"
			class="error rounded-lg white--text file-new-indicator"
		></div>
		<span v-if="!renameFolder">
			<div v-if="!hideDetails" class="folder__checkbox">
				<v-checkbox :value="folder.isSelected" hide-details />
			</div>
		</span>
		<div :class="`folder__top d-flex align-center ${hideDetails && 'pa-0'}`">
			<v-checkbox
				v-if="hideDetails"
				:value="folder.isSelected"
				hide-details
				class="mt-0 me-1"
			/>
			<div
				v-if="showFavorite"
				class="folder__icon favorite__icon"
				@click.stop="toggleFileFavorite"
				@mouseover="favoriteFolderMouseHover"
				@mouseleave="favoriteFolderMouseLeave"
			>
				<svg
					xmlns="http://www.w3.org/2000/svg"
					width="40"
					height="35"
					viewBox="0 0 40 35"
				>
					<path
						fill="currentColor"
						class="primary--text"
						:style="`display: ${defaultIcon};`"
						id="folder-solid"
						d="M40,40.75v22.5A3.751,3.751,0,0,1,36.25,67H3.75A3.751,3.751,0,0,1,0,63.25V35.75A3.751,3.751,0,0,1,3.75,32h12.5l5,5h15A3.751,3.751,0,0,1,40,40.75Z"
						transform="translate(0 -32)"
					/>
					<g
						v-if="folder.favorites || emptyIcon == 'none'"
						:style="`display: ${favoriteIcon} `"
						id="folder_-_favorite_state"
						data-name="folder - favorite state"
						transform="translate(-48.211 -37.623)"
					>
						<path
							class="primary--text"
							fill="currentColor"
							id="folder-_favorite_state"
							data-name="folder- favorite state"
							d="M40,40.75v22.5A3.751,3.751,0,0,1,36.25,67H3.75A3.751,3.751,0,0,1,0,63.25V35.75A3.751,3.751,0,0,1,3.75,32h12.5l5,5h15A3.751,3.751,0,0,1,40,40.75Z"
							transform="translate(48.211 5.623)"
						/>
						<path
							id="Icon_ionic-ios-star"
							data-name="Icon ionic-ios-star"
							d="M21.053,9.657H14.635l-1.95-5.82a.707.707,0,0,0-1.326,0l-1.95,5.82H2.948a.7.7,0,0,0-.7.7.513.513,0,0,0,.013.118.671.671,0,0,0,.292.493L7.83,14.683,5.806,20.568a.7.7,0,0,0,.24.785.675.675,0,0,0,.393.17.855.855,0,0,0,.436-.157L12.022,17.7l5.148,3.669a.817.817,0,0,0,.436.157.627.627,0,0,0,.388-.17.691.691,0,0,0,.24-.785L16.21,14.683l5.231-3.752.127-.109a.732.732,0,0,0,.227-.467A.739.739,0,0,0,21.053,9.657Z"
							transform="translate(56.189 43.812)"
							fill="#fff"
						/>
					</g>
					<g
						class="inactive__favorite__icon"
						id="Icon_ionic-ios-star"
						data-name="Icon ionic-ios-star"
						transform="translate(7.978 6.189)"
						fill="none"
					>
						<path
							d="M21.053,9.657H14.635l-1.95-5.82a.707.707,0,0,0-1.326,0l-1.95,5.82H2.948a.7.7,0,0,0-.7.7.513.513,0,0,0,.013.118.671.671,0,0,0,.292.493L7.83,14.683,5.806,20.568a.7.7,0,0,0,.24.785.675.675,0,0,0,.393.17.855.855,0,0,0,.436-.157L12.022,17.7l5.148,3.669a.817.817,0,0,0,.436.157.627.627,0,0,0,.388-.17.691.691,0,0,0,.24-.785L16.21,14.683l5.231-3.752.127-.109a.732.732,0,0,0,.227-.467A.739.739,0,0,0,21.053,9.657Z"
							stroke="none"
						/>
						<path
							d="M 12.02221298217773 5.005878448486328 L 10.12857723236084 10.65714168548584 L 3.853195190429688 10.65714168548584 L 9.019626617431641 14.29800128936768 L 7.055110931396484 20.00952911376953 L 12.022216796875 16.46941184997559 L 16.98354721069336 20.00541305541992 L 15.02254676818848 14.30415153503418 L 20.10715103149414 10.65714168548584 L 13.91585636138916 10.65714168548584 L 12.02221298217773 5.005878448486328 M 12.022216796875 3.375001907348633 C 12.3232364654541 3.375001907348633 12.58935642242432 3.553871154785156 12.68532657623291 3.837430953979492 L 14.63541698455811 9.65714168548584 L 21.05278778076172 9.65714168548584 C 21.43669700622559 9.65714168548584 21.79443740844727 9.97124195098877 21.79443740844727 10.35515117645264 C 21.79443740844727 10.53402137756348 21.68100738525391 10.69980144500732 21.56757736206055 10.8219518661499 L 21.44106674194336 10.93101119995117 L 16.21030616760254 14.68285179138184 L 18.23455619812012 20.56799125671387 C 18.33489608764648 20.85592269897461 18.23891639709473 21.1700325012207 17.99460601806641 21.35326194763184 C 17.87245750427246 21.44924163818359 17.75466728210449 21.52340126037598 17.60633659362793 21.52340126037598 C 17.46237564086914 21.52340126037598 17.292236328125 21.4536018371582 17.17007637023926 21.36635208129883 L 12.022216796875 17.69740104675293 L 6.874347686767578 21.36635208129883 C 6.752197265625 21.44924163818359 6.582056045532227 21.52340126037598 6.43809700012207 21.52340126037598 C 6.289766311645508 21.52340126037598 6.171976089477539 21.44487190246582 6.045455932617188 21.35326194763184 C 5.805517196655273 21.1700325012207 5.705177307128906 20.8515625 5.805517196655273 20.56799125671387 L 7.829757690429688 14.68285179138184 L 2.555376052856445 10.96592140197754 C 2.341617584228516 10.79577159881592 2.271816253662109 10.62563133239746 2.263086318969727 10.47294139862061 C 2.254356384277344 10.43804168701172 2.249996185302734 10.39442157745361 2.249996185302734 10.35515117645264 C 2.249996185302734 9.97124195098877 2.564105987548828 9.65714168548584 2.948017120361328 9.65714168548584 L 9.409016609191895 9.65714168548584 L 11.35909652709961 3.837430953979492 C 11.45507717132568 3.553871154785156 11.7211971282959 3.375001907348633 12.022216796875 3.375001907348633 Z"
							stroke="none"
							fill="#fff"
						/>
					</g>
				</svg>
			</div>
			<div v-else class="folder__icon favorite__icon">
				<svg
					class="primary--text"
					xmlns="http://www.w3.org/2000/svg"
					width="40"
					height="35"
					viewBox="0 0 40 35"
					fill="currentColor"
				>
					<path
						:style="`display: ${defaultIcon};`"
						id="folder-solid"
						d="M40,40.75v22.5A3.751,3.751,0,0,1,36.25,67H3.75A3.751,3.751,0,0,1,0,63.25V35.75A3.751,3.751,0,0,1,3.75,32h12.5l5,5h15A3.751,3.751,0,0,1,40,40.75Z"
						transform="translate(0 -32)"
					/>
				</svg>
			</div>
			<div v-if="!renameFolder" class="folder__text ms-1">
				<div class="name" :title="folder.name">
					<!-- <nuxt-link
            v-if="folder.is_protected"
            :to="`${$attrs.link}`"
            :customData="folder.favorites"
            v-slot="{}"
            custom
          >
            <a
              :href="`${$attrs.link}`"
              disabled="disabled"
              onclick="return false;"
              @click.stop="openFolder"
              @keypress.enter.stop="openFolder"
            >
              {{ folder.name }}
            </a>
          </nuxt-link> -->
					<a
						:to="`${$attrs.link}`"
						@click.stop="onFolderDoubleClicked"
						:customData="folder.favorites"
					>
						{{ folder.name }}
					</a>
				</div>
				<div class="modify darken__gray__color">
					<div class="me-1" :title="parseDate(folder.updated_at)">
						{{ parseDate(folder.updated_at) }}
					</div>
					●
					<div class="ms-1" :title="parseDate(folder.updated_at, 'time')">
						{{ parseDate(folder.updated_at, "time") }}
					</div>
				</div>
			</div>
			<div v-if="renameFolder" class="ms-1 d-flex align-center">
				<v-tooltip v-model="folderNameErrorMessage" bottom color="error">
					<template v-slot:activator="{}">
						<v-form @submit.prevent="onCreateFolderSubmit">
							<v-text-field
								class="customStyle"
								@click.stop
								:disabled="loading"
								outlined
								hide-details
								dense
								autofocus
								:error="folderNameErrorMessage"
								v-model.trim="renameFolderName"
								@blur.stop="onCreateFolderSubmit"
							>
							</v-text-field>
						</v-form>
					</template>
					<span>{{ $tr(folderErrorText) }}</span>
				</v-tooltip>
			</div>
		</div>
		<div v-if="!renameFolder">
			<div v-if="!hideDetails" class="folder__bottom d-flex align-center">
				<div
					v-if="folder.created_by && showCreatedBy"
					class="d-flex align-center darken__gray__color me-1"
				>
					<v-icon x-small color="#aaa"> fa fa-user </v-icon>
					<div class="ellipse__text ms-1">
						{{ folder.created_by.firstname }} {{ folder.created_by.lastname }}
					</div>
				</div>
				<div class="d-flex align-center darken__gray__color me-1">
					<v-icon x-small color="#aaa"> fa fa-file </v-icon>
					<div
						v-if="folder.info"
						class="ellipse__text ms-1"
						:title="folder.info.files + ' ' + $tr('files')"
					>
						{{ folder.info.files }} {{ $tr("files") }}
					</div>
				</div>
				<div class="d-flex align-center darken__gray__color me-1">
					<v-icon x-small color="#aaa"> fa fa-hdd </v-icon>
					<div
						v-if="folder.info"
						class="ellipse__text ms-1"
						:title="getFileSize(folder.info.size)"
					>
						{{ getFileSize(folder.info.size) }}
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapMutations } from "vuex";
import moment from "moment-timezone";
import HandleException from "~/helpers/handle-exception";

export default {
	props: {
		showCreatedBy: false,
		hideDetails: false,
		isSelected: false,
		isShare: false,
		index: null,
		parent_id: null,
		actionUrl: null,
		folder: {
			type: Object,
			default: () => ({}),
		},
		renameFolderProps: {
			type: Boolean,
			default: false,
		},
		showFavorite: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			favoriteIcon: "block",
			emptyIcon: "block",
			defaultIcon: "block",

			renameFolder: this.renameFolderProps,
			isActive: false,
			renameFolderName: null,
			folderNameErrorMessage: null,
			folderErrorText: "invalid_folder_name",
			loading: false,
		};
	},

	mounted() {
		const { id } = this.folder;
		this.$root.$on(id, this.showRenameInput);
	},

	watch: {
		renameFolderName: function (_) {
			this.folderNameErrorMessage = null;
		},
	},

	methods: {
		...mapMutations("files", ["toggleCreateFolder"]),

		openFolder() {
			this.$emit("openFolder", { item: this.folder });
		},

		toggleNameInput() {
			const { name } = this.folder;
			this.renameFolder = true;
			this.renameFolderName = name;
		},

		toggleFileFavorite() {
			const { id, name, type, favorites } = this.folder;
			this.$emit("onFavorites", [{ id, name, type, favorites }]);
		},

		favoriteFolderMouseHover() {
			if (this.folder.favorites) {
				this.favoriteIcon = "none";
				this.defaultIcon = "block";
				this.emptyIcon = "block";
			} else {
				this.favoriteIcon = "block";
				this.defaultIcon = "none";
				this.emptyIcon = "none";
			}
		},
		favoriteFolderMouseLeave() {
			if (this.folder.favorites) {
				this.favoriteIcon = "block";
				this.defaultIcon = "none";
				this.emptyIcon = "none";
			} else {
				this.favoriteIcon = "none";
				this.defaultIcon = "block";
				this.emptyIcon = "none";
			}
		},

		// fired when create folder blured for unfocused
		onCreateFolderSubmit() {
			if (this.loading) return;
			const folderName = this.renameFolderName;
			if (folderName) {
				if (this.isInvalidFolderName(folderName)) {
					this.folderNameErrorMessage = true;
					this.folderErrorText = "invalid_folder_name";
					return;
				}
				if (folderName.trim().length > 250) {
					this.folderNameErrorMessage = true;
					this.folderErrorText = "folder_length_error_text";
					return;
				}
				this.createOrNameFolder(folderName);
				return;
			}
			this.renameFolder = false;
			this.toggleCreateFolder(false);
		},

		// validate folder name
		isInvalidFolderName(value) {
			let folderRegex =
				/[<>:"\/\\|?*\x00-\x1F]|^(?:aux|con|clock\$|nul|prn|com[1-9]|lpt[1-9])$/i;
			return folderRegex.test(value);
		},

		// create or rename folder in backend
		async createOrNameFolder(folderName) {
			try {
				this.loading = true;
				let body = {};
				if (this.folder && this.folder?.id) {
					body.folder_id = this.folder.id;
					const { name } = this.folder;
					if (folderName.toLowerCase() == name.toLowerCase()) {
						this.loading = false;
						this.renameFolder = false;
						this.toggleCreateFolder(false);
						return;
					}
				}
				body.folder_name = folderName;
				body.parent_id = this.parent_id;
				const { data } = await this.$axios.post(this.actionUrl, body);
				if (data.result) {
					this.toggleCreateFolder(false);
					this.$emit("folderCreated", data.folder);
				} else {
					// this.$toastr.e(this.$tr("unknown_error_try_again"));
			this.$toasterNA("red", this.$tr("unknown_error_try_again"));

				}
			} catch ({ response }) {
				if (response.status == 401) {
					let errorText = "unauthorized_to_do_this_operation";
					// this.$toastr.e(this.$tr(errorText));
			this.$toasterNA("red", this.$tr(errorText));

				} else {
					HandleException.handleApiException(this, { response });
				}
				this.toggleCreateFolder(false);
			}
			this.renameFolder = false;
			this.loading = false;
		},

		getFileSize(totalSize) {
			let sizes = ["Bytes", "KB", "MB", "GB", "TB"];
			if (totalSize == 0) return this.$tr("0_byte");
			let i = parseInt(Math.floor(Math.log(totalSize) / Math.log(1024)));
			return (
				Math.round(totalSize / Math.pow(1024, i), 2) + " " + this.$tr(sizes[i])
			);
		},

		onRightClick(event) {
			event.preventDefault();
			this.$emit("onRightClick", {
				event,
				item: this.folder,
				index: this.index,
			});
		},

		onFolderDoubleClicked() {
			// if (this.folder && this.folder.is_protected) {
			//   this.openFolder();
			//   return;
			// }
			if (this.$attrs.link) {
				this.folder.seen = true;
				this.$router.push(`${this.$attrs.link}`);
			}
		},

		onFolderClicked() {
			this.folder.isSelected = !this.folder.isSelected;
			// this.addOrRemoveToSelected({
			// 	item: this.folder,
			// 	selected: this.isActive,
			// });
			this.$emit("onClicked", this.folder.isSelected);
		},

		parseDate(timestamps, type = "date") {
			if (type == "time") {
				return moment(timestamps)
					.locale(this.$vuetify.lang.current)
					.format("h:mm: A");
			}
			return moment(timestamps)
				.locale(this.$vuetify.lang.current)
				.format("MMMM Do YYYY");
		},
	},
};
</script>

<style lang="scss" scoped>
.v-application--is-rtl .folder__checkbox .v-input--selection-controls {
	position: absolute;
	left: 0;
	right: unset;
}

.folder__checkbox {
	opacity: 0;
	transition: opacity ease-in-out 0.3s;
	.v-input--selection-controls {
		margin: 0;
		padding: 0;
		position: absolute;
		right: 0;
		top: 8px;
	}
}
.darken__gray__color {
	color: #aaa;
	font-size: 12px;
}

.folder__container:hover {
	transform: scale(1);
	.folder__checkbox {
		opacity: 1 !important;
	}

	.inactive__favorite__icon {
		opacity: 1 !important;
	}
}

.folder-create-error {
	border: 1px solid var(--v-error-base) !important;
	outline: 1px solid var(--v-error-base) !important;
	.folder__icon favorite__icon {
		g.core {
			fill: var(--v-error-base) !important;
		}
	}
}
.selected__folder {
	border: 1px solid var(--v-primary-base) !important;
	outline: 1px solid var(--v-primary-base) !important;
	.folder__checkbox {
		transition: unset !important;
		opacity: 1 !important;
	}
}

.hide-details {
	height: 55px !important;
	border: unset !important;
	outline: unset !important;
	box-shadow: unset !important;
}
.inactive__favorite__icon {
	opacity: 0;
	transition: opacity ease-in-out 0.3s;
}

.folder__container {
	border: 1px solid transparent;
	outline: 1px solid transparent;
	background: var(--v-background-base);
	width: 280px;
	height: 92px;
	border-radius: 8px;
	margin: 8px;
	cursor: pointer;
	transition: outline 0.3s, border 0.3s;
	transform: scale(1);
	padding: 12px 8px;

	.ellipse__text {
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		width: calc(240px / 3);
	}
	.folder__top {
		padding: 0 16px;
		.folder__icon favorite__icon {
			g.core {
				fill: var(--v-primary-base);
			}
		}
		.folder__text {
			.name {
				font-weight: 500;
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
				transition: all 0.3s;
				width: calc(280px - 118px);
				a {
					text-decoration: none;
					color: unset;
				}
				a:hover {
					text-decoration: underline;
				}
			}
			.modify {
				display: flex;
				align-items: center;
				div {
					white-space: nowrap;
				}
			}
		}
	}
	.ellipse__text {
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		width: calc(240px / 3);
	}
	.folder__top {
		padding: 0 16px;
		.folder__icon favorite__icon {
			display: flex;
			align-items: center;
			g.core {
				fill: var(--v-primary-base);
			}
		}
		.folder__text {
			.name {
				font-weight: 500;
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
				transition: all 0.3s;
				width: calc(300px - 118px);
				a {
					text-decoration: none;
					color: unset;
				}
				a:hover {
					text-decoration: underline;
				}
			}
			.modify {
				display: flex;
				align-items: center;
				div {
					white-space: nowrap;
				}
			}
		}
	}
	.folder__bottom {
		padding: 0 16px;
		padding-top: 5px;
		border-top: 1px solid #ddd;
		margin-top: 5px;
	}
}
.theme--dark .folder__bottom {
	border-top: 1px solid #333 !important;
}
</style>
