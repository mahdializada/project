<template>
	<div
		class="file__container elevation-1"
		:class="{
			selected__file: file.isSelected,
			'hide-details pa-0 mt-0 d-flex': hideDetails,
		}"
		tabindex="0"
		ref="file"
		@click.stop="onFileClicked"
		@dblclick.stop.prevent="onFileDoubleClicked"
		@contextmenu.stop="onRightClick"
	>
		<div
			v-if="parent_id == null && isShare && file.seen == false"
			class="error rounded-lg white--text file-new-indicator"
		></div>
		<v-btn
			v-if="!hideDetails && showFavorite"
			v-show="!trash"
			class="favorite__icon"
			icon
			text
			@click.stop="toggleFileFavorite"
			@mouseover="favoriteIconMouseHover"
			@mouseleave="favoriteIconMouseLeave"
		>
			<v-icon v-if="file.favorites" color="error">{{ favoriteIcon }}</v-icon>
			<v-icon v-else color="error" class="inactive__favorite__icon">
				{{ noneFavoriteIcon }}
			</v-icon>
		</v-btn>
		<div v-if="!hideDetails" class="file__checkbox">
			<v-checkbox :value="file.isSelected" hide-details />
		</div>
		<div v-if="!hideDetails" class="bg-light-gray">
			<div
				v-if="file.thumbnail"
				class="file_thumbnail"
				:style="`background-image: url('${file.thumbnail}')`"
			></div>
			<div
				v-else
				:style="`
            background-size: 64px !important;
            background-image: url('/file-managment/${getIcon(
							file.extension
						)}.svg')
            `"
				class="file_thumbnail"
			></div>
		</div>

		<div
			class="file__top d-flex align-center"
			:class="{
				'ps-0': hideDetails,
			}"
		>
			<v-checkbox
				v-if="hideDetails"
				:value="file.isSelected"
				hide-details
				class="mt-0 me-1"
			/>
			<div v-if="hideDetails" class="me-1">
				<div
					:style="`
            background-image: url('/file-managment/${getIcon(
							file.extension
						)}.svg')
            `"
					class="file_thumbnail_small"
				></div>
			</div>
			<div class="file__text" v-if="!renameFile">
				<div class="name" :title="file.name">
					<a
						@click.prevent.stop="onFileDoubleClicked"
						:href="`files\\${file.name}`"
						>{{ file.name }}</a
					>
				</div>
				<div class="modify darken__gray__color">
					<div class="me-1" :title="parseDate(file.updated_at)">
						{{ parseDate(file.updated_at) }}
					</div>
					●
					<div class="ms-1" :title="parseDate(file.updated_at, 'time')">
						{{ parseDate(file.updated_at, "time") }}
					</div>
				</div>
			</div>
			<div v-if="renameFile" class="mx-1 w-full">
				<v-tooltip v-model="fileNameErrorMessage" bottom color="error">
					<template v-slot:activator="{}">
						<v-form @submit.prevent="onFileRenameSubmit">
							<v-text-field
								class="customStyle"
								@click.stop
								:disabled="loading"
								outlined
								hide-details
								dense
								autofocus
								:error="fileNameErrorMessage"
								v-model.trim="renameFileName"
								@blur.stop="onFileRenameSubmit"
							>
							</v-text-field>
						</v-form>
					</template>
					<span>{{ $tr("invalid_file_name") }}</span>
				</v-tooltip>
			</div>
		</div>

		<div v-if="!renameFile" class="mx-1">
			<div v-if="!hideDetails" class="file__bottom d-flex align-center px-2">
				<div
					v-if="file.created_by && showCreatedBy"
					class="d-flex align-center darken__gray__color me-1"
				>
					<v-icon x-small color="#aaa"> fa fa-user </v-icon>
					<div class="ellipse__text ms-1">
						{{ file.created_by.firstname }} {{ file.created_by.lastname }}
					</div>
				</div>
				<div class="d-flex align-center darken__gray__color me-1">
					<v-icon x-small color="#aaa"> fa fa-file </v-icon>
					<div class="ellipse__text ms-1" :title="file.extension">
						{{ file.extension }}
					</div>
				</div>
				<div class="d-flex align-center darken__gray__color me-1">
					<v-icon x-small color="#aaa"> fa fa-hdd </v-icon>
					<div class="ellipse__text ms-1" :title="getFileSize(file.size)">
						{{ getFileSize(file.size) }}
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapMutations } from "vuex";
import moment from "moment-timezone";

export default {
	props: {
		showCreatedBy: false,
		hideDetails: false,
		isSelected: false,
		isShare: false,
		actionUrl: null,
		parent_id: null,
		file: {
			type: Object,
			default: () => ({}),
		},
		renameFileProps: {
			type: Boolean,
			default: false,
		},
		trash: {
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
			noneFavoriteIcon: "mdi-star-outline",
			favoriteIcon: "mdi-star",
			renameFile: this.renameFileProps,
			renameFileName: "",
			loading: false,
			fileNameErrorMessage: false,
			filesThumbnails: {
				png: "image",
				jpg: "image",
				jpeg: "image",
				gif: "image",
				svg: "image",
				ico: "image",
				webp: "image",

				aaf: "after-effect",
				aep: "after-effect",
				aet: "after-effect",
				aepx: "after-effect",

				psd: "photoshop",
				psdc: "photoshop",
				psb: "photoshop",

				ai: "illustrator",
				eps: "illustrator",
				ait: "illustrator",
				svgz: "illustrator",

				indd: "indesign",
				indl: "indesign",
				indt: "indesign",
				indb: "indesign",
				inx: "indesign",
				idml: "indesign",

				prel: "premier",
				prproj: "premier",

				pptx: "powerpoint",
				pptm: "powerpoint",
				ppt: "powerpoint",
				potx: "powerpoint",
				potm: "powerpoint",
				pot: "powerpoint",
				ppsx: "powerpoint",
				ppsm: "powerpoint",
				pps: "powerpoint",
				ppam: "powerpoint",
				ppa: "powerpoint",

				adp: "access",
				adn: "access",
				accdb: "access",
				laccdb: "access",
				accdw: "access",
				accdc: "access",
				accda: "access",
				accdr: "access",
				accdt: "access",
				mdb: "access",
				mda: "access",
				mdw: "access",
				mdf: "access",
				mde: "access",
				accde: "access",
				mam: "access",
				mad: "access",
				maq: "access",
				mar: "access",
				mat: "access",
				maf: "access",

				xlsx: "excel",
				xlsm: "excel",
				xltx: "excel",
				xltm: "excel",
				xls: "excel",
				xlt: "excel",
				xls: "excel",
				xla: "excel",
				xlw: "excel",
				xlr: "excel",

				doc: "word",
				docm: "word",
				docx: "word",
				dot: "word",
				dotx: "word",

				mp3: "audio",
				wav: "audio",

				mp4: "video",
				mkv: "video",
				ogg: "video",
				mov: "video",
				webm: "video",

				pdf: "pdf",
				zip: "compressed",
				rar: "compressed",
			},
		};
	},

	watch: {
		renameFileProps: function (value) {
			this.renameFile = value;
		},
	},

	methods: {
		removeExtension(filename) {
			var lastDotPosition = filename.lastIndexOf(".");
			if (lastDotPosition === -1) return filename;
			else return filename.substr(0, lastDotPosition);
		},

		toggleNameInput() {
			const { name } = this.file;
			this.renameFile = true;
			this.renameFileName = this.removeExtension(name);
		},

		// fired when create folder blured for unfocused
		onFileRenameSubmit() {
			if (this.loading) return;
			const fileName = this.renameFileName;
			if (fileName) {
				if (this.isInvalidFileName(fileName)) {
					this.fileNameErrorMessage = true;
					return;
				}

				this.renameFileAction(fileName);
				return;
			}
			this.renameFile = false;
		},

		// validate folder name
		isInvalidFileName(value) {
			let folderRegex =
				/[<>:"\/\\|?*\x00-\x1F]|^(?:aux|con|clock\$|nul|prn|com[1-9]|lpt[1-9])$/i;
			return folderRegex.test(value);
		},

		// create or rename folder in backend
		async renameFileAction(fileName) {
			try {
				this.loading = true;
				let body = {};
				if (this.file && this.file?.id) {
					body.file_id = this.file.id;
					const { name } = this.file;
					let _name = this.removeExtension(name);
					if (fileName.toLowerCase() == _name.toLowerCase()) {
						this.loading = false;
						this.renameFile = false;
						return;
					}
				}
				body.file_name = fileName + `.${this.file.extension}`;
				body.parent_id = this.parent_id;
				const response = await this.$axios.post(this.actionUrl, body);
				if (response.data.result) {
					this.renameFile = false;
					this.$emit("itemRenamed", response.data.file);
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
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr(something_went_wrong));

				}
			}
			this.loading = false;
		},

		toggleFileFavorite() {
			const { id, name, type, favorites } = this.file;
			this.$emit("onFavorites", [{ id, name, type, favorites }]);
		},

		favoriteIconMouseHover() {
			this.noneFavoriteIcon = this.file.favorites
				? "mdi-star-outline"
				: "mdi-star";
			this.favoriteIcon = this.noneFavoriteIcon;
		},

		favoriteIconMouseLeave() {
			this.noneFavoriteIcon = this.file.favorites
				? "mdi-star"
				: "mdi-star-outline";
			this.favoriteIcon = this.noneFavoriteIcon;
		},

		onRightClick(event) {
			event.preventDefault();
			this.$emit("onRightClick", { event, item: this.file });
		},

		onFileDoubleClicked() {
			this.$emit("dbClick", this.file);
		},
		onFileClicked() {
			this.file.isSelected = !this.file.isSelected;
			this.$emit("onClicked", this.file.isSelected);
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

		getIcon(extension) {
			const icon = this.filesThumbnails[extension];
			if (icon) return icon;
			return "unknown";
		},

		getFileSize(totalSize) {
			let sizes = ["Bytes", "KB", "MB", "GB", "TB"];
			if (totalSize == 0) return this.$tr("0_byte");
			let i = parseInt(Math.floor(Math.log(totalSize) / Math.log(1024)));
			return (
				Math.round(totalSize / Math.pow(1024, i), 2) + " " + this.$tr(sizes[i])
			);
		},
	},
};
</script>
<style>
.customStyle .v-input__slot {
	min-height: 32px !important;
}
</style>

<style lang="scss" scoped>
.v-application--is-rtl .file__checkbox .v-input--selection-controls {
	position: absolute;
	left: 0;
	right: unset;
}

.primary__color {
	fill: var(--v-primary-base);
}

.file__checkbox {
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
.inactive__favorite__icon {
	opacity: 0;
	transition: opacity ease-in-out 0.3s;
}
.darken__gray__color {
	color: #aaa;
	font-size: 12px;
}

.file__container:hover {
	transform: scale(1);
	.file__checkbox {
		opacity: 1 !important;
	}
	.inactive__favorite__icon {
		opacity: 1 !important;
	}
}

.selected__file {
	border: 1px solid var(--v-primary-base) !important;
	outline: 1px solid var(--v-primary-base) !important;
	.file__checkbox {
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

.file__container {
	border: 1px solid transparent;
	outline: 1px solid transparent;
	background: var(--v-background-base);
	width: 280px;
	height: 205px;
	border-radius: 8px;
	margin: 8px;
	cursor: pointer;
	transition: border 0.3s, outline 0.3s;
	transform: scale(1);
	padding-bottom: 16px;
	.file_thumbnail_small {
		height: 30px;
		width: 34px;
		min-width: 34px;
		min-height: 30px;
		background-size: contain !important;
		background-position: center;
	}
	.file_thumbnail {
		height: 120px;
		width: 280px;
		min-width: 280px;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
		min-height: 120px;
		background-size: auto !important;
		background-position: center;
	}
	.favorite__icon {
		position: absolute;
	}
	.ellipse__text {
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		width: calc(318px / 3);
	}
	.file__top {
		margin-top: 5px;
		padding: 0 22px;
		.file__icon {
			g#Core {
				fill: var(--v-primary-base);
			}
		}
		.file__text {
			.name {
				font-weight: 500;
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
				transition: all 0.3s;
				width: 230px;
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

	.file__bottom {
		padding: 0 8px;
		padding-top: 5px;
		border-top: 1px solid #ddd;
		margin-top: 5px;
	}
}
.theme--dark .file__bottom {
	border-top: 1px solid #333 !important;
}
</style>
