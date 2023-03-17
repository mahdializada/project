<template>
  <div class="w-full h-full d-flex align-center justify-center pt-5">
    <div class="w-full h-full">
      <div class="mb-3">
        <CDatePicker
          :title="$tr('label_required', $tr('date'))"
          v-model="form.date.$model"
          :placeholder="$tr('date')"
          icon="mdi-calendar"
          :rules="validateRule(form.date, $root, 'Date')"
          :invalid="form.date.$invalid"
        />
      </div>
      <InputCard v-bind="$attrs" v-on="$listeners">
        <template slot-scope="{ attrs, listeners }">
          <v-file-input
            class="custom-file form-custom-file"
            outlined
            :accept="fileExtension"
            prepend-icon=""
            dense
            v-bind="attrs"
            v-on="listeners"
            v-model="form.file.$model"
          >
            <template slot="prepend-inner">
              <div name="prepend-inner" style="max-width: 260px; text-align: center">
                <svg id="upload-icon" width="40.152" height="50.415" class="mt-2 mb-2">
                  <g id="Group_136" data-name="Group 136" transform="translate(0)">
                    <path
                      id="Path_135"
                      data-name="Path 135"
                      d="M27.011,0l12.04,12.578c.2.214.41.428.612.645a1.744,1.744,0,0,1,.488,1.247q-.01,14.576-.009,29.153a6.693,6.693,0,0,1-5.937,6.763,6.59,6.59,0,0,1-.888.027q-13.248,0-26.5,0A6.679,6.679,0,0,1,.047,44.5a8.08,8.08,0,0,1-.025-1.035c0-12.2.033-24.4-.022-36.6A6.823,6.823,0,0,1,5.141.083C5.185.074,5.22.029,5.26,0ZM25.205,2.359h-.614q-8.8,0-17.6,0a4.225,4.225,0,0,0-4.512,4.5q0,18.3,0,36.6a4.224,4.224,0,0,0,4.511,4.5q13.076,0,26.151,0a4.254,4.254,0,0,0,4.539-4.535q0-13.738,0-27.476V15.4h-.611c-2.257,0-4.515.016-6.772-.012a5.249,5.249,0,0,1-5.057-4.922c-.066-2.481-.029-4.965-.037-7.448,0-.194,0-.389,0-.654m2.47,1.885c0,2-.013,3.832,0,5.669a2.865,2.865,0,0,0,3.083,3.025c1.564.007,3.128,0,4.692,0,.136,0,.272-.021.482-.038l-8.26-8.656"
                      transform="translate(0 -0.001)"
                      fill="#2e7be6"
                      opacity="0.29"
                    />
                    <path
                      id="Path_136"
                      data-name="Path 136"
                      d="M54.67,72.885v.3q0,3.493,0,6.987a1.316,1.316,0,0,1-.061.466.687.687,0,0,1-.693.424.717.717,0,0,1-.619-.567,1.768,1.768,0,0,1-.027-.362q0-3.465,0-6.931v-.307l-.094-.08a1.456,1.456,0,0,1-.152.25q-1.239,1.34-2.485,2.675a.834.834,0,0,1-.676.338.647.647,0,0,1-.6-.4.661.661,0,0,1,.1-.736c.338-.376.683-.745,1.028-1.115q1.465-1.571,2.933-3.141a.744.744,0,0,1,1.28,0q1.949,2.08,3.891,4.167a.715.715,0,0,1,.021,1.048.692.692,0,0,1-1.042-.1q-1.278-1.362-2.548-2.731c-.063-.068-.118-.144-.176-.216l-.08.038"
                      transform="translate(-33.9 -48.171)"
                      fill="#2e7be6"
                    />
                    <path
                      id="Path_137"
                      data-name="Path 137"
                      d="M43.447,166.687q2.841,0,5.682,0a.7.7,0,1,1,.128,1.389,2.133,2.133,0,0,1-.281.007H37.949c-.075,0-.15,0-.225,0a.721.721,0,0,1-.679-.721.7.7,0,0,1,.719-.67q2.841-.005,5.682,0"
                      transform="translate(-23.39 -131.525)"
                      fill="#2e7be6"
                    />
                  </g>
                </svg>
                <p class="ma-0 prepend-text mb-1">Drag and drop images to upload</p>
                <p class="ma-0 prepend-text2">or browse files on your computer</p>
              </div>
            </template>
          </v-file-input>
          <CfileItem2
            v-if="isDataClean && !form.file.$invalid"
            :icon="excelIcon"
            :name="getFileName"
            :size="getFileSize"
            :progress="70"
            @close="cleanFile"
          />
          <CfileItem2
            v-if="isAnyErrorOccured"
            :icon="excelIcon"
            :name="getFileName"
            :size="getFileSize"
            :progress="0"
            :hasProgress="false"
            :error="getErrorText"
            @close="cleanFile"
          />
          <CBulkUploadErrors
            :headers="error_headers"
            :data="selectedErrorData"
            :changeErrorData="changeErrorData"
            :tabItems="getTabItems"
            :isDataClean="isDataClean"
          />
        </template>
      </InputCard>
    </div>
  </div>
