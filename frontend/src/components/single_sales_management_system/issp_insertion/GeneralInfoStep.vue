<template>
	<v-container>
		<v-row :dense="true">
			<v-col cols="12">
				<CTextField
					v-model="form.name.$model"
					:min="0"
					title="ISPP Name"
					placeholder="Name"
					prepend-inner-icon="mdi-card-text-outline"
					:rules="
						validateRule(
							form.name, // validate objec
							$root, // context
							$tr('name') // lable for feedback
						)
					"
				/>
			</v-col>
			<v-col cols="12">
				<CTextArea
					:title="$tr('selling_goals')"
					placeholder="selling_goals"
					v-model.trim="form.selling_goals.$model"
					:rules="
						validateRule(
							form.selling_goals, // validate objec
							$root, // context
							$tr('sales_modal') // lable for feedback
						)
					"
					prepend-inner-icon="fa fa-bullseye"
				/>
			</v-col>
			<v-col cols="12" lg="6">
				<CSelect
					@blur="form.sales_modal.$touch()"
					v-model="form.sales_modal.$model"
					:rules="
						validateRule(
							form.sales_modal, // validate objec
							$root, // context
							$tr('sales_modal') // lable for feedback
						)
					"
					:title="$tr('label_required', $tr('sales_modal'))"
					:items="sales_modals"
					item-value="id"
					item-text="name"
					:placeholder="$tr('choose_item', $tr('sales_modal'))"
					prepend-inner-icon="fa fa-cube"
				/>
			</v-col>
			<v-col cols="12" lg="6">
				<CSelect
					@blur="form.creation_type.$touch()"
					v-model="form.creation_type.$model"
					:rules="
						validateRule(
							form.creation_type, // validate objec
							$root, // context
							$tr('sales_modal') // lable for feedback
						)
					"
					:title="$tr('label_required', $tr('creation_type'))"
					:items="creation_type"
					item-value="id"
					item-text="name"
					:placeholder="$tr('choose_item', $tr('creation_type'))"
					type="autocomplete"
					prepend-inner-icon="fa fa-bolt"
				/>
			</v-col>
			<v-col cols="12" lg="6">
				<CSelectMulti
					:items="countries"
					@blur="form.attribute.$touch()"
					v-model="form.attribute.$model"
					:rules="
						validateRule(
							form.attribute, // validate objec
							$root, // context
							$tr('sales_modal') // lable for feedback
						)
					"
					:placeholder="$tr('choose_item', $tr('target sale countries'))"
					:title="$tr('target sale countries')"
					:no-data-text="$tr('no_data_available')"
					item-value="id"
					item-text="name"
					multi
					prepend-inner-icon="fa fa-globe"
				>
				</CSelectMulti>
			</v-col>
			<v-col cols="12" lg="6">
				<CSelect
					@blur="form.company_id.$touch()"
					v-model="form.company_id.$model"
					:rules="
						validateRule(
							form.company_id, // validate objec
							$root, // context
							$tr('sales_modal') // lable for feedback
						)
					"
					:title="$tr('label_required', $tr('company'))"
					:items="companies"
					item-value="id"
					item-text="name"
					:placeholder="$tr('choose_item', $tr('company'))"
					prepend-inner-icon="fa fa-building"
				/>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CustomInput from "../../design/components/CustomInput.vue";
import CTextArea from "../../new_form_components/Inputs/CTextArea";
import CSelect from "../../new_form_components/Inputs/CSelect";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CSelectMulti from "../../new_form_components/Inputs/CSelectMulti.vue";

export default {
	name: "GeneralInfoStep",
	components: {
		CustomInput,
		CTextArea,
		CSelect,
		CTextField,
		CSelectMulti,
	},
	props: {
		form: Object, // default prop
	},
	data() {
		return {
			sales_modals: [
				{ id: "model1", name: this.$tr("model1") },
				{ id: "model2", name: this.$tr("model2") },
			],
			creation_type: [
				{ id: "online", name: this.$tr("online") },
				{ id: "offline", name: this.$tr("offline") },
			],
			countries: [],
			companies: [],

			validateRule: GlobalRules.validate,
		};
	},
	created() {
		this.fetchCountries();
		this.fetchCompanies();
	},

	methods: {
		async fetchCompanies() {
			try {
				const url = "auth-companies";
				const { data } = await this.$axios.get(url);
				this.companies = data;
			} catch (error) {}
		},
		async validate() {
			// validate function must validate this step here and return true of false
			this.form.sales_modal.$touch();
			this.form.selling_goals.$touch();
			this.form.creation_type.$touch();
			this.form.attribute.$touch();
			this.form.name.$touch();
			return (
				!this.form.sales_modal.$invalid &&
				!this.form.selling_goals.$invalid &&
				!this.form.creation_type.$invalid &&
				!this.form.attribute.$invalid &&
				!this.form.name.$invalid
			);
		},
		async fetchCountries() {
			try {
				const url = "common/countries";
				const { data } = await this.$axios.get(url);
				this.countries = data.countries;
			} catch (error) {}
		},
	},
};
</script>

<style scoped></style>
