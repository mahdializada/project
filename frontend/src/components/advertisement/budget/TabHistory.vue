<template>
	<div>
		<v-card
			class="w-full mb-2 pa-2 d-flex align-center card_Page_header"
			elevation="1"
			outlined
			v-if="titleData.name"
		>
			<div class="ps-4">
				<v-avatar
					size="30"
					color="primary darken-2"
					class="white--text text-h6 text-uppercase"
					outlined
				>
					<img v-if="titleData.img" :src="titleData.img" alt="" />
					<FlagIcon
						v-else-if="titleData.flag"
						:flag="titleData.flag"
						:round="true"
						medium
					/>
					<span v-else>{{ titleData.logo }}</span>
				</v-avatar>

				<span class="ps-1 font-weight-medium">{{ titleData.name }}</span>
			</div>

			<v-spacer></v-spacer>
			<div class="pe-4">
				<span
					class="text-body-1 px-1 text-center"
					v-for="total in titleData.totals"
					:key="total.name"
				>
					{{ total.name + ":  " + total.total }}</span
				>
			</div>
		</v-card>

		<v-card
			class="w-full mb-2 pa-2 d-flex position-relative align-center card_Page_header"
			elevation="1"
			outlined
			v-else
		>
			<div class="ps-4">
				<v-avatar
					size="40"
					v-if="item_data.model == 'company' || item_data.model == 'platform'"
					outlined
					class="white--text text-h6 text-uppercase"
				>
					<img :src="getAvatar()" alt="" />
				</v-avatar>
				<v-avatar size="40" v-else-if="item_data.model == 'country'" outlined>
					<FlagIcon :flag="getAvatar()" :round="true" medium />
				</v-avatar>
				<v-avatar
					v-else
					size="40"
					color="primary"
					class="white--text text-h6 text-uppercase"
					outlined
				>
					{{ getAvatar() }}
				</v-avatar>

				<span class="ps-1 font-weight-medium">{{ getBudgetFilterName() }}</span>
			</div>

			<v-spacer></v-spacer>
			<div class="pe-4">
				<span class="font-weight-regular text-body-1 px-1">
					Total Budget: {{ totals.total_budget }}</span
				>
				<v-divider vertical class="grey lighten-2"></v-divider>
				<span class="font-weight-regular text-body-1 px-1">
					Total Spend: {{ totals.total_spend }}</span
				>
			</div>
		</v-card>
		<v-tabs
			v-model="tabIndex"
			height="40"
			:background-color="tabItems.length < 3 ? `primary` : `tabBackground`"
			active-class="activeClass"
			show-arrows
			:hide-slider="true"
			class="custom5"
			center-active
		>
			<v-tab
				v-for="(item, i) in tabItems"
				:key="i"
				:class="`px-3 me-1 customTab3 ${!$vuetify.rtl ? 'ltrTab' : 'rtlTab'}`"
				:style="`z-index: ${
					item.isSelected ? tabItems.length + 1 : tabItems.length - i
				}`"
			>
				<p
					:class="`${
						item.isSelected ? 'selected' : 'not-selected'
					} tab-title text-capitalize mb-0`"
				>
					{{ $tr(item.text) }}
				</p>
			</v-tab>
		</v-tabs>
	</div>
</template>

<script>
import FlagIcon from "../../common/FlagIcon.vue";
export default {
	name: "TableTabs",
	props: {
		totals: {
			type: Object,
			require: true,
		},
		item_data: {
			type: Object,
			require: true,
		},
		titleData: {
			type: Object,
			require: true,
		},
		tab: {
			type: Number,
			default: 0,
		},
	},
	data() {
		return {
			tabIndex: 0,
			selected_tab: 0,
			tabItems: [
				{
					text: "budget_history",
					icon: "fa-table",
					isSelected: 1,
					key: "budget_history",
				},
				{
					text: "label",
					icon: "fa-thumbs-up",
					isSelected: 0,
					key: "label",
				},
				{
					text: "remark",
					icon: "fa-ban",
					isSelected: 0,
					key: "remark",
				},
				{
					text: "bid-history",
					icon: "fa-ban",
					isSelected: 0,
					key: "bid_history",
				},
			],
		};
	},
	watch: {
		tabIndex: function (val, oldValue) {
			this.tabItems[val].isSelected = true;
			this.tabItems[oldValue].isSelected = false;

			this.$emit("getSelectedTabRecords", this.tabItems[val].key);
		},
	},
	methods: {
		getBudgetFilterName() {
			switch (this.item_data.model) {
				case "ad":
					return this.item_data.item.ad_name;
				default:
					return this.item_data.item.name;
			}
		},
		getAvatar() {
			switch (this.item_data.model) {
				case "country":
					return this.item_data.item.iso2.toLowerCase();
				case "company":
					return this.item_data.item.logo;

				case "ad":
					return this.item_data.item.ad_name.charAt(0);
				default:
					return "this.item_data.item.name.charAt(0);";
			}
		},
	},
	components: { FlagIcon },
};
</script>

<style>
.customTab3.v-tab {
	max-width: unset !important;
}
.selected__item__container {
	width: 150px;
}
.selected_chip {
	padding-bottom: 2px;
	padding-top: 2px;
	background-color: var(--v-secondary-lighten1);
	min-width: 80px !important;
}
.customTab3 {
	display: flex;
	justify-content: space-between;
	border-top-right-radius: 8px;
	border-top-left-radius: 8px;
	position: relative;
	background-color: var(--v-primary-base);
	border-top: 0.2px solid var(--v-surface-lighten1);
	border-left: 0.2px solid var(--v-surface-lighten1);
	width: 100%;
}
.customTab3::before {
	background-color: unset !important;
}
.customTab3:after {
	content: " ";
	position: absolute;
	display: block;
	width: 30px;
	height: 100%;
	top: 0;
	right: 0;
	z-index: -1;
	background-color: var(--v-primary-base);
	border-top-right-radius: 8px;
	border-top-left-radius: 8px;
	transform-origin: top right;
	-ms-transform: skew(25deg, 0);
	-webkit-transform: skew(25deg);
	transform: skew(25deg);
	border-right: 0.2px solid var(--v-surface-lighten1);
}
.activeClass:after {
	background-color: var(--v-surface-lighten1);
	border-right: 0.2px solid var(--v-tabBackground-darken2);
}
.activeClass {
	background-color: var(--v-surface-lighten1);
	color: var(--v-primary-base) !important;
	border-top: 0.2px solid var(--v-tabBackground-darken2);
	border-left: 0.2px solid var(--v-tabBackground-darken2);
}
.custom5 .v-slide-group__prev,
.custom5 .v-slide-group__next {
	background-color: var(--v-background-base);
}
.custom5 .v-slide-group__next .v-icon,
.custom5 .v-slide-group__prev .v-icon {
	color: var(--v-primary-base) !important;
}

.custom5 .v-slide-group__next .v-icon.v-icon--disabled,
.custom5 .v-slide-group__prev .v-icon.v-icon--disabled {
	color: var(--v-primary-lighten3) !important;
}

.custom5 .v-ripple__container {
	display: none !important;
}
.custom5 .v-tab.v-tab {
	min-width: 100px !important;
	max-width: 200px !important;
}
</style>
