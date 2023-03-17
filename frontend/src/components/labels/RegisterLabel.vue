<template>
  <v-form lazy-validation ref="labelForm" @submit.prevent="submitForm">
    <CustomStepper
      :headers="steppers"
      @close="closeDialog"
      :canNext="false"
      @validate="() => {}"
      :is-submitting="isLoading"
      @submit="submitForm"
      ref="customStepper"
    >
      <template v-slot:step1>
        <div class="w-full me-0 me-md-2">
          <p class="mb-1 custom-input-title">{{ $tr("label_category") }} *</p>

          <v-combobox
            v-model.trim="$v.label.category.$model"
            ref="labelCategoryInputRef"
            class="custom-input auto-complete"
            dense
            :placeholder="$tr('label_category')"
            :menu-props="{ bottom: true, offsetY: true, class: 'test' }"
            outlined
            :items="labelCategory"
            item-value="title"
            item-text="title"
            :rules="labelCategoryRule($v.label.category)"
          >
            <template v-slot:[`no-data`]>
              <div class="d-flex pa-1 align-center">
                <div class="me-1">{{ $tr("no_data_available") }}</div>
                <div class="ms-5" style="cursor: pointer">
                  <span
                    @click="$refs.labelCategoryInputRef.blur()"
                    class="font-weight-bold primary--text"
                    >{{ $tr("create") }}
                  </span>
                  {{ $tr("new_label_category") }}
                </div>
              </div>
            </template>
          </v-combobox>
        </div>

        <div class="d-flex flex-column flex-md-row">
          <div class="w-full">
            <CustomInput
              :items="label.subSystems"
              v-model="label.subSystem"
              :error-messages="subSystemErrors"
              @blur="touchSubSystem"
              :label="$tr('label_required', $tr('sub_system'))"
              type="autocomplete"
              item-text="name"
              item-value="id"
              :placeholder="$tr('choose_item', $tr('sub_systems'))"
              class="me-md-4 mb-md-2 mb-0"
            />
          </div>
          <div class="w-100">
            <FormBtn
              @click="addSubSystem"
              title="add_plus"
              class="mt-md-4 mb-2"
            />
          </div>
        </div>

        <div class="selected d-flex flex-wrap">
          <template v-for="(sub, index) in label.selectedSubSystems">
            <SelectedItem
              @close="removeSubSystem(index)"
              :key="index"
              :title="$tr(sub.name)"
            />
          </template>
        </div>

        <!-- tabs advertisment -->
        <div class="d-flex flex-column flex-md-row" v-if="advertismentTab">
          <div class="w-full">
            <CustomInput
              :items="tabItems"
              v-model="label.tab"
              :error-messages="tabSystemErrors"
              @blur="touchTabSystem"
              :label="$tr('label_required', $tr('tab_system'))"
              type="autocomplete"
              :item-text="
                (item) => {
                  return $tr(item.tab_name);
                }
              "
              item-value="id"
              :placeholder="$tr('choose_item', $tr('tab_system'))"
              class="me-md-4 mb-md-2 mb-0"
            />
          </div>
          <div class="w-100">
            <FormBtn
              @click="addTabSystem"
              title="add_plus"
              class="mt-md-4 mb-2"
            />
          </div>
        </div>

        <div class="selected d-flex flex-wrap">
          <template v-for="(sub, index) in label.selectedTabSystems">
            <SelectedItem
              @close="removeTabSystem(index)"
              :key="index"
              :title="$tr(sub.tab_name)"
            />
          </template>
        </div>
        <!-- tabs advertisment -->

        <div class="w-full pt-1">
          <CustomInput label="color" />
          <v-text-field
            dense
            style="border: 2px solid var(--input-border-color)"
            v-model.trim="$v.label.color.$model"
            hide-details
            class="ma-0 pa-0"
            solo
          >
            <template v-slot:append>
              <v-menu
                top
                nudge-bottom="105"
                nudge-left="16"
                :close-on-content-click="false"
              >
                <template v-slot:activator="{ on }">
                  <div
                    v-on="on"
                    v-for="(color, index) in colors"
                    :key="index"
                    @click="
                      () => {
                        indexKey = index;
                        label.color = color;
                      }
                    "
                    :style="`cursor: pointer;
                            height: 26px;
                            width: 26px;
                            margin: 0 0.3rem;
                            borderRadius: 4px;
                            transition: border-radius 200ms ease-in-out; background-color: ${colors[index]};`"
                  >
                    <v-icon
                      v-if="colors[index] === label.color"
                      style="padding: 0 0.1rem"
                      color="white"
                    >
                      mdi-circle-medium</v-icon
                    >
                  </div>
                </template>
                <v-card elevation="0">
                  <v-card-text class="pa-0">
                    <v-color-picker
                      @input="checkColor"
                      v-model.trim="$v.label.color.$model"
                      flat
                    />
                  </v-card-text>
                </v-card>
              </v-menu>
            </template>
          </v-text-field>
        </div>

        <div class="w-full pt-4">
          <CustomInput
            :label="$tr('label_required', $tr('label'))"
            type="textfield"
            v-model.trim="$v.label.label.$model"
            :rules="labelRule($v.label.label)"
          />
        </div>

        <div class="w-full pt-4">
          <CustomInput
            :label="$tr('label_required', $tr('title'))"
            :placeholder="$tr('title')"
            type="textarea"
            v-model.trim="$v.label.title.$model"
            :rules="titleRule($v.label.title)"
          />
        </div>
        <div class="w-full pt-0">
          <v-checkbox
            v-model.trim="$v.label.status.$model"
            :label="$tr('archive_this_label')"
          ></v-checkbox>
        </div>
      </template>

      <template v-slot:step2>
        <DoneMessage
          :title="$tr('item_all_set', $tr('label'))"
          :subTitle="$tr('you_can_access_your_item', $tr('label'))"
        />
      </template>
    </CustomStepper>
  </v-form>
</template>

<script>
import { mapActions } from "vuex";
import { minLength, required } from "vuelidate/lib/validators";
import CustomStepper from "../design/FormStepper/CustomStepper";
import HandleException from "~/helpers/handle-exception";
import CustomInput from "../design/components/CustomInput.vue";
import FormBtn from "../design/components/FormBtn.vue";
import SelectedItem from "../design/components/SelectedItem";
import DoneMessage from "../design/components/DoneMessage.vue";

export default {
  name: "RegisterLabel",
  components: {
    CustomInput,
    FormBtn,
    SelectedItem,
    CustomStepper,
    DoneMessage,
  },
  props: {
    subSystems: {
      type: Array,
      required: true,
    },
    systemName: {
      type: String,
      required: true,
    },
    labelCategory: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      indexKey: 0,
      colors: [
        "#5A89E1",
        "#60B1DC",
        "#62C5A0",
        "#9BCD66",
        "#EACB59",
        "#EC6051",
        "#D64643",
        "#9981E1",
        "#D177B3",
        "#E5E1E9",
        "#9DE1AF",
        "#333942",
      ],
      isLoading: false,
      steppers: [
        { icon: "fa-lock", title: "general", slotName: "step1" },
        { icon: "fa-thumbs-up", title: "done", slotName: "step2" },
      ],
      label: {
        subSystems: this.subSystems,
        selectedSubSystems: [],
        selectedTabSystems: [],
        subSystem: "",
        title: "",
        label: "",
        tab: "",
        category: "",
        color: "#D64643",
        status: false,
      },
      subSystemErrors: "",
      tabSystemErrors: "",
      advertismentTab: false,
      tabItems: [
        {
          id: 1,
          tab_name: "country",
        },
        {
          id: 2,
          tab_name: "company",
        },
        {
          id: 3,
          tab_name: "item_code",
        },
        {
          id: 4,
          tab_name: "issp_code",
        },
        {
          id: 5,
          tab_name: "platform",
        },
        {
          id: 6,
          tab_name: "organization",
        },
        {
          id: 7,
          tab_name: "ad_account",
        },
        {
          id: 8,
          tab_name: "campaign",
        },
        {
          id: 9,
          tab_name: "ad_set",
        },
        {
          id: 10,
          tab_name: "ad",
        },
        {
          id: 11,
          tab_name: "project",
        },
      ],
    };
  },

  validations: {
    label: {
      title: { required, minLength: minLength(5) },
      category: { required },
      label: { required, minLength: minLength(5) },
      color: { required },
      status: {},
    },
  },
  watch: {
    ["label.selectedSubSystems"]: function (val) {
      const adExist = val.filter((e) => e.name == "Advertisement");
      if (adExist.length > 0) {
        this.advertismentTab = true;
      } else {
        this.advertismentTab = false;
      }
    },
  },
  methods: {
    ...mapActions({
      insertNewItem: "labels/insertNewItem",
    }),

    labelCategoryChanged(category) {
      if (typeof category == "object") {
      }
    },

    checkColor() {
      this.colors[this.indexKey] = this.label.color;
    },

    async submitForm() {
      this.$refs.labelForm.validate();
      this.touchSubSystem();
      this.touchTabSystem();
      this.$v.label.$touch();
      if (!this.$v.label.$invalid && !this.subSystemErrors) {
        this.isLoading = true;
        const data = {
          system_name: this.systemName,
          color: this.label.color,
          status: this.label.status ? "archive" : "live",
          title: this.label.title,
          label: this.label.label,
          label_category_name: this.label.category,
          subSystems: this.label.selectedSubSystems.map((row) => {
            return row.id;
          }),
          tabSystems: this.label.selectedTabSystems.map((row) => {
            return row.tab_name;
          }),
        };
        await this.insertRecord(data);
        this.isLoading = false;
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
        this.$toasterNA("red", this.$tr("please_fill_all_fields_correctly"));
      }
    },

    async insertRecord(data) {
      try {
        const response = await this.$axios.post("labels", data);
        if (response?.status === 201 && response?.data?.result) {
          this.resetForm();
          this.$refs.customStepper.setCurrent(2);
          this.insertNewItem(response?.data?.data);
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    resetForm() {
      this.subSystemErrors = "";
      this.subTabErrors = "";
      this.label.title = "";
      (this.label.label = ""), (this.label.color = "");
      this.label.status = false;
      this.label.subSystem = "";
      this.label.selectedSubSystems = [];
      this.label.selectedTabSystems = [];
      this.$refs.labelForm?.resetValidation();
    },

    closeDialog() {
      this.resetForm();
      this.$refs.customStepper.fillMore();
    },

    addSubSystem() {
      if (this.label.subSystem == "") {
        this.touchSubSystem();
        return;
      } else {
        this.label.selectedSubSystems.push(
          this.label.subSystems.find((ele) => ele.id === this.label.subSystem)
        );
        var set = new Set(this.label.selectedSubSystems);
        this.label.selectedSubSystems = Array.from(set);
        this.label.subSystem = "";
        this.touchSubSystem();
      }
    },

    addTabSystem() {
      if (this.label.tab == "") {
        this.touchTabSystem();
        return;
      } else {
        this.label.selectedTabSystems.push(
          this.tabItems.find((ele) => ele.id === this.label.tab)
        );
        var set = new Set(this.label.selectedTabSystems);
        this.label.selectedTabSystems = Array.from(set);
        this.label.tab = "";
        this.touchTabSystem();
      }
    },

    removeSubSystem(index) {
      this.label.selectedSubSystems.splice(index, 1);
      this.touchSubSystem();
    },

    removeTabSystem(index) {
      this.label.selectedTabSystems.splice(index, 1);
      this.touchTabSystem();
    },

    touchSubSystem() {
      this.subSystemErrors =
        this.label.selectedSubSystems.length === 0
          ? this.$tr("item_is_required", this.$tr("sub_system"))
          : "";
    },
    touchTabSystem() {
      this.tabSystemErrors =
        this.label.selectedTabSystems.length === 0
          ? this.$tr("item_is_required", this.$tr("tab_system"))
          : "";
    },

    titleRule(title) {
      return [
        (_) =>
          title.required || this.$tr("item_is_required", this.$tr("title")),
        (_) =>
          title.minLength ||
          this.$tr("min_length", this.$tr("title"), this.$tr("5")),
      ];
    },
    labelCategoryRule(title) {
      return [
        (_) =>
          title.required ||
          this.$tr("item_is_required", this.$tr("label_category")),
      ];
    },
    labelRule(label) {
      return [
        (_) =>
          label.required || this.$tr("item_is_required", this.$tr("label")),
        (_) =>
          label.minLength ||
          this.$tr("min_length", this.$tr("label"), this.$tr("5")),
      ];
    },
  },
};
</script>
