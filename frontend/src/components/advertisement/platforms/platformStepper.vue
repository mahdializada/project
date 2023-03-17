<template>
  <Stepper
    title="platforms"
    cookieName="platform_insertion_stepper"
    @close="show = false"
    :show="show"
    :steps="steps"
    :form="form"
    :validateRules="platformRules"
    @reset="reset"
    :submit="submit"
  />
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "~/validations/validations";
import HandleException from "../../../helpers/handle-exception";
import PlatformStep from "./PlatformStep.vue";

export default {
  name: "CategoryStepper",
  components: { Stepper },
  data() {
    return {
      show: false,
      isEdit: false,
      editData:[],
      editId:'',
      steps: [
        {
          title: this.$tr("info"),
          component: PlatformStep,
          props:{editData:this.editData}
        },
      ],

      form: {
        platform_name: "",
        platform_key: "",
        companies: [],
      },
      platformRules: {
        form: {
          platform_name: Validations.requiredValidation,
          platform_key: Validations.requiredValidation,
          companies: Validations.requiredValidation,
        },
      },
    };
  },
  methods: {
    reset() {
      this.form = {
        platform_name: "",
        platform_key: "",
        companies: [],
      };
    },
    async submit() {
      try {
        const url = "advertisement/platforms";
        let data = {};
        if(this.isEdit){
        const response = await this.$axios.put(
						url + `/${this.form.id}`,
						this.form,
					);
          data = response.data;
          this.isEdit = false;
        }else{
          const response = await this.$axios.post(url, this.form);
          data = response.data;
      }
        if (data.result) {
          // this.$toasterNA("green", this.$tr('successfully_inserted'));
          const insertedRecord = data.platform;
          this.$emit("pushRecord", insertedRecord);
          this.$toasterNA("green", this.$tr("successfully_inserted"));
          return true;
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    showDialog(item=null) {
      this.steps[0].props.editData = '';
      if(item){
        this.isEdit = true;
        this.form.id = item.id;
        this.form.platform_key = item.platform_key;
        this.form.companies = item.companies.map((item)=>{
          return item.id;
        });
        this.steps[0].props.editData = item.platform_name;
      }
      this.show = true;
    },

  },
};
</script>

<style scoped>
</style>
