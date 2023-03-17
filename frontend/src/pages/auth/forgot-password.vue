<template>
  <client-only>
    <div>
      <LogPageWrapper>
        <template slot="form">
          <v-card
            color="white"
            class="
              pa-1
              d-flex
              flex-column
              justify-space-around
              pt-3
              pb-2
              px-3
              white--text
            "
            :evaluation="0"
            max-width="430"
            height="450"
            style="
              background-color: rgba(255, 255, 255, 0.1) !important ;
              backdrop-filter: blur(1.5px);
              border-radius: 20px;
            "
          >
            <v-card-title class="mb-2 d-block pt-0">
              <p class="mb-1 text-h4">{{ $tr("forgot_password") }}</p>
              <p class="mb-0 text-subtitle-2">
                {{ $tr("enter_email_to_reset_password") }}
              </p>
            </v-card-title>
            <!-- sign in form -->
            <v-card-text class="pb-0 justify-self-end">
              <v-form ref="form" @submit.prevent="submit">
                <v-text-field
                  v-model.trim="$v.form.email.$model"
                  :placeholder="$tr('email_or_username')"
                  filled
                  rounded
                  dense
                  class="signIn"
                  outlined
                  :error="!this.showVerify ? true : false"
                  prepend-inner-icon="fas fa-user"
                  :append-icon="showVerify ? `mdi-check-circle` : ''"
                  @input="$v.form.email.$touch()"
                  @blur="$v.form.email.$touch()"
                  :rules="Rules.emailRule($v.form, $root)"
                  color="green"
                >
                </v-text-field>
                <div class="text-right mb-2 mt-4">
                  <router-link :to="'/auth/signin'" class="linkSignIn">
                    {{ $tr("back_to_sign_in") }}
                  </router-link>
                </div>

                <!-- </div> -->
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-btn
                class="white--text"
                block
                x-large
                rounded
                color="#e8630b"
                :loading="isLoading"
                :disabled="isLoading"
                @click="submit"
                >{{ $tr("send_otp_code") }}</v-btn
              >
            </v-card-actions>
          </v-card>
        </template>
      </LogPageWrapper>
    </div>
  </client-only>
</template>

<script>
/*
|---------------------------------------------------------------------
| Forgot Page Component
|---------------------------------------------------------------------
|
| Template to send email to remember/replace password
|
*/
import TextField from "../../components/common/TextField";
import Validations from "~/validations/validations";
import Rules from "~/validations/rules";
import LogPageWrapper from "../../components/signin/LogPageWrapper.vue";

export default {
  meta: {
    hasAuth: false,
  },
  auth: false,
  layout: "auth",
  components: { TextField, LogPageWrapper },
  data() {
    return {
      Rules: Rules,
      // reset button
      isLoading: false,
      showVerify: false,
      // form
      isFormValid: true,

      form: {
        email: "",
      },
    };
  },
  validations: {
    form: {
      email: Validations.emailValidation,
    },
  },

  watch: {
    ["form.email"]: function (val) {
      this.showVerify = false;
      if (this.validMail(val)) {
        this.showVerify = true;
      }
    },
  },

  methods: {
    validMail(mail) {
      return /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(
        mail
      );
    },
    checkValidations() {
      if (this.$v.form.$invalid) {
        this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
        return false;
      }
      return true;
    },
    async submit() {
      if (!this.checkValidations()) {
        return;
      }
      try {
        this.isLoading = true;
        const response = await this.$axios.post("forgot-password", this.form);
        if (response.status === 200 && response.data.result) {
          // const data = response.data.data
          // this.$toastr.s(this.$tr("reset_link_sent"));
			this.$toasterNA("green", this.$tr('reset_link_sent'));

          this.$router.push("/");
        }
      } catch (error) {
        if (error.response.status == 422) {
          // return server errors
          const errors = error.response.data.errors;
          for (let error in errors) {
            for (let er in errors[error]) {
              // this.$toastr.e(this.$tr(errors[error][er]));
			          this.$toasterNA("red",this.$tr(errors[error][er]));

            }
          }
        } else {
          // this.$toastr.e(this.$tr(error.response.statusText));
          this.$toasterNA("red",this.$tr(error.response.statusText));

        }
      }
      this.isLoading = false;
    },
    emailRules(data) {
      return [
        (_) =>
          data.email.required ||
          this.$tr("item_is_required", this.$tr("email")),
        (_) =>
          data.email.email || this.$tr("item_is_invalid", this.$tr("email")),
      ];
    },
  },
};
</script>
<style scoped>
.v-sheet.v-card:not(.v-sheet--outlined) {
  box-shadow: unset;
}
.theme--light.v-card {
  background-color: rgba(232, 221, 221, 0.804);
  color: rgba(0, 0, 0, 0.87);
}
#myVideo {
  position: fixed;
  right: 0;
  bottom: 0;
  min-width: 100%;
  min-height: 100%;
}
</style>
<style>
.linkSignIn {
  text-decoration: none;
  color: var(--v-surface-lighten5) !important;
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
</style>
