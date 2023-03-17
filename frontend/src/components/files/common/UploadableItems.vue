<template>
	<v-virtual-scroll height="400" item-height="72" :items="items">
		<template v-slot:default="{ item }">
			<div class="py-1 d-flex align-center my-1 w-full">
				<v-row class="w-full ma-0">
					<v-col cols="6" xs="6" sm="7" md="8" lg="8" xl="8" class="pa-0">
						<div class="d-flex align-center">
							<div class="me-1">
								<v-progress-circular
									v-if="item.status == 'in_progress'"
									:rotate="-96"
									:size="40"
									:width="3"
									:value="item.progress"
									color="primary"
								>
									<v-btn icon class="surface" color="primary">
										<v-icon> mdi-upload </v-icon>
									</v-btn>
								</v-progress-circular>

								<v-progress-circular
									v-else-if="item.status == 'completed'"
									:rotate="-96"
									:size="40"
									:width="3"
									:value="100"
									color="green"
								>
									<v-btn icon class="surface" color="green">
										<v-icon>mdi-check-bold</v-icon>
									</v-btn>
								</v-progress-circular>

								<v-progress-circular
									v-else-if="item.status == 'retry'"
									:rotate="-96"
									:size="40"
									:width="3"
									:value="100"
									color="error"
								>
									<v-btn icon class="surface" color="error">
										<v-icon>mdi-alert-circle-outline</v-icon>
									</v-btn>
								</v-progress-circular>
								<v-btn v-else icon class="surface" color="primary">
									<v-icon>mdi-upload </v-icon>
								</v-btn>
							</div>
							<div
								class="w-10-12"
								style="
									font-size: 1.1rem;
									white-space: nowrap;
									overflow: hidden;
									text-overflow: ellipsis;
								"
							>
								{{ item.name }}
							</div>
						</div>
					</v-col>
					<v-col cols="6" xs="6" sm="5" md="4" lg="4" xl="4" class="pa-0">
						<div class="d-flex align-center justify-end me-2">
							<div>
								<div v-if="item.showFormat && false">
									<v-select
										value="auto"
										@change="(value) => changeQualityInner(value, item.id)"
										v-if="isVideo(item.type)"
										class="me-2"
										style="width: 90px"
										:items="videoFormats"
										item-text="text"
										item-value="value"
										dense
										solo
										hide-details
									></v-select>
								</div>
							</div>
							<div class="me-1" style="font-size: 1.1rem; min-width: 60px">
								{{ getFileSize(item.size) }}
							</div>
							<div>
								<v-btn
									nuxt
									v-if="item.status == 'completed'"
									:to="`/files/personal/${
										item.response.data.is_drive ? 'drive' : 'shared'
									}/${
										item.response.data.parent == null
											? ''
											: item.response.data.parent.id
									}`"
									icon
									class="surface"
									color="primary"
								>
									<v-icon> mdi-magnify </v-icon>
								</v-btn>
								<v-btn
									v-else-if="item.status == 'retry'"
									@click="onRetry(item)"
									icon
									class="surface"
									color="error"
								>
									<v-icon> mdi-restart </v-icon>
								</v-btn>
								<v-btn
									small
									v-else
									@click="onCancel(item)"
									class="ma-1"
									text
									icon
									color="error"
								>
									<v-icon> mdi-close </v-icon>
								</v-btn>
							</div>
						</div>
					</v-col>
				</v-row>
			</div>
			<v-divider class="custom-devider"></v-divider>
		</template>
	</v-virtual-scroll>
</template>

<script>
import CloseBtn from "~/components/design/Dialog/CloseBtn.vue";
import Alert from "~/helpers/alert";
import { mapMutations } from "vuex";

export default {
	components: {
		CloseBtn,
	},

	props: {
		items: {
			type: Array,
			default: () => [],
		},
	},
	data() {
		return {
			videoFormats: [
				{
					value: "auto",
					text: this.$tr("auto"),
				},
				{
					value: "1080",
					text: "1080",
				},
				{
					value: "720",
					text: "720",
				},
				{
					value: "480",
					text: "480",
				},
				{
					value: "360",
					text: "360",
				},
				{
					value: "240",
					text: "240",
				},
			],
		};
	},

	methods: {
		...mapMutations("files", ["retry", "changeQuality"]),

		onCancel(item) {
			Alert.removeDialogAlert(
				this,
				() => {
					this.$emit("onCancel", item);
				},
				"",
				"yes_cancel"
			);
		},

		// onView(item) {
		// 	this.$emit("onView", item);
		// 	this.$router.push("/ldldld/adfk");
		// },

		getFileSize(totalSize) {
			let sizes = ["Bytes", "KB", "MB", "GB", "TB"];
			if (totalSize == 0) return "0 Byte";
			let i = parseInt(Math.floor(Math.log(totalSize) / Math.log(1024)));
			return Math.round(totalSize / Math.pow(1024, i), 2) + " " + sizes[i];
		},

		isVideo(type = "video/mp4") {
			const allowedFormats = [
				"video/mp4",
				"video/avi",
				"video/quicktime",
				"video/webm",
				"video/x-ms-wmv",
				"video/x-matroska",
				"video/ogg",
			];
			return allowedFormats.includes(type);
		},

		onRetry(item) {
			this.retry(item);
		},

		changeQualityInner(value, id) {
			this.changeQuality({ value, id });
		},
	},
};
</script>
