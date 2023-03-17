<template>
	<v-dialog v-model="dialog" persistent width="800" height="50vh">
		<v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
			<v-card-title class="py-1">
				<span class="custom-dialog-title">{{ $tr("profile") }}</span>
				<v-spacer />
				<CloseBtn @click="seProfileUser()" />
			</v-card-title>

			<v-card-text class="pb-1">
				<v-card
					elevation="0"
					style="
						top: 0;
						position: sticky;
						z-index: 10000000;
						max-height: 270px;
						margin-bottom: 10px;
					"
				>
					<ProgressBar v-if="showProgressBar" />
					<!-- edit botton -->

					<!-- end edit botton -->

					<div class="text-center pb-2" style="position: relative">
						<v-avatar size="100" tile rounded class="mt-1">
							<img
								:src="this.image"
								:alt="firstname"
								class="rounded"
								style="border: 1px solid #bbb; border-radius: 60px !important"
							/>
						</v-avatar>

						<div
							class="d-flex flex-column align-center pt-1"
							style="
								position: relative;
								top: 0px;
								z-index: 10000003;
								overflow: hidden;
							"
						>
							<div class="title text-h5 font-weight-bold mb-1">
								{{ this.firstname + " " }} {{ user.lastname }}
							</div>
						</div>
						<v-col cols="12 py-0" style="">
							<v-divider class="grey lighten-1"></v-divider>
							<div class="d-flex flex-column justify-space-between mt-2">
								<div class="">
									<div class="d-flex full-width justify-space-between pb-1">
										<div>
											<v-icon color="primary">mdi-email-outline</v-icon>
											<a :href="`mailto:${user.email}`">{{ user.email }}</a>
										</div>
										<div>
											<v-icon color="primary">mdi-cellphone</v-icon>
											<a :href="`tel:${user.phone}`">{{ user.phone }}</a>
										</div>
										<div>
											<v-icon color="primary">mdi-whatsapp</v-icon>
											<a :href="`https://wa.me/${user.whatsapp}`">
												{{ user.whatsapp }}</a
											>
										</div>
									</div>
								</div>
								<v-divider class="grey lighten-1"></v-divider>
								<div class="d-flex full-width justify-space-between pt-1">
									<div>
										<v-icon color="primary">mdi-map-marker</v-icon>
										<span>
											<span> {{ $tr("address") }} : </span>
											<span class="font-weight-bold">{{ user.address }} </span>
										</span>
									</div>
									<v-btn
										color="primary"
										class="rounded-sm mx-0 pa-2 float-right"
										small
										@click="dialog1Func()"
									>
										<v-icon left small> fa-edit </v-icon>
										<!-- <v-icon @click="dialog1Func()" class="" right small >
                      mdi-account-edit
                    </v-icon> -->
										<span class="text-subtitle-2">{{
											$tr("edit_your_profile")
										}}</span>
									</v-btn>
								</div>
							</div>
						</v-col>
					</div>
				</v-card>

				<div
					class="white pt-2 px-2 rounded-sm"
					style="overflow-y: scroll; overflow-x: hidden; height: 300px"
				>
					<div class="d-flex justify-space-between mx-2 profile-details pb-1">
						<span class="text-subtitle-1">{{ $tr("details") }}</span>
					</div>

					<v-row>
						<v-col>
							<div class="ms-2 d-flex">
								<h3>{{ $tr("gender") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">{{
									user.gender
								}}</span>
							</div>
							<div class="ms-2 mt-1 d-flex">
								<h3>{{ $tr("country") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">{{
									user.country.name
								}}</span>
							</div>
							<div class="ms-2 mt-1 d-flex">
								<h3>{{ $tr("current_country") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">{{
									user.current_country.name
								}}</span>
							</div>
							<div class="ms-2 mt-1 d-flex">
								<h3>{{ $tr("state") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">{{
									user.state.name
								}}</span>
							</div>
							<div class="ms-2 mt-1 d-flex" v-if="user.companies.length > 0">
								<h3>{{ $tr("company") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">
									<v-tooltip bottom>
										<template v-slot:activator="{ on, attrs }">
											<span
												v-bind="attrs"
												v-on="on"
												style="white-space: nowrap"
												class="primary--text"
											>
												{{ $tr("companies") }}
											</span>
										</template>
										<span
											v-for="(company, index) in user.companies"
											:key="index"
											>{{ company.name }} <br
										/></span>
									</v-tooltip>
								</span>
							</div>
						</v-col>
						<v-col>
							<div class="ms-2 d-flex">
								<h3>{{ $tr("username") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">{{
									user.username
								}}</span>
							</div>
							<div class="ms-2 mt-1 d-flex">
								<h3>{{ $tr("created_by") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">{{
									user.created_by == null ? "" : getfullName
								}}</span>
							</div>
							<div class="ms-2 mt-1 d-flex">
								<h3>{{ $tr("created_at") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">{{
									user.created_at.substring(0, 10)
								}}</span>
							</div>
							<div class="ms-2 mt-1">
								<h3 class="float-left">{{ $tr("last_update") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">{{
									user.updated_at.substring(0, 10)
								}}</span>
							</div>
							<div class="ms-2 mt-1 d-flex" v-if="user.teams.length > 0">
								<h3>{{ $tr("team") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">
									<v-tooltip bottom>
										<template v-slot:activator="{ on, attrs }">
											<span
												v-bind="attrs"
												v-on="on"
												style="white-space: nowrap"
												class="primary--text"
											>
												{{ $tr("teams") }}
											</span>
										</template>
										<span v-for="(team, index) in user.teams" :key="index"
											>{{ team.name }} <br
										/></span>
									</v-tooltip>
								</span>
							</div>
							<div class="ms-2 mt-1 d-flex" v-if="user.roles.length > 0">
								<h3>{{ $tr("role") }}:</h3>
								<span class="text-primary ps-1" style="font-size: 1rem">
									<v-tooltip bottom>
										<template v-slot:activator="{ on, attrs }">
											<span
												v-bind="attrs"
												v-on="on"
												style="white-space: nowrap"
												class="primary--text"
											>
												{{ $tr("roles") }}
											</span>
										</template>
										<span v-for="(role, index) in user.roles" :key="index"
											>{{ role.name }} <br
										/></span>
									</v-tooltip>
								</span>
							</div>
							<!-- <div v-if="user.gender=='male'"><h1>hi</h1></div>
              <div v-if="user.gender=='female'"><h1>bye</h1></div> -->
						</v-col>
					</v-row>
				</div>

				<!-- edit -->
				<div>
					<v-dialog v-model="dialog1" width="500" height="50vh">
						<v-card>
							<v-card-title style="padding-top: 0px; padding-bottom: 0px">
								<span class="custom-dialog-title">{{
									$tr("edit_profile")
								}}</span>
								<v-spacer />
								<CloseBtn @click="cancel()" />
							</v-card-title>
							<v-card-text class="d-flex flex-column justify-center pa-0">
								<div
									class="pa-0 rounded text-center mx-auto mb-2"
									style="height: 250px; width: 250px"
								>
									<div>
										<vue-avatar
											:width="400"
											:height="400"
											ref="vueavatar"
											@vue-avatar-editor:image-ready="onImageReady"
											:borderRadius="borderRadius"
											:border="2"
											:style="{ 'background-image': 'url(' + this.image + ')' }"
										>
											<v-icon
												@click="dialogPhoto = true"
												class="mx-1 mb-2 primary pa-1"
												color="white"
												size="25"
												style="border-radius: 50px; opacity: 1 !important"
												>mdi-camera</v-icon
											>
										</vue-avatar>

										<!--  -->
										<v-dialog v-model="dialogPhoto" width="600" height="50vh">
											<v-card
												:color="$vuetify.theme.dark ? 'black' : '#f9fafd'"
											>
												<v-card-title class="py-1">
													<span class="custom-dialog-title">{{
														$tr("profile photo")
													}}</span>
													<v-spacer />
													<CloseBtn @click="dialogPhoto = false" />
												</v-card-title>

												<v-card-text
													height="600"
													width="200"
													class="d-flex justify-center align-center"
												>
													<img
														width="500"
														class="pa-2"
														:src="
															this.cropedImage == null
																? this.image
																: this.cropedImage
														"
														alt=""
													/>
												</v-card-text>
											</v-card>
										</v-dialog>
										<!-- <br /> -->
										<vue-avatar-scale
											ref="vueavatarscale"
											@vue-avatar-editor-scale:change-scale="onChangeScale"
											:min="1"
											:max="3"
											class="my-1"
										>
										</vue-avatar-scale>
										<br />
									</div>
								</div>
								<div class="d-flex justify-end mt-2 mx-2">
									<v-btn
										color="primary"
										class="mx-1"
										outlined
										@click="updateUserProfilePhoto(user.id)"
										:disabled="clickDiabled"
									>
										<v-icon @click="updateUserProfilePhoto(user.id)"></v-icon
										>{{ $tr("upload_photo") }}</v-btn
									>
									<v-btn class="primary" text @click="saveClicked()">{{
										$tr("crop")
									}}</v-btn>
								</div>

								<div class="mt-2">
									<v-tabs fixed-tabs>
										<v-tab @click="(tab1 = true), (tab2 = false)">{{
											$tr("general_information")
										}}</v-tab>
										<v-tab @click="(tab1 = false), (tab2 = true)">{{
											$tr("change_password")
										}}</v-tab>

										<v-tab-item class="ps-2">
											<v-card class="pt-2" style="height: 250px" :elevation="0">
												<CustomInput
													:label="$tr('label_required', $tr('username'))"
													placeholder="username"
													type="textfield"
													class="me-md-2"
													:disabled="this.disabled"
													v-model.trim="$v.username.$model"
													:rules="viewNameRule($v.username)"
												/>

												<v-icon
													@click.prevent="disabledFun()"
													style="position: relative; top: -55px; left: 89%"
													>mdi-pencil</v-icon
												>
											</v-card>
										</v-tab-item>

										<!-- change password by nasim -->
										<v-tab-item class="ps-2">
											<v-card
												class="pt-2"
												style="
													overflow-y: scroll;
													overflow-x: hidden;
													height: 250px;
												"
												:elevation="0"
											>
												<v-form ref="form">
													<CustomInput
														:label="
															$tr('label_required', $tr('current_password'))
														"
														placeholder="current_password"
														v-model.trim="$v.currentPassword.$model"
														:rules="currentpasswordRule($v.currentPassword)"
														type="password"
														class="me-md-2"
													/>
													<CustomInput
														:label="$tr('label_required', $tr('new_password'))"
														placeholder="new_password"
														v-model.trim="$v.newPassword.$model"
														:rules="newPasswordRule($v.newPassword)"
														type="password"
														class="me-md-2"
													/>
													<CustomInput
														:label="
															$tr('label_required', $tr('confirm_new_password'))
														"
														placeholder="confirm_new_password"
														v-model.trim="$v.passwordConfirmation.$model"
														:rules="
															passwordConfirmationRule($v.passwordConfirmation)
														"
														type="password"
														class="me-md-2"
													/>
													<!-- <div class="error" v-if="!$v.passwordConfirmation.sameAsPassword">Passwords must be identical.</div> -->
												</v-form>
											</v-card>
										</v-tab-item>
										<!-- end of change password by nasim -->
									</v-tabs>
									<div class="d-flex justify-end mt-1 mr-1">
										<v-btn
											v-if="tab2"
											color="primary"
											class="mx-1"
											:loading="loadingBtn"
											@click="updateUserPassword(user.id)"
										>
											<v-icon>mdi-lock-plus</v-icon
											>{{ $tr("change_password") }}</v-btn
										>
										<v-btn
											v-if="tab1"
											color="success"
											class="mx-1"
											@click="updateUserProfile(user.id)"
											:disabled="disabled"
										>
											<v-icon>mdi-content-save</v-icon>
											{{ $tr("save_changes") }}</v-btn
										>
										<v-btn
											color="primary"
											class="mx-1 mb-1"
											outlined
											@click="cancel()"
											>{{ $tr("cancel") }}</v-btn
										>
									</div>
								</div>
							</v-card-text>
						</v-card>
					</v-dialog>
				</div>
			</v-card-text>
		</v-card>
	</v-dialog>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CloseBtn from "../design/Dialog/CloseBtn";
import VueAvatar from "../../components/vue-avatar-editor/src/components/VueAvatar.vue";
import VueAvatarScale from "../../components/vue-avatar-editor/src/components/VueAvatarScale.vue";
import CustomInput from "../design/components/CustomInput";

import {
	maxLength,
	minLength,
	required,
	not,
	sameAs,
} from "vuelidate/lib/validators";
// import { validPassword } from "~/validations/validations";

import Helpers from "~/helpers/helpers";
import ProgressBar from "~/components/common/ProgressBar.vue";

export default {
	components: { CloseBtn, VueAvatar, VueAvatarScale, CustomInput, ProgressBar },
	data() {
		return {
			loadingBtn: false,
			dialog: false,
			img3: null,
			// image
			firstname: "",
			username: this.$auth.user.username,
			usernameAuth: this.$auth.user.username,
			lastname: this.$auth.user.lastname,

			email: this.$auth.user.email,
			dialog: false,
			fullname: "",
			image: this.$auth.user.image,
			user: {},
			data: [],
			disEmail: true,
			dis: true,
			photoEdit: false,
			image2: null,

			// photo crop
 
     
			dialog1: false,
			loaded: false,
			borderRadius: 10000,
			cropedImage: null,
			cropedImageForBackend: null,
			imageBeforCrop: null,
			clickDiabled: true,
			showProgressBar: false,
			showEdit: false,
			dialogPhoto: false,
			imageShow: this.$auth.user.image,

			// password
			currentPassword: "",
			newPassword: "",
			passwordConfirmation: "",
			//
			tab2: false,
			tab1: true,
			disabled: true,
		};
	},
	computed: {
		...mapGetters({
			profileUser: "users/profileUser",
		}),
		getfullName() {
			return (
				this.user.created_by.firstname + " " + this.user.created_by.lastname
			);
		},
	},
	methods: {
		//
		disabledFun() {
			this.disabled = !this.disabled;
		},
		//vuex
		...mapActions({
			seProfileUser: "users/seProfileUser",
		}),
		//avatar
		onChangeScale(scale) {
			this.$refs.vueavatar.changeScale(scale);
		},
		saveClicked() {
      if(this.$refs.vueavatar.state.image.resource == null){
        // this.$toastr.e(this.$tr("please select an Image"));
				this.$toasterNA("red", this.$tr('please select an Image'));

      }
      else{
			var img = this.$refs.vueavatar.getImageScaled();
			this.cropedImage = img.toDataURL();
			// this.image = img.toDataURL();
			this.image2 = img.toDataURL();
			this.clickDiabled = false;

			this.cropedImageForBackend = dataURLtoFile(
				this.cropedImage,
				"teebalhoor"
			);
			// this.$toastr.s(this.$tr("Succeefully Cropped"));
			this.$toasterNA("green", this.$tr('Succeefully Cropped'));

			this.imageShow = this.cropedImageForBackend;
      }
		},

		onImageReady(scale) {
			this.$refs.vueavatarscale.setScale(scale);
		},
		//
		//crop dialog
		dialog1Func() {
			this.dialog1 = !this.dialog1;
			this.loaded = false;
			this.avatar = null;
			this.cropedImage = null;

			// this.image = this.$auth.user.image;
		},
		cancel() {
			this.dialog1 = !this.dialog1;
      this.$refs.vueavatar.state.image.resource = null ;  
      this.$refs.vueavatar.showOverlay=true ;
      this.clickDiabled = true;
		},
		// main
		undis() {
			this.dis = !this.dis;
		},
		undisEmail() {
			this.disEmail = !this.disEmail;
		},

		//save changes
		async updateUserProfilePhoto(id) {
      this.clickDiabled = true;
			const data = {
        id: this.$auth.user.id,
				image: this.cropedImageForBackend,
			};
			if (this.$refs.vueavatar.state.image.resource == null) {
        // this.$toastr.e(this.$tr("please select a cropped photo"));
				this.$toasterNA("red", this.$tr('please select a cropped photo'));

			} else {
        this.showProgressBar = true;
        const userProfileData = Helpers.getFormData(data);
				try {
					const response = await this.$axios.post(
						"users/updateUserProfilePhoto/id",
						userProfileData
					);
					this.showProgressBar = false;
					this.image = this.image2;
					this.$emit("changeImage", this.image);
					// this.$toastr.s(this.$tr("Succeefully your photo uploaded"));
			this.$toasterNA("green", this.$tr('Succeefully your photo uploaded'));

				} catch (error) {}
			}
		},

		// username
		async updateUserProfile(id) {
			// this.$refs.formusername.validate();
			this.$v.$touch();
			if (!this.$v.username.$invalid) {
				const data = {
					id: this.$auth.user.id,
					username: this.username,
				};
				try {
					const response = await this.$axios.post(
						"users/profileUserEdit/id",
						data
					);
					this.disabled = true;
					// this.$toastr.s(this.$tr("Succeefully Updated"));
			this.$toasterNA("green", this.$tr('Succeefully Updated'));

				} catch (error) {
					// this.$toastr.w(this.$tr("The username already exite"));
			this.$toasterNA("orange", this.$tr('The username already exist'));

					this.username = this.usernameAuth;
				}
				this.username = this.username;
			} else {
			}
		},

		//change password
		async updateUserPassword(id) {
			this.$refs.form.validate();
			this.$v.$touch();
			if (!this.$v.$invalid) {
				const data = {
					id: this.$auth.user.id,
					currentPassword: this.currentPassword,
					password: this.newPassword,
					password_confirmation: this.passwordConfirmation,
				};
				this.loadingBtn = true;
				try {
					const response = await this.$axios.post(
						"users/changePassword/id",
						data
					);
					// this.passwordConfirmation = "";
					// this.currentPassword = "";
					// this.newPassword = "";
					this.$refs.form.reset();
					// this.$toastr.s(this.$tr("password_changed_successfully"));
			this.$toasterNA("green", this.$tr('password_changed_successfully'));

				} catch (error) {
					if (error?.response.data.errors.currentPassword != null) {
						let err = error?.response.data.errors.currentPassword;
						// this.$toastr.e(err);
				this.$toasterNA("red", this.$tr(err));

					} else if (error?.response.data.errors.password != null) {
						let err = error?.response.data.errors.password;
						// this.$toastr.e(err);
				this.$toasterNA("red", this.$tr(err));

					} else {
						let err = error?.response.data.errors;
						// this.$toastr.e(err);
				this.$toasterNA("red", this.$tr(err));

					}
				}
			} else {
			}
			this.loadingBtn = false;
		},
		//validate
		viewNameRule(username) {
			return [
				(_) =>
					username.required ||
					this.$tr("item_is_required", this.$tr("username")),
				(_) =>
					username.minLength ||
					this.$tr("min_length", this.$tr("username"), "6"),
			];
		},
		currentpasswordRule(currentPassword) {
			return [
				(_) =>
					currentPassword.required ||
					this.$tr("item_is_required", this.$tr("current_password")),
				// (_) =>
				//   currentPassword.validPassword ||
				//   this.$tr("password_must_contain", this.$tr("current_password")),
				(_) =>
					currentPassword.minLength ||
					this.$tr("min_length", this.$tr("current_password"), "6"),
			];
		},
		passwordConfirmationRule(passwordConfirmation) {
			return [
				(_) =>
					passwordConfirmation.required ||
					this.$tr("item_is_required", this.$tr("confirm_new_password")),
				// (_) =>
				//   passwordConfirmation.validPassword ||
				//   this.$tr("password_must_contain", this.$tr("confirm_new_password")),
				(_) =>
					passwordConfirmation.minLength ||
					this.$tr("min_length", this.$tr("confirm_new_password"), "6"),
				(_) =>
					passwordConfirmation.sameAsPassword ||
					this.$tr(
						"The Confirm Password Must Be Same The New Password",
						this.$tr("confirm_new_password")
					),
			];
		},
		newPasswordRule(newPassword) {
			return [
				(_) =>
					newPassword.required ||
					this.$tr("item_is_required", this.$tr("new_password")),
				// (_) =>
				//   newPassword.validPassword ||
				//   this.$tr("password_must_contain", this.$tr("new_password")),
				(_) =>
					newPassword.minLength ||
					this.$tr("min_length", this.$tr("new_password"), "6"),
				(_) =>
					newPassword.isNotSameAsCurrentPassword ||
					this.$tr(
						"New password not be the same of current password",
						this.$tr("new_password")
					),
			];
		},
	},
	watch: {
		profileUser: function (value) {
			this.dialog = value;
		},
	},
	created() {
		this.fullname = this.$auth.user.firstname + " " + this.$auth.user.lastname;
		this.user = this.$auth.user;
		this.username = this.$auth.user.username;
		this.firstname = this.$auth.user.firstname;
		if (this.$auth.user.gender == "male") {
			this.img3 = require("../../static/images/male.png");
		} else {
			this.img3 = require("../../static/images/female.png");
		}
	},
	validations: {
		username: { required, minLength: minLength(6) },
		currentPassword: { required, minLength: minLength(6) },
		newPassword: {
			required,
			minLength: minLength(6),
			isNotSameAsCurrentPassword: not(sameAs("currentPassword")),
		},
		passwordConfirmation: {
			required,
			minLength: minLength(6),
			sameAsPassword: sameAs("newPassword"),
		},
	},
};
</script>
<style scoped>
.card-background {
	background: var(--stepper-header-bg) !important;
}

.active-status-btn {
	width: 4.5rem;
	height: 4.5rem;
	/* background: #37b34a; */
	padding: 10px;
	position: absolute !important;
	right: 10rem !important;
	top: 250px;
	transform: translateY(-50%);
	box-shadow: 0 0 8px 2px rgb(0 0 0 / 15%);
}

.profile-details {
	border-bottom: 2px solid var(--input-border-color);
}

.details_title {
	font-size: 1rem !important;
	font-weight: bold !important;
}

.details_value {
	font-size: 0.9rem !important;
	font-weight: 500;
	color: var(--input-border-color);
}

.list-item {
	padding-top: 0px;
}
a {
	text-decoration: none;
	color: black !important;
}
</style>
