<template>
	<v-dialog v-model="model" persistent max-width="1300" width="1300">
		<CustomFilter
			@onClose="changeModel"
			@onSubmit="onSubmit"
			@onClear="onClear"
			:cardTitle="$tr('filter_item', $tr('advertisement'))"
		>
			<template v-slot:data>
				<!-- COMMON SECTION -->
				<FilterItemList item-title="common" :items="filterForm.common">
					<template v-slot:country>
						<FilterItem
							type="country"
							placeholder="country"
							:items.sync="countries"
							:selectedItems.sync="form.countries"
							:hasFlag="true"
						/>
					</template>
					<template v-slot:company>
						<FilterItem
							type="autocomplete"
							placeholder="company"
							:items.sync="companies"
							:selectedItems.sync="form.companies"
						/>
					</template>
					<template v-slot:platform>
						<FilterItem
							type="autocomplete"
							placeholder="advertisement_platform"
							:items.sync="platforms"
							:selectedItems.sync="form.platforms"
						/>
					</template>
					<template v-slot:ad_account>
						<FilterItem
							type="autocomplete"
							placeholder="advertisement_ad_account"
							:items.sync="ad_accounts"
							:selectedItems.sync="form.ad_accounts"
						/>
					</template>
					<template v-slot:advertisement_campaign>
						<FilterItem
							type="autocomplete"
							placeholder="advertisement_campaign"
							:items.sync="campaigns"
							:selectedItems.sync="form.campaigns"
						/>
					</template>
					<template v-slot:advertisement_ad_set>
						<FilterItem
							type="autocomplete"
							placeholder="advertisement_ad_set"
							:items.sync="adsets"
							:selectedItems.sync="form.adsets"
						/>
					</template>
				</FilterItemList>

				<!-- METRICS SECTION -->
				<FilterItemList item-title="metrics" :items="filterForm.metrics">
					<template v-slot:spend>
						<FilterItem type="number_condition" :form.sync="form.spend" />
					</template>
					<template v-slot:impressions>
						<FilterItem type="number_condition" :form.sync="form.impressions" />
					</template>
					<template v-slot:clicks>
						<FilterItem type="number_condition" :form.sync="form.clicks" />
					</template>
					<template v-slot:video_views>
						<FilterItem type="number_condition" :form.sync="form.video_views" />
					</template>
					<template v-slot:page_views>
						<FilterItem type="number_condition" :form.sync="form.page_views" />
					</template>
					<template v-slot:daily_budget>
						<FilterItem
							type="number_condition"
							:form.sync="form.daily_budget"
						/>
					</template>
					<template v-slot:budget>
						<FilterItem type="number_condition" :form.sync="form.budget" />
					</template>
					<template v-slot:result>
						<FilterItem type="number_condition" :form.sync="form.result" />
					</template>
					<template v-slot:story_opens>
						<FilterItem type="number_condition" :form.sync="form.story_opens" />
					</template>
				</FilterItemList>
				<!-- CRM SECTION -->
				<FilterItemList item-title="crms" :items="filterForm.crms">
					<template v-slot:orders>
						<FilterItem type="number_condition" :form.sync="form.orders" />
					</template>
					<template v-slot:crm_confirmed>
						<FilterItem type="number_condition" :form.sync="form.crm_confirm" />
					</template>
					<template v-slot:crm_cancelled>
						<FilterItem
							type="number_condition"
							:form.sync="form.crm_cancelled"
						/>
					</template>
					<template v-slot:crm_sendback>
						<FilterItem
							type="number_condition"
							:form.sync="form.crm_sendback"
						/>
					</template>
					<template v-slot:total_picked_up>
						<FilterItem
							type="number_condition"
							:form.sync="form.total_pickedup"
						/>
					</template>
					<template v-slot:picked_up>
						<FilterItem type="number_condition" :form.sync="form.pickedup" />
					</template>
					<template v-slot:logis_delivered>
						<FilterItem
							type="number_condition"
							:form.sync="form.logis_delivered"
						/>
					</template>
					<template v-slot:logis_cancelled>
						<FilterItem
							type="number_condition"
							:form.sync="form.logis_cancelled"
						/>
					</template>
					<template v-slot:total_sales>
						<FilterItem type="number_condition" :form.sync="form.total_sales" />
					</template>
					<template v-slot:product_cost>
						<FilterItem
							type="number_condition"
							:form.sync="form.product_cost"
						/>
					</template>
					<template v-slot:delivery_cost>
						<FilterItem
							type="number_condition"
							:form.sync="form.delivery_cost"
						/>
					</template>
					<template v-slot:crm_confirmed_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.crm_confirmed_percentage"
						/>
					</template>
					<template v-slot:crm_cancelled_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.crm_cancelled_percentage"
						/>
					</template>
					<template v-slot:crm_sendback_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.send_back_percentage"
						/>
					</template>
					<template v-slot:crm_difference>
						<FilterItem
							type="number_condition"
							:form.sync="form.crm_difference"
						/>
					</template>
					<template v-slot:logis_delivered_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.logis_delivered_percentage"
						/>
					</template>
					<template v-slot:logis_cancelled_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.logis_cancelled_percentage"
						/>
					</template>
					<template v-slot:final_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.final_percentage"
						/>
					</template>
					<template v-slot:total_expense>
						<FilterItem
							type="number_condition"
							:form.sync="form.total_expense"
						/>
					</template>
					<template v-slot:profit_and_loss>
						<FilterItem
							type="number_condition"
							:form.sync="form.profit_and_loss"
						/>
					</template>
					<template v-slot:profit_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.profit_percentage"
						/>
					</template>
					<template v-slot:product_cost_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.product_cost_percentage"
						/>
					</template>
					<template v-slot:delivery_cost_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.delivery_cost_percentage"
						/>
					</template>
					<template v-slot:ad_cost_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.ad_cost_percentage"
						/>
					</template>
					<template v-slot:crm_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.crm_percentage"
						/>
					</template>
					<template v-slot:logistics_percentage>
						<FilterItem
							type="number_condition"
							:form.sync="form.logistics_percentage"
						/>
					</template>
					<template v-slot:story_open_rate>
						<FilterItem
							type="number_condition"
							:form.sync="form.story_open_rate"
						/>
					</template>
					<template v-slot:cost_per_story_open>
						<FilterItem
							type="number_condition"
							:form.sync="form.cost_per_story_open"
						/>
					</template>
				</FilterItemList>
				<!-- Calculateds SECTION -->
				<FilterItemList
					item-title="calculateds"
					:items="filterForm.calculateds"
				>
					<template v-slot:bid>
						<FilterItem type="number_condition" :form.sync="form.bid" />
					</template>
					<template v-slot:cpm>
						<FilterItem type="number_condition" :form.sync="form.cpm" />
					</template>
					<template v-slot:cpo>
						<FilterItem type="number_condition" :form.sync="form.cpo" />
					</template>
					<template v-slot:ctr>
						<FilterItem type="number_condition" :form.sync="form.ctr" />
					</template>
					<template v-slot:cpc>
						<FilterItem type="number_condition" :form.sync="form.cpc" />
					</template>
					<template v-slot:bp>
						<FilterItem type="number_condition" :form.sync="form.bp" />
					</template>
					<template v-slot:aap>
						<FilterItem type="number_condition" :form.sync="form.aap" />
					</template>
					<template v-slot:vqp>
						<FilterItem type="number_condition" :form.sync="form.vqp" />
					</template>
					<template v-slot:cpp>
						<FilterItem type="number_condition" :form.sync="form.cpp" />
					</template>
				</FilterItemList>
				<!-- Google Analytics SECTION -->
				<FilterItemList
					item-title="google_analytics"
					:items="filterForm.google"
				>
					<template v-slot:users>
						<FilterItem type="number_condition" :form.sync="form.users" />
					</template>
					<template v-slot:new_users>
						<FilterItem type="number_condition" :form.sync="form.new_users" />
					</template>
					<template v-slot:ga_page_views>
						<FilterItem
							type="number_condition"
							:form.sync="form.ga_page_views"
						/>
					</template>
				</FilterItemList>
				<FilterItemList
					item-title="changes_history"
					:items="filterForm.changes"
				>
					<template v-slot:status_history>
						<FilterItem
							type="number_condition"
							:form.sync="form.status_history"
						/>
					</template>
					<template v-slot:remark_label>
						<FilterItem
							type="number_condition"
							:form.sync="form.remark_label"
						/>
					</template>
					<template v-slot:budget_history>
						<FilterItem
							type="number_condition"
							:form.sync="form.budget_history"
						/>
					</template>
				</FilterItemList>

				<!-- PRODUCT SECTION -->
				<FilterItemList
					item-title="product"
					:items="filterForm.productDetail2"
					v-if="tabKey && tabKey == 'ad'"
				>
					<template v-slot:prod_max_adver_cost>
						<FilterItem
							type="number_condition"
							:form.sync="form.prod_max_adver_cost"
						/>
					</template>
				</FilterItemList>

				<FilterItemList
					item-title="product"
					:items="filterForm.productDetail"
					v-if="tabKey && tabKey == 'item_code'"
				>
					<template v-slot:product_source>
						<FilterItem
							type="country"
							placeholder="product_source"
							:items.sync="productSources"
							:selectedItems.sync="form.prod_source"
							:hasIcon="true"
						/>
					</template>
					<template v-slot:sale_goal>
						<FilterItem
							type="country"
							placeholder="sale_goal"
							:items.sync="saleGoal"
							:selectedItems.sync="form.prod_sale_goal"
						/>
					</template>

					<template v-slot:prod_style>
						<FilterItem
							type="country"
							placeholder="prod_style"
							:items.sync="productStyles"
							:selectedItems.sync="form.prod_style"
							:hasIcon="true"
						/>
					</template>
					<template v-slot:product_section>
						<FilterItem
							type="country"
							placeholder="product_section"
							:items.sync="productSections"
							:selectedItems.sync="form.prod_section"
							:hasIcon="true"
						/>
					</template>

					<template v-slot:new_product_source>
						<FilterItem
							type="country"
							placeholder="new_product_source"
							:items.sync="productNewSources"
							:selectedItems.sync="form.prod_new_product_source"
							:hasIcon="true"
						/>
					</template>
					<template v-slot:product_work_type>
						<FilterItem
							type="country"
							placeholder="product_work_type"
							:items.sync="productWorkTyps"
							:selectedItems.sync="form.prod_work_type"
						/>
					</template>
					<template v-slot:prod_cost>
						<FilterItem type="number_condition" :form.sync="form.prod_cost" />
					</template>
					<template v-slot:prod_available_quantity>
						<FilterItem
							type="number_condition"
							:form.sync="form.prod_available_quantity"
						/>
					</template>

					<template v-slot:prod_max_adver_cost>
						<FilterItem
							type="number_condition"
							:form.sync="form.prod_max_adver_cost"
						/>
					</template>
				</FilterItemList>
			</template>
			<template v-slot:date_range>
				<div class="mt-2">
					<FilterDateRange
						ref="filterDateRange"
						@dateChanged="onDateRangeSubmit"
					/>
				</div>
			</template>
			<template v-slot:id_filtering>
				<FilterInput
					:clearInput.sync="clearInput"
					@isInclude="(isInclude) => (form.include = isInclude)"
					@getIds="(ids) => (form.ids = ids)"
					:label="$tr('id')"
					:type="'id_filtering'"
				/>
				<div class="mt-4">
					<div class="subtitle-1 text-capitalize">{{ $tr("status") }}</div>
					<div class="d-flex custom__filter_card">
						<SelectItem
							v-for="item in statuses"
							:key="item.id"
							:item="item"
							:selected="form.status == item.id"
							@click="(id) => (form.status = id)"
						/>
					</div>
				</div>
			</template>
		</CustomFilter>
	</v-dialog>
