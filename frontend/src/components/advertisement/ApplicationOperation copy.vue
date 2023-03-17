<template>
	<v-dialog v-model="model" persistent max-width="1300" width="1300">
		<v-card class="main-Card px-3">
			hhhhhhhhhhhhh
			<v-card-title
				primary-title
				class="pb-1 my-0 title d-flex justify-space-between"
				style="padding: 5px 10px 10px"
			>
				<h2 class="text-h5 font-weight-bold">
					{{
						isEdit
							? $tr("update_item", $tr("platform"))
							: $tr("create_item", $tr("platform"))
					}}
				</h2>
				<CloseBtn @click="toggle" type="button" />
			</v-card-title>
			<v-card-text
				class="position-relative card-pos"
				style="height: 650px; overflow-y: auto"
			>
				<v-form ref="insertForm" lazy-validation>
					<div class="d-flex flex-column">
						<div class="w-full">
							<CustomInput
								@blur="$v.application.platform.$touch()"
								v-model="$v.application.platform.$model"
								:rules="validate($v.application.platform, $root, 'platform')"
								:label="$tr('label_required', $tr('platform'))"
								:items="platforms"
								item-value="id"
								item-text="platform_name"
								return-object
								:placeholder="$tr('choose_item', $tr('platform'))"
								type="autocomplete"
								:loading="isFetchingPlatforms"
								:platform="true"
							/>
							<CustomInput
								:label="$tr('label_required', $tr('name'))"
								placeholder="Application Name"
								v-model="$v.application.name.$model"
								:rules="validate($v.application.name, $root, 'name')"
								type="textfield"
							/>
							<CustomInput
								:label="$tr('label_required', $tr('client_id'))"
								placeholder="Client ID"
								v-model="$v.application.client_id.$model"
								:rules="validate($v.application.client_id, $root, 'client_id')"
								type="textfield"
							/>
							<CustomInput
								:label="$tr('label_required', $tr('client_secret'))"
								placeholder="Client Secret"
								v-model="$v.application.client_secret.$model"
								:rules="
									validate($v.application.client_secret, $root, 'client_secret')
								"
								type="textfield"
							/>
							<div
								v-if="
									application &&
									application.platform &&
									application.platform.platform_name === 'google ads'
								"
							>
								<CustomInput
									:label="$tr('label_required', $tr('developer_token'))"
									:placeholder="$tr('google_ads_developer_token')"
									v-model="$v.application.developer_token.$model"
									:rules="
										validate(
											$v.application.developer_token,
											$root,
											'google_ads_developer_token'
										)
									"
									type="textfield"
								/>
							</div>
							<div
								v-if="
									application &&
									application.platform &&
									application.platform.platform_name === 'tiktok'
								"
							>
								<CustomInput
									:label="$tr('label_required', $tr('rid'))"
									:placeholder="$tr('rid')"
									v-model="$v.application.rid.$model"
									:rules="validate($v.application.rid, $root, 'tiktok_rid')"
									type="textfield"
								/>
							</div>
							<div
								v-if="
									application &&
									application.platform &&
									application.platform.platform_name === 'snapchat'
								"
							>
								<CustomInput
									:label="$tr('label_required', $tr('organization_id'))"
									placeholder="Snapchat Organization ID"
									v-model="$v.application.organization_id.$model"
									:rules="
										validate(
											$v.application.organization_id,
											$root,
											'organization_id'
										)
									"
									type="textfield"
								/>
							</div>
							<div>
								<CustomInput
									:items="fetchedUsers"
									item-value="id"
									multiple
									user
									item-text="firstname"
									:label="$tr('label_required', $tr('users'))"
									placeholder="Users"
									v-model="$v.application.users.$model"
									:rules="validate($v.application.users, $root, 'users')"
									type="select"
									return-object
									:loading="isFetchingUsers"
								/>
								<div class="selected d-flex flex-wrap">
									<SelectedItem
										v-for="selectedMember in $v.application.users.$model"
										:key="selectedMember.id"
										@close="() => removeUser(selectedMember.id)"
										:title="`${selectedMember.firstname} ${selectedMember.lastname}`"
										logo-name=""
										:logo-url="selectedMember.image"
									/>
								</div>
							</div>
						</div>
					</div>
				</v-form>
			</v-card-text>
			<v-card-actions class="pa-3">
				<v-spacer></v-spacer>
				<v-btn
					color="primary"
					class="stepper-btn px-3"
					style="min-width: 160px"
					@click="validateForm"
					:loading="isSubmitting"
					:disable="isSubmitting"
				>
					<template v-slot:loader>
						<span>
							<span>
								{{ $tr("submitting") + "..." }}
							</span>
							<v-progress-circular
								class="ma-0"
								indeterminate
								color="white"
								size="25"
								:width="2"
							/>
						</span>
					</template>
					{{ $tr("submit") }}
				</v-btn>
				<v-btn
					@click="toggle"
					color="error"
					class="stepper-btn"
					:type="'button'"
				>
					{{ $tr("cancel") }}
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import Validations from "../../validations/validations";
import CustomInput from "../design/components/CustomInput.vue";
import GlobalRules from "~/helpers/vuelidate";
import { requiredIf } from "vuelidate/lib/validators";
import SelectedItem from "../design/components/SelectedItem.vue";

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
			},
		};
	},

	validations: {
		application: {
			platform: Validations.requiredValidation,
			name: Validations.requiredValidation,
			client_id: Validations.requiredValidation,
			client_secret: Validations.requiredValidation,
			users: Validations.requiredValidation,
			organization_id: {
				required: requiredIf((value) => {
					return value.platform && value.platform.platform_name == "snapchat";
				}),
			},
			rid: {
				required: requiredIf((value) => {
					return value.platform && value.platform.platform_name == "tiktok";
				}),
			},
			developer_token: {
				required: requiredIf((value) => {
					return value.platform && value.platform.platform_name == "google ads";
				}),
			},
		},
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
			if (this.model) {
				this.fetchPlatformNames();
				this.fetchUsers();
			}
		},

		async fetchPlatformNames() {
			try {
				this.isFetchingPlatforms = true;
				const url = "advertisement/platforms?only_platforms=true";
				const { data } = await this.$axios.get(url);
				this.platforms = data.platforms;
			} catch (error) {}
			this.isFetchingPlatforms = false;
		},

		async fetchUsers() {
			try {
				this.isFetchingUsers = true;
				const { data } = await this.$axios.get(
					"/advertisement/applications/users"
				);
				this.fetchedUsers = data.users;
			} catch (error) {}
			this.isFetchingUsers = false;
		},

		removeUser(id) {
			this.application.users = this.application.users.filter(
				(user) => user.id !== id
			);
		},
		resetForm() {
			if (this.$refs.insertForm) {
				this.$refs.insertForm.reset();
			}
		},

		validateForm() {
			this.$refs.insertForm.validate();
			this.$v.application.$touch();
			if (!this.$v.application.$invalid) {
				this.isSubmitting = true;
				this.authenticatePlatforms();
			}
		},

		authenticatePlatforms() {
			const { platform_name } = this.application.platform;
			const { client_id, rid } = this.application;
			const app_data = {
				...this.application,
				users: this.application?.users.map((user) => user.id),
			};
			const jsonData = JSON.stringify(app_data);
			sessionStorage.setItem("application_data", jsonData);
			let authenticateUrl = "";
			switch (platform_name) {
				case "facebook":
					authenticateUrl = `https://www.facebook.com/v8.0/dialog/oauth?client_id=${client_id}&redirect_uri=https://clientfrontend.oredoh.org/advertisement/applications&scope=ads_management&response_type=code`;
					break;
				case "tiktok":
					authenticateUrl = `https://ads.tiktok.com/marketing_api/auth?app_id=${client_id}&state=your_custom_params&redirect_uri=https://clientfrontend.oredoh.org/advertisement/applications&rid=${rid}`;
					break;
				case "snapchat":
					authenticateUrl = `https://accounts.snapchat.com/accounts/oauth2/auth?scope=snapchat-marketing-api&response_type=code&client_id=${client_id}&redirect_uri=https://clientfrontend.oredoh.org/advertisement/applications`;
					break;
				case "google ads":
					authenticateUrl = `https://accounts.google.com/o/oauth2/v2/auth?redirect_uri=https://clientfrontend.oredoh.org/advertisement/applications&prompt=consent&response_type=code&client_id=${client_id}&scope=https://www.googleapis.com/auth/adwords&access_type=offline`;
					break;
				case "google analytics":
					authenticateUrl = `https://accounts.google.com/o/oauth2/v2/auth?redirect_uri=https://clientfrontend.oredoh.org/advertisement/applications&prompt=consent&response_type=code&client_id=${client_id}&access_type=offline&scope=https://www.googleapis.com/auth/analytics`;
					break;
			}
			window.open(authenticateUrl, "_parent");
		},
	},
	components: { CloseBtn, CustomInput, SelectedItem },
};
</script>
