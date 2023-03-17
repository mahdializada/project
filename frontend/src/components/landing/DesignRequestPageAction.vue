<template>
  <div>
    <v-card class="w-full mb-2 px-2 py-1" elevation="1" outlined>
      <h4 class="title ma-0 mb-2">{{ $tr("actions") }}</h4>
      <div class="d-flex justify-center flex-wrap">
        <CustomButton
          v-if="showRestore"
          icon="fa-trash-restore"
          text="restore"
          type="primary4"
          @click="onRestore"
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
          v-if="showDelete"
          icon="fa-trash"
          text="delete"
          type="danger2"
          @click="onDelete"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
        />
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
                  <v-list-item-title>
                    {{ $tr(item.name) }}
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
import Alert from "../../helpers/alert";
import TextButton from "../../components/common/buttons/TextButton.vue";
import HandleException from "~/helpers/handle-exception";
import { required } from "vuelidate/lib/validators";
import { mapGetters } from "vuex";
import CustomButton from "../design/components/CustomButton.vue";
export default {
  components: {
    TextButton,
    CustomButton,
  },
  props: {
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
    showDownload: {
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
    async fetchAllReasons() {
      this.fetchIds();
      this.isFetchingReasons = true;
      try {
        const response = await this.$axios2.get(
          `reasons/id?type=${this.selectedStatus}&sub_system=${this.subSystemName}`
        );
        this.allReasons = response?.data?.reasons;
        this.showReasonDialog = true;
      } catch (error) {
        this.showReasonDialog = false;
        if (error?.response?.status === 404 && !error?.response?.data?.result)
          this.$toastr.w(this.$tr("sub_system_not_found"));
      }
      this.isFetchingReasons = false;
    },
    fetchIds() {
      this.itemIds = [];
      if (this.selectedStatus == "in design") {
        const filtered = this.selectedItems.filter(
          ({ has_in_storyboard }) => !has_in_storyboard
        );
        this.itemIds = filtered.map((item) => item.id);
      } else if (this.selectedStatus == "in storyboard") {
        const filtered = this.selectedItems.filter(
          ({ has_in_storyboard }) => has_in_storyboard
        );
        this.itemIds = filtered.map((item) => item.id);
      } else {
        this.itemIds = this.selectedItems.map((item) => item?.id);
      }
    },
    onSubmit(reasons, hasReason = true) {
      this.fetchIds();
      const data = {
        item_ids: this.itemIds,
        status:
          this.selectedStatus == "approve in design"
            ? "in design"
            : this.selectedStatus,
        reasons: typeof reasons === "string" ? [reasons] : reasons,
        type: hasReason ? "hasReason" : "noReason",
        no_reason_tabs: this.noReasonSubmitTabs,
        no_reason_operations: this.noReasonSubmitOperations,
      };
      if (reasons?.designNoteIds) {
        try {
          const reciver_ids = [];
          for (let i = 0; i < this.selectedItems.length; i++) {
            this.selectedItems[
              i
            ].design_request_order.design_request_order_user.map((item) => {
              if (
                item.user.id &&
                !reciver_ids.find((rec) => rec == item.user.id)
              )
                reciver_ids.push(item.user.id);
              return item;
            });
          }
          if (reciver_ids.length > 0) {
            const data = {
              ids: reasons?.designNoteIds,
              design_request_id: this.itemIds,
            };
            this.$axios2.post("update_design_request_reason_note", data);
          }
        } catch (error) {
          console.log(error);
        }
      }
      this.submitAction(data);
    },
    async submitAction(data) {
      this.showReasonDialog = false;
      try {
        const response =
          this.selectedStatus == "removed"
            ? await this.$axios2.delete(
                `${this.routeName}/${this.itemIds}?reasons=${data.reasons}`
              )
            : await this.$axios2.post(`${this.routeName}/change-status`, data);
        if (response?.status === 200 && response?.data?.result) {
          Alert.successAlert(
            this,
            this.$tr("successfully", this.$tr(this.selectedStatus))
          );

          this.selectedStatus = "";
          this.$emit("fetchNewData");
        } else this.$toastr.e(this.$tr("something_went_wrong"));
      } catch (error) {
        if (error?.response?.status === 307 && !error?.response?.data?.result) {
          this.$toastr.e(this.$tr("user_has_no_permission"));
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
              message = this.$tr(
                "note_is_either_empty_or_not_enough",
                this.$tr("design")
              );
              Alert.removeLimitAlert(this, message, "restricted");
              break;
            case 1203:
              message = this.$tr(
                "note_is_either_empty_or_not_enough",
                this.$tr("storyboard")
              );
              Alert.removeLimitAlert(this, message, "restricted");
              break;
            case 1201:
              Alert.removeLimitAlert(
                this,
                "design_note_and_design_link_are_empty",
                "restricted"
              );
              break;
            case 1202:
              Alert.removeLimitAlert(
                this,
                "design_files_are_emtpy",
                "restricted"
              );
              break;
            case 1400:
              Alert.removeLimitAlert(
                this,
                "no_employee_has_been_assigned",
                "restricted"
              );
              break;
            case 1600:
              Alert.removeLimitAlert(
                this,
                "employee_task_not_done",
                "restricted"
              );
              break;
            default:
              break;
          }
        } else if (
          error?.response?.status == 406 &&
          !error?.response?.data?.result
        ) {
          this.$toastr.e(this.$tr("not_allowed_to_change_status"));
        } else HandleException.handleApiException(this, error);
      }
    },
    onTracing() {
      // No Changes applied yet
      if (this.selectedItems[0].tracing_status && this.tabKey === "active") {
        this.$toastr.e(this.$tr("user_is_already_in_tracing_status"));
        return;
      }
      if (this.tabKey === "active" || this.tabKey === "tracing") {
        if (this.selectedItems?.length != 1) {
          Alert.removeLimitAlert(
            this,
            "please_select_an_item_first",
            "restricted"
          );
          return false;
        } else {
          this.selectedStatus =
            this.tabKey === "active" ? "tracing" : "untracing";
          const status = this.tabKey === "active" ? 1 : 0;
          Alert.removeDialogAlert(
            this,
            () => {
              this.toggleBooleanStatus("tracing_status", status);
            },
            "",
            this.tabKey === "tracing" ? "yes_untrace" : "yes_trace"
          );
        }
      } else {
        Alert.removeLimitAlert(
          this,
          "you_can_toggle_tracing_from_active_and_tracing_tab",
          "restricted"
        );
        return;
      }
    },
    onView() {
      this.selectedItems?.length === 1
        ? this.$emit("onView")
        : Alert.removeLimitAlert(
            this,
            "cant_view_more_than_one_item_at_the_same_time",
            "restricted"
          );
    },

    onUnAssign() {
      if (this.selectedItems?.length < 1) {
        Alert.removeLimitAlert(
          this,
          "please_select_an_item_first",
          "restricted"
        );
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
      console.log("assign", this.tabKey);
      let notAlloweds =
        "all, storyboard rejected, waiting, design rejected, cancelled, removed, completed";

      let isAllowed = false;
      this.selectedItems.forEach((element) => {
        this.doesInclude(notAlloweds, element.status);
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
        this.$toastr.e(this.$tr("job_is_already_pinned"));
        return 0;
      }
      if (this.tabKey === "published" || this.tabKey === "pinned") {
        if (this.selectedItems?.length != 1) {
          Alert.removeLimitAlert(
            this,
            "please_select_an_item_first",
            "restricted"
          );
          return 0;
        } else {
          this.selectedStatus =
            this.tabKey === "pinned" ? "unpinned" : "pinned";
          const status = this.tabKey === "published" ? 1 : 0;
          Alert.removeDialogAlert(
            this,
            () => {
              this.toggleBooleanStatus("is_pinned", status);
            },
            "",
            this.tabKey === "pinned" ? "yes_unpin" : "yes_pin"
          );
        }
      } else {
        Alert.removeLimitAlert(
          this,
          "you_can_only_toggle_pin_status_from_published_and_pinned_tab",
          "restricted"
        );
        return;
      }
    },

    onRestore() {
      this.selectedStatus = "restore";
      if (this.canDoOperation()) {
        Alert.restoreDialogAlert(this, () => {
          this.onSubmit([], false);
        });
      }
    },
    onApply() {
      if (this.selectedStatus?.length < 1) {
        Alert.chooseStatus(this);
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
        if (
          this.selectedStatus == "in design" ||
          this.selectedStatus == "in storyboard"
        ) {
          let inDesignCount = 0;
          let inStoryboardCount = 0;

          inDesignCount = this.selectedItems.filter(
            ({ has_in_storyboard }) => !has_in_storyboard
          ).length;
          inStoryboardCount = this.selectedItems.filter(
            ({ has_in_storyboard }) => has_in_storyboard
          ).length;

          let text = this.$tr(
            "design_request_alert_two_status",
            this.selectedItems.length,
            inDesignCount,
            inStoryboardCount
          );

          let allowed = this.$tr(
            "design_request_alert_allowed",
            this.selectedStatus
          );

          let html = "";
          if (
            this.selectedItems.length > 1 &&
            (inDesignCount > 0 || inStoryboardCount > 0)
          ) {
            html = `<p> ${text} </p> <p> ${allowed} </p>`;
          }

          Alert.removeDialogAlert(
            this,
            () => this.onSubmit([], false),
            "",
            "yes_sure",
            html
          );
        } else {
          Alert.removeDialogAlert(
            this,
            () => this.onSubmit([], false),
            "",
            "yes_sure"
          );
        }
      } else if (this.canDoOperation()) {
        this.fetchAllReasons();
      }
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
          Alert.removeDialogAlert(
            this,
            () => {
              this.onSubmit([], false);
            },
            "swal_remove_text"
          );
        } else
          Alert.removeDialogAlert(this, () => {
            this.onAgreed();
          });
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
        Alert.removeDialogAlert(
          this,
          () => {
            this.onAgreed();
          },
          "",
          this.$tr("yes_block")
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
          this.$toastr.w(this.$tr("at_least_one_langauge_should_be_active"));
          return false;
        }
        if (
          this.selectedStatus === "active" &&
          this.selectedItems[0]?.translatedCount !==
            this.getTotals("totalWords")
        ) {
          this.$toastr.e(
            this.$tr("can_not_active_language_unless_completely_translated")
          );
          return false;
        }
      }
      return true;
    },
    canDoOperation() {
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
        this.$toastr.e(this.$tr("item_is_already_in_current_status"));
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
        Alert.removeLimitAlert(
          this,
          "only_one_item_is_allowed_for_this_operation",
          "restricted"
        );
        return false;
      }
      if (this.selectedItems?.length < 1 || this.selectedItems?.length > 10) {
        this.selectedItems?.length < 1
          ? Alert.removeLimitAlert(
              this,
              "please_select_an_item_first",
              "restricted"
            )
          : Alert.removeLimitAlert(
              this,
              "cant_do_operation_on_more_than_ten_items_at_the_same_time",
              "restricted"
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
   