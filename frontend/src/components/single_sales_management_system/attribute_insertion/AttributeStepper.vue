<template>
  <div>
    <Stepper
      :title="$tr('create_item', $tr('attribute'))"
      cookieName="cookie_name_must_be_uniqe_across_the_app"
      @close="show = false"
      :show="show"
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
import { required, requiredIf } from "vuelidate/lib/validators";
import AttributeSectioneVue from "./AttributeSectione.vue";
import AttributeValueInputeVue from "./AttributeValueInpute.vue";
export default {
  name: "AttributeStepper",
  data() {
    return {
      show: false,
      steps: [
        {
          title: "attributes_section",
          component: AttributeSectioneVue,
          props: {},
          hints: [],
        },
        {
          title: "attributes",
          component: AttributeStepOne,
          props: {},
          hints: [],
        },
      ],
      form: {
        attribute_section: "",
        value_input: "",
        value_name: "",
        arabic_name: "",
        file_type: [],
        num_min: "",
        num_max: "",
        // text_value: "",
        // file_value: "",
        // number_value: "",
        // link_value: "",
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
          attribute_section: Validations.requiredValidation,
          value_input: Validations.requiredValidation,
          value_name: Validations.name100Validation,
          arabic_name: Validations.name100Validation,
          num_min: {
            required: requiredIf((value) => {
              return this.form.value_input == "number"
                ? Validations.requiredValidation
                : "";
            }),
          },
          num_max: {
            required: requiredIf((value) => {
              return this.form.value_input == "number"
                ? Validations.requiredValidation
                : "";
            }),
          },
          file_type: {
            required: requiredIf((value) => {
              return this.form.value_input == "file"
                ? Validations.requiredValidation
                : "";
            }),
          },
          // number_value: {
          //   required: requiredIf((value) => {
          //     return this.form.value_input == "number"
          //       ? Validations.phoneValidation
          //       : "";
          //   }),
          // },
          // text_value: {
          //   required: requiredIf((value) => {
          //     return this.form.value_input == "text"
          //       ? Validations.name100Validation
          //       : "";
          //   }),
          // },
          attributes: {
            $each: {
              name: Validations.name100Validation,
              arabic_name: Validations.name100Validation,
              statement: {},
              selectedAttr: {},
            },
          },
        },
      },
    };
  },
  watch: {
    "form.attribute_section": function (type) {
      if (type == "value_input") {
        this.steps = this.steps.filter((i) => i.title != "attributes");
        if (!this.steps.find((i) => i.title == "value_input"))
          this.steps.push({
            title: "value_input",
            component: AttributeValueInputeVue,
            props: {},
            hints: [],
          });
      } else {
        this.steps = this.steps.filter((i) => i.title != "value_input");
        if (!this.steps.find((i) => i.title == "attributes"))
          this.steps.push({
            title: "attributes",
            component: AttributeStepOne,
            props: {},
            hints: [],
          });
      }
    },
  },
  methods: {
    clearForm() {
      this.form = {
        attribute_section: "",
        value_input: "",
        value_name: "",
        arabic_name: "",
        file_type: [],
        num_max: "",
        num_min: "",
        // text_value: "",
        // file_value: "",
        // number_value: "",
        // link_value: "",
        attributes: [
          {
            name: "",
            arabic_name: "",
            statement: "",
            selectedAttr: [],
          },
        ],
      };
    },
    async submit() {
      // console.log(this.form);
      // return false
     
      var data = null;
      if (this.form.attribute_section == "value_input") {
        delete this.form.attributes;
        data = {
          value_input: this.form.value_input,
          value_name: this.form.value_name,
          arabic_name: this.form.arabic_name,
        };
        
            if (this.form.value_input == "text") {
            data.value =  this.form.text_value
            }else if(this.form.value_input == "number"){
              data.num =  {
                min:this.form.num_min,
                max:this.form.num_max
              }
            }else if(this.form.value_input == "link"){
              data.value =  this.form.link_value
            }
        else if (this.form.value_input == "file")
          data.num = this.form.file_type
              
          
      } else {
        delete this.form.value_input;
        delete this.form.value_name;
        delete this.form.arabic_name;
        delete this.form.text_value;
        delete this.form.number_value;
        delete this.form.link_value;
        delete this.form.file_value;
        data = {
          attributes: this.form.attributes,
        };
      }
      console.log('hashmat',data);
      try {
        if (this.form.id) {
          data.id = this.form.id
          data.attribute_section = this.form.attribute_section;
            return this.updataData(data);
          }
        const response = await this.$axios.post(
          "single-sales/attribute-ssp",
          data
        );
        console.log(response);
        if (response?.status === 200) {
          this.$toasterNA("green", this.$tr("successfully_inserted"));
          const datas = response.data;
          this.$emit("pushRecord", datas);
          this.clearForm();

          return true;
        } else {
          this.$toasterNA("red", this.$tr("something_went_wrong"));

          return false;
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    showDialog(items) {
      if (items && Array.isArray(items)) {
        alert("done");
        this.form.attributes = items;
      }

      this.show = true;
    },
    showEditDialog(item) {
      this.show = !this.show;
      this.form.id = item.id;
      this.form.attribute_section = item.section
      console.log("item",item);
      if (item.section == "value_input") {
        this.form.value_input = item.type;
        this.form.value_name = item.name;
        this.form.arabic_name = item.arabic_name;
        this.form.file_type = item.file_type;
        this.form.num_max = item.num_max;
        this.form.num_min = item.num_min;
        // this.form.text_value = item.values[0];
        // this.form.file_value = item.values.cloud_path;
        // this.form.number_value = item.values[0];
        // this.form.link_value = item.values[0];
      }else{
        this.form.attributes[0].arabic_name = item.arabic_name
        this.form.attributes[0].name = item.name
        this.form.attributes[0].selectedAttr  = item.values;
      }

    },
    async updataData(data) {
      try {
        // delete this.form.editData;
        const response = await this.$axios.post("single-sales/attribute-ssp", data);
        console.log('hashmat',response);
        if (response.status == 200) {
          const datas= response.data;
          this.$emit('mapRecord',datas);

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

  components: { Stepper },
};
</script>
