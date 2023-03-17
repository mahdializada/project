<template>
  <v-dialog v-model="oredohUnAuthorized" width="420" persistent>
    <v-card>
      <v-card-title style="border-bottom: 1px solid #f0eff1" class="py-1 px-2">
        <p class="mb-0" style="font-size: 17; color: #777; font-weight: 600">
          Login
        </p>
        <v-spacer> </v-spacer>
        <v-btn icon plain @click="ChangeAuthorization(false)">
          <v-icon size="30">mdi-close</v-icon></v-btn
        >
      </v-card-title>
      <v-card-text class="pt-2 pb-1">
        <div
          class="rounded d-flex align-center"
          style="background-color: #fefaef; width: 100%; padding: 12px"
        >
          <v-avatar color="#fab629" size="50">
            <span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20.115"
                height="22.133"
                viewBox="0 0 20.115 22.133"
              >
                <path
                  id="lock-icon"
                  d="M-17748.988-10607.868a3.019,3.019,0,0,1-3.012-3.016v-7.044a3.019,3.019,0,0,1,3.012-3.012h1.016v-3.024a5.974,5.974,0,0,1,1.555-4.046,6.015,6.015,0,0,1,3.871-1.96,6.013,6.013,0,0,1,4.184,1.145,6.041,6.041,0,0,1,2.33,3.658.993.993,0,0,1-.779,1.176,1,1,0,0,1-1.182-.779,4.018,4.018,0,0,0-4.352-3.208,4.015,4.015,0,0,0-3.627,4.015v3.024h11.076a3.02,3.02,0,0,1,3.012,3.012v7.044a3.02,3.02,0,0,1-3.012,3.016Zm-1.012-10.06v7.044a1.015,1.015,0,0,0,1.012,1.016h14.092a1.013,1.013,0,0,0,1.01-1.016v-7.044a1.012,1.012,0,0,0-1.01-1.011h-14.092A1.014,1.014,0,0,0-17750-10617.928Z"
                  transform="translate(17752 10630.001)"
                  fill="#fff"
                />
              </svg>
            </span>
          </v-avatar>
          <div class="ps-2">
            <p
              style="
                color: #ebc062;
                font-size: 16px;
                font-weight: 500;
                margin-bottom: 3px;
              "
              class="text-capital"
            >
              login needed!
            </p>
            <p class="mb-0 grey--text text--darken-2" style="font-size: 14px">
              please sign in first to the Content Management System.
            </p>
          </div>
        </div>

        <div class="py-2 px-3">
          <p
            class="text-center grey--text text--darken-2"
            style="font-size: 18px; font-weight: 600"
          >
            Login to your account
          </p>
          <v-form>
            <p class="mb-1" style="font-size: 15px; font-weight: 500">
              {{ $tr("email_or_username") }}
            </p>
            <v-text-field
              class="rounded-sm rohullah"
              filled
              outlined
              :placeholder="$tr('email_or_username')"
              clearable
              dense
              v-model.trim="email"
              rounded
              :error="this.error.email || this.invalidCreds ? true : false"
              prepend-inner-icon="mdi-account"
              :error-messages="emailErrors"
              @input="$v.email.$touch()"
              @blur="$v.email.$touch()"
            ></v-text-field>

            <p class="mb-1" style="font-size: 15px; font-weight: 500">
              {{ $tr("password") }}
            </p>
            <v-text-field
              v-model.trim="password"
              :append-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showPass ? 'text' : 'password'"
              class="rounded-sm rohullah"
              filled
              outlined
              :placeholder="$tr('password')"
              clearable
              dense
              rounded
              @keyup.enter="submit"
              color="green"
              prepend-inner-icon="mdi-lock"
              :error="this.error.password || this.invalidCreds ? true : false"
              :error-messages="passwordErrors"
              @click:append="showPass = !showPass"
              @input="$v.password.$touch()"
              @blur="$v.password.$touch()"
            ></v-text-field>
            <div class="d-flex align-start">
              <v-icon style="opacity: 0.3">mdi-information-outline</v-icon>
              <p
                class="mb-2 ps-1 grey--text text--darken-2"
                style="font-size: 14px"
              >
                If you do not have an account, please contact your
                administrator.
              </p>
            </div>

            <v-btn
              class="rounded-sm"
              color="primary"
              style="font-size: 15px; font-weight: 500"
              block
              @click="submit"
              :loading="isLoading"
              >{{ $tr("sign_in") }}</v-btn
            >
            <v-btn
              class="rounded-sm mt-2"
              text
              style="font-size: 15px; font-weight: 500"
              block
              @click="ChangeAuthorization(false)"
              >{{ $tr("cancel") }}</v-btn
            >
          </v-form>
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import { required, email, minLength } from "vuelidate/lib/validators";
import { mapState, mapActions } from "vuex";

