<template>
	<div class="w-full h-full d-flex justify-center pt-4">
		<div class="w-full">
			<div class="mb-2 d-flex flex-column flex-lg-row">
				<div class="w-full me-lg-1 mb-2 mb-lg-0">
					<CTextField
						v-model="form.arabic_name.$model"
						:title="$tr('label_required', $tr('arabic_name'))"
						:placeholder="$tr('arabic_name')"
						prepend-inner-icon="mdi-view-grid"
					/>

				</div>
				<div class="w-full ms-lg-1">
					<CTextField
						v-model="form.name.$model"
						:title="$tr('label_required', $tr('english_name'))"
						:placeholder="$tr('english_name')"
						prepend-inner-icon="mdi-view-grid"
						:rules="validateRule(form.name, $root, $tr('english_name'))"
						:invalid="form.name.$invalid"
					/>
				
				</div>

			</div>
			<div class="mb-2 d-flex flex-column flex-lg-row">
				<div class="w-full me-lg-1 mb-2 mb-lg-0">
					<CFileInputSingle
						v-model="form.icon.$model"
						:title="$tr('upload_icon')"
						:placeholder="$tr('upload_icon_here...')"
						prepend-inner-icon="mdi-cloud-upload"
						accept="image/*"
						:rules="validateRule(form.icon, $root, 'icon')"
						:invalid="form.icon.$invalid"
					/>
				</div>
				
			</div>
			<div class="mb-2">
				<CTextArea
					v-model="form.arabic_description.$model"
					:title="$tr('arabic_description')"
					:placeholder="$tr('arabic_description')"
					:disabled="form.arabic_name.$model?false:true"
					prepend-inner-icon="mdi-information"
				/>
				
			</div>
			<div class="mb-2">
				<CTextArea
					v-model="form.description.$model"
					:disabled="!form.arabic_name.$model?false:true && form.name.$model?false:true  "

					:title="$tr('english_description')"
					:placeholder="$tr('english_description')"
					prepend-inner-icon="mdi-information"
					:rules="validateRule(form.description, $root, $tr('english_description'))"
					:invalid="form.description.$invalid"
				/>
			</div>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../../new_form_components/Inputs/CTitle.vue";
import CTextField from "../../../new_form_components/Inputs/CTextField.vue";
import CTextArea from "../../../new_form_components/Inputs/CTextArea.vue";
import CFileInputSingle from "../../../new_form_components/Inputs/CFileInputSingle.vue";
export default {
	props: {
		form: Object,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
			file: null,
		};
	},
	methods: {
		async loaded() {},
		async validate() {
			this.form.name.$touch();
			// this.form.arabic_name.$touch();
			this.form.icon.$touch();
			// this.form.banner.$touch();
			this.form.description.$touch();
			// this.form.arabic_description.$touch();
			return (
				!this.form.name.$invalid &&
				// !this.form.arabic_name.$invalid &&
				!this.form.icon.$invalid &&
				// !this.form.banner.$invalid &&
				// !this.form.arabic_description.$invalid &&
				!this.form.description.$invalid
			);
		},
	},
	components: { CTitle, CTextField, CTextArea, CFileInputSingle },
};
</script>
