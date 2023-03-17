<template>
	<div>
		<CTitle :text="form.item_code.$model" />
		<div class="h-full d-flex align-center-justify-center mt-3">
			<InputCard
				:title="$tr('label_required', $tr('product_source'))"
				:hasSearch="false"
				:hasPagination="false"
				class="product_source__container"
			>
				<div class="d-flex align-center">
					<SelectItem
						v-model="form.prod_source.$model"
						v-for="item in productSources"
						:key="item.id"
						:item="item"
						:icon="true"
						:selected="item.id == form.prod_source.$model"
						:disable="!item.active"
						@click="onItemSelected(item, 'prod_source')"
						:rules="
							validateRule(
								form.prod_source, // validate objec
								$root, // context
								$tr('product_source') // lable for feedback
							)
						"
						:invalid="form.prod_source.$invalid"
					>
					</SelectItem>
				</div>
			</InputCard>
		</div>
		<div class="h-full d-flex align-center-justify-center mt-3">
			<div class="w-full">
				<CRadioWIthBody
					:items="saleGoal"
					:text="$tr('label_required', $tr('sale_goal'))"
					@onChange="(v) => (form.prod_sale_goal.$model = v)"
					:rules="validateRule(form.prod_sale_goal, $root, $tr('sale_goal'))"
					:invalid="form.prod_sale_goal.c$invalid"
				/>
			</div>
		</div>
		<div class="h-full d-flex align-center-justify-center mt-3">
			<InputCard
				:title="$tr('label_required', $tr('product_style'))"
				:hasSearch="false"
				:hasPagination="false"
				class="product_source__container"
			>
				<div class="d-flex align-center">
					<SelectItem
						v-model="form.prod_style.$model"
						v-for="item in productStyles"
						:key="item.id"
						:item="item"
						:icon="true"
						:selected="item.id == form.prod_style.$model"
						:disable="!item.active"
						@click="onItemSelected(item, 'prod_style')"
						:rules="
							validateRule(
								form.prod_style, // validate objec
								$root, // context
								$tr('product_style') // lable for feedback
							)
						"
						:invalid="form.prod_style.$invalid"
					>
					</SelectItem>
				</div>
			</InputCard>
		</div>
		<div class="h-full d-flex align-center-justify-center mt-3">
			<InputCard
				:title="$tr('label_required', $tr('product_section'))"
				:hasSearch="false"
				:hasPagination="false"
				class="product_source__container"
			>
				<div class="d-flex align-center">
					<SelectItem
						v-model="form.prod_section.$model"
						v-for="item in productSections"
						:key="item.id"
						:item="item"
						:icon="true"
						:selected="item.id == form.prod_section.$model"
						:disable="!item.active"
						@click="onItemSelected(item, 'prod_section')"
						:rules="
							validateRule(
								form.prod_section, // validate objec
								$root, // context
								$tr('product_section') // lable for feedback
							)
						"
						:invalid="form.prod_section.$invalid"
					>
					</SelectItem>
				</div>
			</InputCard>
		</div>
		<div class="h-full d-flex align-center-justify-center mt-3">
			<InputCard :title="$tr('label_required', $tr('new_product_source'))">
				<CheckBoxHorizontalItem
					v-model="form.prod_new_product_source.$model"
					:items="productNewSources"
					:loading="isFetchingNewProductSource"
					:no_data="productNewSources.length < 1 && !isFetchingNewProductSource"
					height="auto"
					bgWhite
					:multi="true"
					:hasIcon="true"
					:rules="
						validateRule(
							form.prod_new_product_source,
							$root,
							$tr('new_product_source')
						)
					"
					:invalid="form.prod_new_product_source.c$invalid"
				></CheckBoxHorizontalItem>
			</InputCard>
		</div>
		<div class="h-full d-flex align-center-justify-center mt-3">
			<div class="w-full">
				<CRadioWIthBody
					:items="productWorkTyps"
					:text="$tr('label_required', $tr('product_work_type'))"
					@onChange="(v) => (form.prod_work_type.$model = v)"
					:rules="
						validateRule(form.prod_work_type, $root, $tr('product_work_type'))
					"
					:invalid="form.prod_work_type.c$invalid"
				/>
			</div>
		</div>
		<div class="h-full d-flex align-center-justify-center mt-3">
			<InputCard
				:title="$tr('label_required', $tr('product_import_source'))"
				:hasSearch="false"
				:hasPagination="false"
				class="product_source__container"
			>
				<div class="d-flex align-center">
					<SelectItem
						v-model="form.prod_import_source.$model"
						v-for="item in productImportSources"
						:key="item.id"
						:item="item"
						:icon="true"
						:selected="form.$model.prod_import_source.includes(item.id)"
						:disable="!item.active"
						@click="onItemSelected(item, 'prod_import_source')"
						:rules="
							validateRule(
								form.prod_import_source, // validate objec
								$root, // context
								$tr('product_import_source') // lable for feedback
							)
						"
						:invalid="form.prod_import_source.$invalid"
					>
					</SelectItem>
				</div>
			</InputCard>
		</div>
		<div class="h-full d-flex align-center-justify-center mt-3">
			<div class="w-full">
				<CRadioWIthBody
					:items="productProductionTypes"
					:text="$tr('label_required', $tr('product_production_type'))"
					@onChange="(v) => (form.prod_production_type.$model = v)"
					:rules="
						validateRule(
							form.prod_production_type,
							$root,
							$tr('product_production_type')
						)
					"
					:invalid="form.prod_production_type.c$invalid"
				/>
			</div>
		</div>
	</div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CheckBoxHorizontalItem from "../../new_form_components/cat_product_selection/CheckBoxHorizontalItem.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import SelectItem from "../../new_form_components/components/SelectItem.vue";
