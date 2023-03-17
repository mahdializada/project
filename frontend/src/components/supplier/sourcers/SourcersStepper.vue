<template>
  <Stepper
    ref="hello"
    :title="$tr('create_new') + ' ' + $tr('sourcer')"
    cookieName="create_sourcer"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="validateRules"
    :submit="submit"
    @close="onClose"
    @reset="reset"
  />
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "~/validations/validations";
import SourcersGeneralInfoStep from "./SourcersGeneralInfoStep.vue";
import SourcersInfo from "./SourcersInfo.vue";
import SourcersProfileStepVue from './SourcersProfileStep.vue';

export default {
  components: { Stepper },

  data() {
    return {
      steps: [
        {
          title: this.$tr("General_info"),
          component: SourcersGeneralInfoStep,
        },
        {
          title: this.$tr("sourcers_info"),
          component: SourcersInfo,
        },
        {
          title: this.$tr("sourcers_profile"),
          component: SourcersProfileStepVue,
        },
      ],
      show: false,
      form: {
        country_id: "",
        company_id: "",
        name: "",
        last_name: "",
        phone_number: "",
        email: "",
        image: "",
      },
      validateRules: {
        form: {
          country_id: Validations.requiredValidation,
          company_id: Validations.requiredValidation,
          name: Validations.name100Validation,
          last_name: Validations.name100Validation,
          phone_number: Validations.phoneValidation,
          email: Validations.emailValidation,
          image: Validations.imageValidation,
        },
      },
    };
  },

  methods: {
    onClose() {
      this.show = false;
    },
    showSourcersDialog() {
      this.show = true;
    },
    reset() {
      this.form = {
        country_id: "",
        company_id: "",
        name: "",
        last_name: "",
        phone_number: "",
        email: "",
        image: "",
      };
    },
    async submit() {
      try {
        if (this.form.editData) {
          return this.updataData();
        } else {
          const result = await this.$axios.post("sourcers", this.form);
          if (result.status == 201) {
            this.$toasterNA(
              "green",
              this.$tr("locations_successfully_added_to_selected_supplier")
            );
            const datas = result.data;
            this.$emit("pushRecord", datas);
            return true;
          } else {
            this.$toasterNA("red", this.$tr("someting_went_wrong"));
            return false;
          }
        }
      } catch {
        this.$toasterNA("red", this.$tr("someting_went_wrong"));
        return false;
      }
    },

    toggleEdit(item) {
      this.show = true;
      this.form.id = item.id
      this.form.editData = item;
      this.form.country_id = item.country_id;
      // (this.form.company_id = item.company_id);
      this.form.name = item.name;
      this.form.last_name = item.last_name;
      this.form.phone_number = item.phone_number;
      this.form.email = item.email;
      this.form.image = item.attachments[0]?.path;
      console.log(item);
    },
    async updataData() {
      try {
        delete this.form.editData;
        const response = await this.$axios.put("sourcers/id", this.form);
        if (response.status == 200) {
          this.$emit('updateData',response.data.data);
          this.$toasterNA(
            "green",
            this.$tr("updated_successfully_", this.$tr("supplier"))
          );
          return true;
        } else {
          this.$toasterNA("red", this.$tr("someting_went_wrong"));
          return false;
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("someting_went_wrong"));
        return false;
      }
    },
  },
};
</script>

<style></style>
