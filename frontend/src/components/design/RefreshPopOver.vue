<template>
	<div
		:class="`${position ? '' :'position-relative'} ${right ? 'right' : ''}`"
		v-click-outside="
			() => {
				clickOutside ? (show = false) : '';
			}
		"
		:style="`--indicator: ${indicator}px`"
	>
		<slot name="activator" :listeners="{ click: refreshCrm }"></slot>
		<transition :name="'fade'">
			<div
				:class="`pop-over pa-2 rounded white--text font-weight-medium  ${color}`"
				v-if="show"
				:style="`background: ${color}`"
			>
				<p v-if="canRefresh">
					{{ $tr("the_table_has_been_refreshed_at", "refreshTime") }}<br />
					{{ $tr("do_you_wnat_to_refresh_again") }}
				</p>
				<p v-else>
					{{ $tr("the_table_has_been_refreshed") }}<br />
					{{ $tr("please_try_again_later") }}
				</p>

				<div class="d-flex align-center justify-space-between">
					<div style="font-size: 12px; opacity: 0.6">
						<v-icon size="16" dark class="me-1">fa-redo</v-icon>
						<!-- {{ refreshTimeAgo == "Invalid date" ? "" : refreshTimeAgo }} -->
						2 hr
					</div>
					<v-btn
						elevation="2"
						@click="Refresh()"
						small
						style="font-size: 12px; color: #00bc81"
						v-if="canRefresh"
						class="py-2 px-3 font-weight-medium"
					>
						{{ $tr("refresh") }}
					</v-btn>
					<v-btn
						v-else
						@click="show = false"
						elevation="2"
						small
						:style="`font-size: 12px; color: #00bc81`"
						class="py-2 px-3 font-weight-medium"
					>
						{{ $tr("ok") }}
					</v-btn>
				</div>
				<div
					:class="`pop-over-after ${color}--text`"
					:style="`color: ${color}`"
				></div>
				<v-btn
					fab
					dark
					x-small
					text
					elevation="0"
					class="pop-over-close"
					@click="show = false"
				>
					<v-icon dark> mdi-close </v-icon>
				</v-btn>
			</div>
		</transition>
	</div>
</template>
<script>
import RefreshPercentage from "../advertisement/RefreshPercentage.vue";
import moment from "moment";
export default {
	components: {
		RefreshPercentage,
	},
	props: {
		color: {
			type: String,

			default: "primary",
		},

		clickOutside: {
			type: Boolean,
			default: true,
		},
		icon: {
			type: String,
			default: "mdi-alert-circle-outline",
		},
		value: Boolean,
		right: Boolean,
		indicator: {
			type: Number || String,
			default: 38,
		},
		position:Boolean,
	},
	data() {
		return {
			show: false,
			percentage: 0,
			refresh_length: 0,
			refresh_proccess: 0,
			canRefresh: true,
		};
	},
	mounted() {
		this.getRefreshProgress();
	},

	watch: {
		value(value) {
			this.show = value;
		},
		show(value) {
			this.$emit("input", value);
		},
		percentage: function (value) {
			this.$emit("refresh", value);
		},
	},

	methods: {
		async refreshCrm() {
			this.show = true;

			let { data } = await this.$axios.get(`advertisement/last-refresh`, {
				params: { platform: "crm" },
			});

			if (data.no_record) {
				this.canRefresh = false;
				return;
			}
			let now = moment().format("DD/MM/YYYY HH:mm:ss");
			let ms = moment(now, "DD/MM/YYYY HH:mm:ss").diff(moment(data.date));
			this.refreshTime = data.date;
			if (ms > 40 * 60000) {
				this.canRefresh = true;
			} else {
				this.canRefresh = false;
			}
		},
		toggleShow() {
			this.show = false;
		},
		getRefreshProgress() {
			try {
				this.$echo
					.private(`refresh-crm.${this.$auth.user.id}`)
					.listen("SendRefreshCrmEvent", async (data) => {
						this.refresh_proccess++;

						this.percentage = Math.round(
							(this.refresh_proccess * 100) / this.refresh_length
						);
					});
			} catch (error) {
				console.log("refresh error", error);
			}
		},

		async Refresh() {
			this.refresh_proccess = 0;

			try {
				let url = `advertisement/connection/refresh-crm`;
				const response = await this.$axios.get(url);
				if (response.status) {
					this.refresh_length = response.data.total_countries;
				}

				this.percentage = 1;

				this.toggleShow();
			} catch (_) {}
		},
	},
	// beforeDestroy() {
	//   this.$echo.leave(`refresh-crm.${this.$auth.user.id}`);
	// },
};
</script>
<style scoped>
.pop-over {
	position: absolute;
	bottom: 70px;
	min-width: 300px;
	max-width: 400px;
}

.right .pop-over {
	right: 0;
}

.pop-over-after {
	height: 18px;
	width: 20px;
	position: absolute;
	left: var(--indicator);
	bottom: 0;
	transform: translateY(66%);
	border-top: 18px solid currentColor;
	border-left: 10px solid transparent;
	border-right: 10px solid transparent;
}

.v-application--is-rtl .pop-over-after,
.right .pop-over-after {
	left: unset;
	right: var(--indicator);
}

.v-application--is-rtl .right .pop-over-after {
	right: unset;
	left: var(--indicator);
}

.icon {
	height: 36px;
	width: 36px;
	min-width: 36px;
	border-radius: 50%;
	background: rgba(255, 255, 255, 0.2);
}

.pop-over-close {
	position: absolute;
	top: 2px;
	right: 2px;
}

.v-application--is-rtl .pop-over-close {
	right: unset;
	left: 2px;
}
</style>
<!--    <refresh-percentage :percentage="crm_refresh_percentage" v-if=" crm_refresh" /> -->
<!-- getCrmPercentage(percentage) {
  this.crm_refresh_percentage = percentage;
  this.crm_refresh = true;
  setTimeout(() => {
    if (percentage == 100) {
      this.crm_refresh_percentage = 0;
      this.crm_refresh = false;

    }
  }, 3000);
  this.$toasterNA("green", this.$tr("successfully_refreshed"));

} -->
