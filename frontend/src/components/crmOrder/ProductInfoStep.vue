<template>
	<div class="connection__container">
		<CTitle text="product_info" />
		<div class="mb-3">
			<CTextField
				type="number"
				py="py-0"
				v-model="form.price.$model"
				@blur="form.price.$touch()"
				:rules="validateRule(form.price, $root, $tr('price'))"
				:invalid="form.price.$invalid"
				:title="$tr('prise')"
				:placeholder="$tr('price')"
				prepend-inner-icon="mdi-currency-usd"
				class="custom-number"
			/>
		</div>
		<div class="mb-3">
			<InputCard>
				<div class="d-flex">
					<p class="my-1 input-title">
						{{ $tr("send_brochure") }}
					</p>
					<v-spacer />
					<v-switch
						v-model="form.send_brochure.$model"
						input-value="true"
						inset
						class="mt-0 mx-2 display-inline"
						style="margin-top: -2px !important"
					/>
				</div>
			</InputCard>
		</div>
		<div class="mb-3">
			<InputCard>
				<div class="d-flex">
					<p class="my-1 input-title">
						{{ $tr("with_tax") }}
					</p>
					<v-spacer />
					<v-switch
						:input-value="false"
						inset
						v-model="form.with_tax.$model"
						hide-details
						class="mt-0 mx-2 display-inline"
						style="margin-top: -2px !important"
					/>
				</div>
			</InputCard>
		</div>
		<div class="mb-3">
			<InputCard>
				<div class="d-flex">
					<p class="my-1 input-title">
						{{ $tr("delay") }}
					</p>
					<v-spacer />
					<v-switch
						input-value="true"
						inset
						v-model="form.delay.$model"
						hide-details
						class="mt-0 mx-2 display-inline"
						style="margin-top: -2px !important"
					/>
				</div>
			</InputCard>
		</div>
		<div class="mb-3" v-if="!form.delay.$model">
			<CDatePicker
				title="Arrival time"
				v-model="form.start_date.$model"
				:icon="'mdi-calendar-clock'"
				:placeholder="$tr('start_date')"
				:rules="validateRule(form.start_date, $root, 'Date')"
				@blur="form.start_date.$touch()"
				:invalid="form.start_date.$invalid"
				:minDate="minDate"
			/>
		</div>
		<div class="mb-3">
			<CTextArea
				px="px-0"
				py="py-0"
				:title="$tr('label_required', $tr('note'))"
				:placeholder="$tr('note')"
				v-model="form.note.$model"
				:rules="validateRule(form.note, $root, $tr('note'))"
				prepend-inner-icon="mdi-clipboard-text-outline"
				@blur="form.note.$touch()"
				:invalid="form.note.$invalid"
			/>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
import CTextArea from "../new_form_components/Inputs/CTextArea.vue";
import CDatePicker from "../new_form_components/Inputs/CDatePicker.vue";
import moment from "moment";

export default {
	props: {
		form: Object,
	},

	watch: {},

	data() {
		return {
			searchCountry: "",
			searchCompany: "",
			searchProject: "",
			fetchingCountries: false,
			salesType: [
				{ id: "Single Sale", name: "single_sales" },
				{ id: "WhatsApp", name: "WhatsApp" },
				{ id: "Shopping Cart", name: "shopping_cart" },
			],
			validateRule: GlobalRules.validate,
			isFetchingcompanies: false,
			isFetchingprojects: false,
			isFetchingproducts: false,
			isFetchingispps: false,

			countries: [],
			companies: [],
			projects: [],
			products: [],
			ispps: [],
			minDate: moment().format("YYYY-MM-DD"),
		};
	},

	computed: {},

	methods: {
		async loaded() {},

		validate() {
			this.form.price.$touch();
			this.form.start_date.$touch();
			this.form.note.$touch();
			let isValid =
				!this.form.price.$invalid &&
				!this.form.start_date.$invalid &&
				!this.form.note.$invalid;
			return isValid;
		},
	},
	components: {
		CTitle,
		InputCard,

		CTextField,
		CTextArea,
		CDatePicker,
	},
};
</script>

<style scoped lang="scss">
.sales__type__container {
	display: flex;
	justify-content: space-between;
}
</style>
