<template lang="">
	<div class="financialInfoClass pt-4">
		<div class="me-md-2 w-full">
			<CTextField
				v-model="form.head_selling_price.$model"
				type="number"
				:min="0"
				title="Head selling price"
				placeholder="Price"
				prepend-inner-icon="mdi-tag"
				:rules="
					validateRule(
						form.head_selling_price,
						$root,
						$tr('head_selling_price')
					)
				"
			/>
			<InputCard
				class="mt-2"
				title="Price patterns"
				:rules="validateRule(form.price_patterns, $root, $tr('price_patterns'))"
			>
				<v-row>
					<v-col
						cols="12"
						lg="4"
						v-for="(item, index) in PricePattern"
						:key="index"
					>
						<FinancialInfoCard
							@addPricePattern="addPricePattern(item.title, ...arguments)"
							:title="item.title"
							:key="keys"
						></FinancialInfoCard>
					</v-col>
				</v-row>
			</InputCard>
		</div>
	</div>
</template>
<script>
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import FinancialInfoCard from "./FinancialInfoCard.vue";
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";

export default {
	name: "FinancialInfo",
	components: {
		CTextField,
		FinancialInfoCard,
		InputCard,
	},
	props: {
		form: Object, // default prop
	},
	data() {
		return {
			keys: 0,
			validateRule: GlobalRules.validate, // gloabl function fro validate
			head_selling_price: null,
			PricePattern: [
				{ title: "Free quantity style" },
				{ title: "Multiple quantity style" },
				{ title: "Gift style" },
			],
			freeQuantityStyle: {},
			MultipleQuantityStyle: {},
			GiftStyle: {},
		};
	},
	methods: {
		async loaded() {
			this.keys += 1;
		},
		async validate() {
			await this.chectPricePattern();
			this.form.price_patterns.$touch();
			this.form.head_selling_price.$touch();
			return (
				!this.form.head_selling_price.$invalid &&
				!this.form.price_patterns.$invalid
			);
		},
		chectPricePattern() {
			let free = this.form.$model.price_patterns.free_quantity_style;
			let mult = this.form.$model.price_patterns.multiple_quantity_style;
			let gift = this.form.$model.price_patterns.gift_style;
			if (free && !free?.length > 0)
				this.$delete(this.form.$model.price_patterns, "free_quantity_style");
			if (mult && !mult?.length > 0)
				this.$delete(
					this.form.$model.price_patterns,
					"multiple_quantity_style"
				);
			if (gift && !gift?.length > 0)
				this.$delete(this.form.$model.price_patterns, "gift_style");
		},
		addPricePattern(title, val) {
			if (title == "Free quantity style") {
				this.freeQuantityStyle = {
					free_quantity_style: val,
				};
			}
			if (title == "Multiple quantity style") {
				this.MultipleQuantityStyle = {
					multiple_quantity_style: val,
				};
			}
			if (title == "Gift style") {
				this.GiftStyle = {
					gift_style: val,
				};
			}
			this.form.price_patterns.$model = {
				...this.freeQuantityStyle,
				...this.MultipleQuantityStyle,
				...this.GiftStyle,
			};
		},
	},
};
</script>
<style>
.financialInfoClass .v-input__prepend-inner {
	transform: scaleX(-1);
}
</style>
