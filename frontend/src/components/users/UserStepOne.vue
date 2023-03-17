<template>
  <div>
    <div class="d-flex flex-column flex-md-row">
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required', $tr('firstname'))"
          placeholder="firstname"
          v-model="user.firstname.$model"
          :rules="rules.firstnameRule(user, $root)"
          type="textfield"
          class="me-md-2"
        />
      </div>
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required', $tr('lastname'))"
          placeholder="lastname"
          v-model="user.lastname.$model"
          :rules="rules.lastnameRule(user, $root)"
          type="textfield"
          class="ms-md-2"
        />
      </div>
    </div>
    <div class="d-flex flex-column flex-md-row">
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required', $tr('username'))"
          placeholder="username"
          v-model="user.username.$model"
          :rules="rules.usernameRule(user, $root)"
          :error-messages="usernameErrorMessage"
          type="textfield"
          class="me-md-2"
        />
      </div>
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required', $tr('email'))"
          placeholder="email"
          v-model="user.email.$model"
          :rules="rules.emailRule(user, $root)"
          type="textfield"
          class="ms-md-2"
          :error-messages="emailErrorMessage"
        />
      </div>
    </div>
    <div class="d-flex flex-column flex-md-row">
      <div class="w-full">
        <PhoneNumberInput
          :label="$tr('label_required', $tr('phone'))"
          v-model="user.phone.$model"
          :rules="rules.phoneRule(user, $root)"
          class="me-md-2"
          :error-messages="phoneErrorMessage"
        />
      </div>
      <div class="w-full">
        <PhoneNumberInput
          :label="$tr('label_required', $tr('whatsapp'))"
          :placeholder="$tr('whatsapp')"
          v-model="user.whatsapp.$model"
          :rules="rules.whatsappRule(user, $root)"
          class="ms-md-2"
          :error-messages="whatsappErrorMessage"
        />
      </div>
    </div>

    <div v-if="!isEdit">
      <v-checkbox
        :trueValue="true"
        :falseValue="false"
        class="mt-0 pt-0"
        v-model="user.generate_password.$model"
        :label="$tr(`generate_password`)"
        :value="true"
      >
      </v-checkbox>
      <div class="d-md-flex" v-if="!user.generate_password.$model">
        <CustomInput
          :label="$tr('label_required', $tr('password'))"
          placeholder="password"
          v-model="user.password.$model"
          :rules="validate(user.password, $root, 'password')"
          type="password"
        />
      </div>
      <div class="d-md-flex" v-if="!user.generate_password.$model">
        <CustomInput
          :label="$tr('label_required', $tr('confirm_password'))"
          placeholder="confirm_password"
          v-model="user.confirm_password.$model"
          :rules="validate(user.confirm_password, $root, 'confirm_password')"
          type="password"
        />
      </div>
      <v-checkbox
        :trueValue="true"
        :falseValue="false"
        class="mt-0 pt-0"
        v-model="user.change_password.$model"
        :label="$tr(`change_password_first_sign_in`)"
        :value="true"
      >
      </v-checkbox>
      <v-checkbox
        :trueValue="true"
        :falseValue="false"
        class="mt-0 pt-0"
        v-model="user.email_password.$model"
        :label="$tr(`email_the_password`)"
        :value="true"
      >
      </v-checkbox>
    </div>
  </div>
</template>

<script>
import CustomInput from "../design/components/CustomInput";
import PhoneNumberInput from "../design/components/PhoneNumberInput";
import HandleException from "../../helpers/handle-exception";
import GlobalRules from "~/helpers/vuelidate";

export default {
  name: "UserStepOne",
  components: { CustomInput, PhoneNumberInput },
  props: {
    user: {
      required: true,
      type: Object,
    },
    rules: {
      required: true,
      type: Object,
    },
    isEdit: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      validate: GlobalRules.validate,
      usernameErrorMessage: "",
      emailErrorMessage: "",
      phoneErrorMessage: "",
      whatsappErrorMessage: "",
    };
  },

  methods: {
    // check validations for first step in user validations
    async checkValidations() {
      this.user.$touch();
      const firstnameInvalid = this.user?.firstname.$invalid;
      const lastnameInvalid = this.user?.lastname.$invalid;
      const usernameInvalid = this.user?.username.$invalid;
      const emailInvalid = this.user?.email.$invalid;
      const phoneInvalid = this.user?.phone.$invalid;
      const whatsappInvalid = this.user?.whatsapp.$invalid;

      let passwordInvalid = false;

      if (!this.user?.generate_password.$model && this.isEdit === false) {
        const password1Invalid = this.user?.password.$invalid;
        const confirmPasswordInvalid = this.user?.confirm_password.$invalid;
        passwordInvalid = password1Invalid || confirmPasswordInvalid;
      }

      let accountSectionHasError =
        firstnameInvalid ||
        lastnameInvalid ||
        passwordInvalid ||
        usernameInvalid ||
        emailInvalid ||
        phoneInvalid ||
        whatsappInvalid;

      if (!accountSectionHasError) {
        accountSectionHasError = await this.checkColumns();
      }
      return !accountSectionHasError;
    },

    // check column uniqueness from server
    async checkColumns() {
      const uniqueNess = await this.checkUniqueNess();
      // check username
      const isUsernameAlreadyExists = uniqueNess.isUsernameAlreadyExists;
      if (isUsernameAlreadyExists) {
        this.usernameErrorMessage = this.$tr(
          "item_already_exists",
          this.$tr("username")
        );
      } else {
        this.usernameErrorMessage = "";
      }

      // check email address
      const isEmailAlreadyExists = uniqueNess.isEmailAlreadyExists;
      if (isEmailAlreadyExists) {
        this.emailErrorMessage = this.$tr(
          "item_already_exists",
          this.$tr("email")
        );
      } else {
        this.emailErrorMessage = "";
      }

      // check phone number
      const isPhoneAlreadyExists = uniqueNess.isPhoneAlreadyExists;
      if (isPhoneAlreadyExists) {
        this.phoneErrorMessage = this.$tr(
          "item_already_exists",
          this.$tr("phone")
        );
      } else {
        this.phoneErrorMessage = "";
      }

      // check whatsapp number
      const isWhatsappAlreadyExists = uniqueNess.isWhatsappAlreadyExists;
      if (isWhatsappAlreadyExists) {
        this.whatsappErrorMessage = this.$tr(
          "item_already_exists",
          this.$tr("whatsapp")
        );
      } else {
        this.whatsappErrorMessage = "";
      }

      return (
        isUsernameAlreadyExists ||
        isEmailAlreadyExists ||
        isPhoneAlreadyExists ||
        isWhatsappAlreadyExists
      );
    },

    // check for unique ness of some columns
    async checkUniqueNess() {
      try {
        const username = this.user?.username.$model;
        const email = this.user?.email.$model;
        const phone = this.user?.phone.$model;
        const whatsapp = this.user?.whatsapp.$model;

        const columns = [
          { columnName: "username", value: username },
          { columnName: "email", value: email },
          { columnName: "phone", value: phone },
          { columnName: "whatsapp", value: whatsapp },
        ];
        let url = "users/check-uniqueness";
        if (this.isEdit) {
          const userId = this.user.$model.id;
          url = `users/check-uniqueness?userId=${userId}`;
        }
        const response = await this.$axios.post(url, columns);
        return {
          isUsernameAlreadyExists: response?.data?.username,
          isEmailAlreadyExists: response?.data?.email,
          isPhoneAlreadyExists: response?.data?.phone,
          isWhatsappAlreadyExists: response?.data?.whatsapp,
        };
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },
  },
};
</script>

<style scoped></style>
