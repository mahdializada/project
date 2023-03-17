<template>
  <v-form
    :key="currentIndex"
    lazy-validation
    ref="reasonEditForm"
    @submit.prevent="onSubmit"
  >
    <Edit
      :is-icon="false"
      :icon-or-logo="''"
      :headers="headers"
      :title="''"
      :topIcon="topIcon"
      :tableName="tableName"
      :showTitle="false"
      :isLoading="isLoading"
      :is-submitting="isSubmitting"
      :is-auto-edit="isAutoEdit"
      :totals="editItems.length"
      :current-index="currentIndex"
      ref="editRef"
      @onValidate="validateStepper"
      @onSubmit="onSubmit"
      @close="closeDialog"
      @onItemChange="onItemChange"
      @onSave="onSave"
      @OnSaveAndNext="onSaveAndNext"
    >
      <template v-slot:step1>
        <div>
          <HeaderTitle title="general" />
          <div class="pl-3 pa-1">
            <div class="d-flex flex-column flex-md-row">
              <div class="w-full">
                <CustomInput
                  :items="subSystems"
                  v-model="subSystem"
                  :error-messages="subSystemErrors"
                  @blur="touchSubSystem"
                  :label="$tr('label_required', $tr('sub_system'))"
                  type="autocomplete"
                  item-text="name"
                  item-value="id"
                  :placeholder="$tr('choose_item', $tr('sub_system'))"
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
              <template v-for="(sub, index) in reason.selectedSubSystems">
                <SelectedItem
                  @close="removeSubSystem(index)"
                  :key="index"
                  :title="sub.name"
                />
              </template>
            </div>

            <div class="d-flex flex-column flex-md-row">
              <div class="w-full">
                <CustomInput
                  :items="types"
                  v-model="type"
                  @blur="touchType"
                  :error-messages="typeErrors"
                  :label="$tr('item_is_required', $tr('types'))"
                  type="autocomplete"
                  :placeholder="$tr('choose_item', $tr('types'))"
                  class="me-md-4 mb-md-2 mb-0"
                  :loading="loadingAutocompleteData"
                  :reason_type="true"
                />
              </div>
              <div class="w-100">
                <FormBtn
                  @click="addType"
                  title="add_plus"
                  class="mt-md-4 mb-2"
                />
              </div>
            </div>
            <div class="selected d-flex flex-wrap">
              <template v-for="(type, index) in reason.selectedTypes">
                <SelectedItem
                  @close="removeType(index)"
                  :key="index"
                  :title="type"
                />
              </template>
            </div>
          </div>
        </div>
      </template>
      <template v-slot:step2>
        <HeaderTitle title="reason" />
        <!-- <div class="d-flex flex-column flex-md-row">
          <div class="w-full">
            <CustomInput
              :items="reasons"
              v-model="title"
              :error-messages="subSystemErrors"
              @blur="touchReason"
              disabled
              :label="$tr('label_required', $tr('reason'))"
              type="autocomplete"
              item-text="title"
              item-value="id"
              :placeholder="$tr('choose_item', $tr('reason'))"
              class="me-md-4 mb-md-2 mb-0"
            />
          </div>
        </div> -->

        <div class="selected d-flex flex-wrap">
          <template v-for="(res, index) in reason.selectedReasons">
            <v-card
              style="background-color: #cedcf9"
              class="py-1 px-2 my-1 d-flex w-full"
              elevation="0"
              outlined
              :key="index"
            >
              <span>{{ res.title }}</span>
            </v-card>
          </template>
        </div>
      </template>
      <template v-slot:step3>
        <div class="topDiv">
          <DoneMessage
            :title="$tr('item_all_set', $tr('status_event_list'))"
            :subTitle="
              $tr('you_can_access_your_item', $tr('status_event_list'))
            "
          />
        </div>
      </template>
    </Edit>
  </v-form>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Edit from "~/components/design/Edit.vue";
import HandleException from "~/helpers/handle-exception";
import Alert from "~/helpers/alert";
import DoneMessage from "~/components/design/components/DoneMessage";
import CustomInput from "~/components/design/components/CustomInput.vue";
import FormBtn from "~/components/design/components/FormBtn.vue";
import SelectedItem from "~/components/design/components/SelectedItem";
import { minLength, required } from "vuelidate/lib/validators";
import HeaderTitle from "../../users/HeaderTitle.vue";