import CRadioWIthBody from "../../new_form_components/Inputs/CRadioWIthBody.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

export default {
	props: {
		form: Object,
	},
	components: {
		CTitle,
		CRadioWIthBody,
		SelectItem,
		InputCard,
		CheckBoxHorizontalItem,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
			isFetchingNewProductSource: false,
			saleGoal: [
				{ value: "for_stock_disposal", label: "for_stock_disposal" },
				{ value: "for_profit", label: "for_profit" },
			],
			productWorkTyps: [
				{ value: "creation", label: "creation" },
				{ value: "copy", label: "copy" },
			],
			productProductionTypes: [
				{ value: "ready", label: "ready" },
				{ value: "manufacturing", label: "manufacturing" },
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
		};
	},
	methods: {
		async validate() {
			this.form.prod_source.$touch();
			this.form.prod_sale_goal.$touch();
			this.form.prod_style.$touch();
			this.form.prod_section.$touch();
			this.form.prod_new_product_source.$touch();
			this.form.prod_work_type.$touch();
			this.form.prod_import_source.$touch();
			this.form.prod_production_type.$touch();
			return (
				!this.form.prod_source.$invalid &&
				!this.form.prod_sale_goal.$invalid &&
				!this.form.prod_style.$invalid &&
				!this.form.prod_section.$invalid &&
				!this.form.prod_new_product_source.$invalid &&
				!this.form.prod_work_type.$invalid &&
				!this.form.prod_import_source.$invalid &&
				!this.form.prod_production_type.$invalid
			);
		},
		async loaded() {},
		onItemSelected(item, value) {
			if (item.active == true && value == "prod_source")
				this.form.prod_source.$model = item.id;
			else if (item.active == true && value == "prod_style")
				this.form.prod_style.$model = item.id;
			else if (item.active == true && value == "prod_section")
				this.form.prod_section.$model = item.id;
			else if (item.active == true && value == "prod_import_source") {
				console.log(this.form.$model.prod_import_source);
				if (this.form.$model.prod_import_source.includes(item.id)) {
					this.form.$model.prod_import_source =
						this.form.$model.prod_import_source.filter((row) => row != item.id);
				} else this.form.$model.prod_import_source.push(item.id);
			}
		},
	},
};
</script>

<style scoped lang="scss">
.product_source__container {
	display: flex;
	justify-content: space-between;
}
</style>
