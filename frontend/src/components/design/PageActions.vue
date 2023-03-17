<template>
  <div>
    <v-dialog v-model="showReasonDialog" persistent width="800" scrollable>
      <v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
        <v-card-title class="py-1">
          <v-app-bar-title>
            <span class="custom-dialog-title">{{ $tr("reasons") }}</span>
          </v-app-bar-title>
          <v-spacer />
          <CloseBtn @click="closeDialog" />
        </v-card-title>
        <v-card-text>
          <v-row class="ma-0">
            <v-card elevation="0" class="ma-0 mt-0 w-full mb-5">
              <div class="text-center pt-2">
                <h2 class="text-uppercase" style="letter-spacing: 4px !important">
                  {{ $tr("reasons") }}
                </h2>
              </div>
              <v-divider class="gray lighten-3 mt-1 mb-2"></v-divider>

              <v-form ref="form" lazy-validation @submit.prevent="onSubmit">
                <div class="pa-3" v-if="subSystemName == 'Design Request'">
                  <div v-if="allReasons.length === 0" class="py-5 d-flex align-center justify-center">
                    {{ $tr("no_reasons_available") }}
                  </div>
                  <span class="py-0" v-for="(reason, index) in allReasons" :key="index">
                    <v-checkbox multiple validate-on-blur dense v-model="selectedReasons" hide-details
                      :value="reason.reason_id" :label="reason.title" :error-messages="errorMessages"
                      @change="$v.selectedReasons.$touch()"></v-checkbox>
                  </span>
                </div>
                <v-row v-else class="pt-3 ma-0">
                  <v-col cols="12" class="py-0">
                    <CustomInput :loading="isFetchingReasons" :items="allReasons" v-model="reason_id"
                      :label="$tr('label_required', $tr('reason'))" :error-messages="reasonIdErrors"
                      @input="$v.reason_id.$touch()" @blur="$v.reason_id.$touch()" type="autocomplete" item-text="title"
                      item-value="reason_id" :placeholder="$tr('choose_item', $tr('reason'))" class="mb-md-2 mb-0" />
                  </v-col>
                </v-row>
              </v-form>
            </v-card>
            <v-card elevation="0" class="pa-2 w-full mt-5">
              <TextButton type="secondary" class="ms-1 float-end" :text="$tr('cancel')" @click="closeDialog">
              </TextButton>
              <TextButton type="primary" class="float-end" :text="$tr('submit')" @click="onSubmit">
              </TextButton>
            </v-card>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="showDeleteItems" persistent width="800" scrollable>
      <v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
        <v-card-title class="py-1">
          <v-app-bar-title>
            <span class="custom-dialog-title">{{ $tr("deletion") }}</span>
          </v-app-bar-title>
          <v-spacer />
          <CloseBtn @click="closeDeleteItemsDialog" />
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-card elevation="0" class="ma-3 mt-0 w-full py-2">
              <v-form ref="form" lazy-validation @submit.prevent="selectItemsDelete">
                <div class="d-flex justify-space-between align-center px-3">
                  <div>
                    <h3 class="font-weight-medium">
                      {{ $tr("please_select_items_to_delete") }}
                    </h3>
                  </div>
                  <div>
                    <v-checkbox v-if="routeName != 'labels'" class="primary--text" v-model="selectAll"
                      @change="selectAllItems" :label="$tr('select_all')"></v-checkbox>
                  </div>
                </div>
                <v-row class="ma-0">
                  <v-list class="w-full" dense>
                    <v-list-item-group v-model="selectedItemsDelete" multiple>
                      <v-list-item @change="itemChange" v-for="(item, i) in getDeleteItems(routeName)"
                        :value="item.name" :key="i">
                        <template v-slot:default="{ active }">
                          <v-list-item-action>
                            <v-checkbox @change="itemChange" :input-value="active" color="primary"></v-checkbox>
                          </v-list-item-action>
                          <v-list-item-icon class="mx-1">
                            <v-icon small>{{ item.icon }}</v-icon>
                          </v-list-item-icon>
                          <v-list-item-content>
                            <v-list-item-title>
                              {{ $tr(item.name) }}
                            </v-list-item-title>
                          </v-list-item-content>
                        </template>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>

                  <p v-if="
                    selectedItemsDelete.includes('Delete label with category')
                  " style="color: red" class="px-3">
                    If delete with category all realted labels will delete
                  </p>
                </v-row>
              </v-form>
            </v-card>
            <v-card elevation="0" class="mx-3 pa-2 w-full">
              <TextButton type="secondary" class="ms-1 float-end" :text="$tr('cancel')" @click="closeDeleteItemsDialog">
              </TextButton>

              <TextButton v-if="selectedItemsDelete.length > 0" type="primary" class="float-end" :text="$tr('submit')"
                @click="selectItemsDelete">
              </TextButton>
              <TextButton v-else type="disabled" class="float-end" :text="$tr('submit')">
              </TextButton>
            </v-card>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-card class="w-full mb-2 px-2 py-1" elevation="1" outlined>
      <h4 class="title ma-0 mb-2">{{ $tr("actions") }}</h4>
      <div class="d-flex justify-center flex-wrap">
        <CustomButton v-if="showTracing" @click="onTracing" icon="fa-eye" text="toggle_tracing" type="dark"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
        <CustomButton v-if="showView" @click="onView" icon="fa-eye" text="view" type="dark"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
        <CustomButton v-if="showApprove" icon="fa-book-reader" text="auto_review" type="primary-darken2"
          @click="onApprove" :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
        <CustomButton v-if="showPin" @click="onPin" icon="fa-thumbtack" text="pin" type="primary4"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
        <CustomButton v-if="showEdit" icon="fa-user-edit" text="edit" type="primary-darken2" @click="onEdit"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
        <CustomButton v-if="showAutoEdit" icon="fa-cog" text="auto_edit" type="primary-darken1" @click="onAutoEdit"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />

        <CustomButton v-if="showAssign" icon="fa-cog" text="assign" type="primary4" @click="onAssign"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
        <CustomButton v-if="showBlock" icon="fa-times-circle" text="block" type="danger" @click="onBlock"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
        <CustomButton v-if="showDelete" icon="fa-trash" text="delete" type="danger2" @click="onDelete"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
        <!-- add class customDisabled to disable the select -->
        <div v-if="showSelect" :style="`${
          routeName == 'design-request'
            ? 'width: 200px !important;'
            : 'width: 150px !important;'
        } margin: 0.16rem 0.16rem`" :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`">
          <v-select class="customSelect" v-model="selectedStatus" :items="statusItems"
            :placeholder="$tr('select_status')" dense outlined item-value="id" item-text="name"
            :menu-props="{ bottom: true, offsetY: true }" hide-details="auto">
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
        <ChangeActiveStatus ref="AssignUserRef" />
        <CustomButton v-if="showApply" icon="fa-check" text="apply" type="primary" @click="onApply"
          :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`" />
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
import { required } from "vuelidate/lib/validators";
import { mapActions, mapGetters } from "vuex";
import ChangeActiveStatus from "../companies/ChangeActiveStatus.vue";

export default {
  components: {
    TextButton,
    CustomButton,
    CustomInput,
    CloseBtn,
    ChangeActiveStatus
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
    isLanguage: {
      type: Boolean,
      required: false,
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
      required: false,
    },
    showView: {
      type: Boolean,
      default: true,
    },
    showEdit: {
      type: Boolean,
      default: true,
    },
    showPin: {
      type: Boolean,
      default: false,
    },
    showAutoEdit: {
      type: Boolean,
      default: true,
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
    defaultAction: {
      type: Boolean,
      default: true,
    },
    isLog: {
      type: Boolean,
      default: false,
      required: false,
    },
    showApprove: {
      type: Boolean,
      default: false,
      required: false,
    },
		AssignInCompany:{
      type:Boolean,
      default:false,
    },
  },
  data() {
    return {
      selectedStatus: "",
      reason_id: "",
      allReasons: [],
      selectedReasons: [],
      showReasonDialog: false,
      isFetchingReasons: false,
      showDeleteItems: false,
      selectedItemsDelete: [],
      itemIds: "",
      selectAll: false,
      deleteItems: [
        {
          name: "departments",
          icon: "mdi-school",
        },
        {
          name: "teams",
          icon: "mdi-account-group",
        },
        {
          name: "roles",
          icon: "mdi-shield-account",
        },
        {
          name: "users",
          icon: "mdi-account-outline",
        },
        {
          name: "design_request",
          icon: "mdi-note-edit",
        },
      ],
    };
  },

  watch: {
    selectedItemsDelete(value) {
      if (value.length == this.deleteItems.length) {
        this.selectAll = true;
      }
    },
  },

  validations: {
    reason_id: { required },
    selectedReasons: { required },
  },

  computed: {
    ...mapGetters({
      getTotals: "languages/getTotal",
    }),

    errorMessages() {
      const errors = [];
      if (!this.$v.selectedReasons.$dirty) return errors;
      !this.$v.selectedReasons.required &&
        errors.push(this.$tr("please_select_one_option"));
      return errors;
    },

    reasonIdErrors() {
      const errors = [];
      if (!this.$v.reason_id.$dirty) return errors;
      !this.$v.reason_id.required &&
        errors.push(this.$tr("please_select_one_option"));
      return errors;
    },
  },
  methods: {
    getDeleteItems(type) {
      switch (type) {
        case "companies":
          return this.deleteItems;
        case "departments":
          let depItems = ["teams", "roles"];
          return this.deleteItems.filter((item) => {
            if (depItems.includes(item.name)) {
              return item;
            }
          });
        case "teams":
        case "roles":
          let teamItems = ["users"];
          return this.deleteItems.filter((item) => {
            if (teamItems.includes(item.name)) {
              return item;
            }
          });
        case "labels":
          return (this.deleteItems = [
            {
              name: "Delete label",
              icon: "fa-trash",
            },
            {
              name: "Delete label with category",
              icon: "fa-trash",
            },
          ]);
      }
    },

    async fetchAllReasons() {
      this.isFetchingReasons = true;

      try {
        const response = await this.$axios.get(
          `reasons/id?type=${this.selectedStatus}&sub_system=${this.subSystemName}`
        );
        this.allReasons = response?.data?.reasons;
        this.showReasonDialog = true;
      } catch (error) {
        this.showReasonDialog = false;
        if (error?.response?.status === 404 && !error?.response?.data?.result) {
        this.$toasterNA("orange", this.$tr("sub_system_not_found"));
        }
      }
      this.isFetchingReasons = false;
    },

    noReasonSubmit() {
      let data = {
        item_id: this.selectedItems[0]?.id,
        status: this.selectedStatus,
        type: "pending",
      };
      this.submitAction(data);
    },

    reasonSubmit() {
      let data = {
        item_id: this.selectedItems[0]?.id,
        status: this.selectedStatus,
        reasons:
          this.subSystemName == "Design Request" ? this.selectedReasons : [],
        reason_id: this.reason_id,
        type: this.subSystemName == "Design Request" ? "multiple" : "status",
      };
      this.submitAction(data);
    },

    async submitAction(data) {
      let systemName = "labels" === this.routeName ? "system_name=" : "slug=";
      if (["labels", "status-events", "reasons"].includes(this.routeName)) {
        var substr = this.subSystemName.substring(
          this.subSystemName.indexOf("(") + 1,
          this.subSystemName.lastIndexOf(")")
        );
        systemName += substr;
      }

      this.showReasonDialog = false;
      try {
        const response =
          this.selectedStatus == "removed"
            ? await this.$axios.delete(
              `${this.routeName}/${this.itemIds}?reasons=${this.subSystemName == "Design Request"
                ? this.selectedReasons
                : this.reason_id
              }&categories_to_delete=${this.selectedItemsDelete
              }&${systemName}`
            )
            : await this.$axios.post(`${this.routeName}/change-status`, data);

        if (response?.status === 200 && response?.data?.result) {
          this.$toasterNA('green', this.$tr("successfully", this.$tr(this.selectedStatus)));
          this.selectedStatus = "";
          this.selectedItemsDelete = [];
          this.selectAll = false;
          this.$emit("fetchNewData");
        } else {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        if (error?.response?.status === 307 && !error?.response?.data?.result) {
          this.$toasterNA("red", this.$tr("user_has_no_permission"));
          this.$emit("askToAddPermissions", this.selectedItems[0].id);
          return;
        }
        if (
          (error?.response?.status === 406 ||
            error?.response?.status === 403 ||
            error?.response?.status === 404) &&
          !error?.response?.data?.result
        ) {
          error?.response?.status === 406
            ? this.$toasterNA("red", this.$tr("not_allowed_to_change_status"))
            : error?.response?.status === 403
              ? this.$toasterNA("red", this.$tr("employee_task_not_done"))
              : this.$toasterNA("red", this.$tr("no_employee_has_been_assigned"))
        } else {
          HandleException.handleApiException(this, error);
        }
      }
    },

    onTracing() {
      if (this.selectedItems[0].tracing_status && this.tabKey === "active") {
        this.$toasterNA("orange", this.$tr("user_is_already_in_tracing_status"))
        return;
      }
      if (this.tabKey === "active" || this.tabKey === "tracing") {
        if (this.selectedItems?.length != 1) {
          this.$toasterNA("primary", this.$tr("view_item_max_limit_text"));
          return false;
        } else {
          this.selectedStatus =
            this.tabKey === "active" ? "tracing" : "untracing";
          const status = this.tabKey === "active" ? 1 : 0;
          this.$TalkhAlertNA(
            this.tabKey === "tracing" ? "Do you want to untrace the events?" : "Do you want to trace the events?", //text
              this.tabKey === "tracing" ? "untracing" : "tracing", //icon
              async () => {
                this.toggleBooleanStatus("tracing_status", status);
              }, // callback function
              this.tabKey === "tracing" ? "yes_untrace" : "yes_trace", // btn text
            );
        }
      } else {
        this.$toasterNA('orange', this.$tr('you_can_toggle_tracing_from_active_and_tracing_tab'))
        return;
      }
    },
    // do view  action
    onView() {
      if (this.selectedItems?.length < 1) {
        this.$toasterNA("orange", this.$tr("view_item_max_limit_text"));
        return false;
      }
      this.$emit("onView");
    },
    //on approve
    onApprove() {
      if (["storyboard review", "design review"].includes(this.tabKey)) {
        if (this.canDoOperation()) this.$emit("onApprove");
      } else {
        this.$toasterNA("red", this.$tr("cant_do_operation_on_current_tab"));
      }
    },

    onAssign() {
      if (
        [
          "all",
          "storyboard rejected",
          "waiting",
          "design rejected",
          "cancelled",
          "removed",
          "completed",
        ].includes(this.tabKey)
      ) {
        this.$toasterNA("red", this.$tr("cant_do_operation_on_current_tab"));
        return;
      }
      if (this.selectedItems?.length > 1) {
        this.$toasterNA("orange", this.$tr("item_max_limit_text"));
        return false;
      }
      this.$emit("onAssign");
    },

    onSubmit() {
      this.$v.$touch();
      if (!this.$v.reason_id.$invalid || !this.$v.selectedReasons.$invalid) {
        this.reasonSubmit();
        this.reason_id = "";
        this.selectedReasons = [];
        this.$v.$reset();
      }
    },

    closeDialog() {
      this.$v.$reset();
      this.selectedReasons = [];
      this.selectedStatus = "";
      this.itemIds = [];
      this.isFetchingReasons = false;
      this.showReasonDialog = false;
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
      if (this.selectedItems[0]?.is_pinned && this.tabKey == "published") {
        this.$toasterNA("red", this.$tr("job_is_already_pinned"))
        return 0;
      }
      if (this.tabKey === "published" || this.tabKey === "pinned") {
        if (this.selectedItems?.length != 1) {
        this.$toasterNA("orange", this.$tr("item_max_limit_text"));

          return 0;
        } else {
          this.selectedStatus =
            this.tabKey === "pinned" ? "unpinned" : "pinned";
          const status = this.tabKey === "published" ? 1 : 0;
          this.$TalkhAlertNA(
              this.tabKey === "pinned" ? 'Do you want to unpinned it?' : "Do you want to pin it?", //text
              this.tabKey === "pinned" ? "unpin" : "pin", //icon
              async () => {
                this.toggleBooleanStatus("is_pinned", status);
              }, // callback function
              this.tabKey === "pinned" ? "yes_unpin" : "yes_pin", // btn text
            );
        }
      } else {
        this.$toasterNA('orange',this.$tr('you_can_only_toggle_pin_status_from_published_and_pinned_tab'));
        return;
      }
    },

    // do apply action
    onApply() {
      if (this.isLanguage) {
        if (this.tabKey === "active" && this.getTotals("active") <= 1) {
          this.$toasterNA("orange", this.$tr("at_least_one_langauge_should_be_active"));
          return;
        }
      }

      if (this.canDoOperation()) {
        if (this.selectedStatus?.length < 1) {
          this.$toasterNA("orange" , this.$tr('status_not_selected_please_status_first'))
          return;
        }
        if (this.tabKey === this.selectedStatus) {
          this.$toasterNA("red", this.$tr("record_is_already_in_desired_status"))
        } else {
          let isNoReason = false;
          if (this.routeName.includes("design")) {
            const noReasons = [
              "in storyboard",
              "storyboard review",
              "in design",
              "design review",
              "in programming",
              "completed",
            ];
            isNoReason = noReasons.includes(this.selectedStatus);
           
          }
          console.log();
          if (this.tabKey === "pending" || isNoReason ) {
            if (this.AssignInCompany && this.selectedStatus == 'active') {
              this.$refs.AssignUserRef.toggleDialog(this.selectedItems[0])
            }
            else{
              this.$TalkhAlertNA(
              "Are you sure to change status of this record?",
              "changeStatus",
            async () => this.noReasonSubmit()
            );
            }
            
          } else {
            this.fetchAllReasons();
          }
        }
      }
    },

    // do remove action
    onDelete() {
      if (this.subSystemName == "History") {
        this.$emit("onDelete");
        return;
      }
      if(this.tabKey=='all' && this.routeName ==`reasons`){
        this.$emit("onDelete");
        return;
      }
      if (this.isLanguage) {
        if (this.tabKey === "active" && this.getTotals("active") <= 1) {
          this.$toasterNA("orange", this.$tr("at_least_one_langauge_should_be_active"));
          return;
        }
      }

      this.selectedStatus = "removed";
      this.itemIds = this.selectedItems.map((item) => item?.id);
      if (this.canDoOperation()) {
        if (this.tabKey === "deleted" || this.tabKey === "removed") {
          this.$TalkhAlertNA(
              "Are you sure?",
              "delete",
              async () => {
                if (
                    this.routeName == "companies" ||
                    this.routeName == "departments" ||
                    this.routeName == "teams" ||
                    this.routeName == "roles"
                  ) {
                    this.showDeleteItems = true;
                  } else {
                    this.noReasonSubmit();
                  }
              },
              "delete",
              "swal_remove_text"
            );
        } else {
          if (this.routeName == "labels") {
            this.showDeleteItems = true;
          } else {
            this.$TalkhAlertNA(
              "Are you sure?",
              "delete",
              async () => {
                this.tabKey === "deleted" ||
                      this.tabKey === "removed" ||
                      this.tabKey === "pending"
                      ? this.noReasonSubmit()
                      : this.fetchAllReasons();
              },
              "delete",
            );
          }
        }
      }
    },
    selectItemsDelete() {
      this.showDeleteItems = false;
      if (this.routeName == "labels") {
        this.fetchAllReasons();
      } else {
        this.noReasonSubmit();
      }
    },

    selectAllItems() {
      if (this.selectAll == true) {
        this.selectedItemsDelete = this.deleteItems.map((item) => {
          return item.name;
        });
      } else {
        this.selectedItemsDelete = [];
      }
    },

    itemChange() {
      if (this.selectAll == true) {
        this.selectAll = false;
      }
    },

    closeDeleteItemsDialog() {
      this.showDeleteItems = false;
    },

    // do edit action
    onEdit() {
      if (this.tabKey === "all" && this.defaultAction) {
        this.$toasterNA("orange", this.$tr("cant_do_operation_on_all_tab"));
        return;
      }
      if (this.selectedItems?.length > 1) {
        this.$toasterNA("orange", this.$tr("edit_item_max_limit_text"));
        return;
      }
      this.$emit("onEdit");
    },

    // do auto edit action
    onAutoEdit() {
      if (this.selectedItems?.length < 1) {
        this.$toasterNA("orange", this.$tr("auto_edit_item_max_limit_text"));

        return false;
      }
      if (this.selectedItems?.length > 10) {
        this.$toasterNA("orange", this.$tr("unable_to_auto_edit_more_than_ten_items_at_the_same_time"));
        return;
      }
      if (this.tabKey === "all" && this.defaultAction) {
        this.$toasterNA("orange", this.$tr("cant_do_operation_on_all_tab"));
        return;
      }
      this.$emit("onAutoEdit");
    },

    // do block action
    onBlock() {
      if (this.isLanguage) {
        if (this.tabKey === "active" && this.getTotals("active") <= 1) {
        this.$toasterNA("orange", this.$tr("at_least_one_langauge_should_be_active"));
          return;
        }
      }

      this.selectedStatus = "blocked";
      if (this.tabKey === "blocked") {
        this.$toasterNA("red", this.$tr("item_is_already_blocked"))
        return;
      } else {
        if (this.canDoOperation()) {
            this.$TalkhAlertNA(
              "Do you want to block the record?",
              "block",
              async () => {
                this.tabKey === "pending"
                ? this.noReasonSubmit()
                : this.fetchAllReasons();
              },
              "yes_block",
            );
        }
      }
    },

    canDoOperation() {
      if (this.isLog) {
        return true;
      }
      if (this.tabKey === "tracing") {
        this.$toasterNA('orange' , this.$tr('cant_change_status_in_tracing_tab'))
        return false;
      }
      return this.defaultAction
        ? this.defaultOperation()
        : this.customOperation();
    },

    defaultOperation() {
      if (this.selectedItems?.length < 1) {
        this.$toasterNA("orange", this.$tr("item_max_limit_text"));
        return false;
      }
      if (this.tabKey === "all") {
        this.$toasterNA("orange", this.$tr("cant_do_operation_on_all_tab"));
        return false;
      }

      if (
        this.selectedItems?.length > 1 &&
        (this.tabKey !== "deleted" || this.tabKey !== "removed")
      ) {
        this.$toasterNA("orange", this.$tr("item_max_limit_text"));
        return false;
      }
      return true;
    },

    customOperation() {
      if (this.selectedItems?.length < 1) {
        this.$toasterNA("orange", this.$tr("item_max_limit_text"));
        return false;
      }
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
