<template>
  <div>
    <v-dialog v-model="fileUploadError" width="450">
      <Dialog persistent @closeDialog="fileUploadError = false">
        <BulkUploadError
          :error_text="error_text"
          :isErrorOrWarning="isErrorOrWarning"
        />
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
          ref="user_bulk_upload_file_form"
          lazy-validation
          @submit.prevent="onSubmit"
          :key="formKey"
        >
          <div class="w-full">
            <v-file-input
              class="custom-file"
              style="
                border: 0px !important;
                background-color: white !important;
                margin-bottom: 10px !important;
              "
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
                  style="max-width: 260px; text-align: center"
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
        <BulkUploadFileLoader
          :value="percentage"
          v-if="errors.length == 0 && !isDataClean"
        />
        <FileUploadError v-else-if="errors.length > 0 && !isDataClean" />
        <BulkUploadSuccessLoader v-else-if="isDataClean" />
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
          <template v-slot:[`item.firstname`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.firstname"
                large
                @open="onOpen(item.firstname, 'firstname')"
                @save="
                  onSave('firstname', 'firstname', index + 6, user.firstname)
                "
              >
                <div
                  :class="`${
                    checkIfHasErr(item.firstname, 'firstname', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.firstname ? item.firstname : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('firstname')"
                    :label="$tr('label_required', $tr('firstname'))"
                    type="textfield"
                    v-model="$v.user.firstname.$model"
                    :rules="rules.firstnameRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.lastname`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.lastname"
                large
                @open="onOpen(item.lastname, 'lastname')"
                @save="onSave('lastname', 'lastname', index + 6, user.lastname)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.lastname, 'lastname', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.lastname ? item.lastname : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('lastname')"
                    :label="$tr('label_required', $tr('lastname'))"
                    type="textfield"
                    v-model="$v.user.lastname.$model"
                    :rules="rules.lastnameRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.username`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.username"
                large
                @open="onOpen(item.username, 'username')"
                @save="onSave('username', 'username', index + 6, user.username)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.username, 'username', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.username ? item.username : $tr("empty") }}
                  <v-progress-circular
                    v-if="
                      checkIfHasErr(item.name, 'username', index + 6) &&
                      index + 6 == current_index &&
                      current_column == 'username'
                    "
                    class="ma-0 float-right ml-2"
                    indeterminate
                    color="white"
                    size="20"
                    :width="2"
                  />
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :label="$tr('label_required', $tr('username'))"
                    :placeholder="$tr('username')"
                    type="textfield"
                    v-model="$v.user.username.$model"
                    :rules="rules.usernameRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.password`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.password"
                large
                @open="onOpen(item.password, 'password')"
                @save="onSave('password', 'password', index + 6, user.password)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.password, 'password', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.password ? item.password : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('password')"
                    :label="$tr('label_required', $tr('password'))"
                    type="textfield"
                    v-model="$v.user.password.$model"
                    :rules="rules.passwordRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.whatsapp`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.whatsapp"
                large
                @open="onOpen(item.whatsapp, 'whatsapp')"
                @save="onSave('whatsapp', 'whatsapp', index + 6, user.whatsapp)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.whatsapp, 'whatsapp', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.whatsapp ? item.whatsapp : $tr("empty") }}
                  <v-progress-circular
                    v-if="
                      checkIfHasErr(item.name, 'whatsapp', index + 6) &&
                      index + 6 == current_index &&
                      current_column == 'whatsapp'
                    "
                    class="ma-0 float-right ml-2"
                    indeterminate
                    color="white"
                    size="20"
                    :width="2"
                  />
                </div>
                <template v-slot:input>
                  <PhoneNumberInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('label_required', $tr('whatsapp'))"
                    :label="$tr('label_required', $tr('whatsapp'))"
                    v-model="$v.user.whatsapp.$model"
                    :rules="rules.whatsappRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.phone`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.phone"
                large
                @open="onOpen(item.phone, 'phone')"
                @save="onSave('phone', 'phone', index + 6, user.phone)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.phone, 'phone', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.phone ? item.phone : $tr("empty") }}
                  <v-progress-circular
                    v-if="
                      checkIfHasErr(item.name, 'phone', index + 6) &&
                      index + 6 == current_index &&
                      current_column == 'phone'
                    "
                    class="ma-0 float-right ml-2"
                    indeterminate
                    color="white"
                    size="20"
                    :width="2"
                  />
                </div>
                <template v-slot:input>
                  <PhoneNumberInput
                    style="width: 300px !important"
                    class="mt-3"
                    :label="$tr('label_required', $tr('phone'))"
                    v-model="$v.user.phone.$model"
                    :rules="rules.phoneRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.birth_date`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.birth_date"
                large
                @open="onOpen(item.birth_date, 'birth_date')"
                @save="
                  onSave('birth_date', 'birth_date', index + 6, user.birth_date)
                "
              >
                <div
                  :class="`${
                    checkIfHasErr(item.birth_date, 'birth_date', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.birth_date ? item.birth_date : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('label_required',$tr('birth_date'))"
                    :label="$tr('birth_date')"
                    type="date-picker"
                    :rules="rules.dobRule($v.user, $root)"
                    @getDate="(birth_date)=>{
                      $v.user.birth_date.$model = birth_date;
                    }"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.gender`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.gender"
                large
                @open="onOpen(item.gender, 'gender')"
                @save="onSave('gender', 'gender', index + 6, user.gender)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.gender, 'gender', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.gender ? item.gender : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('choose_item', $tr('gender'))"
                    :label="$tr('label_required', $tr('gender'))"
                    v-model="$v.user.gender.$model"
                    :rules="rules.genderRule($v.user, $root)"
                    :items="genders"
                    item-value="name"
                    item-text="name"
                    type="autocomplete"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.address`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.address"
                large
                @open="onOpen(item.address, 'address')"
                @save="onSave('address', 'address', index + 6, user.address)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.address, 'address', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.address ? item.address : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :label="$tr('label_required', $tr('address'))"
                    :placeholder="$tr('address')"
                    type="textarea"
                    v-model="$v.user.address.$model"
                    :rules="rules.addressRule($v.user, $root)"
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
                @save="onSave('note', 'note', index + 6, user.note)"
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
                    :placeholder="$tr('label_required', $tr('note'))"
                    :label="$tr('note')"
                    type="textarea"
                    v-model="$v.user.note.$model"
                    :rules="rules.noteRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.language`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.language"
                large
                @open="onOpen(item.language, 'language_id')"
                @save="
                  onSave('language_id', 'language', index + 6, user.language_id)
                "
              >
                <div
                  :class="`${
                    checkIfHasErr(item.language, 'language', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.language ? item.language : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    :items="countryLanguages"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    :label="$tr('label_required', $tr('language'))"
                    :placeholder="$tr('choose_item', $tr('language'))"
                    type="autocomplete"
                    v-model="$v.user.language_id.$model"
                    :rules="rules.languageRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.current_country`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.current_country"
                large
                @open="onOpen(item.current_country, 'current_country_id')"
                @save="
                  onSave(
                    'current_country_id',
                    'current_country',
                    index + 6,
                    user.current_country_id
                  )
                "
              >
                <div
                  :class="`${
                    checkIfHasErr(
                      item.current_country,
                      'current_country',
                      index + 6
                    )
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{
                    item.current_country ? item.current_country : $tr("empty")
                  }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    :items="countries"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    :label="$tr('label_required', $tr('country'))"
                    :placeholder="$tr('choose_item', $tr('country'))"
                    type="autocomplete"
                    v-model="$v.user.current_country_id.$model"
                    :rules="rules.currentCountryRule($v.user, $root)"
                    country
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.state`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.state"
                large
                @open="onOpen(item.state, 'state_id')"
                @save="onSave('state_id', 'state', index + 6, user.state_id)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.state, 'state', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.state ? item.state : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    :items="selectedCountryStates"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    type="autocomplete"
                    class="mt-3"
                    :label="$tr('label_required', $tr('state'))"
                    :placeholder="$tr('choose_item', $tr('state'))"
                    v-model="$v.user.state_id.$model"
                    :rules="rules.countryStateRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.role`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.role"
                large
                @open="onOpen(item.role, 'role_id')"
                @save="onSave('role_id', 'role', index + 6, user.role_id)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.role, 'role', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.role ? item.role : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    :items="selectedRoles"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    :label="$tr('label_required', $tr('roles'))"
                    :placeholder="$tr('choose_item', $tr('roles'))"
                    type="autocomplete"
                    v-model="$v.user.role_id.$model"
                    :rules="rules.roleRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.company`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.company"
                large
                @open="onOpen(item.company, 'company_id')"
                @save="()=>{
                  onSave('company_id', 'company', index + 6, user.company_id);
                  let company_id = companies.find(c => c.name === user.company_id).id;
                  loadTeamsOfCompany(company_id);
                  loadRolesOfCompany(company_id);
                }"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.company, 'company', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.company ? item.company : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    :items="companies"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    :label="$tr('label_required', $tr('companies'))"
                    :placeholder="$tr('choose_item', $tr('companies'))"
                    type="autocomplete"
                    v-model="$v.user.company_id.$model"
                    :rules="rules.companyRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.team`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.team"
                large
                @open="onOpen(item.team, 'team_id')"
                @save="onSave('team_id', 'team', index + 6, user.team_id)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.team, 'team', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.team ? item.team : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    :items="selectedTeams"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    :label="$tr('label_required', $tr('teams'))"
                    :placeholder="$tr('choose_item', $tr('teams'))"
                    type="autocomplete"
                    v-model="$v.user.team_id.$model"
                    :rules="rules.teamRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
          <template v-slot:[`item.email`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.email"
                large
                @open="onOpen(item.email, 'email')"
                @save="onSave('email', 'email', index + 6, user.email)"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.email, 'email', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.email ? item.email : $tr("empty") }}
                  <v-progress-circular
                    v-if="
                      checkIfHasErr(item.name, 'email', index + 6) &&
                      index + 6 == current_index &&
                      current_column == 'email'
                    "
                    class="ma-0 float-right ml-2"
                    indeterminate
                    color="white"
                    size="20"
                    :width="2"
                  />
                </div>
                <template v-slot:input>
                  <CustomInput
                    style="width: 300px !important"
                    class="mt-3"
                    :placeholder="$tr('label_required',$tr('email'))"
                    :label="$tr('email')"
                    type="textfield"
                    v-model="$v.user.email.$model"
                    :rules="rules.emailRule($v.user, $root)"
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>          
          <template v-slot:[`item.country`]="{ item, index }">
            <div>
              <v-edit-dialog
                :return-value.sync="item.country"
                large
                @open="onOpen(item.country, 'country_id')"
                @save="()=>{
                  let countryId = countries.find(c => c.name === user.country_id).id;
                  onSave('country_id', 'country', index + 6, user.country_id);
                  loadCountryLanguage(countryId);
                  onCurrentCountrySelected(countryId);
                  fetchCompanies([countryId]);
                }"
              >
                <div
                  :class="`${
                    checkIfHasErr(item.country, 'country', index + 6)
                      ? 'text--white error-class px-5 d-flex align-center justify-center'
                      : ''
                  }`"
                >
                  {{ item.country ? item.country : $tr("empty") }}
                </div>
                <template v-slot:input>
                  <CustomInput
                    class="mt-3"
                    :items="countries"
                    style="width: 300px !important"
                    item-value="name"
                    item-text="name"
                    :label="$tr('label_required', $tr('countries'))"
                    :placeholder="$tr('choose_item', $tr('countries'))"
                    type="autocomplete"
                    v-model="$v.user.country_id.$model"
                    :rules="rules.countryRule($v.user, $root)"
                    country
                  />
                </template>
              </v-edit-dialog>
            </div>
          </template>
        </DataTable>
        <v-simple-table dense v-show="errors.length > 0">
          <template v-slot:default>
            <thead>
              <tr class="table-header">
                <th colspan="3" style="color: red" class="font-weight-bold">
                  {{ $tr("validation_table_header") }}
                </th>
              </tr>
              <tr>
                <th class="text-left">
                  {{ $tr("line") }}
                </th>
                <th class="text-left">
                  {{ $tr("column") }}
                </th>
                <th class="text-left">
                  {{ $tr("descriptions") }}
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
                    {{ Array.from(new Set(error.value.map((e) => e.value))) }}
                  </div>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </template>
      <template v-slot:check>
        <v-alert dense text type="success">
          {{ $tr("record_has_no_error") }}
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
            :title="$tr('item_all_set', $tr('users'))"
            :subTitle="$tr('you_can_access_your_item', $tr('users'))"
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
import UserValidation from "../../validations/bulkupload-validations";
import readXlsxFile from "read-excel-file";
import Dialog from "../design/Dialog/Dialog.vue";
import BulkUploadError from "../../components/users/BulkUploadError";
import BulkUploadFileLoader from "../../components/users/BulkUploadFileLoader";
import BulkUploadSuccessLoader from "../../components/users/BulkUploadSuccessLoader";
import FileUploadError from "../../components/users/FileUploadError";
import DoneMessage from "../design/components/DoneMessage.vue";
import HandleException from "../../helpers/handle-exception";
import CustomInput from "../../components/design/components/CustomInput.vue";
import PhoneNumberInput from "../../components/design/components/PhoneNumberInput";
import { mapActions, mapGetters } from "vuex";

export default {
  name: "user-bulk-upload",
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
    PhoneNumberInput,
  },
  props: {
    tabKey: {
      type: String,
      require: true,
    },
  },
  data() {
    return {
      states: [],
      showStartTimePicker: false,
      height: "",
      percentage: 0,
      error_text: "",
      isDataClean: false,
      isErrorOrWarning: true,
      fileUploadError: false,
      isDataLoaded: false,
      isLoadingOrSubmitting: false,
      genders: [{id: 1, name: this.$tr('male')}, {id: 2, name: this.$tr('female')}],
      stepper_header: [
        {
          icon: "fa-info-circle",
          title: "choose_file",
          slotName: "chooseFile",
        },
        {
          icon: "fas fa-cloud-upload-alt",
          title: "file_upload",
          slotName: "fileUpload",
        },
        { icon: "far fa-check-circle", title: "check", slotName: "check" },
        { icon: "fa-thumbs-up", title: "done", slotName: "done" },
      ],
      isFileSelected: false,
      filePath: "",
      rules: UserValidation.rules,
      user: UserValidation.schema,
      fileExtension: UserValidation.fileExtension,
      isFileDownloaded: false,
      errors: [],
      formKey: 0,
      rows: [],
      headers: [],
      selectedHeaders: [
        {
          text: this.$tr("firstname"),
          value: "firstname",
        },
        {
          text: this.$tr("lastname"),
          value: "lastname",
        },
        {
          text: this.$tr("username"),
          value: "username",
        },
        {
          text: this.$tr("email"),
          value: "email",
        },
        {
          text: this.$tr("phone"),
          value: "phone",
        },
        {
          text: this.$tr("password"),
          value: "password",
        },
        {
          text: this.$tr("whatsapp"),
          value: "whatsapp",
        },
        {
          text: this.$tr("gender"),
          value: "gender",
        },
        {
          text: this.$tr("address"),
          value: "address",
        },
        {
          text: this.$tr("birth_date"),
          value: "birth_date",
        },
        {
          text: this.$tr("country"),
          value: "country",
        },
        {
          text: this.$tr("language"),
          value: "language",
        },
        {
          text: this.$tr("companies"),
          value: "company",
        },
        {
          text: this.$tr("roles"),
          value: "role",
        },
        {
          text: this.$tr("team"),
          value: "team",
        },
        {
          text: this.$tr("state"),
          value: "state",
        },
        {
          text: this.$tr("current_country"),
          value: "current_country",
        },
        {
          text: this.$tr("note"),
          value: "note",
        },
      ],
      selectedRows: [],
      form: UserValidation.form,
      perpageDropdown: UserValidation.perpageDropdown,
      isDataLoaded: false,
      result: {},
      tableKey: 0,
      current_index: 0,
      current_step: 0,
      industries: [],
      current_column: "",
      showStartDatePicker: false,
      selectedCountryStates: [],
      selectedTeams: [],
      selectedRoles: [],
    };
  },
  validations: {
    user: UserValidation.validations,
    form: UserValidation.validations,
  },
  methods: {
    ...mapActions({
      fetchUsers: "users/fetchItems",
      fetchTeams: "teams/fetchTeams",
      fetchCountries: "filterData/fetchCountriesWithCompanies",
      fetchLanguages: "languages/fetchAllLanguages",
      fetchRoles: "roles/fetchItems",
      fetchCountryLanguages: "filterData/fetchCountryLanguages",
			fetchCompanies: "filterData/fetchCompaniesOfCountries",
    }),
    // find all selected company's role
    async loadRolesOfCompany(company_id){
      try {
        const response = await this.$axios.get(
          `roles?key=roles-of-company&companyId=${company_id}`
        );
        this.selectedRoles = response?.data?.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    // find all selected company's team
    async loadTeamsOfCompany(company_id){
      try {
        const response = await this.$axios.get(
          `teams?key=teams-of-company&companyId=${company_id}`
        );
        this.selectedTeams = response?.data?.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    // find all selected country states
    async onCurrentCountrySelected(countryId) {
      try {
        const response = await this.$axios.get(
          `states/country-states/${countryId}`
        );
        this.selectedCountryStates = response?.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    loadCountryLanguage(countryId){
      this.fetchCountryLanguages(countryId);  
    },
    onOpen(value, col) {
      this.$v.user[col].$model = value;
    },
    async onSave(key, col, index, value) {
      let non_dublicable_columns = ["email", "username", "phone", "whatsapp"];
      if (!this.$v.user[key].$invalid) {
        this.rows[index - 6].some((column, i) => {
          if (this.headers[i].toLowerCase() == col) {
            this.rows[index - 6][i] = value;
          }
        });
        // update selected rows
        this.selectedRows = await this.prepareRows(this.headers, this.rows);
        this.tableKey++;
        const errors_of_first_step = await this.validateSelectedRows(
          this.rows,
          this.headers
        );
        if (non_dublicable_columns.includes(col)) {
          let names = [];
          this.selectedRows.forEach((row, counter) => {
            names.push({ value: row.name, index: counter + 6 });
          });
          const errors_of_second_step = await this.checkUniquenessInFrontEnd(
            this.headers,
            this.rows
          );
          // merge both errors
          const finalErrors = [
            ...errors_of_first_step,
            ...errors_of_second_step,
          ];
          this.current_index = index;
          this.current_column = col;
          let columns = [
            ...this.mapUserColumns("username"),
            ...this.mapUserColumns("email"),
            ...this.mapUserColumns("phone"),
            ...this.mapUserColumns("whatsapp"),
          ];
          let if_names_exists = await this.checkUniquenessFromApi(columns);
          this.current_index = 0;
          this.current_column = "";
          await if_names_exists.forEach(async (ele) => {
            if (ele.if_exists) {
              finalErrors.push({
                row: ele.index,
                column: ele.column,
                value: {
                  index: ele.index,
                  value: this.$tr("item_already_exists", ele.column_value),
                },
              });
            }
          });
          this.errors = await this.reArrangeErrArr(finalErrors);
          this.isDataClean = this.errors.length == 0 ? true : false;
        } else {
          this.errors.forEach((e) => {
            if (e.column == col) {
              e.row = e.row.filter((x) => x != index);
              if (e.row.length == 0) {
                e.value.pop();
              }
            }
          });
          this.errors = this.errors.filter((err) => err.row.length > 0);
          this.isDataClean = this.errors.length == 0 ? true : false;
        }
        this.isDataClean = this.errors.length == 0 ? true : false;
      }
    },
    mapUserColumns(column){
      return this.selectedRows.map((row, index) => {
        return {
          name: column,
          value: row[column],
          index: index + 6,
        };
      });
    },
    checkIfHasErr(value, col, index) {
      let hasErr = false;
      if (value) {
        this.errors.forEach((e) => {
          if (e.row.includes(index) && e.column === col) {
            hasErr = true;
            return;
          }
        });
      } else {
        return true;
      }
      return hasErr;
    },
    async onSubmit() {
      if (this.selectedRows.length) {
        this.isDataLoaded = true;
        this.isLoadingOrSubmitting = true;
        const users = this.selectedRows;
        try {
          const response = await this.$axios.post("/bulk-upload-user", users);
          if (response?.status === 200) {
            await this.fetchUsers({ key: this.tabKey });
            this.$refs.customStepper.forceNext();
          }
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
        this.isDataLoaded = false;
        this.isLoadingOrSubmitting = false;
        this.resetForm();
        this.clearFile();
      }
    },
    resetForm() {
      this.$v.user.$reset();
      this.$v.form.$reset();
      this.$refs.user_bulk_upload_file_form?.reset();
      this.$refs.user_bulk_upload_file_form?.resetValidation();
      this.isFileSelected = false;
      this.selectedHeaders = [];
      this.selectedRows = [];
      this.errors = [];
      this.isDataClean = false;
      this.percentage = 0;
      this.headers = [];
      this.rows = [];
    },
    clearFile() {
      this.formKey++;
      this.filePath = "";
      this.$v.form.file.$model = "";
    },
    toggleFileError() {
      this.fileUploadError = !this.fileUploadError;
    },
    validateFile(file) {
      if (file) {
        const fileExtension = file.type;
        if (fileExtension !== this.fileExtension) {
          this.isErrorOrWarning = true;
          this.error_text = this.$tr("file_upload_extension_conflict");
          this.toggleFileError();
          return;
        }
        if (file) {
          this.clearFile();
        }
        this.isFileSelected = true;
        this.$v.form.file.$model = file;
        this.filePath = file;
      }
    },
    async onDownload() {
      if (!this.isFileDownloaded) {
        try {
          this.isFileDownloaded = true;
          const response = await this.$axios.get("/export-user-template");
          const url = response.data;
          window.location.href = url;
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
          this.isFileDownloaded = false;
      }
    },
    validateForm() {
      this.$refs.user_bulk_upload_file_form.validate();
      this.$v.form.file.$touch();
    },
    isStepOneDataValid() {
      return this.$v.form.file.$invalid === false;
    },
    async changeStepper(step) {
      this.current_step = step;
      switch (step) {
        case 1:
          {
            if (!this.isStepOneDataValid()) {
              this.validateForm();
              return;
            }
            this.$refs.customStepper.forceNext();
            await this.readFileAsXlsx();
          }
          break;
        case 2:
          {
            if (this.errors.length == 0 && this.isDataClean) {
              this.$refs.customStepper.forceNext();
            }
          }
          break;
      }
    },
    async readFileAsXlsx() {
      readXlsxFile(this.form.file).then(async (rows) => {
        if (this.selectedRows.length) {
          this.selectedRows = [];
          this.selectedHeaders = [];
        }
        let selectedRows = [],
          selectedHeaders = [];
        selectedRows = rows.filter((row, index) => {
          if (index > 4) return row;
        });
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
          if (!selectedHeaders) {
            this.error_text = this.$tr("wrong_template_uploaded");
            this.isErrorOrWarning = true;
          } else {
            this.error_text = this.$tr("inappropriate_uploaded_file");
            this.isErrorOrWarning = false;
          }
          this.$refs.customStepper.prev();
          this.toggleFileError();
          return;
        }
        if (selectedRows.length == 0) {
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
    async prepareMainValidations(selectedHeaders, selectedRows) {
      this.selectedRows = await this.prepareRows(selectedHeaders, selectedRows);
      // errors of first step of validation
      const errors_of_first_step = await this.validateSelectedRows(
        selectedRows,
        selectedHeaders
      );
      // check for uniqueness in front before backend,
      const errors_of_second_step = await this.checkUniquenessInFrontEnd(
        selectedHeaders,
        selectedRows
      );
      // merge both errors
      const finalErrors = [...errors_of_first_step, ...errors_of_second_step];
      //check for uniqueness in backend,'
      let columns = [
        ...this.mapUserColumns('username'), 
        ...this.mapUserColumns('email'), 
        ...this.mapUserColumns('phone'), 
        ...this.mapUserColumns('whatsapp')
      ];
      this.percentage =
        this.percentage + Number.parseInt(100 / selectedRows.length);
      let values = await this.checkUniquenessFromApi(columns);
      await values.forEach(async (ele, counter) => {
        if (ele.if_exists) {
          finalErrors.push({
            row: ele.index,
            column: ele.column,
            value: {
              index: ele.index,
              value: this.$tr("item_already_exists", ele.column_value),
            },
          });
        }
        if (counter == values.length - 1) {
          this.percentage = 100;
        }
      });
      this.errors = await this.reArrangeErrArr(finalErrors);
      this.isDataClean = this.errors.length == 0 ? true : false;
    },
    async prepareRows(selectedHeaders, selectedRows) {
      let rows = [];
      selectedRows.forEach(async (r, counter) => {
        let row = {};
        r.forEach(async (col_value, index) => {
          let col_name = selectedHeaders[index].toLowerCase();
          row[col_name] = col_value;
          row.index = counter + 6;
        });
        rows.push(row);
      });
      return rows;
    },
    async checkUniquenessInFrontEnd(selectedHeaders, selectedRows) {
      let not_dublicable_columns = [
          "username",
          "phone",
          "whatsapp",
          "email",
          "image",
        ],
        returnedData = [];
      for (let index = 0; index < selectedHeaders.length; index++) {
        for (let k = 0; k < selectedRows.length; k++) {
          for (let counter = k + 1; counter < selectedRows.length; counter++) {
            if (
              selectedRows[counter][index] == selectedRows[k][index] &&
              not_dublicable_columns.includes(
                selectedHeaders[index].toLowerCase()
              )
            ) {
              returnedData.push({
                row: counter + 6,
                column: selectedHeaders[index].toLowerCase(),
                value: {
                  index: counter + 6,
                  value: this.$tr(
                    "duplicate_row_for",
                    selectedRows[counter][index]
                  ),
                },
              });
            }
          }
        }
      }
      return returnedData;
    },
    async reArrangeErrArr(errors) {
      let final_errors = [],
        returnedData = [];
      await errors.forEach(async (e, i) => {
        if (final_errors[e.column]) {
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
      Object.values(final_errors).forEach(async (element) => {
        returnedData.push({
          column: element.column,
          row: Array.from(element.row).sort(function (a, b) {
            return a - b;
          }),
          value: Array.from(element.value),
          column_value: element.column_value,
        });
      });
      return returnedData;
    },
    async checkUniquenessFromApi(columns) {
      try {
        let url = `users/check-uniqueness-of-mulitple-columns`;
        const response = await this.$axios.post(url, columns);
        return response?.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },
    async validateSelectedRows(rows, selectedHeaders) {
      let errors = [];
      rows.forEach((elements, counter) => {
        elements.forEach((col, index) => {
          let key = selectedHeaders[index].toLowerCase();
          if (key === "country") {
            this.$v.user.country_id.$model = col;
          } else if (key === "language") {
            this.$v.user.language_id.$model = col;
          } else if (key === "state") {
            this.$v.user.state_id.$model = col;
          } else if (key === "current_country") {
            this.$v.user.current_country_id.$model = col;
          } else if (key === "team") {
            this.$v.user.team_id.$model = col;
          } else if (key === "company") {
            this.$v.user.company_id.$model = col;
          } else if (key === "role") {
            this.$v.user.role_id.$model = col;
          } else {
            this.$v.user[key].$model = col;
          }
          this.validateExcelColumn(counter, this.$v.user, errors, key);
        });
      });
      return errors;
    },
    validateExcelColumn(row, user, errors, key) {
      let validatedArr,
        rule = key + "Rule";
      if (key === "current_country") {
        validatedArr = this.rules.currentCountryRule(user, this.$root);
      } else if (key === "birth_date") {
        validatedArr = this.rules.dobRule(user, this.$root);
      } else if (key === "state") {
        validatedArr = this.rules.countryStateRule(user, this.$root);
      } else {
        validatedArr = this.rules[rule](user, this.$root);
      }
      this.ifHasError(errors, validatedArr, row, key);
    },
    ifHasError(errors, validationArr, row, col) {
      validationArr.forEach((element) => {
        if (typeof element == "function") {
          if (typeof element() == "string") {
            let rowError = row + 6;
            errors.push({
              row: rowError,
              column: col,
              value: {
                index: rowError,
                value: element(),
              },
            });
          }
        }
      });
      return errors;
    },
  },
  watch: {
    rows: function (rows) {
      let init_height = 100;
      if (rows.length == 1) {
        this.height = init_height + "px";
      } else if (rows.length == 2) {
        this.height = init_height + 50 + "px";
      } else if (rows.length == 3) {
        this.height = init_height + 75 + "px";
      } else if (rows.length == 4) {
        this.height = init_height + 100 + "px";
      } else {
        this.height = "205px";
      }
    },
  },
  async created() {
    // if (this.countries.lenght == 0) {
      this.fetchCountries();
    // }
  },
  computed: {
    ...mapGetters({
      languages: "languages/getLanguages",
			countries: "filterData/getCountriesWithCompanies",
      countryLanguages: "filterData/getCountryLanguages",
      companies: "filterData/getCompaniesOfTheCountries",
    }),
  },
};
</script>
<style scoped>
.error-class {
  background-color: red;
  color: white;
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

.table-header,
.table-header > th {
  border-top-right-radius: 5px !important;
  border-top-left-radius: 5px !important;
}

.v-data-table table tbody tr:not(.v-data-table__selected):hover {
  background-color: #fffff7 !important;
}
.custom-row-error:hover .custom-error-details {
  color: black !important;
}
.custom-error-details {
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