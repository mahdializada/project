<template>
	<transition :name="'fade'">
		<div
			:class="`refresh-prompt pa-2 rounded white--text font-weight-medium ${type}`"
			v-if="show"
			:style="`background:${color} `"
		>
			<div v-if="!canRefresh">
				{{ $tr("the_table_has_been_refreshed") }}<br />
				{{ $tr("please_try_again_later") }}
			</div>
			<div v-else>
				{{ $tr("the_table_has_been_refreshed_at", "refreshTime") }}<br />
				{{ $tr("do_you_wnat_to_refresh_again") }}
			</div>
			<div class="d-flex justify-space-between align-center mt-1">
				<div style="font-size: 12px; opacity: 0.6">
					<v-icon size="16" dark class="me-1">fa-redo</v-icon>
					{{ refreshTimeAgo == "Invalid date" ? "" : refreshTimeAgo }}
				</div>
				<v-btn
					v-if="canRefresh"
					@click="
						() => {
							$emit('refresh');
							$emit('close');
						}
					"
					elevation="2"
					small
					:style="`font-size: 12px; color:${color}`"
					class="py-2 px-3 font-weight-medium"
				>
					{{ $tr("refresh_anyway") }}
				</v-btn>
				<v-btn
					v-else
					@click="$emit('close')"
					elevation="2"
					small
					:style="`font-size: 12px; color:${color}`"
					class="py-2 px-3 font-weight-medium"
				>
					{{ $tr("ok") }}
				</v-btn>
			</div>
		</div>
	</transition>
</template>
<script>
export default {
	props: {
		show: Boolean,
		canRefresh: Boolean,
		refreshTime: String,
		refreshTimeAgo: String,
		color: {
			type: String,
			default: "#00bc81",
		},
		type: {
			type: String,
			default: "refresh",
		},
	},
};
</script>
<style scoped>
.refresh-prompt {
	position: absolute;
	bottom: 70px;
}

.refresh-prompt::before {
	display: block;
	content: "";
	height: 18px;
	width: 20px;
	position: absolute;
	left: 46px;
	bottom: 0;
	transform: translateY(66%);

	border-left: 10px solid transparent;
	border-right: 10px solid transparent;
}
.refresh::before {
	border-top: 18px solid #00bc81;
}

.warning::before {
	border-top: 18px solid rgb(255 214 51);
}
.label::before {
	border-top: 18px solid #2dccff !important;
}

.v-application--is-rtl .refresh-prompt::before {
	left: unset;
	right: 46px;
}
</style>
