<template>
  <div>
    <div>
      <ReasonDialog
        @closeDialog="closeDialog"
        @onSubmit="onSubmit"
        :title="reasonTitle"
        :allReasons="allReasons"
        :isMultiple="isMultipleChoice"
        :showReasonDialog.sync="showReasonDialog"
        :isFetchingReasons.sync="isFetchingReasons"
      />
    </div>

    <v-card class="w-full mb-2 px-2 py-1" elevation="1" outlined>
      <h4 class="title ma-0 mb-2">{{ $tr("actions") }}</h4>
      <div class="d-flex justify-center flex-wrap">
        <slot name="firstButton" />

        <CustomButton
          v-if="showAddSupplier"
          @click="canDoOperation() ? $emit('onAddSupplier') : ''"
          icon="fa-user-circle"
          :text="$tr('add_item', $tr('supplier'))"
          type="dark"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showTracing"
          @click="onTracing"
          icon="fa-eye"
          text="toggle_tracing"
          type="dark"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showView"
          @click="onView"
          icon="fa-eye"
          text="view"
          type="dark"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showPin"
          @click="onPin"
          icon="fa-thumbtack"
          text="pin"
          type="primary4"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showEdit"
          icon="fa-user-edit"
          text="edit"
          type="primary-darken2"
          @click="onEdit"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showApprove"
          icon="fa-book-reader"
          text="auto_review"
          type="primary-darken2"
          @click="onApprove"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showRestore"
          icon="fa-trash-restore"
          text="restore"
          type="primary4"
          @click="onRestore"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showAutoEdit"
          icon="fa-cog"
          text="auto_edit"
          type="primary-darken1"
          @click="onAutoEdit"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />

        <CustomButton
          v-if="showUnAssign"
          icon="fa-user-minus"
          text="un_assign"
          type="danger2"
          @click="onUnAssign"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />

        <CustomButton
          v-if="showAssign"
          icon="fa-cog"
          text="assign"
          type="primary4"
          @click="onAssign"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showBlock"
          icon="fa-times-circle"
          text="block"
          type="danger"
          @click="onBlock"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <CustomButton
          v-if="showDelete"
          icon="fa-trash"
          text="delete"
          type="danger2"
          @click="onDelete"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
        <slot name="afterDelete" />

        <!-- add class customDisabled to disable the select -->
        <div
          v-if="showSelect"
          :style="`${
            routeName == 'design-request'
              ? 'width: 200px !important;'
              : 'width: 150px !important;'
          } margin: 0.16rem 0.16rem`"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        >
          <v-select
            class="customSelect"
            v-model="selectedStatus"
            :items="statusItems"
            :placeholder="$tr('select_status')"
            dense
            outlined
            item-value="id"
            item-text="name"
            :menu-props="{ bottom: true, offsetY: true }"
            hide-details="auto"
          >
            <template v-slot:[`selection`]="{ item }">
              <div>
                {{ $tr(item.name) }}
              </div>
            </template>
            <template v-slot:[`item`]="{ item }">
              <v-list-item-content>
                <div>
                  <v-list-item-title v-html="`${$tr(item.name)}`">
                  </v-list-item-title>
                </div>
              </v-list-item-content>
            </template>
          </v-select>
        </div>
        <slot />
        <CustomButton
          v-if="showApply"
          icon="fa-check"
          text="apply"
          type="primary"
          @click="onApply"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
      </div>
    </v-card>
  </div>
</template>
<script>
import CustomButton from "./components/CustomButton.vue";
import CustomInput from "./components/CustomInput.vue";
import CloseBtn from "./Dialog/CloseBtn.vue";
import TextButton from "../../components/common/buttons/TextButton.vue";
import HandleException from "~/helpers/handle-exception";
import ReasonDialog from "../../components/common/ReasonDialog.vue";
import { required } from "vuelidate/lib/validators";
import { mapGetters } from "vuex";
export default {
  components: {
    ReasonDialog,
    TextButton,
    CustomButton,
    CustomInput,
    CloseBtn,
  },
  props: {
    type: {
      type: String,
      default: "multiple",
    },
    subSystemName: {
      type: String,
      default: "",
    },
    routeName: {
      type: String,
      default: "",
    },
    isMultipleChoice: {
      type: Boolean,
      default: true,
    },
    notAllowedTabs: {
      type: String,
      default: "all",
    },
    noReasonSubmitTabs: {
      type: String,
      default: "pending, removed, deleted",
    },
    noReasonSubmitOperations: {
      type: String,
      default: "",
    },
    singleOperationTabs: {
      type: String,
      default: "",
    },
    singleItemOperations: {
      type: String,
      default: "view, edit",
    },

    isLanguage: {
      type: Boolean,
      default: false,
    },
    ignoreReason: {
      type: Boolean,
      default: false,
    },
    statusItems: {
      default: () => [
        { id: "active", name: "active" },
        { id: "inactive", name: "inactive" },
      ],
    },
    tabKey: {
      type: String,
      default: "all",
    },
    showTracing: {
      type: Boolean,
      default: false,
    },
    showView: {
      type: Boolean,
      default: true,
    },
    showEdit: {
      type: Boolean,
      default: true,
    },
    showRestore: {
      type: Boolean,
      default: false,
    },
    showPin: {
      type: Boolean,
      default: false,
    },
    showAutoEdit: {
      type: Boolean,
      default: true,
    },

    showUnAssign: {
      type: Boolean,
      default: false,
    },

    showAssign: {
      type: Boolean,
      default: false,
    },
    showSelect: {
      type: Boolean,
      default: true,
    },
    showBlock: {
      type: Boolean,
      default: true,
    },
    showDelete: {
      type: Boolean,
      default: true,
    },
    showApply: {
      type: Boolean,
      default: true,
    },
    selectedItems: {
      type: Array,
    },
    isLog: {
      type: Boolean,
      default: false,
    },
    showApprove: {
      type: Boolean,
      default: false,
    },
    showAddSupplier: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      reasonTitle: "",
      selectedStatus: "",
      allReasons: [],
      showReasonDialog: false,
      isFetchingReasons: false,
      itemIds: [],
    };
  },
  computed: {
    ...mapGetters({
      getTotals: "languages/getTotal",
    }),
  },
  methods: {
    async fetchAllReasons(statusTo = null) {
      this.isFetchingReasons = true;
      try {
        const response = await this.$axios.get(
          `reasons/id?type=${
            statusTo == null
              ? this.selectedStatus
              : (this.selectedStatus = statusTo)
          }&sub_system=${this.subSystemName}`
        );
        this.allReasons = response?.data?.reasons;
        this.showReasonDialog = true;
      } catch (error) {
        this.showReasonDialog = false;
        if (error?.response?.status === 404 && !error?.response?.data?.result)
          // this.$toastr.w(this.$tr("sub_system_not_found"));
          this.$toasterNA("orange", this.$tr("sub_system_not_found"));
      }
      this.isFetchingReasons = false;
    },
    onSubmit(reasons, hasReason = true, statusTo = null) {
      this.itemIds = this.selectedItems.map((item) => item?.id);

      const data = {
        item_ids: this.itemIds,
        status: statusTo == null ? this.selectedStatus : statusTo,
        reasons: typeof reasons === "string" ? [reasons] : reasons,
        type: hasReason ? "hasReason" : "noReason",
        no_reason_tabs: this.noReasonSubmitTabs,
        no_reason_operations: this.noReasonSubmitOperations,
      };
      this.submitAction(data);
    },
    async submitAction(data) {
      this.showReasonDialog = false;
      try {
        const response =
          this.selectedStatus == "removed"
            ? await this.$axios.delete(
                `${this.routeName}/${this.itemIds}?reasons=${data.reasons}`
              )
            : await this.$axios.post(`${this.routeName}/change-status`, data);
        if (response?.status === 200 && response?.data?.result) {
          this.$toasterNA(
            "green",
            this.$tr("successfully", this.$tr(this.selectedStatus))
          );
          this.selectedStatus = "";
          this.$emit("fetchNewData");
        } else this.$toasterNA("red", this.$tr("something_went_wrong"));
      } catch (error) {
        if (error?.response?.status === 307 && !error?.response?.data?.result) {
          this.$toasterNA("red", this.$tr("user_has_no_permission"));
          this.$emit("askToAddPermissions", this.selectedItems[0].id);
          return;
        }
        if (
          [1200, 1400, 1600, 1201, 1202, 1203].includes(
            error?.response?.data?.code
          ) &&
          !error?.response?.data?.result
        ) {
          let message = "";
          switch (error?.response?.data?.code) {
            case 1200:
              this.$toasterNA(
                "orange",
                this.$tr(
                  "note_is_either_empty_or_not_enough",
                  this.$tr("design")
                )
              );
              break;
            case 1203:
              this.$toasterNA(
                "orange",
                this.$tr(
                  "note_is_either_empty_or_not_enough",
                  this.$tr("storyboard")
                )
              );
              break;
            case 1201:
              this.$toasterNA(
                "orange",
                this.$tr("design_note_and_design_link_are_empty")
              );
              break;
            case 1202:
              this.$toasterNA("orange", this.$tr("design_files_are_emtpy"));
              break;
            case 1400:
              this.$toasterNA(
                "orange",
                this.$tr("no_employee_has_been_assigned")
              );
              break;
            case 1600:
              this.$toasterNA("orange", this.$tr("employee_task_not_done"));
              break;
            default:
              break;
          }
        } else if (
          error?.response?.status == 406 &&
          !error?.response?.data?.result
        ) {
          this.$toasterNA("red", this.$tr("not_allowed_to_change_status"));
        } else HandleException.handleApiException(this, error);
      }
    },
    onTracing() {
      // No Changes applied yet
      if (this.selectedItems[0].tracing_status && this.tabKey === "active") {
        this.$toasterNA("red", this.$tr("user_is_already_in_tracing_status"));
        return;
      }
      if (this.tabKey === "active" || this.tabKey === "tracing") {
        if (this.selectedItems?.length != 1) {
          this.$toasterNA("red", this.$tr("please_select_an_item_first"));
          return false;
        } else {
          this.selectedStatus =
            this.tabKey === "active" ? "tracing" : "untracing";
          const status = this.tabKey === "active" ? 1 : 0;
          this.$TalkhAlertNA(
            this.tabKey === "tracing"
              ? "Do you want to untrace the events?"
              : "Do you want to trace the events?", //text
            this.tabKey === "tracing" ? "untracing" : "tracing", //icon
            async () => {
              this.toggleBooleanStatus("tracing_status", status);
            }, // callback function
            this.tabKey === "tracing" ? "yes_untrace" : "yes_trace" // btn text
          );
        }
      } else {
        this.$toasterNA(
          "orange",
          this.$tr("you_can_toggle_tracing_from_active_and_tracing_tab")
        );
        return;
      }
    },
    onView() {
      this.selectedItems?.length === 1
        ? this.$emit("onView")
        : this.$toasterNA("orange", this.$tr("item_max_limit_text"));
    },

    onUnAssign() {
      if (this.selectedItems?.length < 1) {
        this.$toasterNA("orange", this.$tr("please_select_an_item_first"));
        return;
      }
      const notAlloweds =
        "all, storyboard rejected, waiting, design rejected, cancelled, removed, completed, not assigned";

      if (this.doesInclude(notAlloweds, this.tabKey)) {
        this.$toasterNA("orange", this.$tr("cant_do_operation_on_current_tab"));
        return;
      }
      const app = this;
      this.$TalkhAlertNA(
        "Are you sure ?", //text
        "delete", //icon
        async () => {
          app.$emit("onUnAssign");
        }, // callback function
        "yes_unassign" // btn text
      );
    },

    onAssign() {
      let notAlloweds =
        "all, storyboard rejected, waiting, design rejected, cancelled, removed, completed";

      let isAllowed = false;
      this.selectedItems.forEach((element) => {
        isAllowed = isAllowed || this.doesInclude(notAlloweds, element.status);
      });
      if (this.doesInclude(notAlloweds, this.tabKey) || isAllowed) {
        this.$toasterNA("orange", this.$tr("cant_do_operation_on_current_tab"));
        return 0;
      }
      if (this.canDoOperation()) this.$emit("onAssign");
    },
    closeDialog() {
      this.selectedStatus = "";
      this.itemIds = [];
      this.isFetchingReasons = false;
      this.showReasonDialog = false;
      this.reasonTitle = "";
    },
    toggleBooleanStatus(column, status) {
      const data = {
        item_id: this.selectedItems[0].id,
        type: "toggle",
        column: column,
        toggle_status: status,
      };
      this.submitAction(data);
    },
    onPin() {
      // Multiple Not Applied
      if (this.selectedItems[0]?.is_pinned && this.tabKey == "published") {
        this.$toasterNA("red", this.$tr("job_is_already_pinned"));
        return 0;
      }
      if (this.tabKey === "published" || this.tabKey === "pinned") {
        if (this.selectedItems?.length != 1) {
          this.$toasterNA("orange", this.$tr("please_select_an_item_first"));
          return 0;
        } else {
          this.selectedStatus =
            this.tabKey === "pinned" ? "unpinned" : "pinned";
          const status = this.tabKey === "published" ? 1 : 0;
          this.$TalkhAlertNA(
            this.tabKey === "pinned"
              ? "Do you want to unpinned it?"
              : "Do you want to pin it?", //text
            this.tabKey === "pinned" ? "unpin" : "pin", //icon
            async () => {
              this.toggleBooleanStatus("is_pinned", status);
            }, // callback function
            this.tabKey === "pinned" ? "yes_unpin" : "yes_pin" // btn text
          );
        }
      } else {
        this.$toasterNA(
          "red",
          this.$tr(
            "you_can_only_toggle_pin_status_from_published_and_pinned_tab"
          )
        );
        return;
      }
    },

    onRestore() {
      this.selectedStatus = "restore";
      if (this.canDoOperation()) {
        this.$TalkhAlertNA(
          "Are you sure?",
          "changeStatus",
          async () => {
            if (this.ignoreReason) {
              this.$emit("onRestore");
              return;
            }
            this.onSubmit([], false);
          },
          "yes_restore",
          "swal_restore_text"
        );
      }
    },

    onApply() {
      if (this.selectedStatus?.length < 1) {
        this.$toasterNA(
          "orange",
          this.$tr("status_not_selected_please_status_first")
        );
        return 0;
      }
      this.reasonTitle = this.$tr(
        "ask_for_why",
        this.$tr(this.selectedStatus).toLowerCase(),
        this.selectedItems?.length > 1
          ? this.$tr("items").toLowerCase()
          : this.$tr("item").toLowerCase()
      );
      if (!this.checkLanguage()) return 0;
      if (
        this.canDoOperation() &&
        ((this.doesInclude(this.noReasonSubmitTabs, this.tabKey) &&
          this.doesInclude(this.noReasonSubmitTabs, this.selectedStatus)) ||
          this.doesInclude(this.noReasonSubmitOperations, this.selectedStatus))
      ) {
        this.$TalkhAlertNA(
          "Are you sure ?", //text
          "changeStatus", //icon
          async () => this.onSubmit([], false), // accept callback function
          "yes_sure" // accept btn text
        );
      } else if (this.canDoOperation()) this.fetchAllReasons();
    },
    doesInclude(arr, key) {
      return arr
        .split(",")
        .map((item) => item.trim())
        .includes(key);
    },
    onDelete() {
      this.reasonTitle = this.$tr(
        "ask_for_why",
        this.$tr("delete").toLowerCase(),
        this.selectedItems?.length > 1
          ? this.$tr("items").toLowerCase()
          : this.$tr("item").toLowerCase()
      );
      this.selectedStatus = "removed";
      if (!this.checkLanguage()) return 0;
      if (this.canDoOperation()) {
        if (["deleted", "removed"].includes(this.tabKey)) {
          this.$TalkhAlertNA(
            "Are you sure?", //text
            "delete", //icon
            async () => {
              if (this.ignoreReason) {
                this.$emit("onDelete");
                return;
              }
              this.onSubmit([], false);
            }, // accept callback functions
            "yes_delete",
            "swal_remove_text"
          );
        } else
          this.$TalkhAlertNA(
            "Are you sure?", //text
            "delete", // icon
            async () => {
              if (this.ignoreReason) {
                this.$emit("onDelete");
                return;
              }
              this.onAgreed();
            }, // call back function
            "yes_delete" // accept btn text
          );
      }
    },
    onEdit() {
      if (this.canDoOperation()) this.$emit("onEdit");
    },
    onApprove() {
      if (["storyboard review", "design review"].includes(this.tabKey)) {
        if (this.canDoOperation()) this.$emit("onApprove");
      } else {
        this.$toasterNA("orange", this.$tr("cant_do_operation_on_current_tab"));
      }
    },
    onAutoEdit() {
      if (this.canDoOperation()) this.$emit("onAutoEdit");
    },
    onBlock() {
      this.reasonTitle = this.$tr(
        "ask_for_why",
        this.$tr("block").toLowerCase(),
        this.selectedItems?.length > 1
          ? this.$tr("items").toLowerCase()
          : this.$tr("item").toLowerCase()
      );
      this.selectedStatus = "blocked";
      if (!this.checkLanguage()) return 0;

      if (this.canDoOperation()) {
        this.$TalkhAlertNA(
          "Do you want to block the record?",
          "block",
          async () => {
            this.onAgreed();
          },
          "yes_block"
        );
      }
    },
    onAgreed() {
      this.doesInclude(this.noReasonSubmitTabs, this.tabKey) ||
      this.doesInclude(this.noReasonSubmitOperations, this.selectedStatus)
        ? this.onSubmit([], false)
        : this.fetchAllReasons();
    },
    checkLanguage() {
      if (this.isLanguage) {
        if (this.tabKey === "active" && this.getTotals("active") <= 1) {
          this.$toasterNA(
            "orange",
            this.$tr("at_least_one_langauge_should_be_active")
          );
          return false;
        }
        if (
          this.selectedStatus === "active" &&
          this.selectedItems[0]?.translatedCount !==
            this.getTotals("totalWords")
        ) {
          this.$toasterNA(
            "red",
            this.$tr("can_not_active_language_unless_completely_translated")
          );
          return false;
        }
      }
      return true;
    },
    canDoOperation() {
      if (this.isLog) return true;
      if (
        this.notAllowedTabs
          .split(",")
          .map((item) => item.trim())
          .includes(this.tabKey)
      ) {
        this.$toasterNA("orange", this.$tr("cant_do_operation_on_current_tab"));
        return false;
      }
      if (
        this.tabKey === this.selectedStatus &&
        this.selectedStatus !== "removed"
      ) {
        this.$toasterNA("red", this.$tr("item_is_already_in_current_status"));
        return false;
      }
      if (
        (this.singleOperationTabs
          .split(",")
          .map((item) => item.trim())
          .includes(this.tabKey) ||
          this.singleItemOperations
            .split(",")
            .map((item) => item.trim())
            .includes(this.selectedStatus)) &&
        this.selectedItems?.length !== 1
      ) {
        this.$toasterNA(
          "orange",
          this.$tr("only_one_item_is_allowed_for_this_operation")
        );
        return false;
      }
      if (this.selectedItems?.length < 1 || this.selectedItems?.length > 10) {
        this.selectedItems?.length < 1
          ? this.$toasterNA("orange", this.$tr("please_select_an_item_first"))
          : this.$toasterNA(
              "red",
              this.$tr(
                "cant_do_operation_on_more_than_ten_items_at_the_same_time"
              )
            );
        return false;
      }
      return true;
    },
  },
};
</script>
<style>
.customDisabled {
  opacity: 0.6 !important;
  pointer-events: none !important;
  cursor: not-allowed !important;
}
</style>
<style scoped>
.title,
.title2 {
  font-size: 1rem !important;
  line-height: 1rem !important;
}
.customSelect ::placeholder {
  color: black !important;
  font-weight: 500;
}
.theme--dark .customSelect ::placeholder {
  color: white !important;
}
</style>
