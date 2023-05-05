<template>
	<v-slide-x-transition>
		<v-alert
			:color="item.type"
			:colored-border="item.outline"
			border="left"
			elevation="24"
			class="nasimToaster py-1"
			:style="item.outline ? 'background-color: white !important' : ''">
			<v-row align="center">
				<v-avatar size="60" class="ms-3 d-flex align-center justify-center">
					<v-avatar size="48" color="blue">
						<lottie-player
							v-if="item.type == 'green'"
							src="/assets/toasterSuccess.json"
							background="transparent"
							speed="1"
							style="width: 400px; height: 800px"
							loop
							autoplay>
						</lottie-player>

						<lottie-player
							v-if="item.type == 'red'"
							src="/assets/toasterError.json"
							background="transparent"
							speed="1"
							style="width: 400px; height: 800px"
							loop
							autoplay>
						</lottie-player>

						<lottie-player
							v-if="item.type == 'primary'"
							src="/assets/toasterInfo.json"
							background="transparent"
							speed="1"
							style="
								position: fixed;
								z-index: 100000;
								width: 75px;
								height: 800px;
							"
							loop
							autoplay>
						</lottie-player>

						<lottie-player
							v-if="item.type == 'orange'"
							src="/assets/toasterWarring.json"
							background="transparent"
							speed="1"
							style="
								position: fixed;
								z-index: 100000;
								width: 75px;
								height: 800px;
							"
							loop
							autoplay>
						</lottie-player>
					</v-avatar>
				</v-avatar>
				<v-col
					style="width: 120px"
					class="grow py-0 ps-1 text-caption"
					:class="item.outline ? 'black--text' : 'white--text'">
					{{ item.text }}
				</v-col>
				<v-col class="shrink py-0 px-0 me-2">
					<v-progress-circular
						:rotate="-90"
						:size="30"
						:value="item.value"
						:color="item.outline ? item.type : 'white'"
						class="d-flex align-center justify-center">
						<v-btn
							icon
							class="d-flex align-center justify-center"
							:color="item.outline ? item.type : 'white'"
							@click="close(item)">
							<v-icon>mdi-close</v-icon>
						</v-btn>
					</v-progress-circular>
				</v-col>
			</v-row>
		</v-alert>
	</v-slide-x-transition>
</template>

<script>
import { mapMutations } from "vuex";
export default {
	props: {
		item: {
			type: Object,
			default: {},
		},
	},
	data() {
		return {
			interval: {},
		};
	},
	methods: {
		...mapMutations("nas_toaster", ["setInterval", "setValue"]),

		close(item) {
			this.$emit("close", item);
		},
	},
	mounted() {
		this.interval = setInterval(() => {
			if (this.item.value === 100) {
				this.close(this.item);
				return this.setValue({ item: this.item, value: 0 });
			}
			this.setValue({ item: this.item, value: 10 });
		}, 500);
		this.setInterval({ item: this.item, value: this.interval });
	},
	beforeDestroy() {
		clearInterval(this.interval);
	},
};
</script>
<style>
.nasimToaster {
	transition: all 3s ease;
}
</style>
