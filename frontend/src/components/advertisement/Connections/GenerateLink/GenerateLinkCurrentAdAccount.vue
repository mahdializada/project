<template>
  <stpperLiner
    ref="stepper"
    :show="show"
    :title="$tr('generate_connection_link')"
    cookieName="generate_connection_link"
    :steps="steps"
    :skipStep="skip"
    :form="connection"
    :validateRules="connectionRules"
    :submit="validateForm"
    @close="onClose"
    @reset="reset"
    :showStartOver="true"
    :showBack="false"
    @startOver="startOver"
  ></stpperLiner>
</template>

<script>
import Stepper from "../../../horizontal_stepper/Stepper.vue";
import ConnectionType from "./ConnectionType.vue";
import ConnectionAdItem from "./ConnectionAdItem.vue";
import MediaStep from "./MediaStep.vue";
import Validations from "~/validations/validations";
import HandleException from "~/helpers/handle-exception";
import { requiredIf } from "vuelidate/lib/validators";
import stpperLiner from "../../../../components/stepper-liner/stpperLiner.vue";

export default {
  components: { Stepper, stpperLiner },
  props: ["adAccounts"],
  data() {
    return {
      connectionData: [],
      selectedAd: {},
      result: [],
      show: false,
      skip: [],
      showBack: true,

      steps: [
        {
          title: this.$tr("media"),
          component: MediaStep,
          id: "media",
        },
        {
          title: this.$tr("creation_type"),
          component: ConnectionType,
          id: "type",
        },

        {
          title: this.$tr("ad_information"),
          component: ConnectionAdItem,
          id: "link",
        },
      ],
      isSubmitting: false,

      connection: {
        connection_copy: {},
        canva_type: "current_canva",
        creation_type: "current_account",
        shouldReset: true,
        connection_type: "new",
        template_id: null,
        pcode: null,
        sales_type: "Single Sale",
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
      },
      connectionRules: {
        form: {
          connection_copy: {},
          creation_type: Validations.requiredValidation,
          canva_type: Validations.requiredValidation,
          shouldReset: {},
          product_has_link: {},
          connection_type: Validations.requiredValidation,
          template_id: Validations.requiredValidation,
          pcode: Validations.requiredValidation,
          sales_type: Validations.requiredValidation,
          landing_page_link: Validations.urlValidationRequired,
          platform_id: Validations.requiredValidation,
          application_id: Validations.requiredValidation,
          ad_account_id: {
            required: requiredIf((value) => {
              return value.creation_type == "new_account";
            }),
          },
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
      emptyConnection: {
        connection_copy: {},
        canva_type: "current_canva",
        creation_type: "current_account",
        shouldReset: true,
        connection_type: "new",
        template_id: null,
        pcode: null,
        sales_type: "Single Sale",
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
      },
    };
  },

  watch: {},

  methods: {
    reset() {
      this.connection = this.emptyConnection;
    },
    startOver() {
      this.connection = this.connectionData;
      console.log("start over", this.connection);
    },
    async onClose() {
      this.show = false;
      // await this.$refs?.stepper?.$refs?.StepperBody?.$refs?.step_1[0]?.onColseDialog();

      this.reset();
    },
    async toggleConnection(data = null) {
      this.show = !this.show;
      if (this.show) {
        this.selectedAd = data;

        this.connection = this.emptyConnection;
        await this.getConnection(this.selectedAd?.connection.id);
        if (this.$refs.stepper?.$refs.StepperBody?.$refs)
          this.$refs.stepper?.$refs.StepperBody?.$refs?.step_0[0]?.setAdAccounts(
            this.adAccounts
          );
        this.connection.adAccounts = this.adAccounts;
      }
    },
    async getConnection(id) {
      try {
        const { data } = await this.$axios.get(
          `advertisement/get-connection/${id}`
        );
        console.log("data", data);
        if (data) {
          this.connection = {
            connection_copy: data,
            canva_type: "current_canva",
            creation_type: "current_account",
            shouldReset: true,
            template_id: null,
            pcode: data.pcode,
            sales_type: data.sales_type,
            landing_page_link: data.landing_page_link,
            product_has_link: true,
            platform_id: data.platform_id,
            application_id: data.application_id,
            ad_account_id: data.server_account_id,
            project_id: data.project_id,
            company_id: data.company_id,
            country_id: data.country_id,
            ispp_id: data.ispp_id,
            is_account_po_required: false,
            account_po: null,

            save_as_template: "no",
            template_name: null,
            ads: [
              {
                media_link: data.media_link,
                media_id: data.media_id,
                media_size: data.media_size,
                ad_id: "",
                generated_link: null,
                connection_id: null,
              },
            ],
            adAccounts: [],
          };
          this.connectionData = [];
          this.connectionData = this._.cloneDeep(this.connection);
        } else {
        }
      } catch (error) {}
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

        if (data[0].result) {
          this.$toasterNA("green", this.$tr("successfully_inserted"));
          return true;
        } else {
          // await this.$refs.stepper?.$refs.StepperBody?.$refs?.step_1[0].onError();

          if (data[0].not_found) {
            this.$toasterNA("red", this.$tr("ad_id_not_found"));
            return false;
          } else if (data[0].invalid_ad_id) {
            this.$toasterNA("red", this.$tr("invalid_ad_ad_account_id"));
            return false;
          } else if (data[0].ad_id_exists) {
            this.$toasterNA("red", this.$tr("ad_id_already_exists"));
            return false;
          }
        }
      } catch (error) {
        console.log(error);
        HandleException.handleApiException(this, error);
        return false;
      }
      this.$toasterNA("red", this.$tr("something_went_wrong"));

      // this.onClose();
      return false;
    },
  },
};
</script>

<style></style>
