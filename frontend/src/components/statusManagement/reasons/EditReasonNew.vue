<template>
	<div>
		<div class="h-full d-flex align-center flex-column justify-center mt-5">
			<CTitle :text="`reason`" />

			<CTextArea
				v-model="form.title.$model"
        :title="$tr('reason')"
				:placeholder="'Please give us more details...'"
				prepend-inner-icon="mdi-text-box"
				:rules="validateRule(form.title, $root, $tr('title'))"
				:invalid="form.title.$invalid" />
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTextArea from "../../new_form_components/Inputs/CTextArea.vue";

export default {
	name: "SectionStep",
	props: {
		form: Object, // default prop
	},
	data() {
		return {
			validateRule: GlobalRules.validate, // gloabl function fro validate
		};
	},
	methods: {
		async validate() {
			this.form.title.$touch();
			return !this.form.title.$invalid;
		},
	},

	components: { CTitle, InputCard, CTextArea },
};
</script>

<style>
.form-custom-text-area .v-input__prepend-inner {
	margin-top: 12px;
	margin-right: 8px !important;
}
.form-custom-text-area .v-input__slot,
.form-custom-text-area .v-input__slot fieldset {
	border-radius: 12px !important;
}
.form-custom-text-area .v-counter {
	position: absolute;
	top: -20px;
	right: 8px;
	padding: 0;
	color: var(--v-primary-base);
}
.v-application--is-rtl .form-custom-text-area .v-counter {
	right: unset;
	left: 8px;
}
.form-custom-text-area.has-append .v-input__append-inner {
	margin-top: auto !important;
	margin-bottom: auto !important;
}
.customTextArea .v-input__icon.v-input__icon--prepend-inner .v-icon {
	color: var(--v-primary-base) !important;
	margin-top: auto !important;
	margin-bottom: auto !important;
}
</style>
