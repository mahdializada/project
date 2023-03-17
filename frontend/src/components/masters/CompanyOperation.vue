<template>
  <v-card>
    <v-card-title primary-title>
      <span>
        {{
          $tr(
            this.isEdit ? "update_company" : "create_company"
          )
        }}</span
      >
      <v-spacer />
      <CloseDialogButton @click="closeDialog" />
    </v-card-title>
    <v-divider class="mb-4 grey lighten-1" />

    <v-card-text>
      <v-form ref="companyForm" lazy-validation @submit.prevent="onSubmit">
        <v-row>
          <v-col cols="12" sm="6" class="py-0">
            <TextField
              label="textField.company.name"
              v-model.trim="$v.company.name.$model"
              :rules="nameRule($v.company, $root)"
              :invalid="$v.company.name.$invalid"
              :maxLength="$v.company.name.$params.maxLength.max"
            />
          </v-col>
          <v-col cols="12" sm="6" class="py-0">
            <TextField
              label="textField.company.email"
              v-model.trim="$v.company.email.$model"
              :rules="emailRule($v.company, $root)"
              :invalid="$v.company.email.$invalid"
            />
          </v-col>
          <v-col cols="12" sm="6" class="py-0">
            <TextField
              label="textField.company.phone"
              v-model.trim="$v.company.phone.$model"
              :rules="phoneRule($v.company, $root)"
              :invalid="$v.company.phone.$invalid"
            />
          </v-col>
          <v-col cols="12" sm="6" class="py-0">
            <TextField
              label="textField.company.whatsapp"
              v-model.trim="$v.company.whatsapp.$model"
              :rules="whatsappRule($v.company, $root)"
              :invalid="$v.company.whatsapp.$invalid"
            />
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
    <v-divider class="mb-1 grey lighten-1" />
    <v-card-actions>
      <v-spacer />
      <v-btn
        class="ml-1"
        color="primary"
        :disabled="isSaving"
        :loading="isSaving"
        small
        @click="onSubmit"
      >
        {{ $tr("submit") }}
        <v-icon right>mdi-check</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import TextField from "~/components/common/TextField";
import Validations from "~/validations/validations";
import { mapActions, mapGetters } from "vuex";
import HandleException from "~/helpers/handle-exception";
import CloseDialogButton from "~/components/common/buttons/CloseDialogButton";

export default {
  name: "CompanyOperation",
  components: { CloseDialogButton, TextField },
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
    editCompany: {
      type: Object,
      default: () => ({}),
    },
  },

  data() {
    return {
      company: this.isEdit
        ? this.editCompany
        : { name: "", email: "", phone: "", whatsapp: "" },
      isSaving: false,
      actionButtons: {
        icon: "mdi-plus",
        text: this.$tr("submit"),
        color: "primary",
      },
    };
  },

  validations: {
    company: {
      name: Validations.name100Validation,
      email: Validations.emailValidation,
      phone: Validations.phoneValidation,
      whatsapp: Validations.phoneValidation,
    },
  },

  methods: {
    ...mapActions({
      insertNewItem: "companies/insertNewItem",
      updateItem: "companies/updateItem",
    }),

    async onSubmit() {
      this.$refs.companyForm.validate();
      this.$v.company.$touch();
      if (!this.$v.company.$invalid) {
        this.isSaving = true;
        this.isEdit
          ? await this.updateRecord(this.company)
          : await this.insertRecord(this.company);
        this.isSaving = false;
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
					this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },

    async insertRecord(data) {
      try {
        const response = await this.$axios.post("/companies", data);
        if (response?.status === 201 && response?.data?.result) {
          this.insertNewItem(response?.data?.data);
          this.$toasterNA("green", this.$tr('successfully_inserted'));
          this.$v.company.$reset();
          this.$refs.companyForm.reset();
          this.$refs.companyForm.resetValidation();
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
					this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    async updateRecord(data) {
      try {
        const response = await this.$axios.put("/companies/id", data);
        if (response?.status === 200 && response?.data?.result) {
          this.updateItem(response?.data?.data);
          this.$toasterNA("green", this.$tr('successfully_updated'));
          this.$v.company.$reset();
          this.$refs.companyForm.reset();
          this.$refs.companyForm.resetValidation();
          this.closeDialog();
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
					this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    closeDialog() {
      this.$emit("closeDialog");
    },

    nameRule(validationObject, context) {
      const requiredText = context.$tr("item_is_required", [
        context.$tr("textField.company.name"),
      ]);
      const minLengthText = context.$tr("min_length", [
        context.$tr("textField.company.name"),
        validationObject.name.$params.minLength.min,
      ]);
      const maxLength = context.$tr("max_length", [
        context.$tr("textField.company.name"),
        validationObject.name.$params.maxLength.max,
      ]);

      return [
        (_) => validationObject.name.required || requiredText,
        (_) => validationObject.name.minLength || minLengthText,
        (_) => validationObject.name.maxLength || maxLength,
      ];
    },

    emailRule(validationObject, context) {
      const requiredText = context.$tr("item_is_required", [
        context.$tr("textField.company.email"),
      ]);
      const emailText = context.$tr("validations.email", [
        context.$tr("textField.company.email"),
      ]);

      return [
        (_) => validationObject.email.required || requiredText,
        (_) => validationObject.email.email || emailText,
      ];
    },

    phoneRule(validationObject, context) {
      const requiredText = context.$tr("validations.required", [
        context.$tr("textField.company.phone"),
      ]);
      const phoneNumber = context.$tr("validations.phoneNumber", [
        context.$tr("textField.company.phone"),
      ]);

      return [
        (_) => validationObject.phone.required || requiredText,
        (_) => validationObject.phone.phoneNumber || phoneNumber,
      ];
    },
    whatsappRule(validationObject, context) {
      const requiredText = context.$tr("validations.required", [
        context.$tr("textField.company.whatsapp"),
      ]);
      const phoneNumber = context.$tr("validations.phoneNumber", [
        context.$tr("textField.company.whatsapp"),
      ]);

      return [
        (_) => validationObject.phone.required || requiredText,
        (_) => validationObject.phone.phoneNumber || phoneNumber,
      ];
    },
  },
};
</script>
