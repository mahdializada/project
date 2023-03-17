<template>
	<v-container>
		<v-row dense>
			<v-col cols="12">
				<CSelect
					@blur="form.landing_page_style.$touch()"
					v-model="form.landing_page_style.$model"
					:rules="
						validateRule(
							form.landing_page_style, // validate objec
							$root, // context
							'landing_page_style' // lable for feedback
						)
					"
					:title="$tr('label_required', $tr('landing_page_style'))"
					:items="landing_styles"
					item-value="id"
					item-text="name"
					:placeholder="$tr('choose_item', $tr('landing_page_style'))"
				/>
			</v-col>
			<v-col cols="12" lg="6">
				<CSelect
					@blur="form.display_language_id.$touch()"
					v-model="form.display_language_id.$model"
					:rules="
						validateRule(
							form.display_language_id, // validate objec
							$root, // context
							'display_language_id' // lable for feedback
						)
					"
					:title="$tr('label_required', $tr('display_language'))"
					:items="languages"
					item-value="id"
					item-text="name"
					:placeholder="$tr('choose_item', $tr('display_language'))"
					prepend-inner-icon="fa fa-globe"
				/>
			</v-col>

			<v-col cols="12" lg="6">
				<CSelect
					@blur="form.sale_currency.$touch()"
					v-model="form.sale_currency.$model"
					:rules="
						validateRule(
							form.sale_currency, // validate objec
							$root, // context
							'sale_currency' // lable for feedback
						)
					"
					:title="$tr('sale_currency')"
					:items="currencies"
					item-value="id"
					item-text="name"
					:placeholder="$tr('choose_item', $tr('sale_currency'))"
					prepend-inner-icon="mdi-currency-rupee"
				/>
			</v-col>
			<v-col cols="12">
				<CTextArea
					:title="$tr('landing_page_note')"
					placeholder="landing_page_note"
					v-model.trim="form.landing_page_note.$model"
					:rules="
						validateRule(
							form.landing_page_note, // validate objec
							$root, // context
							'landing_page_note' // lable for feedback
						)
					"
					prepend-inner-icon="mdi-clipboard-text-outline"
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

export default {
	name: "StoreInfo",
	props: {
		form: Object, // default prop
	},
	components: {
		CustomInput,
		CTextArea,
		CSelect,
	},
	data() {
		return {
			landing_styles: [
				{ id: "simple", name: this.$tr("simple") },
				{ id: "good", name: this.$tr("good") },
				{ id: "nice", name: this.$tr("nice") },
			],
			languages: [],
			currencies: [
				{ id: "dollor", name: this.$tr("dollor") },
				{ id: "afg", name: this.$tr("afghani") },
			],
			validateRule: GlobalRules.validate,
		};
	},
	created() {
		this.fetchLanguage();
	},
	methods: {
		async validate() {
			// validate function must validate this step here and return true of false
			this.form.landing_page_style.$touch();
			this.form.display_language_id.$touch();
			this.form.sale_currency.$touch();
			this.form.landing_page_note.$touch();

			return (
				!this.form.landing_page_style.$invalid &&
				!this.form.display_language_id.$invalid &&
				!this.form.sale_currency.$invalid &&
				!this.form.landing_page_note.$invalid
			);
		},
		async fetchLanguage() {
			try {
				const url = "common/languages";
				const { data } = await this.$axios.get(url);
				this.languages = data.languages;
			} catch (error) {}
		},
	},
};
</script>

<style scoped></style>
