<template>
  <div>
    <v-dialog
      v-model="fileUploadError"
      width="450"
    >
      <Dialog  persistent @closeDialog="fileUploadError = false">
        <BulkUploadError :error_text="error_text" :isErrorOrWarning="isErrorOrWarning" />
      </Dialog>
    </v-dialog>
    <CustomStepper
      :headers="stepper_header"
      :canNext="false"
      ref="customStepper"
      @submit="onSubmit"
      :loading="isDataLoaded"
      :isSubmitting="isLoadingOrSubmitting"
      @validate="changeStepper"
    >
      <template v-slot:chooseFile>
        <v-form
          ref="reason_bulk_upload_file_form"
          lazy-validation
          @submit.prevent="onSubmit"
          :key="formKey"
        >
          <div class="w-full">
              <v-file-input
              class="custom-file"
              style="border: 0px !important; background-color: white !important; margin-bottom: 10px !important;"
              outlined
              :accept="fileExtension"
              prepend-icon=""
              dense
              @change="validateFile"
              @click:clear="clearFile"
              :success="filePath !== ''"
              :rules="rules.fileRule($v.form, $root)"
              >
              <template slot="prepend-inner">
                  <div
                  name="prepend-inner"
                  style="max-width: 260px; text-align: center;"
                  >
                  <p class="ma-0">
                      <svg
                      width="60"
                      height="60"
                      viewBox="100 100 500 140"
                      fill="currentColor"
                      style="transform: translateY(4px)"
                      >
                      <g>
                          <path
                          d="m479.71 198.78h-0.86328c-6.1133-51.496-50.938-91.512-102.99-91.512-42.559 0-81.152 26.367-96.625 65.824-2.7773-0.30469-5.5078-0.44141-8.168-0.44141-33.949 0-63.816 23.754-71.656 56.746-32.059 2.8945-56.934 29.938-56.934 62.348 0 34.512 28.094 62.625 62.625 62.625h119.63c4.293 0 7.7695-3.4766 7.7695-7.793 0-4.3164-3.4766-7.793-7.7695-7.793l-119.6-0.003906c-25.922 0-47.039-21.094-47.039-47.039 0-25.898 21.023-47.016 46.828-47.016l1.0039 0.023438c3.8281 0 7.1406-2.8477 7.7227-6.6484 4.0586-28.426 28.746-49.867 57.445-49.867 3.9883 0 7.957 0.42188 11.945 1.2383l1.4023 0.13672c3.4062 0 6.4648-2.2617 7.3984-5.4375 11.738-36.656 45.5-61.32 84.023-61.32 47.414 0 86.078 37.191 88.059 84.699 0.14063 2.0781 1.0977 4.0117 2.7305 5.4375 1.5391 1.3086 3.2422 1.6562 5.9258 1.8203 2.7305-0.30469 5.0859-0.46484 7.1641-0.46484 34.301 0 62.207 27.906 62.207 62.207s-27.906 62.207-62.207 62.207h-104.42c-4.293 0-7.7695 3.4766-7.7695 7.793s3.5 7.793 7.7695 7.793h104.42c42.887 0 77.793-34.883 77.793-77.793s-34.93-77.77-77.816-77.77z"
                          />
                          <path
                          d="m413.02 318.92c0.023437 0 0.023437 0 0 0 2.125 0 4.1289-0.81641 5.5312-2.2383 2.9883-3.0352 2.9883-7.957 0-11.012l-63.047-63.047c-2.9648-2.9414-8.0273-2.918-11.012 0.070312l-63.023 62.977c-1.4688 1.4688-2.2617 3.4297-2.2617 5.5078 0 2.1016 0.81641 4.0586 2.2383 5.4609 1.3984 1.4453 3.4062 2.2852 5.4844 2.3086h0.046875c2.1016 0 4.1055-0.81641 5.5312-2.2617l49.699-49.676v177.92c0 4.3164 3.5 7.793 7.7695 7.793 4.3164 0 7.8164-3.4766 7.8164-7.793v-177.92l49.652 49.652c1.4453 1.4375 3.4492 2.2578 5.5742 2.2578z"
                          />
                      </g>
                      </svg>
                  </p>
                  <div v-if="!isFileSelected">
                    <p class="mb-0" v-show="!isFileSelected">
                      {{ $tr("bulk_upload") }}
                    </p>
                    <p class="mb-0" v-show="!isFileSelected">
                      <small>{{ $tr("upload_only_excel") }}</small>
                    </p>
                  </div>
                  <div v-if="isFileSelected">
                    <p class="mb-0">name: {{ $v.form.file.$model.name }}</p>
                    <p class="mb-0">
                      <small>
                        size:
                        {{ ($v.form.file.$model.size / 1024).toFixed(2) }}
                        KB
                      </small>
                    </p>
                  </div>
                  </div>
              </template>
              </v-file-input>
          </div>
          <v-btn
            color="primary"
            class="download-button px-3"
            style="min-width: 160px"
            @click="onDownload"
            :loading="isFileDownloaded"
          >
            <template v-slot:loader>
              <span>
                <span>
                  {{ $tr("downloading") }}
                </span>
                <v-progress-circular
                  class="ma-0"
                  indeterminate
                  color="white"
                  size="25"
                  :width="2"
                />
              </span>
            </template>
            <template>
              <div>
                <span> {{ $tr("download_format") }}</span>
                <i class="fas fa-download ml-1"></i>
              </div>
            </template>
          </v-btn>
        </v-form>
      </template>
      <template v-slot:fileUpload>
        <!-- errors.length == 0 && !isDataClean -->
        <BulkUploadFileLoader :value="percentage" v-if="errors.length == 0 && !isDataClean" />
        <FileUploadError v-else-if="errors.length > 0 && !isDataClean" />
        <BulkUploadSuccessLoader v-else-if="isDataClean"/>
        <DataTable
          ref="tableRef"
          :headers="selectedHeaders"
          :items="selectedRows"
          :ItemsLength="selectedRows.length"
          :show_select="false"
          :elevation="0"
          :perpageDropdown="perpageDropdown"
          v-show="errors.length > 0"
          :height="height"
          :itemsPerPage="5"
          disbale_data_table_hover="disbale-data-table-hover"
          :defaultNote="false"
          :defaultLogo="false"
          :defaultCountry="false"
          :showRefreshBtn="false"
          :key="tableKey"
        >
          <template v-slot:[`item.title`]="{ item, index }">
            <div class="w-full">
              <v-edit-dialog
              class="d-inline-block"
                :return-value.sync="item.title"
                large
                @open="onOpen(item.title, 'title')"
                @save="onSave('title', 'title', index+6, reason.title)" 
              >
                <div
                  :class="`${
                    checkIfHasErr(item.title, 'title', index + 6)
                      ? 'text--white error-class  d-flex align-center justify-center w-full w-2-12'
                      : ''
                  }`"
                >
                  <span class="me-1">{{ item.title ? item.title : $tr("empty") }}</span>
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    placeholder="title"
                    label="title"
                    type="textfield"
                    v-model="$v.reason.title.$model"
                    :rules="rules.titleRule($v.reason, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
        </DataTable>
        <v-simple-table
          dense v-show="errors.length > 0"
        >
          <template v-slot:default>
            <thead>
              <tr class="table-header">
                <th colspan="3" style="color:red;" class="font-weight-bold">
                  {{$tr('validation_table_header')}}
                </th>
              </tr>
              <tr>
                <th class="text-left">
                  {{$tr('line')}}
                </th>
                <th class="text-left">
                  {{$tr('column')}}
                </th>
                <th class="text-left">
                  {{$tr('descriptions')}}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(error, index) in errors"
                :key="index"
                class="custom-row-error"
              >
                <td style="color: black !important">{{ error.row }}</td>
                <td style="color: black !important">{{ error.column }}</td>
                <td>
                  <div class="custom-error-details">
                    {{Array.from(new Set(error.value.map(e => e.value)))}}
                  </div>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </template>
      <template v-slot:check>
        <v-alert
          dense
          text
          type="success"
        >
          {{$tr('record_has_no_error')}}
        </v-alert>
        <DataTable
          ref="tableRef"
          :headers="selectedHeaders"
          :items="selectedRows"
          :ItemsLength="selectedRows.length"
          :show_select="false"
          :elevation="0"
          :perpageDropdown="perpageDropdown"
          :itemsPerPage="10"
          :showRefreshBtn="false"
        >
        </DataTable>
      </template>
      <template v-slot:done>
        <div class="d-flex justify-center">
          <done-message
            :title="$tr('item_all_set', $tr('reasons'))"
            :subTitle="$tr('you_can_access_your_item', $tr('reasons'))"
          />
        </div>
      </template>
    </CustomStepper>
  </div>
</template>
<script>
import DataTable from "../../../components/design/bulkupload/DataTable";
import CustomStepper from "../../design/FormStepper/CustomStepper.vue";
import CustomButton from "../../design/components/CustomButton";
import ReasonValidation from '../../../validations/reason_validations';
import readXlsxFile from 'read-excel-file';
import Dialog from "../../design/Dialog/Dialog.vue";
import BulkUploadError from '../../../components/users/BulkUploadError';
import BulkUploadFileLoader from '../../../components/users/BulkUploadFileLoader';
import BulkUploadSuccessLoader from '../../../components/users/BulkUploadSuccessLoader';
import FileUploadError from '../../../components/users/FileUploadError';
import DoneMessage from "../../design/components/DoneMessage.vue";
import HandleException from "../../../helpers/handle-exception";
import CustomInput from "../../../components/design/components/CustomInput.vue";
import {mapActions} from 'vuex';

export default {
  name: 'reason-bulk-upload',
  components: {
    CustomStepper,
    CustomButton,
    DataTable,
    Dialog,
    BulkUploadError,
    BulkUploadFileLoader,
    BulkUploadSuccessLoader,
    FileUploadError,
    DoneMessage,
    CustomInput,
  },
  props:{
    slug:{
      type: String
    }
  },
  data(){
    return {
      tableKey: 0, 
      formKey: 0,
      height: '',
      percentage: 0,
      error_text: '',
      isDataClean: false,
      isErrorOrWarning: true,
      fileUploadError: false,
      isDataLoaded: false,
      isLoadingOrSubmitting: false,
      stepper_header: [
      {icon: "fa-info-circle", title: "choose_file", slotName: "chooseFile"},
      {icon: "fas fa-cloud-upload-alt", title: "file_upload", slotName: "fileUpload"},
      {icon: "far fa-check-circle", title: "check", slotName: "check"},
      {icon: "fa-thumbs-up", title: "done", slotName: "done"},
      ],
      isFileSelected: false,
      filePath: '',
      rules: ReasonValidation.rules,
      reason: { title: '' },
      fileExtension: ReasonValidation.fileExtension,
      isFileDownloaded: false,
      errors: [],
      rows: [],
      headers: [],
      selectedHeaders: [],
      selectedRows: [],
      form: ReasonValidation.form,
      perpageDropdown: ReasonValidation.perpageDropdown,
      isDataLoaded: false,
    }
  },
  validations:{
    reason: ReasonValidation.validations.reason,
    form: ReasonValidation.validations
  },
  methods: {
    ...mapActions({
        fetchreasons: "reasons/fetchReasons"
    }),
    onOpen(value, col){
      this.$v.reason[col].$model = value;
    },
    async onSave(key, col, index, value){
      if(!this.$v.reason[key].$invalid){
        this.rows[index-6].some((column, i)=> {
          if(this.headers[i].toLowerCase() == col){
            this.rows[index-6][i] = value;
          }
        });
        // update selected rows
        this.selectedRows = await this.prepareRows(this.headers, this.rows);
        this.tableKey++;
        const errors = await this.validateSelectedRows(this.rows);
        this.errors = await this.reArrangeErrArr(errors);
        this.isDataClean = this.errors.length == 0 ? true : false;

      }
    },
    checkIfHasErr(value, col, index){
      let hasErr = false;
      if(value){
        this.errors.forEach(e => {
          if( e.row.includes(index) && e.column === col ){
            hasErr = true;
            return;
          }
        });
      }
      else {
        return true;
      }
      return hasErr;
    },
    async onSubmit(){
      if (this.selectedRows.length) {
        this.isDataLoaded = true;
        this.isLoadingOrSubmitting = true;
        const reasons = this.selectedRows;
        try {
          const response = await this.$axios.post("/reason-bulk-upload", {reasons, slug: this.slug});
          if (response?.status === 200) {
            await this.fetchreasons({
                slug: this.slug,
            });
            this.$refs.customStepper.forceNext();
            this.clearFile();
            this.resetForm();
          }
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
        this.isDataLoaded = false;
        this.isLoadingOrSubmitting = false;
      }
    },
    resetForm(){
      this.$v.reason.$reset();
      this.$v.form.$reset();
      this.$refs.reason_bulk_upload_file_form?.reset();
      this.$refs.reason_bulk_upload_file_form?.resetValidation();
      this.isFileSelected = false
      this.selectedHeaders = [];
      this.selectedRows = [];
      this.errors = [];
      this.isDataClean = false;
      this.percentage = 0;
      this.headers = [];
      this.rows = [];
    },
    clearFile(){
      this.formKey++;
      this.filePath = '';
      this.$v.form.file.$model = '';
    },
    toggleFileError(){
      this.fileUploadError = !this.fileUploadError;
    },
    validateFile(file){
      if (file) {
        const fileExtension = file.type;
        if (fileExtension !== this.fileExtension) {
          this.isErrorOrWarning = true;
          this.error_text = this.$tr("file_upload_extension_conflict");
          this.toggleFileError();
          return;
        }
        if(file){
            this.clearFile();
        }
        this.isFileSelected = true;
        this.$v.form.file.$model = file;
        this.filePath = file;
      }
    },
    async onDownload() {
      if(!this.isFileDownloaded){
        try {
          this.isFileDownloaded = true;
          const response = await this.$axios.get("/export-reason-template");
          const url = response.data;
          window.location.href = url;
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
        this.isFileDownloaded = false;
      }
    },
    validateForm(){
      this.$refs.reason_bulk_upload_file_form.validate();
      this.$v.form.file.$touch();
    },
    isStepOneDataValid(){
      return this.$v.form.file.$invalid === false;
    },
    async changeStepper(step){
      this.current_step = step;
      switch (step) {
        case 1:{
          if(!this.isStepOneDataValid()){
            this.validateForm();
            return;
          }
          this.$refs.customStepper.forceNext();
          await this.readFileAsXlsx();
        }
          break;
        case 2: {
          if(this.errors.length == 0 && this.isDataClean){
            this.$refs.customStepper.forceNext();
          }
        }
          break;
      }
    },
    async readFileAsXlsx(){
      readXlsxFile(this.form.file).then(async(rows) => {
        if(this.selectedRows.length){
          this.selectedRows = [];
          this.selectedHeaders = [];
        }
        let selectedRows = [], selectedHeaders = [];
        selectedRows = rows.filter((row, index)=>{ if(index > 4) return row });
        selectedHeaders = rows[4];
        this.rows = selectedRows;
        this.headers = selectedHeaders;
        if (selectedHeaders == undefined || selectedHeaders.length != 1) {
            this.clearFile();
            if(!selectedHeaders){
              this.error_text = this.$tr("wrong_template_uploaded");
              this.isErrorOrWarning = true;
            }
            else {
              this.error_text = this.$tr("inappropriate_uploaded_file");
              this.isErrorOrWarning = false;
            }
            this.$refs.customStepper.prev();
            this.toggleFileError();
            return;
        }
        if(selectedRows.length == 0){
            this.clearFile();
            this.error_text = this.$tr("empty_file_uploaded");
            this.isErrorOrWarning = false;
            this.$refs.customStepper.prev();
            this.toggleFileError();
            return;
        }
        await this.prepareMainValidations(selectedHeaders, selectedRows);
      });
    },
    async prepareMainValidations(selectedHeaders, selectedRows){
      selectedHeaders.forEach(element => {
        this.selectedHeaders.push({
          text: element,
          value: element.toLowerCase()
        });
      });
      this.percentage = this.percentage + Number.parseInt(100/this.selectedRows.length);
      this.selectedRows = await this.prepareRows(selectedHeaders, selectedRows);
      // errors of first step of validation
      const errors_of_first_step = await this.validateSelectedRows(selectedRows, selectedHeaders);
      this.errors = await this.reArrangeErrArr(errors_of_first_step);
      this.percentage = 100;
      this.isDataClean = this.errors.length == 0 ? true : false;
    },
    async prepareRows(selectedHeaders, selectedRows){
      let rows = [];
      selectedRows.forEach(async(r) => {
        let row = {};
        r.forEach(async(col_value, index) => {
          let col_name = selectedHeaders[index].toLowerCase();
          row[col_name] = col_value;
        });
        rows.push(row);
      });
      return rows;
    },
    async reArrangeErrArr(errors){
      let final_errors = [], returnedData = [];
      await errors.forEach(async (e, i) => {
        if(final_errors[e.column]){
          final_errors[e.column].row.add(e.row);
          final_errors[e.column].value.add(e.value);
          final_errors[e.column].column_value = e.column_value;
          return;
        }
        final_errors[e.column] = {
          row: new Set().add(e.row),
          value: new Set().add(e.value),
          column: e.column,
          column_value: e.column_value,
        };
      });
      Object.values(final_errors).forEach(async element => {
        returnedData.push({
          column: element.column,
          row: Array.from(element.row).sort(function(a, b){return a-b}),
          value: Array.from(element.value),
          column_value: element.column_value
        });
      });
      return returnedData;
    },
    async validateSelectedRows(rows){
      let errors = [];
      rows.forEach((elements, counter) => {
        elements.forEach(col => {
          this.$v.reason.title.$model = col;
          this.validateExcelColumn(counter, this.$v.reason, errors, 'title');
        });
      });
      return errors;
    },
    validateExcelColumn(row, reason, errors, key){
      let validatedArr, rule = key+'Rule';
      validatedArr = this.rules[rule](reason, this.$root);
      this.ifHasError(errors, validatedArr, row, key);
    },
    ifHasError(errors, validationArr, row, col){
      validationArr.forEach(element => {
        if(typeof element == 'function'){
          if(typeof element() == 'string'){
            let rowError = row + 6;
            errors.push({
              row: rowError,
              column: col,
              value: {
                index: rowError,
                value: element(),
              }
            });
          }
        }
      });
      return errors;
    },
    // fetch items according to table name
    async fetchNessaryData(tableName) {
      try {
        const url = `data?table=${tableName}`;
        const response = await this.$axios.get(url);
        return response.data;
      } catch (error) {
        return null;
      }
    },
  },
  watch: {
    rows: function(rows){
      let init_height = 100;
      if(rows.length == 1){
        this.height = init_height+'px';
      }
      else if(rows.length == 2){
        this.height = init_height+25 + 'px';
      }
      else if(rows.length == 3 ){
        this.height = init_height+50+'px';
      }
      else if(rows.length == 4){
        this.height = init_height+75+'px';
      }
      else {
        this.height = '205px';
      }
    }
  }
}
</script>
<style scoped>
.error-class {
  background-color: red;
  color:white;
  border-radius: 2px;
}
.download-button {
  font-size: 16px;
  height: 38px !important;
  font-weight: 400;
  border-radius: 4px;
}
.avatar {
  height: 110px;
  width: 110px;
  background-color: var(--gray-cyan);
  border: 2px solid var(--gray-cyan);
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
}

.avatar .v-icon {
  color: var(--color-white);
  font-size: 60px;
}

.custom-file .v-file-input__text {
  background-color: white !important;
}

.custom-file .v-input__slot fieldset {
  border-style: dashed !important;
}

.custom-file .v-input__slot {
  display: flex;
  justify-content: center;
  align-items: center;
}

.upload-first-p {
  font-size: 18px;
  color: var(--upload-input-first-p);
  letter-spacing: 0.5px;
}

.upload-second-p {
  color: var(--input-border-color);
  line-height: 1.5;
}

.custom-file .v-input__prepend-inner {
  position: absolute;
}

.table-header, .table-header > th {
  border-top-right-radius: 5px !important;
  border-top-left-radius: 5px !important;
}

.v-data-table table tbody tr:not(.v-data-table__selected):hover{
  background-color: #fffff7 !important;
}
.custom-row-error:hover
  .custom-error-details{
    color: black !important;
}
.custom-error-details{
  color: red !important;
}

.v-small-dialog__actions {
  display: none !important;
}

.stepper-btn {
  font-size: 16px;
  height: 38px !important;
  font-weight: 400;
  border-radius: 4px;
}
</style>
