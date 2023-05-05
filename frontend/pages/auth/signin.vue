<template>
	<client-only>
		<LogPageWrapper :signKey="signKey">
			<template slot="form">
				<v-card
					color="white"
					class="pa-1 d-flex flex-column justify-space-around pt-5 pb-4 px-3 white--text"
					:evaluation="0"

					max-width="430"
					height="450"
					style="
						background-color: rgba(255, 255, 255, 0.1) !important;
						backdrop-filter: blur(1.5px);
						border-radius: 20px;
					">

					<lottie-player
						v-show="aniPass"
						src="/assets/loginAni.json"
						background="transparent"
						id="animationPassword"
						speed="1.5"
						style="
							position: absolute;
							left: -221px;
							top: -50px;
							width: 300px;
							height: 631px;
						">
					</lottie-player>

					<lottie-player
						v-show="aniSuccess"
						src="/assets/successLogin.json"
						background="transparent"
						id="animationSuccess"
						speed="1.5"
						style="
							position: absolute;
							left: -221px;
							top: -50px;
							width: 300px;
							height: 631px;
							z-index: 10000;
						">
					</lottie-player>

					<lottie-player
						v-show="aniError"
						src="/assets/errorLogin.json"
						background="transparent"
						id="animationError"
						speed="1.5"
						style="
							position: absolute;
							left: -221px;
							top: -50px;
							width: 300px;
							height: 631px;
							z-index: 10000;
						">
					</lottie-player>

					<v-card-title class="mb-2 d-block pt-0">
						<p class="mb-1 text-h4">{{ $tr("welcome") }}</p>
						<p class="mb-0 text-subtitle-2">
							{{ $tr("sign_in_to_your_account") }}
						</p>
					</v-card-title>
					<!-- sign in form -->
					<v-card-text class="pb-0 justify-self-end">
						<v-form @submit.prevent="submit" lazy-validation>
							<v-text-field
								v-model.trim="email"
								:placeholder="$tr('email_or_username')"
								filled
								rounded
								dense
								class="signIn"
								outlined
								:error="
									this.error.email || this.invalidCreds || !this.showVerify
										? true
										: false
								"
								prepend-inner-icon="fas fa-user"
								:append-icon="showVerify ? `mdi-check-circle` : ''"
								:error-messages="emailErrors"
								@input="$v.email.$touch()"
								@blur="$v.email.$touch()"
								@focus="focusEmail"
								color="green">
							</v-text-field>

							<v-text-field
								v-model.trim="password"
								:append-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
								:type="showPass ? 'text' : 'password'"
								rounded
								@keyup.enter="submit"
								dense
								class="signIn"
								outlined
								color="green"
								prepend-inner-icon="fa-solid fa-lock"
								:error="this.error.password || this.invalidCreds ? true : false"
								:error-messages="passwordErrors"
								@click:append="showPass = !showPass"
								@input="$v.password.$touch()"
								@focus="focusPassword"
								@blur="$v.password.$touch()"></v-text-field>

							<div
								class="mb-4 d-flex justify-space-between align-center white--text">
								<span
									class="d-flex align-center rememberMe"
									@click="rememberClick">
									<v-icon v-if="rememberMe" color="white"
										>mdi-checkbox-marked</v-icon
									>
									<v-icon v-else color="white"
										>mdi-checkbox-blank-outline</v-icon
									>
									<p class="mb-0 ms-1">{{ $tr("remember_me") }}</p>
								</span>
								<v-icon></v-icon>
								<span
									@click="
										() => {
											$router.push('/auth/forgot-password');
											signKey++;
										}
									"
									class="linkSignIn"
									color="white"
									>{{ $tr("forgot_password") }}</span
								>
							</div>
						</v-form>
					</v-card-text>
					<v-card-actions>
						<v-btn
							type="submit"
							class="white--text btnSignin"
							block
							x-large
							rounded
							color="#e8630b"
							:disabled="isLoading"
							@click="submit"
							:loading="isLoading">
							{{ $tr("sign_in") }}
						</v-btn>
					</v-card-actions>
				</v-card>
			</template>
		</LogPageWrapper>
	</client-only>
</template>

