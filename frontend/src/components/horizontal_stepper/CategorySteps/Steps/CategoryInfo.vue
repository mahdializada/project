<template>
	<div class="w-full h-full d-flex justify-center pt-4">
		<div class="w-full">
			<div class="mb-2">
				<CTextField
					v-model="form.name.$model"
					:title="$tr('label_required', $tr('name'))"
					:placeholder="$tr('name')"
					prepend-inner-icon="mdi-view-grid"
					:rules="validateRule(form.name, $root, $tr('name'))"
					:invalid="form.name.$invalid"
				/>
			</div>
			<div class="mb-2 d-flex flex-column flex-lg-row">
				<div class="w-full me-lg-1 mb-2 mb-lg-0">
					<CFileInputSingle
						v-model="form.icon.$model"
						:title="'Upload Icon'"
						:placeholder="'Upload your icon here...'"
						prepend-inner-icon="mdi-cloud-upload"
						accept="image/*"
						:rules="validateRule(form.icon, $root, 'Icon')"
						:invalid="form.icon.$invalid"
					/>
				</div>
				<div class="w-full ms-lg-1">
					<CFileInputSingle
						v-model="form.banner.$model"
						:title="'Upload Banner'"
						:placeholder="'Upload your banner here...'"
						prepend-inner-icon="mdi-cloud-upload"
						accept="image/*"
						:rules="validateRule(form.banner, $root, 'Banner')"
						:invalid="form.banner.$invalid"
					/>
				</div>
			</div>
			<div class="mb-2">
				<CTextArea
					v-model="form.description.$model"
					:title="$tr('description')"
					:placeholder="'Please give us more details...'"
					prepend-inner-icon="mdi-information"
					:rules="validateRule(form.description, $root, $tr('name'))"
					:invalid="form.name.$invalid"
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
			this.form.icon.$touch();
			this.form.banner.$touch();
			this.form.description.$touch();
			return (
				!this.form.name.$invalid &&
				!this.form.icon.$invalid &&
				!this.form.banner.$invalid &&
				!this.form.description.$invalid
			);
		},
	},
	components: { CTitle, CTextField, CTextArea, CFileInputSingle },
};
</script>
