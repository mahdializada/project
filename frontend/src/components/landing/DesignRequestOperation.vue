<template>
  <div>
    <v-dialog v-model="model" width="1300" persistent>
      <Dialog @closeDialog="closeDialog">
        <v-form lazy-validation ref="designRequestForm">
          <CustomStepper
            :headers="steppers"
            @close="close"
            :canNext="false"
            @validate="validateStepper"
            :is-submitting="isSubmitting"
            :loading="isLoading"
            ref="designRequestRef"
            @submit="insertSubmitForm"
            @changeToValidate="changeToValidate"
            @onStartOver="onStartOver"
            :show_percentage="true"
            :percentage.sync="progressBar"
          >
            <template v-slot:step1>
              <div class="d-flex flex-column align-center mb-2">
                <div
                  class="d-flex flex-column align-center"
                  style="border-bottom: 1px solid lightgray; width: 60%"
                >
                  <h3>{{ $tr("select_work_area") }}</h3>
                  <span class="mt-1 mb-2">
                    {{ $tr("only_one_option") }}
                  </span>
                </div>
              </div>

              <v-row>
                <template v-for="(type, index) in types">
                  <v-col
                    @click="onSelectWorkArea(type.id)"
                    cols="12"
                    md="5"
                    :key="type.id"
                    :class="`py-1 ${
                      $v.designRequest.type.$model === type.id
                        ? 'is__item_selected'
                        : ''
                    }`"
                  >
                    <div
                      :style="`background-color: ${
                        $vuetify.theme.dark ? '#191919' : '#f5efef'
                      }`"
                      class="d-flex align-center type__itme-hover pa-1"
                    >
                      <div class="type__logo pa-2" v-html="type.logo" />
                      <div
                        class="ms-2 d-flex align-center w-full justify-space-between"
                      >
                        <div
                          :class="`type__name text-capitalize  ${
                            $vuetify.theme.dark ? 'white--text' : 'black--text'
                          }`"
                        >
                          {{ $tr(type.name) }}
                        </div>
                        <div v-if="$v.designRequest.type.$model === type.id">
                          <v-icon color="primary" class="ms-2">
                            mdi-check-circle</v-icon
                          >
                        </div>
                      </div>
                    </div>
                  </v-col>
                  <v-col
                    :key="index"
                    v-if="(index + 1) % 2 != 0"
                    cols="2"
                    class="d-none d-md-block py-0"
                  >
                  </v-col>
                </template>
              </v-row>
            </template>
            <template v-slot:step2>
              <!-- <CustomInput
                @blur="$v.designRequest.company_id.$touch()"
                v-model="$v.designRequest.company_id.$model"
                :rules="validate($v.designRequest.company_id, $root, 'company')"
                :label="$tr('label_required', $tr('company'))"
                :items="companies"
                item-value="id"
                logoName=""
                has-logo
                item-text="name"
                :placeholder="$tr('choose_item', $tr('company'))"
                type="autocomplete"
              /> -->
              <!-- <div class="d-flex flex-column flex-md-row">
                <div class="w-full me-0 me-md-2">
                  <p class="mb-1 custom-input-title">
                    {{ $tr("product_code") }} *
                  </p>
                  <v-combobox
                    disabled
                    validate-on-blur
                    @blur="$v.designRequest.product_code.$touch()"
                    v-model="$v.designRequest.product_code.$model"
                    ref="productCodeInputRef"
                    :rules="
                      validate(
                        $v.designRequest.product_code,
                        $root,
                        'product_code'
                      )
                    "
                    class="custom-input auto-complete"
                    dense
                    :placeholder="$tr('product_code')"
                    :menu-props="{ bottom: true, offsetY: true, class: 'test' }"
                    outlined
                    :loading="isFetchingProducts"
                    :items="products"
                    item-value="pcode"
                    item-text="pcode"
                    @change="onProductCodeChanged"
                  >
                    <template v-slot:[`no-data`]>
                      <div class="d-flex pa-1 align-center">
                        <div class="me-1">{{ $tr("no_data_available") }}</div>
                        <div class="ms-5" style="cursor: pointer">
                          <span
                            @click="$refs.productCodeInputRef.blur()"
                            class="font-weight-bold primary--text"
                            >{{ $tr("create") }}
                          </span>
                          {{ $tr("new_product") }}
                        </div>
                      </div>
                    </template>
                  </v-combobox>
                </div>

                <div class="w-full ms-0 ms-md-2">
                  <p class="mb-1 custom-input-title">
                    {{ $tr("product_name") }} *
                  </p>
                  <v-combobox
                    validate-on-blur
                    @blur="$v.designRequest.product_name.$touch()"
                    v-model="$v.designRequest.product_name.$model"
                    ref="productNameInputRef"
                    :rules="
                      validate(
                        $v.designRequest.product_name,
                        $root,
                        'product_name'
                      )
                    "
                    class="custom-input auto-complete"
                    dense
                    :placeholder="$tr('product_name')"
                    :menu-props="{ bottom: true, offsetY: true, class: 'test' }"
                    outlined
                    :loading="isFetchingProducts"
                    :items="products"
                    item-value="name"
                    item-text="name"
                    @change="onProductNameChanged"
                    disabled
                  >
                    <template v-slot:[`no-data`]>
                      <div class="d-flex pa-1 align-center">
                        <div class="me-1">{{ $tr("no_data_available") }}</div>
                        <div class="ms-5" style="cursor: pointer">
                          <span
                            @click="$refs.productNameInputRef.blur()"
                            class="font-weight-bold primary--text"
                            >{{ $tr("create") }}
                          </span>
                          {{ $tr("new_product") }}
                        </div>
                      </div>
                    </template>
                  </v-combobox>
                </div>
              </div> -->

              <div class="d-flex flex-column flex-md-row">
                <div class="w-full me-0 me-md-2">
                  <CustomInput
                    @blur="$v.designRequest.priority.$touch()"
                    :items="priorities"
                    :label="$tr('label_required', $tr('priority'))"
                    :placeholder="$tr('choose_item', $tr('priority'))"
                    v-model.trim="$v.designRequest.priority.$model"
                    :rules="
                      validate($v.designRequest.priority, $root, 'priority')
                    "
                    type="autocomplete"
                    item-text="name"
                    item-value="id"
                  />
                </div>
                <div class="w-full ms-0 ms-md-2">
                  <p class="mb-1 custom-input-title">{{ $tr("template") }}</p>
                  <v-combobox
                    validate-on-blur
                    @blur="$v.designRequest.template.$touch()"
                    v-model="$v.designRequest.template.$model"
                    :rules="
                      validate($v.designRequest.template, $root, 'template')
                    "
                    class="custom-input auto-complete"
                    ref="templateRefs"
                    dense
                    :placeholder="$tr('template')"
                    :menu-props="{ bottom: true, offsetY: true, class: 'test' }"
                    outlined
                    :loading="isFetchingTemplate"
                    :items="templates"
                    item-value="id"
                    item-text="name"
                  >
                    <template v-slot:[`no-data`]>
                      <div class="d-flex pa-1 align-center">
                        <div class="me-1">{{ $tr("no_data_available") }}</div>
                        <div
                          v-if="$v.designRequest.type.$model"
                          class="ms-5"
                          style="cursor: pointer"
                        >
                          <span
                            @click="$refs.templateRefs.blur()"
                            class="font-weight-bold primary--text"
                            >{{ $tr("create") }}
                          </span>
                          {{ $tr("new_template") }}
                        </div>
                      </div>
                    </template>
                  </v-combobox>
                </div>
              </div>
              <div class="d-flex flex-column flex-md-row">
                <CustomInput
                  v-model="numberOfRecords"
                  :items="[1, 2, 3, 4, 5, 6, 7]"
                  :label="$tr('number_of_records')"
                  :placeholder="$tr('number_of_records')"
                  type="select"
                  class="w-full"
                />
              </div>
            </template>
            <template v-slot:step3>
              <CustomInput
                @blur="$v.designRequest.order_type.$touch()"
                :items="orderTypes"
                :label="$tr('label_required', $tr('order_type'))"
                :placeholder="$tr('choose_item', $tr('order_type'))"
                v-model.trim="$v.designRequest.order_type.$model"
                :rules="
                  validate($v.designRequest.order_type, $root, 'order_type')
                "
                type="autocomplete"
                item-text="name"
                item-value="id"
              />
              <CustomFileDropzone2
                :files="files"
                :fileSize="2097152"
                :showFiles="showUploadedFiles"
                :cantDownload="true"
                :rules="true"
                :fileUploadFlag="true"
                @fileRemoved="removeFile"
                @filesSelectedFileObject="onUploadFilesHandler"
                :errorText="errorText"
                ref="fileDropZone"
                :skipBtn="
                  designRequest.type == 'landing page design' ? true : false
                "
                @skip="skip"
                @Cancelskip="Cancelskip"
              />
              <Editor
                :key="salesNoteKey"
                v-model.trim="$v.designRequest.sales_note.$model"
                :rules="
                  validate($v.designRequest.sales_note, $root, 'sales_note')
                "
                :label="$tr('sales_note')"
              />

              <v-checkbox
                :trueValue="true"
                :falseValue="false"
                class="mt-0 pt-0"
                v-model="$v.designRequest.has_in_storyboard.$model"
                :label="$tr(`has_in_storyboard`)"
              />
              <div>
                <!-- <v-progress-linear
                     v-model="progressBar"
                     color="blue-grey"
                     height="25"
                   >
                     <template v-slot:default="{ value }">
                       <strong>{{ Math.ceil(value) }}%</strong>
                     </template>
                   </v-progress-linear> -->
              </div>
            </template>
            <!-- v-if="!($v.designRequest.type.$model == 'landing page design')" -->
            <template v-slot:step4 v-if="false">
              <div>
                <CustomInput
                  @blur="$v.designRequest.category_id.$touch()"
                  v-model="$v.designRequest.category_id.$model"
                  :rules="
                    validate($v.designRequest.category_id, $root, 'category')
                  "
                  :label="$tr('label_required', $tr('category'))"
                  :items="categories"
                  item-value="id"
                  item-text="title"
                  :placeholder="$tr('choose_item', $tr('category'))"
                  type="autocomplete"
                  class="w-full"
                  :loading="isFetchingCategory"
                  @change="onCategoryChange"
                />
                <CustomInput
                  @blur="$v.designRequest.label_id.$touch()"
                  v-model="$v.designRequest.label_id.$model"
                  :rules="validate($v.designRequest.label_id, $root, 'label')"
                  :label="$tr('label_required', $tr('label'))"
                  :items="labels"
                  item-value="id"
                  item-text="label"
                  :placeholder="$tr('choose_item', $tr('label'))"
                  type="autocomplete"
                  class="w-full"
                  :loading="isFetchingLabel"
                />
              </div>
              <!-- <label-category-stepper :categories="categories" /> -->
            </template>

            <template v-slot:step5>
              <DoneMessage
                :title="$tr('item_all_set', $tr('design_request'))"
                :subTitle="
                  $tr('you_can_access_your_item', $tr('design_request'))
                "
              />
            </template>
          </CustomStepper>
        </v-form>
      </Dialog>
    </v-dialog>
  </div>
</template>
   
   <script>
import Editor from "../design/Editor.vue";
import StepTwo from "./StepTwo.vue";
import HandleException from "~/helpers/handle-exception";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import CustomStepper from "~/components/design/FormStepper/CustomStepper";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import CustomInput from "~/components/design/components/CustomInput";
import DesignRequestValidtions from "~/validations/design-request-validations";
import GlobleRules from "~/helpers/vuelidate";
import LabelCategoryStepper from "./LabelCategoryStepper.vue";
import CustomFileDropzone2 from "../../components/landing/CustomFileDropzone2.vue";
import Helpers from "../../helpers/helpers";
import { requiredIf } from "vuelidate/lib/validators";
import axios from "axios";
export default {
  components: {
    Dialog,
    CustomStepper,
    DoneMessage,
    CustomInput,
    StepTwo,
    Editor,
    LabelCategoryStepper,
    CustomFileDropzone2,
  },
  name: "DesignRequestOperation",

  data() {
    return {
      errorText: "",
      fileUploadFlag: false,
      progressBar: 0,
      files: [],
      numberOfRecords: 1,
      model: false,
      isLoading: false,
      isSubmitting: false,
      shouldGoesBack: false,
      salesNoteKey: 0,
      labels: [],
      isFetchingLabel: false,
      validate: GlobleRules.validate,
      steppers: DesignRequestValidtions.steppers,

      designRequest: JSON.parse(JSON.stringify(DesignRequestValidtions.schema)),
      // necessary items
      products: [],
      companies: [],
      categories: [],
      isFetchingProducts: false,
      types: DesignRequestValidtions.types,
      isFetchingCategory: false,
      orderTypes: [
        { id: "scratch", name: this.$tr("scratch") },
        { id: "copy", name: this.$tr("copy") },
        { id: "mixed", name: this.$tr("mixed") },
      ],
      priorities: [
        { id: "low", name: this.$tr("low") },
        { id: "medium", name: this.$tr("medium") },
        { id: "high", name: this.$tr("high") },
      ],
      isFetchingTemplate: false,
      templates: [],
      showUploadedFiles: false,
      token:
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMDZkMGVkNmNhMzQ1ZmY4MDUxMzcyMWUwNzkwYTk2MDk2MDcwYmQ4N2ExNzk1MjI0NzZhNzU3YjA4NDdlMWI5N2ZmMjU1NDViNWNhZDYwODYiLCJpYXQiOjE2NzczMjA3ODcuMTcxMzM2LCJuYmYiOjE2NzczMjA3ODcuMTcxMzQ0LCJleHAiOjE3MDg4NTY3ODcuMDM2NjExLCJzdWIiOiI1Y2M1NjJmYi0zOTdhLTQyMGUtODg1NC05OGNkYjExNjAzMDgiLCJzY29wZXMiOlsidWMiLCJ1ZCIsInVlIiwidXUiLCJ1diIsInRjIiwidGQiLCJ0ZSIsInR1IiwidHYiLCJyYyIsInJkIiwicmUiLCJydSIsInJ2IiwidWFjIiwidWFkIiwidWFlIiwidWF1IiwidWF2IiwibHVjIiwibHVkIiwibHVlIiwibHV1IiwibHV2Iiwic2V1YyIsInNldWQiLCJzZXVlIiwic2V1dSIsInNldXYiLCJybnVjIiwicm51ZCIsInJudWUiLCJybnV1Iiwicm51diIsImJsYyIsImJsZCIsImJsZSIsImJsdSIsImJsdiIsInNtYyIsInNtZCIsInNtZSIsInNtdSIsInNtdiIsImNuYyIsImNuZCIsImNuZSIsImNudSIsImNudiIsImNtYyIsImNtZCIsImNtZSIsImNtdSIsImNtdiIsImRwYyIsImRwZCIsImRwZSIsImRwdSIsImRwdiIsImxuYyIsImxuZCIsImxuZSIsImxudSIsImxudiIsImxtYyIsImxtZCIsImxtZSIsImxtdSIsImxtdiIsInNlbWMiLCJzZW1kIiwic2VtZSIsInNlbXUiLCJzZW12Iiwicm5tYyIsInJubWQiLCJybm1lIiwicm5tdSIsInJubXYiLCJzeWMiLCJzeWQiLCJzeWUiLCJzeXUiLCJzeXYiLCJtZGMiLCJtZGQiLCJtZGUiLCJtZHUiLCJtZHYiLCJ1cmMiLCJ1cmQiLCJ1cmUiLCJ1cnUiLCJ1cnYiLCJwZmMiLCJwZmQiLCJwZmUiLCJwZnUiLCJwZnYiLCJyZmMiLCJyZmQiLCJyZmUiLCJyZnUiLCJyZnYiLCJmYyIsImZkIiwiZmUiLCJmdSIsImZ2Iiwic2ZjIiwic2ZkIiwic2ZlIiwic2Z1Iiwic2Z2IiwidGZjIiwidGZkIiwidGZlIiwidGZ1IiwidGZ2Iiwic2V0YyIsInNldGQiLCJzZXRlIiwic2V0dSIsInNldHYiLCJkcmMiLCJkcmQiLCJkcmUiLCJkcnUiLCJkcnYiLCJkcmEiLCJkcmlzYiIsImRyaXNiciIsImRyaWQiLCJkcmlkciIsImRyaXAiLCJkcmFwcmoiLCJkcnZhIiwiZHJvYyIsImRyb2QiLCJkcm9lIiwiZHJvdSIsImRyb3YiLCJkcm9pc2IiLCJkcm9pc2JyIiwiZHJvaWQiLCJkcm9pZHIiLCJkcm9pcCIsImxwYWMiLCJscGFkIiwibHBhZSIsImxwYXUiLCJscGF2Iiwic2VjYyIsInNlY2QiLCJzZWNlIiwic2VjdSIsInNlY3YiLCJybmNjIiwicm5jZCIsInJuY2UiLCJybmN1Iiwicm5jdiIsImxjYyIsImxjZCIsImxjZSIsImxjdSIsImxjdiIsImFmYyIsImFmZCIsImFmZSIsImFmdSIsImFmdiIsImxmYyIsImxmZCIsImxmZSIsImxmdSIsImxmdiJdfQ.aAowABW86-sKUsl9fc2S3isKJnEBYSCuFT8n417hP1BtE6oasFoA7PciMThFiOpqQw3lAsH6leVMxX6fa3NmKAIAwpSC1gYDpSToT-9xPykbtbWVTcP9HkcatxqjfPLyJAXgEeMSwwCAAXhfweDfl6TGmGbjio3oocsMrvilfcO79aGIFIgcef-pkpgC03UDD7o-33gk8Yj8py7XSEcrMNMd1kXy9R2GqgpcWvZ54p6R7VEnd6fjlL84Sjp-kbct3-QoaAdmiktQVJg9r9KSUGClqmEUCsdlDOowYoIvZPMLlpbm_pK3WH2aAQoZkP7YdWMhYTNRTnUXfJJYeAKLU9RBwQ9By0ZXdoShKSyT4BKZ9D_aWpi7Qt1YcXr_H4eqWURlotMzBSuaGv-K5dHZzicQfduIB2TQgqO9EGOdcySREhxceDQU1yo_Gbec0Gf97MWJONbr2FMcFhjjj6PNsEbb8Qsj7mIK3_q1XqeNyGMp1Vb_B-VRyl20GCucW33-IsWRyI86FPy9WXaD9uEFQXXlFQJLq7g7NSbTVX9FWz4H_zUm_YT462CsWOLr2I01yX3kLxo5vcbl13y5CNHHvEVCL0eKOlBEsCxWvU4EDzhlgeQyF9DLYVXBKZl11rAO1qgyOtMaiu6R5JKsschNUhH_ViU_TPOIA9iBvAAlM0w",

      selectedItem: {},
    };
  },
  created() {
    // const token =
    //   "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMDZkMGVkNmNhMzQ1ZmY4MDUxMzcyMWUwNzkwYTk2MDk2MDcwYmQ4N2ExNzk1MjI0NzZhNzU3YjA4NDdlMWI5N2ZmMjU1NDViNWNhZDYwODYiLCJpYXQiOjE2NzczMjA3ODcuMTcxMzM2LCJuYmYiOjE2NzczMjA3ODcuMTcxMzQ0LCJleHAiOjE3MDg4NTY3ODcuMDM2NjExLCJzdWIiOiI1Y2M1NjJmYi0zOTdhLTQyMGUtODg1NC05OGNkYjExNjAzMDgiLCJzY29wZXMiOlsidWMiLCJ1ZCIsInVlIiwidXUiLCJ1diIsInRjIiwidGQiLCJ0ZSIsInR1IiwidHYiLCJyYyIsInJkIiwicmUiLCJydSIsInJ2IiwidWFjIiwidWFkIiwidWFlIiwidWF1IiwidWF2IiwibHVjIiwibHVkIiwibHVlIiwibHV1IiwibHV2Iiwic2V1YyIsInNldWQiLCJzZXVlIiwic2V1dSIsInNldXYiLCJybnVjIiwicm51ZCIsInJudWUiLCJybnV1Iiwicm51diIsImJsYyIsImJsZCIsImJsZSIsImJsdSIsImJsdiIsInNtYyIsInNtZCIsInNtZSIsInNtdSIsInNtdiIsImNuYyIsImNuZCIsImNuZSIsImNudSIsImNudiIsImNtYyIsImNtZCIsImNtZSIsImNtdSIsImNtdiIsImRwYyIsImRwZCIsImRwZSIsImRwdSIsImRwdiIsImxuYyIsImxuZCIsImxuZSIsImxudSIsImxudiIsImxtYyIsImxtZCIsImxtZSIsImxtdSIsImxtdiIsInNlbWMiLCJzZW1kIiwic2VtZSIsInNlbXUiLCJzZW12Iiwicm5tYyIsInJubWQiLCJybm1lIiwicm5tdSIsInJubXYiLCJzeWMiLCJzeWQiLCJzeWUiLCJzeXUiLCJzeXYiLCJtZGMiLCJtZGQiLCJtZGUiLCJtZHUiLCJtZHYiLCJ1cmMiLCJ1cmQiLCJ1cmUiLCJ1cnUiLCJ1cnYiLCJwZmMiLCJwZmQiLCJwZmUiLCJwZnUiLCJwZnYiLCJyZmMiLCJyZmQiLCJyZmUiLCJyZnUiLCJyZnYiLCJmYyIsImZkIiwiZmUiLCJmdSIsImZ2Iiwic2ZjIiwic2ZkIiwic2ZlIiwic2Z1Iiwic2Z2IiwidGZjIiwidGZkIiwidGZlIiwidGZ1IiwidGZ2Iiwic2V0YyIsInNldGQiLCJzZXRlIiwic2V0dSIsInNldHYiLCJkcmMiLCJkcmQiLCJkcmUiLCJkcnUiLCJkcnYiLCJkcmEiLCJkcmlzYiIsImRyaXNiciIsImRyaWQiLCJkcmlkciIsImRyaXAiLCJkcmFwcmoiLCJkcnZhIiwiZHJvYyIsImRyb2QiLCJkcm9lIiwiZHJvdSIsImRyb3YiLCJkcm9pc2IiLCJkcm9pc2JyIiwiZHJvaWQiLCJkcm9pZHIiLCJkcm9pcCIsImxwYWMiLCJscGFkIiwibHBhZSIsImxwYXUiLCJscGF2Iiwic2VjYyIsInNlY2QiLCJzZWNlIiwic2VjdSIsInNlY3YiLCJybmNjIiwicm5jZCIsInJuY2UiLCJybmN1Iiwicm5jdiIsImxjYyIsImxjZCIsImxjZSIsImxjdSIsImxjdiIsImFmYyIsImFmZCIsImFmZSIsImFmdSIsImFmdiIsImxmYyIsImxmZCIsImxmZSIsImxmdSIsImxmdiJdfQ.aAowABW86-sKUsl9fc2S3isKJnEBYSCuFT8n417hP1BtE6oasFoA7PciMThFiOpqQw3lAsH6leVMxX6fa3NmKAIAwpSC1gYDpSToT-9xPykbtbWVTcP9HkcatxqjfPLyJAXgEeMSwwCAAXhfweDfl6TGmGbjio3oocsMrvilfcO79aGIFIgcef-pkpgC03UDD7o-33gk8Yj8py7XSEcrMNMd1kXy9R2GqgpcWvZ54p6R7VEnd6fjlL84Sjp-kbct3-QoaAdmiktQVJg9r9KSUGClqmEUCsdlDOowYoIvZPMLlpbm_pK3WH2aAQoZkP7YdWMhYTNRTnUXfJJYeAKLU9RBwQ9By0ZXdoShKSyT4BKZ9D_aWpi7Qt1YcXr_H4eqWURlotMzBSuaGv-K5dHZzicQfduIB2TQgqO9EGOdcySREhxceDQU1yo_Gbec0Gf97MWJONbr2FMcFhjjj6PNsEbb8Qsj7mIK3_q1XqeNyGMp1Vb_B-VRyl20GCucW33-IsWRyI86FPy9WXaD9uEFQXXlFQJLq7g7NSbTVX9FWz4H_zUm_YT462CsWOLr2I01yX3kLxo5vcbl13y5CNHHvEVCL0eKOlBEsCxWvU4EDzhlgeQyF9DLYVXBKZl11rAO1qgyOtMaiu6R5JKsschNUhH_ViU_TPOIA9iBvAAlM0w";
    // localStorage.setItem("oredoh-token", token);
  },
  methods: {
    removeFile(uploaded) {
      this.onUploadFilesHandler(uploaded.file);
    },
    onUploadFilesHandler(files) {
      try {
        let url = "";
        this.files = files;
        this.designRequest.sales_note = null;
        if (typeof files == "object") {
          files?.forEach((element) => {
            url = URL.createObjectURL(element);
            if (this.designRequest.sales_note) {
              this.designRequest.sales_note +=
                "<u><i><a href='" +
                url +
                "'a>" +
                element.name +
                "</a></i></ul>\n";
            } else {
              this.designRequest.sales_note =
                "<u><i><a href='" +
                url +
                "'a>" +
                element.name +
                "</a></i></ul>\n";
            }
          });
        }
        this.errorText = null;
      } catch (error) {
        console.log(error);
      }
      this.fileUploadFlag = false;
    },
    openDialog(item) {
      this.selectedItem = structuredClone(item[0]);
      this.designRequest.product_name = this.selectedItem.product_name??this.selectedItem.pname;
      this.designRequest.product_code = this.selectedItem.product_code??this.selectedItem.pcode;
      this.model = true;
      this.fetchCompanies();
    },

    onSelectWorkArea(typeId) {
      if (typeId === this.$v.designRequest.type.$model)
        this.$v.designRequest.type.$model = null;
      else this.$v.designRequest.type.$model = typeId;
    },

    async fetchCompanies() {
      if (this.companies.length > 0) {
        this.checkCompany();
        return;
      }
      try {
        let response = await this.$axios2.get(
          "auth-companies?has-landing=true"
        );
        if (response.status == 200) {
          this.companies = await response.data;
          this.checkCompany();
        }
      } catch (error) {
        console.log("erroddddr", error.response.status);
        if (error.response.status === 401) {
          this.model = false;
        }
      }
    },
    checkCompany() {
      const company = this.companies.find(
        (row) => row.name == this.selectedItem.company.name
      );
      if (company) {
        this.designRequest.company_id = company.id;
      } else {
        this.$toastr.e(
          this.$tr("company_name_not_mutched_with_content_management_companies")
        );
        this.model = false;
      }
    },

    async fetchProducts() {
      if (this.products.length) {
        return;
      }
      try {
        this.isFetchingProducts = true;
        const currentComUrl = "design-request-data?product-code=true";
        const response = await this.$axios2.get(currentComUrl);
        if (response.status == 200) this.products = await response.data;
      } catch (error) {
        if (error.response.status === 401) {
          this.model = true;
        }
        console.log("error", error);
      }
      this.isFetchingProducts = false;
    },

    async fetchTemplates(type) {
      this.$v.designRequest.template.$model = null;
      this.templates = [];
      try {
        this.isFetchingTemplate = true;
        const currentComUrl = `design-request-data?template=true&type=${type}`;
        const response = await this.$axios2.get(currentComUrl);
        this.templates = await response.data;
      } catch (error) {
        if (error.response.status === 401) {
          this.model = true;
        }
      }
      this.isFetchingTemplate = false;
    },

    onProductCodeChanged(product) {
      if (product) {
        this.designRequest.product_name = product.name
          ? product.name
          : this.designRequest.product_name;
        this.designRequest.product_code = product.pcode
          ? product.pcode
          : product;
      } else {
        this.designRequest.product_code = null;
        this.designRequest.product_name = null;
      }
    },

    onProductNameChanged(product) {
      if (product) {
        this.designRequest.product_name = product.name ? product.name : product;
        this.designRequest.product_code = product.pcode
          ? product.pcode
          : this.designRequest.product_code;
      } else {
        this.designRequest.product_code = null;
        this.designRequest.product_name = null;
      }
    },

    changeToValidate(changeTo) {
      let isValid = false;
      this.$refs.designRequestForm.validate();
      this.$v.designRequest.$touch();
      if (changeTo === 2) {
        isValid = !this.$v.designRequest.type.$invalid;
        if (isValid) {
          this.fetchTemplates(this.$v.designRequest.type.$model);
        }

        // this.$v.designRequest.type.$model &&

        //   this.fetchTemplates(this.$v.designRequest.type.$model);
      }
      if (changeTo === 3) {
        isValid = GlobleRules.isDataValid(this.$v.designRequest, [
          "template",
          "priority",
          "product_code",
          "product_name",
          "company_id",
        ]);
      }

      if (changeTo === 4) {
        isValid = GlobleRules.isDataValid(this.$v.designRequest, [
          "order_type",
          "sales_note",
        ]);
      }
      if (changeTo === 5) {
        isValid = GlobleRules.isDataValid(this.$v.designRequest, [
          "order_type",
          "sales_note",
        ]);
      }

      if (isValid) {
        this.$v.$reset();
        this.$refs.designRequestRef.changeTo(changeTo);
        this.salesNoteKey++;
      }
    },

    // validate form and data
    async validateStepper(currentStep) {
      this.$refs.designRequestForm.validate();
      this.$v.designRequest.$touch();
      if (currentStep === 1) {
        if (!this.$v.designRequest.type.$invalid) {
          this.fetchTemplates(this.$v.designRequest.type.$model);
          this.$v.$reset();
          this.$refs.designRequestRef?.forceNext();
        } else {
          this.$toastr.i(this.$tr("please_select_your_work_area"));
        }
      }

      if (currentStep === 2) {
        if (
          GlobleRules.isDataValid(this.$v.designRequest, [
            "template",
            "product_code",
            "product_name",
            "company_id",
          ])
        ) {
          this.$v.$reset();
          this.$refs.designRequestRef?.forceNext();
          this.salesNoteKey++;
        }

        this.$refs.fileDropZone?.Cancelskip();
      }
      if (currentStep === 3) {
        if (this.files.length == 0) {
          this.errorText = "Please upload files";
        } else {
          if (
            GlobleRules.isDataValid(this.$v.designRequest, [
              "order_type",
              "priority",
              "sales_note",
            ])
          ) {
            this.isLoading = true;
            await this.getCategory();
            this.isLoading = false;
            this.$v.$reset();
            this.$refs.designRequestRef?.forceNext();
            this.salesNoteKey++;
          }
        }
      }
      if (currentStep === 4) {
        if (
          GlobleRules.isDataValid(this.$v.designRequest, [
            "order_type",
            "priority",
            "sales_note",
          ])
        ) {
          this.$v.$reset();
          this.$refs.designRequestRef?.forceNext();
          this.salesNoteKey++;
        }
      }
      if (currentStep === 5) {
        this.$v.$reset();
        this.$refs.designRequestRef?.forceNext();
        this.salesNoteKey++;
      }
    },
    async getCategory() {
      try {
        const response = await this.$axios.get("labels_category");
        this.categories = response.data;
      } catch (error) {}
    },

    // submit registration form
    async insertSubmitForm() {
      if (this.files.length == 0 && this.designRequest.skip == false) {
        this.errorText = "Please upload files";
      } else if (this.designRequest.skip == true || this.files.length > 0) {
        this.$refs.designRequestForm.validate();
        this.$v.designRequest.$touch();
        if (!this.$v.designRequest.$invalid) {
          this.isSubmitting = true;
          this.designRequest.number_of_records = this.numberOfRecords;
          await this.insertRecord(this.designRequest);
          this.isSubmitting = false;
        } else {
          this.$toastr.e(this.$tr("please_fill_all_fields_correctly"));
        }
      }
    },
    // insert record into database
    async insertRecord(data) {
      try {
        const formData = Helpers.getFormData({
          ...data,
          files: this.files,
        });
        var config = {
          onUploadProgress: (progressEvent) => {
            this.progressBar = Math.round(
              (progressEvent.loaded * 100) / progressEvent.total
            );
          },
        };

        const response = await this.$axios2.post(
          "design-request",
          formData,
          config
        );

        if (response?.status === 201 && response?.data?.result) {
          this.$emit("fetchNewData");
          this.salesNoteKey++;
          this.$refs.designRequestRef.forceNext();
          this.clearAllPrevData();
        } else {
          this.$toastr.e(this.$tr("something_went_wrong"));
        }
      } catch (error) {
        if (error?.response?.status === 502) {
          this.$toastr.e(this.$tr("sending_email_failed"));
          return;
        }
        HandleException.handleApiException(this, error);
      }
      this.progressBar = 0;
    },

    // remove all previous data
    clearAllPrevData() {
      this.designRequest = JSON.parse(
        JSON.stringify(DesignRequestValidtions.schema)
      );
      // this.$v.designRequest.$reset();
      // this.$refs.designRequestForm.reset();
      // this.$v.$reset();
      const company = this.companies.find(
        (row) => row.name == this.selectedItem.company.name
      );
      if (company) {
        this.designRequest.company_id = company.id;
      }
      this.shouldGoesBack = true;
      this.$v.designRequest.has_in_storyboard.$model = false;
      this.numberOfRecords = 1;
      this.files = [];
      this.$refs.fileDropZone?.clearItems();
      this.designRequest.product_name = this.selectedItem.product_name??this.selectedItem.pname;
      this.designRequest.product_code = this.selectedItem.product_code??this.selectedItem.pcode;
      console.log("dd", this.designRequest);
    },

    // close dialog
    close() {
      if (this.shouldGoesBack) {
        this.shouldGoesBack = false;
        this.$refs.designRequestRef.setCurrent(1);
      }
    },
    // close dialog
    closeDialog() {
      if (this.$refs.designRequestRef) {
        if (this.$refs.designRequestRef.current == 5) {
          this.$refs.designRequestRef.changeTo(1);
        }
      }
      this.model = false;
    },

    async onCategoryChange() {
      await this.getLabels();
    },
    async getLabels() {
      try {
        const params = {
          sub_system: "Design Request",
          category_id: this.$v.designRequest.category_id.$model,
        };
        this.isFetchingLabel = true;
        const response = await this.$axios.get("labels/id", { params });
        this.labels = response.data.labels;
        this.isFetchingLabel = false;
      } catch (error) {
        this.isFetchingLabel = false;
      }
    },
    onStartOver() {
      this.steppers = DesignRequestValidtions.steppers;
      this.designRequest.skip = false;
      // this.$refs.fileDropZone?.skipDD = false
      this.$refs.fileDropZone?.Cancelskip();
    },
    skip() {
      this.designRequest.skip = true;
      this.errorText = "";
      this.$refs.fileDropZone.errorTexts = null;
    },

    Cancelskip() {
      this.designRequest.skip = false;
    },
  },
  validations: {
    designRequest: DesignRequestValidtions.validations,
  },
  watch: {
    "designRequest.sales_note": {
      handler: (note) => {
        // console.log(note);
      },
      deep: true,
    },
  },
};
</script>
   
   <style scoped>
.items--container {
  display: grid;
  grid-template-columns: 1fr 1fr;
}

.type__logo--active {
  background-color: var(--v-primary-base);
}

.type__name {
  font-size: 16px;
  font-weight: bold;
}

.type__logo {
  display: flex;
  align-items: center;
  background-color: lightgrey;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  transition: border 500ms linear;
}

.type__itme-hover {
  border-bottom: 6px solid transparent;
  border-radius: 10px;
}

.type__itme-hover:hover {
  cursor: pointer;
  border-bottom: 6px solid var(--v-primary-base);
  border-end-start-radius: 0;
  border-end-end-radius: 0;
}

.type__itme-hover:hover .type__logo {
  background-color: var(--v-primary-base);
}
</style>
   
   <style>
.is__item_selected .logo__item-hover {
  fill: white !important;
}

.is__item_selected .type__itme-hover {
  cursor: pointer;
  border-bottom: 6px solid var(--v-primary-base);
  border-end-start-radius: 0;
  border-end-end-radius: 0;
}

.is__item_selected .type__logo {
  background-color: var(--v-primary-base);
}

.type__itme-hover:hover .logo__item-hover {
  fill: white !important;
}
</style>
   