export default {
  data() {
    return {
      show: true,
      error: {},

      email: "",
      password: "",
      isLoading: false,
      showPass: false,
      invalidCreds: "",
    };
  },

  validations: {
    email: { required },
    password: { required, minLength: minLength(4) },
  },

  computed: {
    ...mapState(["oredohUnAuthorized"]),

    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.required &&
        errors.push(
          this.$tr("item_is_required", this.$tr("email_or_username"))
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
  methods: {
    ...mapActions({
      ChangeAuthorization: "ChangeAuthorization",
    }),

    validMail(mail) {
      return /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(
        mail
      );
    },
    onValidUsername(val) {
      const usernameRegex = /^\D[a-zA-Z0-9_]+$/;
      return usernameRegex.test(val);
    },

    async submit() {
      this.$v.$touch();
      if (!this.$v.$invalid) {
        await this.signIn();
      }
    },

    async signIn() {
      const data = {
        email_username: this.email,
        password: this.password,
        browser: "",
        longitute: "",
        latitute: "",
      };
      try {
        this.isLoading = true;
        const response = await this.$axios2.post("login", data);
        if (response.status == 200) {
          localStorage.setItem("oredoh-token", response.data.token);
          this.isLoading = false;
          this.ChangeAuthorization(false);
        }
      } catch (err) {
        this.isLoading = false;

        if (err.response) {
          if (err.response.status == 422) {
            this.error = err.response.data.errors;
            if (this.error.password) {
              this.$toasterNA("red", this.$tr(this.error.password[0]));
            }
            if (this.error.email) {
              this.$toasterNA("red", this.$tr(this.error.email[0]));
            }
          } else if (
            err.response.status === 401 &&
            err.response.data.timeLimit
          ) {
            this.$toasterNA("red", this.$tr("not_allowed_at_current_time"));
          } else if (err.response.status == 401) {
            this.invalidCreds = err.response.data.message;
            this.$toasterNA("red", this.invalidCreds);
          } else if (err.response.status == 406) {
            this.$toasterNA("red", this.$tr("account_not_activated"));
          } else if (err.response.status == 500) {
            this.networkError = true;
            this.invalidCreds = err.response.data.message;
            this.$toasterNA("red", this.$tr("server_error_please_try_again"));
          } else if (err.response.status == 404) {
            this.invalidCreds = err.response.data.message;

            this.$toasterNA(
              "red",
              this.$tr("account_warning_password_incorrect_5_times")
            );
          } else if (err.response.status == 405) {
            this.invalidCreds = err.response.data.message;

            this.$toasterNA("red", this.$tr("account_in_warning_cant_login"));
          } else if (err.response.status == 406) {
            this.invalidCreds = err.response.data.message;
            this.$toasterNA("red", this.$tr("account_in_inactive_cant_login"));
          } else if (err.response.status == 307) {
            this.invalidCreds = err.response.data.message;

            this.$toasterNA(
              "red",
              this.$tr("account_in_warning_because_country_changes")
            );
          } else if (err.response.status == 308) {
            this.invalidCreds = err.response.data.message;
            this.$toasterNA("red", this.$tr("please_turn_on_location"));
          }
        } else if (err.message == "Network Error") {
          this.networkError = true;
          this.$toasterNA("red", this.$tr("network_error_please_try_again"));
        }
      }
    },
  },
};
</script>

<style>
.rohullah.v-text-field--rounded > .v-input__control > .v-input__slot {
  padding: 0 10px !important;
}
</style>