</template>

<script>
import FilterInput from "~/components/design/components/FilterInput.vue";
import CustomFilter from "~/components/common/CustomFilter.vue";
import SelectCard from "../new_form_components/Inputs/SelectCard.vue";
import FilterItemList from "./FilterItemList.vue";
import FilterItem from "./FilterItem.vue";
import FilterDateRange from "./FilterDateRange.vue";
import SelectItem from "../new_form_components/components/SelectItem.vue";
export default {
	components: {
		FilterInput,
		CustomFilter,
		SelectCard,
		FilterItemList,
		FilterItem,
		FilterDateRange,
		SelectItem,
	},
	data() {
		return {
			model: false,
			countries: [],
			companies: [],
			platforms: [],
			ad_accounts: [],
			campaigns: [],
			adsets: [],
			statuses: [
				{ id: "ACTIVE", name: "active" },
				{ id: "INACTIVE", name: "inactive" },
			],

			sales_type: [
				{ id: "Single Sale", name: this.$tr("single_sales") },
				{ id: "Shopping Cart", name: this.$tr("shopping_cart") },
			],

			filterForm: {
				common: [
					{ title: "country", active: false, showClear: false },
					{ title: "company", active: false },
					{ title: "platform", active: false },
					{ title: "ad_account", active: false },
					{ title: "advertisement_campaign", active: false },
					{ title: "advertisement_ad_set", active: false },
				],
				status: [{ title: "status", active: false, showClear: false }],
				metrics: [
					{ title: "spend", active: false },
					{ title: "impressions", active: false },
					{ title: "clicks", active: false },
					{ title: "video_views", active: false },
					{ title: "page_views", active: false },
					{ title: "daily_budget", active: false },
					{ title: "budget", active: false },
					{ title: "result", active: false },
					{ title: "story_opens", active: false },
				],
				calculateds: [
					{ title: "bid", active: false },
					{ title: "cpm", active: false },
					{ title: "cpo", active: false },
					{ title: "ctr", active: false },
					{ title: "cpc", active: false },
					{ title: "bp", active: false },
					{ title: "aap", active: false },
					{ title: "vqp", active: false },
					{ title: "cpp", active: false },
				],
				crms: [
					{ title: "orders", active: false },
					{ title: "crm_confirmed", active: false },
					{ title: "crm_cancelled", active: false },
					{ title: "crm_sendback", active: false },
					{ title: "total_picked_up", active: false },
					{ title: "picked_up", active: false },
					{ title: "logis_delivered", active: false },
					{ title: "logis_cancelled", active: false },
					{ title: "total_sales", active: false },
					{ title: "product_cost", active: false },
					{ title: "delivery_cost", active: false },
					{ title: "crm_confirmed_percentage", active: false },
					{ title: "crm_cancelled_percentage", active: false },
					{ title: "crm_sendback_percentage", active: false },
					{ title: "crm_difference", active: false },
					{ title: "logis_delivered_percentage", active: false },
					{ title: "logis_cancelled_percentage", active: false },
					{ title: "final_percentage", active: false },
					{ title: "total_expense", active: false },
					{ title: "profit_and_loss", active: false },
					{ title: "profit_percentage", active: false },
					{ title: "product_cost_percentage", active: false },
					{ title: "delivery_cost_percentage", active: false },
					{ title: "ad_cost_percentage", active: false },
					{ title: "crm_percentage", active: false },
					{ title: "logistics_percentage", active: false },
					{ title: "story_open_rate", active: false },
					{ title: "cost_per_story_open", active: false },
				],
				google: [
					{ title: "users", active: false },
					{ title: "new_users", active: false },
					{ title: "ga_page_views", active: false },
				],
				changes: [
					{ title: "status_history", active: false },
					{ title: "remark_label", active: false },
					{ title: "budget_history", active: false },
				],

				productDetail: [
					{ title: "product_source", active: false, showClear: false },
					{ title: "sale_goal", active: false },
					{ title: "prod_style", active: false },
					{ title: "product_section", active: false },
					{ title: "new_product_source", active: false },
					{ title: "product_work_type", active: false },
					{ title: "prod_cost", active: false },
					{ title: "prod_max_adver_cost", active: false },
					{ title: "prod_available_quantity", active: false },
				],
				productDetail2: [{ title: "prod_max_adver_cost", active: false }],
			},

			form: this.columns(),
			newFormData: {},
			clearInput: false,
			isFetched: false,

			// product details
			saleGoal: [
				{ id: "for_stock_disposal", name: "for_stock_disposal" },
				{ id: "for_profit", name: "for_profit" },
			],
			productWorkTyps: [
				{ id: "creation", name: "creation" },
				{ id: "copy", name: "copy" },
			],
			productProductionTypes: [
				{ id: "ready", name: "ready" },
				{ id: "manufacturing", name: "manufacturing" },
			],
			productSources: [
				{
					id: "warehouse",
					name: "warehouse",
					icon: "mdi-warehouse",
					active: true,
				},
				{ id: "market", name: "market", icon: "mdi-cart", active: true },
				{ id: "both", name: "both", icon: "mdi-message-alert", active: true },
			],
			productStyles: [
				{ id: "single", name: "single", icon: "mdi-book", active: true },
				{
					id: "collection",
					name: "collection",
					icon: "mdi-bookmark-box-multiple",
					active: true,
				},
				{ id: "project", name: "project", icon: "mdi-file", active: true },
			],
			productSections: [
				{ id: "new", name: "new", icon: "mdi-plus", active: true },
				{ id: "old", name: "old", icon: "mdi-timer-sand", active: true },
				{ id: "stock", name: "stock", icon: "mdi-reload", active: true },
				{ id: "renew", name: "renew", icon: "mdi-trending-up", active: true },
			],
			productImportSources: [
				{ id: "uae", name: "uae", image: "/uae.jpg", active: true },
				{ id: "china", name: "China", image: "/china.jpg", active: true },
			],
			productNewSources: [
				{
					id: "market_visit",
					name: "market_visit",
					icon: "mdi-share-variant",
				},
				{
					id: "supplier_suggestion",
					name: "supplier_suggestion",
					icon: "mdi-account-search",
				},
				{
					id: "advertiser_products",
					name: "advertiser_products",
					icon: "mdi-chart-line",
				},
				{
					id: "company_source",
					name: "company_source",
					icon: "fa fa-star",
				},
			],
			tabKey: "",
		};
	},
	methods: {
		changeModel(tab = null) {
			this.model = !this.model;

			if (tab) this.tabKey = tab;
			if (this.model) {
				if (this.isFetched) return;
				this.fetchRecords("countries");
				this.fetchRecords("companies");
				this.fetchRecords("platforms");
				this.fetchRecords("ad_accounts");
				this.fetchRecords("campaigns");
				this.fetchRecords("adsets");
				this.isFetched = true;
			}
		},
		columns() {
			return {
				spend: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				impressions: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				clicks: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				video_views: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				page_views: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				daily_budget: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				budget: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				story_opens: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				result: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				// Calculateds
				bid: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				cpo: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				cpm: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				bp: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				aap: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				vqp: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				ctr: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				cpp: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				cpc: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				// CRMS
				orders: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				crm_confirm: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				crm_cancelled: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				crm_sendback: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				total_pickedup: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				pickedup: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				logis_delivered: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				logis_cancelled: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				total_sales: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				product_cost: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				delivery_cost: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				crm_confirmed_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				crm_cancelled_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				send_back_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				crm_difference: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				logis_delivered_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				logis_cancelled_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				final_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				total_expense: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				profit_and_loss: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				profit_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},

				product_cost_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},

				delivery_cost_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},

				ad_cost_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				crm_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				logistics_percentage: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				story_open_rate: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				cost_per_story_open: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				// Google Analytics
				users: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				new_users: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				ga_page_views: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				status_history: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				remark_label: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				budget_history: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				status: null,
				include: 1,
				ids: [],
				prod_cost: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				prod_max_adver_cost: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
				prod_available_quantity: {
					min: null,
					max: null,
					condition: "IS_EQUAL_TO",
				},
			};
		},

		async fetchCompanies() {
			try {
				if (this.companies.length > 0) {
					return;
				}
				const url = `advertisement/connection/generate_link/companies/connections`;
				const { data } = await this.$axios.get(url);
				this.companies = data.items;
			} catch (_) {}
		},

		async fetchCountries() {
			try {
				if (this.countries.length > 0) {
					return;
				}
				const url = "advertisement/connection/generate_link/country/all";
				const { data } = await this.$axios.get(url);
				this.countries = data.items;
			} catch (_) {}
		},

		async fetchRecords(itemValue) {
			if (itemValue in this) {
				try {
					if (this[itemValue].length > 0) {
						return;
					}
					const url = `advertisement/filter/${itemValue}`;
					const { data } = await this.$axios.get(url);
					this[itemValue] = data.items || [];
				} catch (_) {}
			}
		},

		onDateRangeSubmit(range) {
			this.form.start_date = range.start_date;
			this.form.end_date = range.end_date;
			if (range.end_date == "1970-01-01") {
				delete this.form?.end_date;
			}
		},

		onSubmit() {
			this.newFormData = this.makeReadyFilter();
			this.$emit("filterRecords", this.newFormData);
			this.changeModel();
		},

		onClear() {
			this.$refs.filterDateRange?.clear();
			this.form = this.columns();
			this.clearInput = true;
			setTimeout(() => {
				this.clearInput = false;
			}, 2000);
			const f1 = JSON.stringify(this.newFormData);
			const f2 = JSON.stringify(this.form);
			if (f1 != f2) {
				this.newFormData = this.form;
				this.$emit("filterRecords", {});
			}
			this.changeModel();
		},

		makeReadyFilter() {
			const {
				countries,
				companies,
				platforms,
				ad_accounts,
				campaigns,
				adsets,
			} = this.form;
			let newFormData = {};
			let noCheck = [
				"countries",
				"companies",
				"platforms",
				"ad_accounts",
				"campaigns",
				"adsets",
			];

			if (Array.isArray(countries) && countries.length > 0) {
				const countryIds = countries.map((item) => item.id);
				newFormData.countries = null;
				if (countryIds.length > 0) newFormData.country = countryIds;
			}
			if (Array.isArray(companies) && companies.length > 0) {
				const companyIds = companies.map((item) => item.id);
				if (companyIds.length > 0) newFormData.company = companyIds;
			}
			if (Array.isArray(platforms) && platforms.length > 0) {
				const platformIds = platforms.map((item) => item.id);
				if (platformIds.length > 0) newFormData.platform = platformIds;
			}
			if (Array.isArray(ad_accounts) && ad_accounts.length > 0) {
				const accountIds = ad_accounts.map((item) => item.id);
				if (accountIds.length > 0) newFormData.ad_account = accountIds;
			}

			if (Array.isArray(campaigns) && campaigns.length > 0) {
				const campaignIds = campaigns.map((item) => item.campaign_id);
				if (campaignIds.length > 0) newFormData.campaign = campaignIds;
			}
			if (Array.isArray(adsets) && adsets.length > 0) {
				const adsetIds = adsets.map((item) => item.adset_id);
				if (adsetIds.length > 0) newFormData.ad_set = adsetIds;
			}
			for (const [key, value] of Object.entries(this.form)) {
				if (typeof value === "object" && value != null)
					if (value.min || value.max) newFormData[key] = value;
					else if (Array.isArray(value) && this.form[key].length > 0)
						if (!noCheck.includes(key))
							newFormData[key] = this.form[key].map((item) => item.id);
			}
			if (this.form.status) newFormData["status"] = this.form.status;
			if (this.form.include && this.form.ids.length > 0)
				newFormData["include"] = this.form.include;
			if (this.form.ids.length > 0) newFormData["ids"] = this.form.ids;

			return newFormData;
		},
	},
};
</script>

<style scoped>
.custom__filter_card .item__card {
	width: unset !important;
	padding-top: 6px !important;
	padding-bottom: 6px !important;
	margin: unset;
	margin-right: 20px !important;
	margin-top: 8px !important;
	margin-left: 0 !important;
}
.custom__expansion--header {
	min-height: 40px;
	font-weight: 700;
}
.custom__expansion::before {
	box-shadow: none !important;
}
.filter_list_group .v-list-item__icon .v-icon {
	font-size: 16px !important;
}
</style>