</template>
<script>
import InputCard from "../../new_form_components/components/InputCard.vue";
import CfileItem2 from "../../new_form_components/Inputs/CfileItem2.vue";
import CDatePicker from "../../new_form_components/Inputs/CDatePicker.vue";
import GlobalRules from "~/helpers/vuelidate";
import CBulkUploadErrors from "../../new_form_components/components/CBulkUploadErrors.vue";
import { read, utils } from "xlsx";
import Utils from "./helpers/util.helper";
import {
  parseHeadersAndRows,
  isCorrectSheetUploaded,
  validateRows,
} from "./helpers/helper";
import UtilValidation from "./helpers/util.validation";
import Validations from "~/validations/validations";

export default {
  props: {
    sheets: Object,
    form: Object,
    connection_data: Array,
  },
  data() {
    return {
      validateRows: validateRows,
      isCorrectSheetUploaded: isCorrectSheetUploaded,
      parseHeadersAndRows: parseHeadersAndRows,
      validateRule: GlobalRules.validate,
      tabKey: "campaign",
      headers: [],
      data: [],
      errorText: "",
      error_headers: Utils(this).error_headers,
      excelIcon: Utils(this).excelIcon,
      fileExtension: Utils(this).fileExtension,
      campaign_headers: Utils(this).campaign_headers,
      ad_set_headers: Utils(this).ad_set_headers,
      ad_headers: Utils(this).ad_headers,
      campaign_form: UtilValidation(null).campaign_form,
      ad_set_form: UtilValidation(null).ad_set_form,
      ad_form: UtilValidation(null).ad_form,
      tabItems: Utils(this).tabItems,
      isFileSelected: false,
      isError: false,
      isDataClean: false,
      isFileBeingParsed: false,
      errors: {
        campaign: [],
        ad_set: [],
        ad: [],
      },
    };
  },
  validations: {
    campaign_form: { ...UtilValidation(Validations.requiredValidation).campaign_form },
    ad_set_form: { ...UtilValidation(Validations.requiredValidation).ad_set_form },
    ad_form: { ...UtilValidation(Validations.requiredValidation).ad_form },
  },
  computed: {
    getErrorText() {
      return this.errorText;
    },
    getFileSize() {
      let file = this.form.file.$model;
      if (file) {
        let size = file.size;
        let KB = size / 1024;
        if (KB < 1024) {
          return parseInt(size / 1024) + " KB";
        } else {
          return parseInt(KB / 1024) + " MB";
        }
      }
      return 0 + "GB";
    },
    isAnyErrorOccured() {
      let isValid =
        this.errors.campaign.length ||
        this.errors.ad_set.length ||
        this.errors.ad.length ||
        this.isError;
      return isValid && !this.form.file.$invalid;
    },
    getFileName() {
      return this.form.file.$model
        ? this.form.file.$model.name
        : "connection_template.xlsx";
    },
    selectedErrorData() {
      return this.errors[this.tabKey];
    },
    getTabItems() {
      if (this.isErrorFree()) {
        return this.tabItems;
      }
      return this.tabItems.filter((t) => {
        if (this.errors[t.key].length) {
          this.tabKey = t.key;
          return t;
        }
      });
    },
  },
  methods: {
    isErrorFree() {
      return (
        this.errors.campaign.length && this.errors.ad_set.length && this.errors.ad.length
      );
    },
    changeErrorData(tabKey) {
      this.tabKey = tabKey;
    },
    cleanFile() {
      this.sheets.campaign = [];
      this.sheets.ad_set = [];
      this.sheets.ad = [];
      this.isDataClean = false;
      this.form.file.$model = null;
      this.errors["ad"] = [];
      this.errors["ad_set"] = [];
      this.errors["campaign"] = [];
    },
    loaded() {},
    validate() {
      return !this.form.$invalid;
    },
    validateFile(file) {
      if (file) {
        const fileExtension = file.type;
        if (fileExtension !== this.fileExtension) {
          this.errorText = this.$tr("file_upload_extension_conflict");
          this.isFileSelected = false;
          return;
        }
        this.errorText = "";
        this.isFileSelected = true;
      } else {
        this.errorText = this.$tr("file_upload_extension_conflict");
        this.isFileSelected = false;
      }
      return this.isFileSelected;
    },
    clearFile() {
      this.form.file.$model = null;
      this.isFileSelected = false;
    },
    importExcelFile(file) {
      let reader = new FileReader();
      reader.onload = () => {
        this.isFileBeingParsed = true;
        let fileData = reader.result;
        let wb = read(fileData, { type: "binary" });
        wb.SheetNames.forEach((sheetName) => {
          let rowObj = utils.sheet_to_json(wb.Sheets[sheetName]);
          this.parseExcelRows(rowObj, sheetName);
        });
      };
      reader.readAsBinaryString(file);
    },
    parseAdAccountId(rows) {
      let field = rows.find((r) => r.__rowNum__ == 3);
      if (field) {
        field = field.__EMPTY.split("(")[1].split(")")[0];
      }
      return field ? field : null;
    },
    parseData(sheetName, rows) {
      switch (sheetName) {
        case "connection-&-campaign-template":
          return this.campaign_headers;
        case "ad-set-template":
          return this.ad_set_headers;
        case "ad-template":
          return this.ad_headers;
      }
    },
    async parseExcelRows(rows, sheetName) {
      // selecting proper headers,
      const main_headers = this.parseData(sheetName, rows);
      // preparing headers and rows
      const [selectedHeaders, selectedRows] = this.parseHeadersAndRows(rows);
      // 1. check the type of template by checking the headers
      const isSheetOk = this.isCorrectSheetUploaded(selectedHeaders, main_headers);
      if (!selectedHeaders || !isSheetOk) {
        this.clearFile();
        if (!selectedHeaders) {
          this.errorText = this.$tr("wrong_template_uploaded");
          this.isError = true;
        } else {
          this.errorText = this.$tr("inappropriate_uploaded_file");
          this.isError = true;
        }
        return;
      }
      if (selectedRows.length == 0) {
        this.clearFile();
        this.errorText = this.$tr("empty_file_uploaded");
        this.isError = true;
        return;
      }
      this.isDataClean = await this.validateRows(
        selectedHeaders,
        selectedRows,
        this,
        sheetName
      );
      if (!this.isDataClean) {
        this.errorText =
          "The excel sheet contains some errors. Fix those errors and re-upload!";
      }
      this.isFileBeingParsed = false;
    },
  },
  watch: {
    "form.file.$model": {
      handler: function (file) {
        if (this.validateFile(file)) {
          this.importExcelFile(file);
        }
      },
      deep: true,
    },
  },
  components: { InputCard, CfileItem2, CDatePicker, CBulkUploadErrors },
};
</script>
<style>
.form-custom-file .v-input__slot {
  background-color: var(--new-input-bg) !important;
}

.form-custom-file .v-input__slot .v-file-input__text {
  height: 140px;
}

.form-custom-file.v-input--is-focused .v-input__slot {
  background-color: #2e7be60f !important;
}

.form-custom-file .prepend-text {
  color: var(--v-primary-base);
  font-weight: 500;
}

.prepend-text2 {
  font-size: 12px;
}
</style>
