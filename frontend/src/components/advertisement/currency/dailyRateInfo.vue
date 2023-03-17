<template>
	<div class="h-full mt-5">
		<div class="w-full d-flex">
			<CAutoComplete
				v-model="form.currency_from.$model"
				:label="$tr('label_required', $tr('currency_from'))"
				:rules="validateRule(form.currency_from, $root, $tr('currency_from'))"
				:items="currencies"
				item-value="id"
				item-text="name"
				:placeholder="$tr('choose_item', $tr('currency'))"
				:invalid="form.currency_from.$invalid"
				prepend-inner-icon="fa fa-th"
				:loading="countryLoading"
				title="From"
			/>
			<CAutoComplete
				v-model="form.currency_to.$model"
				:label="$tr('label_required', $tr('currency_to'))"
				:rules="
					validateRule(
						form.currency_to, // validate objec
						$root, // context
						$tr('currency_to') // lable for feedback
					)
				"
				:items="currencies"
				item-value="id"
				item-text="name"
				:placeholder="$tr('choose_item', $tr('currency_to'))"
				:invalid="form.currency_to.$invalid"
				prepend-inner-icon="fa fa-th"
				:loading="countryLoading"
				title="To"
			/>
		</div>

		<div class="w-half">
			<CNumber
				title="today_rate"
				placeholder="rate"
				v-model="form.rate.$model"
				:invalid="form.rate.$invalid"
				:label="$tr('label_required', $tr('currency_to'))"
				:rules="
					validateRule(
						form.rate, // validate objec
						$root, // context
						$tr('rate') // lable for feedback
					)
				"
			/>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CNumber from "../../new_form_components/Inputs/CNumber.vue";
export default {
	props: {
		form: Object,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
			countryLoading: false,
			currencies: [],
			rate: 0,
		};
	},
	methods: {
		async validate() {
			this.form.currency_from.$touch();
			this.form.currency_to.$touch();
			this.form.rate.$touch();
			return (
				!this.form.currency_from.$invalid &&
				!this.form.currency_to.$invalid &&
				!this.form.rate.$invalid
			);
		},
		async loaded() {
			this.countryLoading = true;
			try {
				const { data } = await this.$axios.get("advertisement/currencies");
				this.currencies = data;
			} catch (error) {}
			this.countryLoading = false;
		},
	},
	components: { CAutoComplete, CNumber },
};
</script>
