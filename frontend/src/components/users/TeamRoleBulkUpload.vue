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
          ref="team_role_bulk_upload_file_form"
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
          <template v-slot:[`item.name`]="{ item, index }">
            <div class="w-full">
              <v-edit-dialog
              class="d-block"
                :return-value.sync="item.name"
                large
                @open="onOpen(item.name, 'name')"
                @save="onSave('name', 'name', index+6, team_or_role_form.name)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.name, 'name', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center w-full'
                      : ''
                  }`"
                >
                  <span class="me-1">{{ item.name ? item.name : $tr("empty") }}</span>
                  <v-progress-circular v-if="checkIfHasErr(item.name, 'name', index + 6) && (index+6) == current_index" class="ma-0 float-right" indeterminate color="white" size="20" :width="2"/>
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('name')"
                    :label="$tr('label_required', $tr('name'))"
                    type="textfield"
                    v-model="$v.team_or_role_form.name.$model"
                    :rules="rules.nameRule($v.team_or_role_form, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.date_range`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.date_range"
                large
                @open="onOpen(item.date_range, 'date_range')"
                @save="onSave('date_range', 'date_range', index+6, team_or_role_form.date_range)"
              >
                <div>
                  {{ item.date_range ? item.date_range : "Optional" }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('label_required',$tr('date_range'))"
                    :label="$tr('date_range')"
                    type="date-picker"
                    @getDate="(date_range)=>{
                      $v.user.date_range.$model = date_range;
                    }"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.time_range`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.time_range"
                large
                @open="onOpen(item.time_range, 'time_range')"
                @save="onSave('time_range', 'time_range', index+6, team_or_role_form.time_range)"
              >
                <div>
                  {{ item.time_range ? item.time_range : "Optional" }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('time_range')"
                    :label="$tr('label_required', $tr('time_range'))"
                    type="time-picker"
                    v-model="$v.team_or_role_form.time_range.$model"
                    @getTime="(time)=>{
                      $v.team_or_role_form.time_range.$model = time
                    }"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.note`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.note"
                large
                @open="onOpen(item.note, 'note')"
                @save="onSave('note', 'note', index+6, team_or_role_form.note)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.note, 'note', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.note ? item.note : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :label="$tr('label_required', $tr('note'))"
                    :placeholder="$tr('note')"
                    type="textarea"
                    v-model="$v.team_or_role_form.note.$model"
                    :rules="rules.noteRule($v.team_or_role_form, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.schedule_type`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.schedule_type"
                large
                @open="onOpen(item.schedule_type, 'schedule_type')"
                @save="onSave('schedule_type', 'schedule_type', index+6, team_or_role_form.schedule_type)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.schedule_type, 'schedule_type', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.schedule_type ? item.schedule_type : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    :items="schedule_types"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    :label="$tr('label_required', $tr('schedule_type'))"
                    :placeholder="$tr('choose_item', $tr('schedule_type'))"
                    type="autocomplete"
                    v-model="$v.team_or_role_form.schedule_type.$model"
                    :rules="rules.schedule_typeRule($v.team_or_role_form, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>         
          <template v-slot:[`item.department`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.department"
                large
                @open="onOpen(item.department, 'department_id')"
                @save="onSave('department_id', 'department', index+6, team_or_role_form.department_id)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.department, 'department', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.department ? item.department : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    :items="departments"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    :label="$tr('label_required', $tr('department'))"
                    :placeholder="$tr('choose_item', $tr('department'))"
                    department
                    type="autocomplete"
                    v-model="$v.team_or_role_form.department_id.$model"
                    :rules="rules.departmentRule($v.team_or_role_form, $root)"
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
            :title="$tr('item_all_set', team_or_role == 'team' ? $tr('teams') : $tr('roles') )"
            :subTitle="$tr('you_can_access_your_item', team_or_role == 'team' ? $tr('teams') : $tr('roles'))"
          />
        </div>
      </template>
    </CustomStepper>
  </div>
</template>
<script>
import DataTable from "../../components/design/bulkupload/DataTable";
import CustomStepper from "../design/FormStepper/CustomStepper.vue";
import CustomButton from "../design/components/CustomButton";
import TeamRoleValidation from '../../validations/team-role-bulkpload-validations';
import readXlsxFile from 'read-excel-file';
import Dialog from "../design/Dialog/Dialog.vue";
import BulkUploadError from '../../components/users/BulkUploadError';
import BulkUploadFileLoader from '../../components/users/BulkUploadFileLoader';
import BulkUploadSuccessLoader from '../../components/users/BulkUploadSuccessLoader';
import FileUploadError from '../../components/users/FileUploadError';
import DoneMessage from "../design/components/DoneMessage.vue";
import HandleException from "../../helpers/handle-exception";
import CustomInput from "../../components/design/components/CustomInput.vue";
import { mapActions, mapGetters } from 'vuex';

export default {
  name: "team-or-bulk-upload",
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
  props: {
    tabKey: {
      type: String,
      require: true,
    }, 
    team_or_role: {
      type: String,
      require: true, 
    },
  },
  data(){
    return {
      showStartDatePicker: false, 
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
      rules: TeamRoleValidation.rules,
      team_or_role_form: {
        name: '', 
        note: '',
        department_id: '',
        schedule_type:  '',
        date_range: '',
        time_range : '',
      },
      fileExtension: TeamRoleValidation.fileExtension,
      isFileDownloaded: false,
      errors: [],
      rows: [],
      headers: [],
      selectedHeaders: [
        {
          text: this.$tr("name"),
          value: "name",
        },
        {
          text: this.$tr("department"),
          value: "department",
        },
        {
          text: this.$tr("note"),
          value: "note",
        },
        {
          text: this.$tr("schedule_type"),
          value: "schedule_type",
        },
        {
          text: this.$tr("time_range"),
          value: "time_range",
        },
        {
          text: this.$tr("date_range"),
          value: "date_range",
        },
      ],
      selectedRows: [],
      form: {
        file: '',
      },
      perpageDropdown: TeamRoleValidation.perpageDropdown,
      isDataLoaded: false,
      result: {},
      tableKey: 0,
      current_index: 0,
      schedule_types: [
        { id: "time", name: "time" },
        { id: "date", name: "date" },
        { id: 'unlimited', name: 'unlimited'},
      ],
      departments: [],
    }
  },
  validations: {
    team_or_role_form: TeamRoleValidation.validations.team_role,
    form: TeamRoleValidation.validations
  },
  methods: {
    ...mapActions({
      fetchTeams: "teams/fetchTeams",
      fetchRoles: "roles/fetchItems",
    }),
    onOpen(value, col){
      if(col =='date_range'){
        this.$v.team_or_role_form[col].$model = value.toISOString().substring(0, 10);
      }
      else {
        this.$v.team_or_role_form[col].$model = value;
      }
    },
    async onSave(key, col, index, value){
      let dublicable_columns = ['note', 'department', 'schedule_type'];
      if(!this.$v.team_or_role_form[key].$invalid){
        this.rows[index-6].some((column, i)=> {
          if(this.headers[i].toLowerCase() == col){
            this.rows[index-6][i] = value;
          }
        });
        // update selected rows
        this.selectedRows = await this.prepareRows(this.headers, this.rows);
        this.tableKey++;
        const errors_of_first_step = await this.validateSelectedRows(this.rows, this.headers);
        if(!dublicable_columns.includes(col)){
          let names = [], multi = true;
          this.selectedRows.forEach((row, counter) => {
            names.push({ value: row.name, index: counter+6 });
          });
          const errors_of_second_step = await this.checkUniquenessInFrontEnd(this.headers, this.rows);
          // merge both errors
          const finalErrors = await [...errors_of_first_step, ...errors_of_second_step];
          this.current_index = index;
          let if_names_exists = await this.addUniquenessErrors({names}, multi);
          this.current_index = 0;
          if_names_exists.forEach(row => {
            if(row.value){
              finalErrors.push({
                row: row.index,
                column: 'name',
                value: {
                  index: row.index,
                  value: this.$tr("item_already_exists", row.row_name_value),
                },
              });
            }
          });
          this.errors = await this.reArrangeErrArr(finalErrors);
          this.isDataClean = this.errors.length == 0 ? true : false;
        }
        else {
          this.errors.forEach(e => {
            if(e.column == col){
              e.row = e.row.filter(x => x != index);
              if(e.row.length == 0){
                e.value.pop();
              }
            }
          });
          this.errors = this.errors.length == 0 ? [] : this.errors;
          this.isDataClean = this.errors.length == 0 ? true : false;
          this.errors = this.errors.filter(err => (err.row.length > 0));
        }
        this.percentage = this.errors.length === 0 ? 0 : this.percentage;
        this.errors = this.errors.length == 0 ? [] : this.errors;
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
        const team_or_role_forms = this.selectedRows;
        try {
          const url = this.team_or_role + "-bulk-upload";
          const response = await this.$axios.post(url, team_or_role_forms);
          if (response?.status === 200 || response?.status == 201) {
            if(this.team_or_role == 'team'){
              await this.fetchTeams({key: this.tabKey});
            }
            else {
              await this.fetchRoles({key: this.tabKey});
            }
            this.$refs.customStepper.forceNext();
          }
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
        this.resetForm();
        this.clearFile();
        this.isDataLoaded = false;
        this.isLoadingOrSubmitting = false;
      } else {

      }
    },
    resetForm(){
      this.$v.team_or_role_form.$reset();
      this.$v.form.$reset();
      this.$refs.team_role_bulk_upload_file_form?.reset();
      this.$refs.team_role_bulk_upload_file_form?.resetValidation();
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
        this.isFileSelected = true;
        this.$v.form.file.$model = file;
        this.filePath = file;
      }
    },
    async onDownload() {
      if(!this.isFileDownloaded){
        try {
          this.isFileDownloaded = true;
          const url1 = "export-" + this.team_or_role + "-template";
          const response = await this.$axios.get(url1);
          const url = response.data;
          window.location.href = url;
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
          this.isFileDownloaded = false;
      }
    },
    validateForm(){
      this.$refs.team_role_bulk_upload_file_form.validate();
      this.$v.form.file.$touch();
    },
    isStepOneDataValid(){
      return this.$v.form.file.$invalid === false;
    },
    async changeStepper(step){
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
        //1. check the type of template by checking the headers
        let is_correct_form_uploaded = 
          selectedHeaders.every(h =>
            this.selectedHeaders.map(h=>h.value).includes(h.toLowerCase()) 
          );
        if (selectedHeaders == undefined || !is_correct_form_uploaded) {
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
      this.selectedRows = await this.prepareRows(selectedHeaders, selectedRows);
      // errors of first step of validation
      const errors_of_first_step = await this.validateSelectedRows(selectedRows, selectedHeaders);
      // check for uniqueness in front before backend,
      const errors_of_second_step = await this.checkUniquenessInFrontEnd(selectedHeaders, selectedRows);
      // start progress bar
      this.percentage = this.percentage + Number.parseInt(100/this.selectedRows.length);
      // merge both errors
      const finalErrors = await [...errors_of_first_step, ...errors_of_second_step];
      //check for uniqueness in backend,
      await this.selectedRows.forEach(async(row, counter) => {
        let multi = false;
        let value = await this.addUniquenessErrors(row, multi);
        if(value){
          finalErrors.push({
            row: counter + 6,
            column: 'name',
            value: {
              index: counter + 6,
              value: value,
            },
          });
        }
        let arranged_arr = await this.reArrangeErrArr(finalErrors);
        if((this.selectedRows.length-1) == counter){
          this.errors = arranged_arr.length == 0 ? [] : arranged_arr;
          this.isDataClean = arranged_arr.length == 0 ? true : false;
        }
        this.percentage = this.percentage + Number.parseInt(100/this.selectedRows.length);
      });
    },
    async prepareRows(selectedHeaders, selectedRows){
      let rows = [];
      selectedRows.forEach(async(r, counter) => {
        let row = {};
        r.forEach(async(col_value, index) => {
          let col_name = selectedHeaders[index].toLowerCase();
          row[col_name] = col_value;
          row.index = counter+6
        });
        rows.push(row);
      });
      return rows;
    },
    async checkUniquenessInFrontEnd(selectedHeaders, selectedRows){
      let returnedData = [], index = 0;
      for (let k = 0; k < selectedRows.length; k++) {
        for (let counter = k+1; counter < selectedRows.length; counter++) {
          if( selectedRows[counter][index] == selectedRows[k][index] ){
            returnedData.push({
              column_value: selectedRows[counter][index],
              row: counter+6,
              column: selectedHeaders[index].toLowerCase(),
              value: {
                index: counter+6,
                value: this.$tr("duplicate_row_for", selectedRows[counter][index])
              },
            });
          }
        }
      }
      return returnedData;
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
      await Object.values(final_errors).forEach(async element => {
        returnedData.push({
          column: element.column,
          row: Array.from(element.row).sort(function(a, b){return a-b}),
          value: Array.from(element.value),
          column_value: element.column_value
        });
      });
      return returnedData;
    },
    async addUniquenessErrors(rows, multi){
      if(multi){
        const result = await this.checkUniquenessFromApi(rows, multi);
        return result;
      }
      else {
        let columns = { name: rows.name };
        const result = await this.checkUniquenessFromApi(columns, multi);
        let isNameAlreadyExists = result?.exist;
        if(isNameAlreadyExists){
          let value = this.$tr("item_already_exists", rows.name);
          return value;
        }
        else {
          return false;
        }
      }
    },
    async checkUniquenessFromApi(columns, multi){
      try {
        let url = this.team_or_role + `s/check-uniqueness?multi=${multi}`;
        const response = await this.$axios.post(url, columns);
        return response?.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },
    async validateSelectedRows(rows, selectedHeaders){
      let errors = [];
      rows.forEach((elements, counter) => {
        elements.forEach((col, index) => {
          let key = selectedHeaders[index].toLowerCase();
          if(key === 'schedule_tye'){
            this.$v.team_or_role_form.schedule_tye.$model = col;
          } else if(key == 'department'){
            this.$v.team_or_role_form.department_id.$model = col;
          } else {
            this.$v.team_or_role_form[key].$model = col;
          }
          this.validateExcelColumn(counter, this.$v.team_or_role_form, errors, key);
        });
      });
      return errors;
    },
    validateExcelColumn(row, team_or_role_form, errors, key){
      let validatedArr, rule = key+'Rule';
      if(key == 'date_range' || key == 'time_range'){
        validatedArr = [true];
      } else {
        validatedArr = this.rules[rule](team_or_role_form, this.$root);
      }
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
  },
  async mounted() {
    if (this.departments.length == 0) {
      try {
        const res = await this.$axios.get(`departments?key=departments-of-logged-in-company`);
        this.departments = res?.data?.data;
      } catch (error) {
      }
    }
  },
  watch: {
    rows: function(rows){
      let init_height = 100;
      if(rows.length == 1){
        this.height = init_height+'px';
      }
      else if(rows.length == 2){
        this.height = init_height+50 + 'px';
      }
      else if(rows.length == 3 ){
        this.height = init_height+75+'px';
      }
      else if(rows.length == 4){
        this.height = init_height+100+'px';
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
