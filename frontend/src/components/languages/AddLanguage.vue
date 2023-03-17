<template>
	<v-form
		lazy-validation
		ref="languageRegisterForm"
		@submit.prevent="submitForm"
	>
		<CustomStepper
			:headers="headers"
			@close="() => {}"
			:canNext="false"
			@validate="() => {}"
			:is-submitting="false"
			ref="registerLanguageStepper"
			@submit="onSubmit"
			:skip="[]"
			@onSkip="() => {}"
			:isSubmitting="isSubmitting"
		>
			<template v-slot:step1>
				<div class="language">
					<div class="w-full">
						<CustomInput
							:items="getAllLanguages"
							v-model="$v.language.selectedLangaugeId.$model"
							:rules="rules.addLanguageRule($v.language, $root, 'language')"
							item-value="country_language_id"
							item-text="language_native"
							language
							:label="$tr('label_required', $tr('language'))"
							:placeholder="$tr('choose_item', $tr('language'))"
							ref="languageInput"
							type="autocomplete"
							class="mb-md-2 mb-0"
							@change="addSelectedLanguage"
						/>
					</div>
					<div class="selected d-flex flex-wrap">
						<template v-for="lang in selectedLanguage">
							<SelectedItem
								v-if="lang"
								:key="lang.country_language_id"
								@close="() => removeSelectedLanguage(lang.country_language_id)"
								:title="`${lang.language_native} (${lang.country_name})`"
								:flag="`${lang.iso2.toLowerCase()}`"
							/>
						</template>
					</div>

					<div class="w-full">
						<CustomInput
							disabled
							:items="directions"
							v-model="$v.language.direction.$model"
							:rules="rules.directionRule($v.language, $root, 'direction')"
							item-value="id"
							item-text="name"
							:label="$tr('direction')"
							:placeholder="$tr('choose_item', $tr('direction'))"
							ref="directionInput"
							type="autocomplete"
							class="mb-md-2 mb-0"
						/>
					</div>
				</div>
			</template>
			<template v-slot:step2>
				<DoneMessage
					:title="$tr('item_all_set', $tr('language'))"
					:subTitle="$tr('you_can_access_your_item', $tr('language'))"
				/>
			</template>
		</CustomStepper>
	</v-form>
</template>
<script>
import CustomStepper from "~/components/design/FormStepper/CustomStepper.vue";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import CustomInput from "~/components/design/components/CustomInput.vue";
import SelectedItem from "~/components/design/components/SelectedItem.vue";
import { mapActions, mapGetters } from "vuex";
import Helpers from "~/helpers/helpers";
import Validations from "~/validations/language_validations.js";

export default {
	components: {
		CustomStepper,
		DoneMessage,
		CustomInput,
		SelectedItem,
	},
	data() {
		return {
			rules: Validations.rules,
			language: {
				direction: "",
				selectedLangaugeId: "",
			},
			headers: [
				{
					icon: "fa-globe",
					title: "language",
					slotName: "step1",
				},
				{
					icon: "fa-thumbs-up",
					title: "done",
					slotName: "step2",
				},
			],
			languages: [],
			// selectedLangaugeId: "",
			selectedLanguage: [],

			directions: [
				{
					id: "ltr",
					name: "LTR",
				},
				{
					id: "rtl",
					name: "RTL",
				},
			],
			direction: "ltr",
			isSubmitting: false,
		};
	},

	props: {
		tabKey: {
			type: String,
		},
	},

	async fetch() {
		await this.fetchAllLanguages({ itemsPerPage: -1, withCountries: true });
	},
	computed: {
		...mapGetters({
			getAllLanguages: "languages/getAllLanguages",
		}),
	},
	validations: {
		language: Validations.validations.language,
	},
	methods: {
		...mapActions({
			fetchAllLanguages: "languages/fetchAllLanguages",
			insertLanguage: "languages/insertLanguageDb",
		}),
		addSelectedLanguage() {
			this.selectedLanguage = [];
			let langs = JSON.parse(JSON.stringify(this.getAllLanguages));
			if (
				!this.selectedLanguage.find((lang) => {
					return lang.country_language_id == this.language.selectedLangaugeId;
				})
			) {
				let language = langs.find((lang) => {
					return lang.country_language_id == this.language.selectedLangaugeId;
				});
				this.selectedLanguage.push(language);
				this.language.direction = language ? language.dir : "";
			}
		},
		removeSelectedLanguage(id) {
			this.selectedLanguage = this.selectedLanguage?.filter((lang) => {
				return lang.country_language_id !== id;
			});
			this.language.selectedLangaugeId = "";
			this.language.direction = "";
		},

		async onSubmit() {
			this.$refs.languageRegisterForm.validate();
			let formData = Helpers.getFormData(this.language);
			if (
				!this.$v.language.direction.$invalid &&
				!this.$v.language.selectedLangaugeId.$invalid
			) {
				try {
					this.isSubmitting = true;
					let res = await this.insertLanguage({
						formData,
						tabKey: this.tabKey,
					});
					if (res) {
						this.reset();
						this.$refs.registerLanguageStepper.forceNext();
					} else {
						// this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr('something_went_wrong'));

					}
					this.isSubmitting = false;
				} catch (error) {
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
					this.$toasterNA("red", this.$tr('something_went_wrong'));

				}
			}
		},

		reset() {
			this.$refs.languageRegisterForm.reset();
			this.selectedLanguage = [];
		},
	},
};
</script>
