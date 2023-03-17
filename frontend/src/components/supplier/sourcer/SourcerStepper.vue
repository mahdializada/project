<template>
  <stepper
    :title="$tr('sourcers')"
    cookieName="label"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="SourcerRules"
    :submit="submit"
    @close="close"
    @reset="reset"
    :showBack="showBack"
  />
</template>
  <script>
import Validations from "~/validations/validations";
import Stepper from "../../horizontal_stepper/Stepper.vue";
import GeneralInfoStep from "./GeneralInfoStep.vue";
export default {
  components: { Stepper },
  data() {
    return {
      show: false,
      showBack: true,
      steps: [
        {
          title: this.$tr("general_info"),
          component: GeneralInfoStep,
        },
      ],
      form: {
        companyIds: [],
        country_id: "",
        name: "",
        last_name: "",
        phone: "",
        email: "",
      },
      SourcerRules: {
        form: {
          country_id: Validations.requiredValidation,
          companyIds: Validations.requiredValidation,
          name: Validations.requiredValidation,
          last_name: Validations.requiredValidation,
          phone: Validations.requiredValidation,
          email: Validations.requiredValidation,
        },
      },
    };
  },
  methods: {
    async submit() {
      try {
        const result = await this.$axios.post(
          "sourcers", this.form
        );
        if (result.status == 201) {
          this.reset();
          this.$toasterNA(
                "green",
                this.$tr("locations_successfully_added_to_selected_supplier")
              );
        } else {
          this.$toasterNA("red", this.$tr("someting_went_wrong"));
        }
      }
      catch (error) {
          this.isSubmitting = false;
          this.$toasterNA("red", this.$tr("someting_went_wrong"));
        }
    },
    reset() {
      this.form = {
        country_id: "",
        companyIds: [],
        name: "",
        last_name: "",
        phone: "",
        email: "",
      };
    },
    close() {
      this.show = false;
      this.reset();
    },
    toggle() {
      this.show = true;
    },
  },
};
</script>
  