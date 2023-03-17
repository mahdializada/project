<template>
  <Stepper
    :title="isEdit ? $tr('update_item',$tr('ipa')) : $tr('create_item',$tr('ipa'))"
    cookieName="cookie_name_must_be_uniqe_across_the_app"
    @close="show = false"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="validateRules"
    :submit="submit"
    @reset="reset"
  />
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "../../../validations/validations";
import HandleException from "../../../helpers/handle-exception";
import TargetAudience from "./TargetAudience";
import PlatfromsStep from "./PlatfromsStep";
export default {
  name: "StepperComponent",
  components: { Stepper },
  data() {
    return {
      show: false,
      isEdit: false,
      ipa_id: "",
      steps: [
        {
          title: "target_audience",
          component: TargetAudience,
          props: {},
          hints: [], 
        },
        {
          title: "platforms",
          skip: true,
          component: PlatfromsStep,
          props: { isEdit: this.isEdit },
          hints: [],
        },
      ],
      form: {
        country_name: null,
        city: [],
        ispp_id: null,
        target_age: [0, 70],
        date: {
          start_date: "",
          end_date: "",
        },
        target_gender: "",
        platforms: [
          {
            platform_name: "",
            platform_placement: "",
            budget: "",
            target_cpos: "",
          },
        ],
      },

      validateRules: {
        form: {
          city: Validations.requiredValidation,
          target_age: Validations.requiredValidation,
          ispp_id: Validations.name100Validation,
          country_name: Validations.name100Validation,
          target_gender: Validations.requiredValidation,
          date: {
            start_date: Validations.name100Validation,
            end_date: Validations.name100Validation,
          },
          platforms: Validations.requiredValidation,
        },
      },
    };
  },
  methods: {
    reset() {
      this.form = {
        country_name: null,
        city: [],
        ispp_id: null,
        target_age: [0, 70],
        date: {
          start_date: "",
          end_date: "",
        },
        target_gender: "",
        platforms: [
          {
            platform_name: "",
            platform_placement: "",
            budget: "",
            target_cpos: "",
          },
        ],
      };
    },
    async submit(formRef, vuelidate) {
      try {
        const url = "single-sales/ipa/";
        let data = [];
        if (this.isEdit) {
          vuelidate.$model.id = this.ipa_id;
          data = await this.$axios.put(url + this.ipa_id, vuelidate.$model);
        } else {
          data = await this.$axios.post(url, vuelidate.$model);
        }
        if (data.data.result) {
          if (this.isEdit) {
            this.$toasterNA("green", this.$tr('successfully_updated'));
            this.$emit("updateRecord", data?.data?.data);
          } else {
            this.$toasterNA("green", this.$tr('successfully_inserted'));
            this.$emit("pushRecord", data?.data?.data);
          }
          return true;
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },
    showDialog(item = null) {
      this.isEdit = false;
      this.steps[1].props.isEdit = false;
      if (item !== null) {
        this.ipa_id = item[0].id;
        let country = JSON.parse(item[0].target_countries);
        this.form = {
          country_name: country[0].name,
          city: country[0].cities,
          ispp_id: item[0].ispp_id,
          target_age: [item[0].target_age_min, item[0].target_age_max],
          date: {
            start_date: item[0].target_time_start,
            end_date: item[0].target_time_start,
          },
          target_gender: item[0].target_gender,
          platforms: item[0].platforms,
        };
        this.steps[1].props.isEdit = true;
        this.isEdit = true;
      }
      this.show = true;
    },
  },
};
</script>

<style scoped></style>
