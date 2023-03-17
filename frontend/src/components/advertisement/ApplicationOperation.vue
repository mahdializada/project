<template>
	<Stepper
		ref="hello"
		:title="$tr('create_item', $tr('application'))"
		cookieName="generate_project"
		:show="model"
		:steps="steps"
		:form="application"
		:validateRules="applicationRules"
		:submit="validateForm"
		@close="
			() => {
				model = false;
				resetForm();
			}
		"
		:showBack="false"
	/>
</template>
<script>
import Stepper from "../horizontal_stepper/Stepper.vue";
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import Validations from "../../validations/validations";
import CustomInput from "../design/components/CustomInput.vue";
import GlobalRules from "~/helpers/vuelidate";
import { requiredIf } from "vuelidate/lib/validators";
import SelectedItem from "../design/components/SelectedItem.vue";
import ApplicationStep from "./ApplicationStep.vue";

export default {
	data() {
		return {
			model: false,
			isEdit: false,
			validate: GlobalRules.validate,
			isSubmitting: false,
			isFetchingPlatforms: false,
			platforms: [],
			isFetchingUsers: false,
			fetchedUsers: [],

			application: {
				platform: null,
				name: null,
				client_id: null,
				client_secret: null,
				organization_id: null,
				rid: null,
				developer_token: null,
				users: [],
				platform_name: null,
				country_id: null,
			},
			applicationRules: {
				form: {
					country_id: Validations.requiredValidation,
					platform: Validations.requiredValidation,
					name: Validations.requiredValidation,
					client_id: Validations.requiredValidation,
					client_secret: Validations.requiredValidation,
					users: Validations.requiredValidation,
					platform_name: "",
					organization_id: {
						required: requiredIf((value) => {
							return value.platform_name == "snapchat";
						}),
					},
					rid: {
						required: requiredIf((value) => {
							return value.platform_name == "tiktok";
						}),
					},
					developer_token: {
						required: requiredIf((value) => {
							return value.platform_name == "google ads";
						}),
					},
				},
			},
			steps: [
				{
					title: this.$tr("application"),
					component: ApplicationStep,
					id: "type",
					props: {
						isEdit: false,
					},
				},
			],
		};
	},

	async created() {},

	methods: {
		toggle(item) {
			this.isSubmitting = false;
			if (item) {
				this.platform = item;
				this.isEdit = true;
			} else {
				if (this.isEdit) {
					this.isEdit = false;
					setTimeout(() => {
						this.resetForm();
					}, 100);
				}
			}
			this.model = !this.model;
		},

		removeUser(id) {
			this.application.users = this.application.users.filter(
				(user) => user.id !== id
			);
		},
		resetForm() {
			this.application = {
				platform: null,
				name: null,
				client_id: null,
				client_secret: null,
				organization_id: null,
				rid: null,
				developer_token: null,
				users: [],
				platform_name: null,
				country_id: null,
			};
		},

		validateForm() {
			this.authenticatePlatforms();
		},

		authenticatePlatforms() {
			const platform_name = this.application.platform_name;
			const { client_id, rid } = this.application;
			const app_data = {
				...this.application,
			};
			const jsonData = JSON.stringify(app_data);

			sessionStorage.setItem("application_data", jsonData);
			let authenticateUrl = "";
			switch (platform_name) {
				case "facebook":
					authenticateUrl = `https://www.facebook.com/v8.0/dialog/oauth?client_id=${client_id}&redirect_uri=https://apps.smartfriqi.com/advertisement/applications&scope=ads_management&response_type=code`;
					break;
				case "tiktok":
					authenticateUrl = `https://ads.tiktok.com/marketing_api/auth?app_id=${client_id}&state=your_custom_params&redirect_uri=https://apps.smartfriqi.com/advertisement/applications&rid=${rid}`;
					break;
				case "snapchat":
					authenticateUrl = `https://accounts.snapchat.com/accounts/oauth2/auth?scope=snapchat-marketing-api&response_type=code&client_id=${client_id}&redirect_uri=https://apps.smartfriqi.com/advertisement/applications`;
					break;
				case "google ads":
					authenticateUrl = `https://accounts.google.com/o/oauth2/v2/auth?redirect_uri=https://apps.smartfriqi.com/advertisement/applications&prompt=consent&response_type=code&client_id=${client_id}&scope=https://www.googleapis.com/auth/adwords&access_type=offline`;
					break;
				case "google analytics":
					authenticateUrl = `https://accounts.google.com/o/oauth2/v2/auth?redirect_uri=https://apps.smartfriqi.com/advertisement/applications&prompt=consent&response_type=code&client_id=${client_id}&access_type=offline&scope=https://www.googleapis.com/auth/analytics`;
					break;
			}
			window.open(authenticateUrl, "_parent");
		},
	},
	components: { Stepper },
};
</script>
