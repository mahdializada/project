<template>
  <Stepper
    :sidebarKey="key"
    ref="hello"
    :title="$tr('ad_manual_upload')"
    cookieName="ad_manual_upload"
    :show="show"
    :steps="steps"
    :skipStep="skip"
    :form="form"
    :validateRules="formRules"
    :submit="submit"
    @close="$emit('close')"
    :submitText="submitText"
  />
</template>
<script>
import Stepper from "../../horizontal_stepper/Stepper.vue";
import Validations from "~/validations/validations";
import SelectTemplateOption from "./SelectTemplateOption.vue";
import UploadXlsxTemplate from "./UploadXlsxTemplate.vue";
import DownloadXlsxTemplate from "./DownloadXlsxTemplate.vue";
import helpers from "../../../helpers/helpers";

export default {
  props: {
    selectedTabData: Object,
    sheets: Object,
    show: Boolean,
  },
  data() {
    return {
      key: 0,
      skip: [],
      steps: [],
      downloadSteps: [
        {
          id: "type",
          title: this.$tr("action"),
          component: SelectTemplateOption,
        },
        {
          id: "step_2",
          title: this.$tr("download_template"),
          props: { selectedTabData: this.selectedTabData },
          component: DownloadXlsxTemplate,
        },
      ],
      uploadSteps: [
        {
          id: "type",
          title: this.$tr("action"),
          component: SelectTemplateOption,
        },
        {
          id: "step_2",
          title: this.$tr("upload_template"),
          props: { sheets: this.sheets },
          component: UploadXlsxTemplate,
        },
      ],
      form: {
        type: "download",
        file: null,
        date: "",
      },
      formRules: {
        form: {
          type: Validations.requiredValidation,
          file: Validations.requiredValidation,
          date: Validations.requiredValidation,
        },
      },
    };
  },
  watch: {
    "form.type": {
      handler: function (value) {
        this.key++;
        if (value == "download") {
          this.steps = this.downloadSteps;
        } else if (value == "upload") {
          this.steps = this.uploadSteps;
        }
      },
      deep: true,
    },
  },
  methods: {
    async submit() {
      if (this.form.type == "download") {
        this.$emit("close");
        return ;
      }
      return await this.submitSheets();
    },
    async submitSheets() {
      try {
        const formData = helpers.getFormData({
          campaign: JSON.stringify(this.sheets.campaign),
          ad_set: JSON.stringify(this.sheets.ad_set),
          ad: JSON.stringify(this.sheets.ad),
          con_data: JSON.stringify(this.sheets.connection_data),
          data_date: this.form.date,
        });
        const { data } = await this.$axios.post(
          "advertisement/mu-connection-data",
          formData
        );
        if (data.result) {
          this.$toasterNA("green", this.$tr(data.message));
          return data.result;
        } else {
          return false;
        }
      } catch (error) {
        this.$toasterNA("green", this.$tr("network_error_occured"));
      }
    },
  },
  computed: {
    submitText() {
      return this.form["type"] == "download" ? "close" : "submit";
    },
  },
  created() {
    this.steps = this.downloadSteps;
  },
  components: { Stepper },
};
</script>
