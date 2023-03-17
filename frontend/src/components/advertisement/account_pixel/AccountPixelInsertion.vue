<template>
     <Stepper title="Brand Insertion" cookieName="brand_insertion_stepper" @close="show = false" :show="show"
          :steps="steps" :form="connection" :validateRules="connectionRules" @reset="reset" :submit="submit" />
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "~/validations/validations";

import HandleException from "../../../helpers/handle-exception";
import PixelStepper1 from "./PlatformPixelStepper.vue";
import PixelInfoStepper from './PixelInfoStepper.vue';


export default {
     name: "CategoryStepper",
     components: { Stepper },
     data() {
          return {
               show: false,
               steps: [
                    {
                         title: this.$tr("info"),
                         component: PixelInfoStepper,
                         id: "info",
                         props: {
                              fetchCountry: false,
                         },
                    },
                    {
                         title: "Platform Info",
                         component: PixelStepper1,
                         props: {},
                         hints: [],
                    },
               ],

               connection: {
                    shouldReset: true,
                    platform_id: null,
                    application_id: null,
                    ad_account_id: null,
                    company_id: null,
                    country_id: null,
                    pixel_id: null,
               },
               connectionRules: {
                    form: {
                         shouldReset: {},
                         platform_id: Validations.requiredValidation,
                         application_id: Validations.requiredValidation,
                         ad_account_id: Validations.requiredValidation,
                         company_id: Validations.requiredValidation,
                         country_id: Validations.requiredValidation,
                         pixel_id: Validations.requiredValidation,

                    },
               },
          };
     },
     watch: {
          "form.company_id.$model": function (company_id) {
               if (company_id) {
                    this.platforms = [];
                    this.applications = [];
                    this.adAccounts = [];
                    this.fetchItems({ type: "platforms", id: company_id });
               }
          },
          "form.platform_id.$model": function (platform_id) {
               if (this.form.shouldReset.$model) {
                    this.form.application_id.$model = null;
               }
               if (platform_id) {
                    this.applications = [];
                    this.adAccounts = [];
                    this.fetchItems({ type: "applications", id: platform_id });
               }
          },
          "form.application_id.$model": function (application_id) {
               if (this.form.shouldReset.$model) {
                    this.form.ad_account_id.$model = null;
               }
               if (application_id) {
                    this.adAccounts = [];
                    this.fetchItems({ type: "adAccounts", id: application_id });
               }
          },


     },
     methods: {
          reset() {
               // this.form.brands = [
               //      {
               //           logo: "",
               //           name: "",
               //      },
               // ];
          },
          async fetchItems({ type, id }) {
               try {
                    this["isFetching" + type] = true;
                    const url = `advertisement/connection/generate_link/${type}/${id}`;
                    const { data } = await this.$axios.get(url);
                    this[data.type] = data.items;
               } catch (error) { }
               this["isFetching" + type] = false;
          },
          async submit(formRef, vuelidate) {
          },

          showDialog() {
               this.show = true;
          },
     },
}
</script>

<style scoped>

</style>
