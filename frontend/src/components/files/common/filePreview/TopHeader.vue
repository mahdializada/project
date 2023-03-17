<template>
	<v-card
		elevation="2"
		class="pa-1 my-2 mx-3 rounded d-flex justify-space-between"
	>
		<div class="d-flex align-center">
			<v-btn @click="$emit('closeDialog')" class="me-1" icon text>
				<v-icon dense color="primary">mdi-arrow-left</v-icon>
			</v-btn>
			<v-skeleton-loader
				v-if="isLoading"
				type="text"
				width="250px"
				style="margin-top: 12px"
			></v-skeleton-loader>
			<span
				v-else
				class="font-weight-medium"
				style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden"
			>
				{{ fileData.name | getFilenameSubstr(40) }}
			</span>
		</div>
		<RightClickMenu
			@onCopy="$emit('on-copy')"
			@onShare="$emit('on-share')"
			@onMove="$emit('on-move')"
			@onProperty="$emit('on-property')"
			@onDelete="$emit('on-delete', false)"
			@onDownload="$emit('on-download')"
			@onFavorites="$emit('on-favorites')"
		/>
		<div class="ms-2" style="min-width: 138px">
			<v-btn
				class="me-1"
				@click="$emit('on-download')"
				icon
				text
				:loading="isDownloading"
			>
				<v-icon color="primary">mdi-download</v-icon>
			</v-btn>
			<v-btn
				v-if="showDelete"
				:loading="isDeleting"
				class="me-1"
				icon
				text
				@click="$emit('on-delete')"
			>
				<v-icon color="primary">mdi-delete</v-icon>
			</v-btn>

			<v-btn
				v-if="fileType !== 'library'"
				@click="onRightClick"
				color="primary"
				small
				text
				fab
			>
				<v-icon class="float-right">mdi-dots-vertical</v-icon>
			</v-btn>
		</div>
	</v-card>
</template>
<script>
import RightClickMenu from "~/components/files/common/RightClickMenu.vue";

export default {
	name: "top-header",
	components: { RightClickMenu },
	props: {
		isDeleting: Boolean,
		isLoading: Boolean,
		isFavorite: Boolean,
		isRoot: Boolean,
		fileData: Object,
		fileType: String,
		slugName: String,
	},
	data() {
		return {
			isDownloading: false,
		};
	},

	computed: {
		showDelete: function () {
			return (
				this.fileType !== "library" &&
				(this.slugName === "shared"
					? this.fileData.user_id === this.$auth.user.id || this.isRoot
					: true)
			);
		},
	},
	methods: {
		onRightClick(event) {
			event.preventDefault();
			const items =
				this.slugName === "shared"
					? [
							"download",
							"copy_to",
							this.fileData.user_id === this.$auth.user.id || this.isRoot
								? "delete"
								: "nothing",
							"properties",
					  ]
					: [
							"download",
							`${
								this.isFavorite ? "remove_from_favorites" : "add_to_favorites"
							}`,
							"share_to",
							"copy_to",
							"move_to",
							"move_to_trashed",
							"properties",
					  ];
			const options = {
				items: items,
				showMenu: true,
				positionX: event.clientX,
				positionY: event.clientY,
			};
			this.$root.$emit("toggleContextMenu", options);
		},
	},
};
</script>
