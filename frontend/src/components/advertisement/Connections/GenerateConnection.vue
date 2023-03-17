<template>
  <Stepper
    ref="stepper"
    :title="$tr('generate_connection_link')"
    cookieName="generate_connection_link"
    :show="show"
    :steps="steps"
    :skipStep="skip"
    :form="connection"
    :validateRules="connectionRules"
    :submit="validateForm"
    @close="onClose"
    @reset="reset"
    :showBack="showBack"
    :doneStepper="doneStepper"
  />
</template>

<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import ConnectionType from "./ConnectionType.vue";
import ConnectionInfo from "./ConnectionInfo.vue";
import ConnectionTemplate from "./ConnectionTemplate.vue";
import ConnectionPlatform from "./ConnectionPlatform.vue";
import ConnectionLandingLink from "./ConnectionLandingLink.vue";
import ConnectionAdItem from "./ConnectionAdItem.vue";
import Validations from "~/validations/validations";
import HandleException from "~/helpers/handle-exception";
import ConnectionResult from "./ConnectionDoneStep.vue";
import { requiredIf } from "vuelidate/lib/validators";

export default {
  components: { Stepper },

  data() {
    return {
      result: [],
      show: false,
      skip: [1, 2, 3, 4, 5],
      showBack: true,
      doneStepper: {
        title: this.$tr("result"),
        component: ConnectionResult,
        id: "type",
        props: {
          data: this.result,
        },
      },
      steps: [
        {
          title: this.$tr("connection_type"),
          component: ConnectionType,
          id: "type",
        },
        {
          title: this.$tr("connection_template"),
          component: ConnectionTemplate,
          id: "template",
        },
        {
          title: this.$tr("info"),
          component: ConnectionInfo,
          id: "info",
          props: {
            fetchCountry: false,
          },
        },
        {
          title: this.$tr("platform"),
          component: ConnectionPlatform,
          id: "platform",
        },
        {
          title: this.$tr("landing_page_link"),
          component: ConnectionLandingLink,
          id: "link",
        },
        {
          title: this.$tr("generated_link"),
          component: ConnectionAdItem,
          id: "link",
        },
      ],
      isSubmitting: false,

      connection: {
        quick_by: false,
        shouldReset: true,
        connection_type: "new",
        template_id: null,
        pcode: null,
        sales_type: "Landing Page Sales",
        landing_page_link: null,
        product_has_link: false,
        platform_id: null,
        application_id: null,
        ad_account_id: null,
        project_id: null,
        company_id: null,
        country_id: null,
        ispp_id: null,
        is_account_po_required: false,
        account_po: null,

        save_as_template: "no",
        template_name: null,

        ad_id: null,
        ads: [
          {
            media_link: "",
            media_id: "",
            media_size: "",
            ad_id: "",
            generated_link: null,
            connection_id: null,
          },
        ],
        companies: [],
      },
      connectionRules: {
        form: {
          companies: {},

          shouldReset: {},
          quick_by: {},
          product_has_link: {},
          connection_type: Validations.requiredValidation,
          template_id: Validations.requiredValidation,
          pcode: Validations.requiredValidation,
          sales_type: Validations.requiredValidation,
          landing_page_link: Validations.urlValidationRequired,
          platform_id: Validations.requiredValidation,
          application_id: Validations.requiredValidation,
          ad_account_id: Validations.requiredValidation,
          project_id: Validations.requiredValidation,
          company_id: Validations.requiredValidation,
          country_id: Validations.requiredValidation,
          ispp_id: Validations.requiredValidation,
          is_account_po_required: {},
          account_po: {
            required: requiredIf(function (value) {
              return value.is_account_po_required;
            }),
          },

          save_as_template: Validations.requiredValidation,
          template_name: {
            required: requiredIf(function (value) {
              return value.save_as_template == "yes";
            }),
          },

          ad_id: {},
          ads: {
            $each: {
              media_link: "",
              media_id: "",
              media_size: "",
              ad_id: Validations.requiredValidation,
              generated_link: Validations.requiredValidation,
              connection_id: Validations.requiredValidation,
            },
          },
        },
      },
    };
  },

  watch: {
    "connection.connection_type": function (type) {
      if (type == "template") {
        this.skip = [];
      } else {
        this.skip = [1];
      }
    },
    "connection.connection_id": function (connection_id) {
      if (connection_id) {
        this.skip = [0, 1, 2, 3, 4];
        this.steps[2].props.fetchCountry = false;
        this.showBack = false;
      }
    },
  },

  methods: {
    reset() {
      this.connection = {
        shouldReset: true,
        quick_by: false,
        companies: [],
        connection_type: "new",
        template_id: null,
        pcode: null,
        sales_type: "Landing Page Sales",
        landing_page_link: null,
        product_has_link: false,
        platform_id: null,
        application_id: null,
        ad_account_id: null,
        project_id: null,
        company_id: null,
        country_id: null,
        ispp_id: null,
        is_account_po_required: false,
        account_po: null,

        save_as_template: "no",
        template_name: null,

        generated_link: null,
        connection_id: null,
        ad_id: null,
        ads: [
          {
            media_link: "",
            media_id: "",
            media_size: "",
            ad_id: "",
            generated_link: null,
            connection_id: null,
          },
        ],
      };
      // this.$refs.stepper?.$refs.StepperBody?.$refs?.step_5[0].submit=false;
    },
    async onClose() {
      this.show = false;
      await this.$refs.stepper?.$refs.StepperBody?.$refs?.step_5[0].onColseDialog();

      this.reset();
    },
    toggleConnection() {
      this.show = !this.show;
      if (this.show) {
        this.skip = [1];
        this.showBack = true;
        this.steps[2].props.fetchCountry = true;
        this.connection = {
          shouldReset: true,
          companies: [],
          quick_by: false,
          connection_type: "new",
          pcode: null,
          sales_type: "Landing Page Sales",
          landing_page_link: null,
          product_has_link: false,
          platform_id: null,
          application_id: null,
          ad_account_id: null,
          project_id: null,
          template_id: null,
          company_id: null,
          country_id: null,
          ispp_id: null,
          is_account_po_required: false,
          account_po: null,

          save_as_template: "no",
          template_name: null,

          generated_link: null,
          connection_id: null,
          ad_id: null,
          ads: [
            {
              media_link: "",
              media_id: "",
              media_size: "",
              ad_id: "",
              generated_link: null,
              connection_id: null,
            },
          ],
        };
      }
    },

    async validateForm() {
      if (this.connection.ads[0].connection_id) {
        const { ads } = this.connection;
        const connection = { ads };
        return await this.updateConnection(connection);
      }
    },

    async updateConnection(connectionMedia) {
      try {
        const url = "advertisement/connection/update";
        const { data } = await this.$axios.put(url, connectionMedia);
        this.doneStepper.props.data = data;
        let step_refs = this.$refs.stepper.$refs.StepperBody;
        step_refs.$refs.doneStep.loaded(data);
        // this.result=
        return true;
        if (data.result) {
          this.$toasterNA("green", this.$tr("successfully_inserted"));
          return true;
        } else if (data.not_found) {
          // this.$toastr.e(this.$tr("ad_id_not_found"));
          this.$toasterNA("red", this.$tr("ad_id_not_found"));
        } else if (data.invalid_ad_id) {
          // this.$toastr.e(this.$tr("invalid_ad_ad_account_id"));
          this.$toasterNA("red", this.$tr("invalid_ad_ad_account_id"));
        } else if (data.ad_id_exists) {
          // this.$toastr.e(this.$tr("ad_id_already_exists"));
          this.$toasterNA("red", this.$tr("ad_id_already_exists"));
        }
      } catch (error) {
        console.log(error);
        HandleException.handleApiException(this, error);
      }
      return false;
    },
  },
};
</script>

<style></style>
