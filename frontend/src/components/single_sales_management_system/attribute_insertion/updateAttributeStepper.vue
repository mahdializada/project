<template>
  <div>
    <Stepper
      title="update Attribute"
      cookieName="cookie_name_must_be_uniqe_across_the_app"
      @close="show = false"
      :show="show"
      :isEdit="isEdit"
      :steps="steps"
      :form="form"
      :validateRules="validateRules"
      :submit="submit"
      @reset="clearForm"
    />
  </div>
</template>
<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import AttributeStepOne from "./AttributeStepOne.vue";
import Helpers from "../../../helpers/helpers";

import Validations from "../../../validations/validations";
import HandleException from "../../../helpers/handle-exception";
import { required } from "vuelidate/lib/validators";
export default {
  name: "AttributeStepper",
  data() {
    return {
      show: false,
      isEdit: false,
      steps: [
        {
          title: "Attribute A Section",
          component: AttributeStepOne,
          props: {
            isEdit: false,
          },
          hints: [],
        },
      ],
      form: {
        attributes: [
          {
            name: "",
            arabic_name: "",
            statement: "",
            selectedAttr: [],
          },
        ],
      },
      validateRules: {
        form: {
          attributes: {
            $each: {
              name: Validations.name100Validation,
              arabic_name: Validations.name100Validation,
              statement: {},

              selectedAttr: {
                $each: {
                  required: required,
                },
              },
            },
          },
        },
      },
    };
  },

  methods: {
    clearForm() {
      this.form.attributes = [
        {
          statement: "",

          name: "",
          arabic_name: "",
          selectedAttr: [],
        },
      ];
    },
    async submit() {
      const response = await this.$axios.put(
        "single-sales/attribute-ssp/" + 1,
        this.form.attributes
      );
      if (response?.status === 200) {
        this.$toasterNA("green", this.$tr('successfully_updated'));
        const datas = response.data;
        this.$emit("updateRecord", datas);

        this.clearForm();

        return true;
      } else {
				this.$toasterNA("red", this.$tr('something_went_wrong'));

        return false;
      }
    },
    showDialog(items) {
      if (items && Array.isArray(items)) {
        this.form.attributes = items;
      }
      this.show = true;
      this.steps[0].props.isEdit = true;
    },
  },

  components: {Stepper},
};
</script>