<script>
import LogPageWrapper from "../../components/signin/LogPageWrapper.vue";
import { validationMixin } from "vuelidate";
import { required, email, minLength } from "vuelidate/lib/validators";
export default {
	meta: {
		hasAuth: false,
	},
	auth: false,
	layout: "auth",
	mixins: [validationMixin],
	components: { LogPageWrapper },
	validations: {
		email: { required },
		password: { required, minLength: minLength(4) },
	},

	watch: {
		email: function (val) {
			this.showVerify = false;
			if (this.onValidUsername(val)) {
				this.showVerify = true;
			}
			if (this.validMail(val)) {
				this.showVerify = true;
			}
		},
	},

	data() {
		return {
			signKey: 1,
			showVerify: false,
			rememberMe: true,
			disableLoginButton: true,
			error: {},
			invalidCreds: "",
			networkError: false,
			email: "",
			password: "",
			isLoading: false,
			showPass: false,
			gettingLocation: false,
			location: null,
			errorStr: null,
			fetch: true,
			show1: false,
			aniPass: true,
			aniSuccess: false,
			aniError: false,
		};
	},
	created() {
		// if (process.client) this.getGeolocation();
	},
	methods: {
		focusEmail() {
			this.aniPass = true;
			this.aniError = false;
			this.aniSuccess = false;
			let play = document.querySelector("#animationPassword");
			play.setDirection(-1);
			play.getLottie().play();
		},
		focusPassword() {
			this.aniPass = true;
			this.aniError = false;
			this.aniSuccess = false;
			let play = document.querySelector("#animationPassword");
			play.setDirection(1);
			play.getLottie().stop();
			play.getLottie().play();
		},
		validMail(mail) {
			return /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(
				mail,
			);
		},
		onValidUsername(val) {
			const usernameRegex = /^\D[a-zA-Z0-9_]+$/;
			return usernameRegex.test(val);
		},

		rememberClick() {
			this.rememberMe = !this.rememberMe;
		},
		async submit() {
			this.$v.$touch();
			if (!this.$v.$invalid) {
				await this.signIn();
			}
		},
		async signIn() {
			this.aniSuccess = true;
			this.aniPass = false;
			this.aniError = false;
			let play = document.querySelector("#animationSuccess");
			play.getLottie().stop();
			play.getLottie().play();

			this.isLoading = true;
			await this.$auth
				.loginWith("local", {
					data: {
						email_username: this.email,
						password: this.password,
						browser: this.detectBrowser(),
						latitude: this.latitude,
						longitude: this.longitude,
					},
				})
				.then((res) => {
					localStorage.removeItem("company_popup_dialog");
					this.$router.push("/");
					// this.$toastr.s(this.$tr("successfully_logged_in"));
					this.$toasterNA("green", this.$tr("successfully_logged_in"));
					this.isLoading = false;
				})
				.catch(async (err) => {
					this.aniSuccess = false;
					this.aniPass = false;
					this.aniError = true;
					let play = document.querySelector("#animationError");
					play.getLottie().stop();
					await play.getLottie().play();
					this.showVerify = false;
					this.isLoading = false;
					if (err.response) {
						if (err.response.status == 422) {
							this.error = err.response.data.errors;
							if (this.error.password) {
								// this.$toastr.e(this.$tr(this.error.password[0]));
								this.$toasterNA("red", this.$tr(this.error.password[0]));
							}
							if (this.error.email) {
								// this.$toastr.e(this.$tr(this.error.email[0]));
								this.$toasterNA("red", this.$tr(this.error.email[0]));
							}
						} else if (
							err.response.status === 401 &&
							err.response.data.timeLimit
						) {
							// this.$toastr.e(this.$tr("not_allowed_at_current_time"));
							this.$toasterNA("red", this.$tr("not_allowed_at_current_time"));
						} else if (err.response.status == 401) {
							this.invalidCreds = err.response.data.message;
							// this.$toastr.e(this.invalidCreds);
							this.$toasterNA("red", this.invalidCreds);
						} else if (err.response.status == 406) {
							// this.$toastr.e(this.$tr("account_not_activated"));
							this.$toasterNA("red", this.$tr("account_not_activated"));
						} else if (err.response.status == 500) {
							this.networkError = true;
							this.invalidCreds = err.response.data.message;
							// this.$toastr.e(this.$tr("server_error_please_try_again"));
							this.$toasterNA("red", this.$tr("server_error_please_try_again"));
						} else if (err.response.status == 404) {
							this.invalidCreds = err.response.data.message;
							// this.$toastr.e(
							// 	this.$tr("account_warning_password_incorrect_5_times"),
							// );
							this.$toasterNA(
								"red",
								this.$tr("account_warning_password_incorrect_5_times"),
							);
						} else if (err.response.status == 405) {
							this.invalidCreds = err.response.data.message;
							// this.$toastr.e(this.$tr("account_in_warning_cant_login"));

							this.$toasterNA("red", this.$tr("account_in_warning_cant_login"));
						} else if (err.response.status == 406) {
							this.invalidCreds = err.response.data.message;
							// this.$toastr.e(this.$tr("account_in_inactive_cant_login"));
							this.$toasterNA(
								"red",
								this.$tr("account_in_inactive_cant_login"),
							);
						} else if (err.response.status == 307) {
							this.invalidCreds = err.response.data.message;
							// this.$toastr.e(
							// 	this.$tr("account_in_warning_because_country_changes"),
							// );

							this.$toasterNA(
								"red",
								this.$tr("account_in_warning_because_country_changes"),
							);
						} else if (err.response.status == 308) {
							this.invalidCreds = err.response.data.message;
							// this.$toastr.e(this.$tr("please_turn_on_location"));
							this.$toasterNA("red", this.$tr("please_turn_on_location"));
						}
					} else if (err.message == "Network Error") {
						this.networkError = true;
						// this.$toastr.e(this.$tr("network_error_please_try_again"));
						this.$toasterNA("red", this.$tr("network_error_please_try_again"));
					}
				});
		},

		detectBrowser() {
			let userAgent = navigator.userAgent;
			let browserName;

			if (userAgent.match(/chrome|chromium|crios/i)) {
				browserName = "chrome";
			} else if (userAgent.match(/firefox|fxios/i)) {
				browserName = "firefox";
			} else if (userAgent.match(/safari/i)) {
				browserName = "safari";
			} else if (userAgent.match(/opr\//i)) {
				browserName = "opera";
			} else if (userAgent.match(/edg/i)) {
				browserName = "edge";
			} else {
				browserName = "No browser detection";
			}
			return browserName;
		},
		async getGeolocation() {
			if (!("geolocation" in navigator)) {
				this.errorStr = "Geolocation is not available.";
			} else {
				this.gettingLocation = true;
				await navigator.geolocation.getCurrentPosition(
					async (pos) => {
						this.gettingLocation = false;
						this.latitude = pos.coords.latitude;
						this.longitude = pos.coords.longitude;
					},
					async (err) => {
						this.gettingLocation = false;
						this.errorStr = err.message;
						return this.$nuxt.error({
							statusCode: 403,
						});
					},
				);
			}
		},
	},
	computed: {
		emailErrors() {
			const errors = [];
			if (!this.$v.email.$dirty) return errors;
			!this.$v.email.required &&
				errors.push(
					this.$tr("item_is_required", this.$tr("email_or_username")),
				);
			return errors;
		},
		passwordErrors() {
			const errors = [];
			if (!this.$v.password.$dirty) return errors;
			!this.$v.password.required &&
				errors.push(this.$tr("item_is_required", this.$tr("password")));
			return errors;
		},
	},
};
</script>
<style>
.linkSignIn {
	text-decoration: none;
	color: var(--v-surface-lighten5) !important;
}
.rememberMe:hover,
.linkSignIn:hover {
	cursor: pointer;
}
.signIn .v-input__slot {
	background: white !important;
	padding: 0px 12px !important;
}
.signIn .v-icon {
	font-size: 15px !important;
}
.signIn .v-messages__wrapper {
	color: white !important;
}
.btnSignin.theme--light.v-btn.v-btn--disabled.v-btn--has-bg {
	background-color: #e8630b !important;
}
.btnSignin .v-btn__loader {
	color: var(--v-friqiBase-base);
}
</style>