export default {
  name: "EditReason",
  components: {
    DoneMessage,
    Edit,
    CustomInput,
    FormBtn,
    SelectedItem,
    HeaderTitle,
  },
  props: {
    editItems: {
      require: true,
      type: Array,
    },

    editDialog: {
      required: true,
      type: Boolean,
    },

    isAutoEdit: {
      required: false,
      type: Boolean,
    },
    subSystems: {
      type: Array,
      required: true,
    },
    slug: {
      type: String,
      default: "",
    },
  },
  data() {
    const firstIndex = 0;
    return {
      currentIndex: firstIndex,
      topIcon: "mdi-account",
      tableName: this.$tr("reason_status"),
      headers: [
        { icon: "fa-user", title: "general", slotName: "step1" },
        { icon: "fa-comment", title: "reason", slotName: "step2" },
        { icon: "fa-thumbs-up", title: "done", slotName: "step3" },
      ],
      types: [],
      reason: {
        id: "",
        selectedSubSystems: [],
        selectedTypes: [],
        selectedReasons: [],
      },

      title: "",
      type: "",
      subSystem: "",

      typeErrors: "",
      subSystemErrors: "",
      isLoading: false,
      isSubmitting: false,
      loadingAutocompleteData: false,
    };
  },

  validations: {
    title: { required, minLength: minLength(2) },
  },
  computed: {
    ...mapGetters({
      reasons: "reasons/get_reasons",
    }),
  },
  created() {
    this.setReason(this.editItems[0]);
  },

  methods: {
    ...mapActions({
      fetchStatusEvent: "statusEvents/fetchStatusEvent",
    }),

    addType() {
      if (this.reason.type == "") {
        this.touchType();
        return;
      } else {
        this.reason.selectedTypes.push(
          this.types.find((type) => type === this.type)
        );
        var set = new Set(this.reason.selectedTypes);
        this.reason.selectedTypes = Array.from(set);
        this.type = "";
        this.touchType();
      }
    },
    removeType(index) {
      this.reason.selectedTypes.splice(index, 1);
      this.touchType();
    },

    touchType() {
      this.typeErrors =
        this.reason.selectedTypes.length === 0
          ? this.$tr("item_is_required", this.$tr("type"))
          : "";
    },

    addReason() {
      if (this.title == "") {
        this.touchReason();
        return;
      } else {
        this.reason.selectedReasons.push(
          this.reasons.find((row) => row.id === this.title)
        );
        var set = new Set(this.reason.selectedReasons);
        this.reason.selectedReasons = Array.from(set);
        this.title = "";
        this.touchReason();
      }
    },

    removeReason(index) {
      this.reason.selectedReasons.splice(index, 1);
    },
    touchReason() {
      this.reasonErrors =
        this.reason.selectedReasons.length === 0
          ? this.$tr("item_is_required", this.$tr("reason"))
          : "";
    },

    async addSubSystem() {
      if (this.subSystem == "") {
        this.touchSubSystem();
        return;
      } else {
        this.reason.selectedSubSystems = [];
        this.reason.selectedSubSystems.push(
          this.subSystems.find((ele) => ele.id === this.subSystem)
        );
        var set = new Set(this.reason.selectedSubSystems);
        this.reason.selectedSubSystems = Array.from(set);
        this.touchSubSystem();
        this.loadingAutocompleteData = true;
        const response = await this.$axios.get("subsystems", {
          params: { subsystem: this.reason.selectedSubSystems[0].name },
        });
        this.types = response.data;
        this.loadingAutocompleteData = false;
        this.subSystem = "";
        const temp_type = [];
        this.reason.selectedTypes.forEach((i) => {
          this.types.forEach((j) => {
            if (i == j) {
              temp_type.push(i);
            }
          });
        });
        this.reason.selectedTypes = temp_type;
      }
    },
    removeSubSystem(index) {
      this.reason.selectedSubSystems.splice(index, 1);
      this.touchSubSystem();
    },

    touchSubSystem() {
      this.subSystemErrors =
        this.reason.selectedSubSystems.length === 0
          ? this.$tr("item_is_required", this.$tr("sub_system"))
          : "";
    },

    closeDialog() {
      this.$emit("update:editDialog", false);
      this.$emit("update:isAutoEdit", false);
    },

    async setReason(data) {
      this.reason.id = data.id;
      this.reason.selectedTypes = data?.reason_types.map((row) => row.type);

      this.reason.selectedSubSystems = [
        this.subSystems.find((sub) => sub.id === data.sub_system_id),
      ];
      const response = await this.$axios.get("subsystems", {
        params: { subsystem: this.reason.selectedSubSystems[0].name },
      });
      this.types = response.data;

      this.reason.selectedReasons = [data.reason];
      this.title = data.reason.id;
    },

    // validate form and data
    async validateStepper(currentStep) {
      // this.$refs.updateForm.validate();
      switch (currentStep) {
        case 1:
          this.$refs.reasonEditForm.validate();
          this.touchType();
          this.touchSubSystem();
          this.touchReason();
          if (!this.typeErrors && !this.subSystemErrors) {
            this.$refs.editRef.nextForce();
          }
          break;
        case 2:
          this.$refs.editRef.nextForce();
          break;
        default:
          break;
      }
    },

    async onItemChange(actionType) {
      if (actionType === "next") {
        const index = this.currentIndex + 1;
        if (index < this.editItems.length) {
          this.setReason(this.editItems[index]);
          this.currentIndex = index;
        }
      } else if (actionType === "back") {
        const index = this.currentIndex - 1;
        if (index >= 0) {
          this.setReason(this.editItems[index]);
          this.currentIndex = index;
        }
      }
    },

    async onSaveAndNext() {
      await this.onSubmit("onSaveAndNext");
    },

    async onSave() {
      await this.onSubmit("can't");
    },

    // this.$refs.editRef.nextForce();

    async onSubmit(canNext = "canNext") {
      this.$refs.reasonEditForm.validate();
      this.touchType();
      this.touchSubSystem();
      this.touchReason();
      if (!this.reasonErrors && !this.typeErrors && !this.subSystemErrors) {
        const data = {
          slug: this.slug,
          id: this.reason.id,
          reason: this.reason.selectedReasons.map((row) => {
            return row.id;
          }),
          type: this.reason.selectedTypes.map((row) => {
            return row;
          }),
          subSystem: this.reason.selectedSubSystems.map((row) => {
            return row.id;
          }),
        };
        await this.updateRecord(data, canNext);
      } else {
        // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
					this.$toasterNA("orange",this.$tr('please_fill_all_fields_correctly'));

      }
    },

    async updateRecord(data, canNext) {
      this.isSubmitting = true;
      try {
        const response = await this.$axios.put("status_events/id", data);
        this.fetchStatusEvent({ slug: this.slug });
        this.isSubmitting = false;
        if (response?.status === 200 && response?.data?.result) {
          if (canNext === "onSaveAndNext") {
            if (this.currentIndex < this.editItems.length) {
              Alert.successAlert(this, this.$tr("successfully_updated"));
              this.onItemChange("next");
            }
          } else if (canNext === "canNext") {
            this.$refs.editRef.nextForce();
          } else {
            Alert.successAlert(this, this.$tr("successfully_updated"));
          }
        } else {
          // this.$toastr.w(this.$tr("something_went_wrong"));
					this.$toasterNA("orange",this.$tr('something_went_wrong'));

        }
      } catch (error) {
        this.isSubmitting = false;
        HandleException.handleApiException(this, error);
      }
    },

    titleRule(title) {
      return [
        (_) => title.required || this.$tr("required", this.$tr("title")),
        (_) =>
          title.minLength ||
          this.$tr("min_length", this.$tr("title"), this.$tr("2")),
      ];
    },
  },
};
</script>

<style>
.main-Card {
  background-color: #f9fafd !important;
}

.main-Card .title {
  color: #b6c1d2 !important;
}

.main-Card .v-stepper .v-stepper__header .v-stepper__step__step {
  display: none !important;
}

.main-Card .v-stepper__step.v-stepper__step--active .v-stepper__label .v-btn {
  background-color: var(--v-primary-base);
  color: white;
}
</style>
