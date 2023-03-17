<template>
  <v-container fluid class="pa-0">
    <v-dialog v-model="reviewDialog" persistent width="1300" class="rounded-0">
      <DesignRequestAutoReview
        :request_data="selectedItems"
        @closeDialog="reviewDialog = false"
        :key="reviewDialogKey"
      />
    </v-dialog>
    <!-- task prograssive dialog -->
    <v-dialog
      v-model="TaskPrograssiveDialog"
      persistent
      max-width="900"
      width="700"
      :key="dialogKey"
    >
      <TaskPrograssive
        @close="TaskPrograssiveDialog = !TaskPrograssiveDialog"
        :profileData="profileData"
        @fetch="fetchDataAccordingtoStatus"
      />
    </v-dialog>

    <!-- filter dilaog -->
    <v-dialog
      v-if="$isInScope('drv')"
      v-model="requestFilterDialog"
      persistent
      max-width="1300"
      width="1300"
    >
      <DesignRequestFilter
        @close="requestFilterDialog = false"
        @filterRecords="onFilterRecords"
      />
    </v-dialog>
    <!-- end -->
    <v-dialog v-model="columnDialog" width="1300" persistent>
      <CustomizeColumn
        @saveChanges="
          (data) => {
            all_headers[0] = data;
            all_headers = JSON.parse(JSON.stringify({ ...all_headers }));
            columnDialog = false;
          }
        "
        :page_headers.sync="all_headers[0]"
        :page_name="'my_orders_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <v-dialog
      v-model="isEdit"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('drou')"
    >
      <DesignRequestEdit
        v-if="isEdit"
        :editData="editData"
        :edit-dialog.sync="isEdit"
        :is-auto-edit.sync="isAutoEdit"
        @fetchNewData="fetchDataAccordingtoStatus()"
      />
    </v-dialog>
    <ProgressBar v-if="showProgressBar" />

    <!--DesignRequest Profile Section -->

    <Profile
      :profileData.sync="profileData"
      @fetchNewData="fetchDataAccordingtoStatus"
      :hasDrawer="true"
      ref="desingRequestProfile"
    >
      <v-icon size="20" color="white" @click="onEdit(), toggleDrawerEdit()"
        >mdi-pencil</v-icon
      >
    </Profile>

    <v-row no-gutters>
      <ProgressBar v-if="showProgressBar" />
      <v-col cols="12">
        <PageHeader
          :Icon="`my_orders`"
          Title="my_orders"
          :Breadcrumb="breadcrumb"
        />
      </v-col>

      <v-col cols="12">
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="design_request_data"
          :downloadDialog="downloadDialog"
          :filename="'My Orders'"
          @onDownload="
            () => {
              downloadDialog = !downloadDialog;
            }
          "
          @onFilter="onFilter"
          @onColumn="columnDialog = true"
          note-title="buttons.add_project_note"
          :showBulkUpload="false"
          :showDownload="$isInScope('dre')"
          @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
        >
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :showView="$isInScope('drov')"
          :showEdit="false"
          :showAutoEdit="false"
          :showDelete="false"
          :showSelect="false"
          :showApply="false"
          :showAssign="false"
          :showApprove="$isInScope(['droisbr', 'droidr'])"
          :selectedItems.sync="selectedItems"
          :tab-key.sync="tabItems[tabIndex].key"
          :showBlock="false"
          @onAssign="onAssign"
          @onApprove="showAutoReviewDialog"
          @onView="onView"
          @onAutoEdit="onAutoEdit"
          @onEdit="onEdit"
          :statusItems="statusItems"
          :routeName="'order_requests'"
          :subSystemName="'Design Request'"
          @fetchNewData="fetchDataAccordingtoStatus"
        >
          <CustomButton
            v-if="$isInScope(['droisb', 'droid', 'droip'])"
            icon="fa-cog"
            text="prograssive"
            type="primary"
            @click="taskPrograss"
            :class="`${selectedItems.length > 0 ? '' : 'customDisabled'}`"
          />
        </PageActions>
      </v-col>
      <v-col cols="12">
        <v-row class="mx-0">
          <v-col cols="12 pa-0">
            <v-tabs
              v-model="tabIndex"
              height="40"
              background-color="primary"
              active-class="active-background"
              show-arrows
              dark
              center-active
            >
              <client-only>
                <v-tab v-for="(item, i) in tabItems" :key="i" class="px-3">
                  <template>
                    <span
                      :class="`${
                        item.isSelected ? 'selected' : 'not-selected'
                      } tab-icon`"
                    >
                      <v-icon left small class="me-1">{{ item.icon }}</v-icon>
                    </span>
                    <p
                      :class="`${
                        item.isSelected ? 'selected' : 'not-selected'
                      } tab-title text-capitalize mb-0`"
                    >
                      <!-- {{$isInScope("drisb")}} -->
                      {{ $tr(item.text) }}
                    </p>
                    <v-chip
                      class="ms-1 tab-chip"
                      :color="item.isSelected ? 'primary' : 'white'"
                      :text-color="item.isSelected ? 'white' : 'primary'"
                      small
                      :key="item.key"
                      v-text="getTotals(item.key)"
                    />
                  </template>
                </v-tab>
              </client-only>
            </v-tabs>
          </v-col>
        </v-row>
        <DataTable
          ref="projectTableRef"
          :headers="all_headers[0].selected_headers"
          :items="design_request_data"
          :ItemsLength="getTotals(tabItems[tabIndex].key)"
          v-model="selectedItems"
          :loading="apiCalling"
          @onTablePaginate="onTableChanges"
          @click:row="onRowClicked"
        >
          <template v-slot:[`item.code`]="{ item }">
            <span
              @click="viewOrderItem(item)"
              class="mx-1 primary--text font-weight-bold"
            >
              {{ item.code }}
            </span>
          </template>
          <template v-slot:[`item.company`]="{ item }">
            {{ item.company ? item.company.name : "" }}
          </template>
          <template v-slot:[`item.total_time_spent`]="{ item }">
            <div v-if="item.status != 'removed' || item.status != 'cancelled'">
              <div
                v-for="total_time in total_times_spent"
                :key="total_time.index"
              >
                <span v-if="total_time.code == item.code">
                  {{ total_time.total_time }}
                </span>
              </div>
            </div>
          </template>

          <!----------    DATATABLE Percentage SECTION        ---------->
          <template v-slot:[`item.percentage`]="{ item }">
            <v-avatar color="primary" size="30" class="text-center">
              <span
                class="white--text"
                style="font-size: 10px; text-align: center; line-height: 2px"
              >
                {{ item.percentage }}%</span
              >
            </v-avatar>
          </template>

          <template v-slot:[`item.updatedBy`]="{ item }">
            {{ item.user ? item.user.firstname + item.user.lastname : "" }}
          </template>

          <template v-slot:[`item.order_type`]="{ item }">
            <div
              v-if="item.design_request_order"
              class="custompriority text-capitalize"
              :style="
                item.design_request_order.order_type == 'copy'
                  ? 'background:#00B411BF'
                  : item.design_request_order.order_type == 'scratch'
                  ? 'background:#00B41180'
                  : item.design_request_order.order_type == 'mix'
                  ? 'background:#00B411'
                  : ''
              "
            >
              {{ item.design_request_order.order_type }}
            </div>
          </template>

          <template v-slot:[`item.priority`]="{ item }">
            <div
              class="custompriority"
              :style="
                item.priority == 'medium'
                  ? 'background:#00B411BF'
                  : item.priority == 'low'
                  ? 'background:#00B41180'
                  : item.priority == 'high'
                  ? 'background:#00B411'
                  : ''
              "
            >
              {{ item.priority }}
            </div>
          </template>

          <template v-slot:[`item.template`]="{ item }">
            <div v-if="item.template">
              {{ item.template.name }}
            </div>
          </template>
          <template v-slot:[`item.label`]="{ item }">
            <div v-if="item.label">
              {{ item.label.label }}
            </div>
          </template>

          <template v-slot:[`item.storyboard_reject_count`]="{ item }">
            <div
              class="
                red
                lighten-1
                rounded-lg
                d-flex
                justify-space-between
                text-center
                mx-auto
              "
              style="width: 100px; padding: 3px 8px"
            >
              <img
                width="25px"
                src="/icons/LANDING_REJECT_ICONS/storyboard.svg"
                alt="icon"
                class="ms-1"
                style=""
              />
              <v-avatar size="20" class="inner white red--text">{{
                item.storyboard_reject_count
              }}</v-avatar>
            </div>
          </template>

          <template v-slot:[`item.design_reject_count`]="{ item }">
            <div
              class="red lighten-1 rounded-lg d-flex justify-space-between px-2"
              style="width: 100px; padding: 3px"
            >
              <img
                width="20px"
                src="/icons/LANDING_REJECT_ICONS/design.svg"
                alt="icon"
              />
              <v-avatar size="20" class="inner white red--text">{{
                item.design_reject_count
              }}</v-avatar>
            </div>
          </template>
          <template v-slot:[`item.content_type`]="{ item }">
            <div
              class="text-center rounded-lg px-1"
              :style="
                item.type == 'landing page design'
                  ? 'background:#00B411BF'
                  : item.type == 'advertisement content'
                  ? 'background:#00B41180'
                  : item.type == 'translation'
                  ? 'background:#00B7C180'
                  : item.type == 'social media design'
                  ? 'background:#00B411'
                  : item.type == 'texts and writings'
                  ? 'background:#A4CA11'
                  : 'background:#CDB711'
              "
            >
              {{ item.type && $tr(item.type.replaceAll(" ", "_")) }}
            </div>
          </template>
        </DataTable>
      </v-col>
    </v-row>
    <br />
    <br />
    <br />
    <br />
    <br />
  </v-container>
</template>

<script>
import Alert from "~/helpers/alert";
import { mapActions, mapGetters } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import PageActions from "~/components/design/PageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import CompanyProfile from "~/components/masters/CompanyProfile.vue";
import AssignUserStepper from "~/components/assign/AssignUserStepper";
import order_design_request from "~/configs/pages/order_design_request";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import CompanyBulkUpload from "~/components/companies/CompanyBulkUpload";
import ColumnHelper from "~/helpers/column-helper";
import DesignRequestFilter from "../../../components/landing/DesignRequestFilter.vue";
import Profile from "../../../components/landing/profile";
import TaskPrograssive from "../../../components/landing/Orders/TaskPrograssive.vue";
import DesignRequestAutoReview from "../../../components/landing/DesignRequestAutoReview.vue";
import HandleException from "../../../helpers/handle-exception";

export default {
  meta: {
    hasAuth: true,
    scope: "drov",
  },
  asyncData({ params }) {
    let order = params?.slug?.split(",");
    return {
      order,
    };
  },
  components: {
    ProgressBar,
    PageActions,
    CustomButton,
    CompanyProfile,
    DesignRequestEdit,
    PageHeader,
    PageFilters,
    Dialog,
    DataTable,
    CustomizeColumn,
    CompanyBulkUpload,
    AssignUserStepper,
    DesignRequestFilter,
    Profile,
    TaskPrograssive,
    DesignRequestAutoReview,
  },

  data() {
    return {
      reviewDialogKey: 0,
      reviewDialog: false,
      design_request_data: [],

      tabItems: order_design_request(this).tabItems,
      breadcrumb: order_design_request(this).breadcrumb,
      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      options: {},
      companyBulkUpload: false,
      //register section
      registerDialog: false,

      downloadDialog: false,
      statusItems: [],

      // profile section
      showProfile: false,

      // for custom columns
      selected_headers: [],
      columnDialog: false,
      all_headers: [
        {
          key: "all",
          selected_headers: [],
          headers: [],
        },
      ],
      dialogKey: 0,
      apiCalling: false,

      showProgressBar: false,
      autoEdit: false,
      editKey: 0,
      autoEditData: [],
      selectedItems: [],
      type: 1,
      content: [],
      contentData: "",
      total_times_spent: [],
      filterData: {},
      profileData: {},

      isEdit: false,
      isAutoEdit: false,
      DesignRequestEditKey: 0,
      editData: {},
      bulkUploadDialog: false,

      assignUser: false,
      // filter
      requestFilterDialog: false,

      //   taskPrograssive
      TaskPrograssiveDialog: false,
      limitedInterval: "",
    };
  },

  watch: {
    tabIndex: function () {
      this.fetchDataAccordingtoStatus();
    },
  },

  async created() {
    await this.fetchHeaders();
    if (this.order != undefined) {
      this.filterData.ids = [...this.order];
      this.filterData.include = 1;
    }
    this.fetchDataAccordingtoStatus();

    await this.$root.$on("getData", (data) => {
      this.filterData = data;
    });

    this.$root.$on("closeEdit", this.toggleEdit);
    //customize columns
  },
  mounted() {
    if (process.client) {
      this.limitedInterval = window.setInterval(() => {
        this.setTotalTimeSpent();
      }, 1000);
    }
    let new_tab_items = [];
    this.tabItems.forEach((item) => {
      if (this.$isInScope(this.prepareTabKey(item.key)) || item.key == "all") {
        new_tab_items.push(item);
      }
    });
    this.tabItems = new_tab_items;
  },
  beforeMount() {},
  beforeDestroy() {
    clearInterval(this.limitedInterval);
  },
  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      companies: "companies/getCompanies",
      getSystems: "systems/items",
    }),
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "my_orders_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    prepareTabKey(key) {
      switch (key) {
        case "in storyboard":
          return "droisb";
        case "storyboard review":
          return "droisbr";
        case "design review":
          return "droidr";
        case "in design":
          return "droid";
        case "in programming":
          return "droip";
      }
    },
    showAutoReviewDialog() {
      this.reviewDialogKey++;
      this.reviewDialog = true;
    },
    checkSpentTime(diff) {
      let total_time_spent = "";
      let total_year = "";
      let total_month = "";
      let total_day = "";
      let total_hour = "";
      let total_minute = "";

      total_year = Math.floor(diff / 31536000);
      if (total_year > 0) {
        diff -= total_year * 31536000;
        total_time_spent = total_time_spent.concat(total_year, "Y");
      }

      total_month = Math.floor(diff / (86400 * 30));
      if (total_month > 0) {
        diff -= total_month * (86400 * 30);
        total_time_spent = total_time_spent.concat(" ", total_month, "MO");
      }

      total_day = Math.floor(diff / 86400);
      if (total_day > 0) {
        diff -= total_day * 86400;
        total_time_spent = total_time_spent.concat(" ", total_day, "D");
      }

      total_hour = Math.floor(diff / 3600);
      if (total_hour > 0) {
        diff -= total_hour * 3600;
        total_time_spent = total_time_spent.concat(" ", total_hour, "H");
      }

      total_minute = Math.floor(diff / 60);
      if (total_minute > 0) {
        diff -= total_minute * 60;
        total_time_spent = total_time_spent.concat(" ", total_minute, "M");
      }
      if (diff > 0) {
        total_time_spent = total_time_spent.concat(" ", Math.round(diff), "S");
      }
      return total_time_spent;
    },
    setTotalTimeSpent() {
      var d = new Date();
      d.toString();
      this.total_times_spent = [];
      this.design_request_data.forEach((element) => {
        if (element?.status == "completed") {
          d = new Date(element?.completed_date + " UTC");
          d.toString();
        } else {
          d = new Date();
          d.toString();
        }
        let cancelledTotalSecond = 0;
        if (element.status_times?.length > 0) {
          element.status_times.forEach((e) => {
            if (e.status == "cancelled") {
              let cancelledDate = new Date(e.created_at + " UTC");
              cancelledDate.toString();
              if (e.end_date) {
                let endDate = new Date(e.end_date + " UTC");
                endDate.toString();
                let diff = Math.abs(
                  cancelledDate.getTime() / 1000 - endDate.getTime() / 1000
                );
                cancelledTotalSecond += diff;
              } else {
                d = new Date(e.created_at + " UTC");
                d.toString();
              }
            }
          });
        }
        let created_date = new Date(element.created_at);
        created_date.toString();
        if (element.status_times?.length > 0) {
          element.status_times.forEach((e) => {
            if (e.end_date == null) {
              created_date = new Date(e.created_at + " UTC");
              created_date.toString();
            }
          });
        }
        let total_time_spent = "";
        let diff = Math.abs(d.getTime() / 1000 - created_date.getTime() / 1000);
        diff = diff - cancelledTotalSecond;
        total_time_spent = this.checkSpentTime(diff);
        this.total_times_spent.push({
          code: element.code,
          diff: diff,
          total_time: total_time_spent,
        });
      });
    },
    async fetchDataAccordingtoStatus() {
      this.apiCalling = true;
      this.selectedItems = [];
      this.checkSelectedTab(this.tabIndex);
      const data = this.fetchOptions();
      const response = await this.$axios.get(`order_requests`, {
        params: data,
      });

      if (response?.status === 200) {
        this.design_request_data = response.data?.data;
        this.extraData = response?.data;
      }
      this.apiCalling = false;
    },

    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.fetchDataAccordingtoStatus();
      } else this.options = this._.clone(options);
    },

    checkSelectedTab(value) {
      this.tableKey++;
      this.selectedItems = [];
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },
    getTabKey() {
      return this.tabItems[this.tabIndex].key;
    },

    async onView() {
      this.profileData = this.selectedItems[0];
      this.$refs.desingRequestProfile.toggleDrawer();
    },
    toggleDrawerEdit() {
      this.$refs.desingRequestProfile.toggleDrawer();
    },
    async viewOrderItem(item) {
      if (this.isFetchingFiles) {
        return;
      }
      this.profileData = item;
      this.$refs.desingRequestProfile.toggleDrawer();
    },

    toggleEdit() {
      this.editKey++;
      this.isEdit = !this.isEdit;
    },
    // fired on edit button clicked to edit candidate
    async onEdit() {
      if (this.selectedItems.length > 0) {
        this.editData = this.selectedItems;
        this.isEdit = true;
        this.editKey++;
      }
    },

    // fired on auto edit button clicked to edit multiple candidate
    async onAutoEdit() {
      if (this.selectedItems.length > 0) {
        this.editData = this.selectedItems;
        this.isEdit = true;
        this.isAutoEdit = true;
        this.editKey++;
      }
    },

    onAssign() {
      this.assignUser = !this.assignUser;
    },

    // add or remove item from selected items
    onRowClicked(item) {
      switch (item.status) {
        case "waiting":
        case "storyboard rejected":
          this.statusItems = [
            {
              id: "in storyboard",
              name: this.$tr("in_storyboard"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        case "in storyboard":
          this.statusItems = [
            {
              id: "storyboard review",
              name: this.$tr("storyboard_review"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;

        case "storyboard review":
          this.statusItems = [
            {
              id: "storyboard rejected",
              name: this.$tr("storyboard_rejected"),
            },
            {
              id: "in design",
              name: this.$tr("in_design"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;

        case "design rejected":
          this.statusItems = [
            { id: "in design", name: this.$tr("in_design") },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        case "in design":
          this.statusItems = [
            {
              id: "design review",
              name: this.$tr("design_review"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        case "design review":
          this.statusItems = [
            {
              id: "design rejected",
              name: this.$tr("design_rejected"),
            },
            {
              id: "in programming",
              name: this.$tr("in_programming"),
            },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        case "in programming":
          this.statusItems = [
            { id: "completed", name: this.$tr("completed") },
            { id: "cancelled", name: this.$tr("cancel") },
          ];
          break;
        default:
          this.statusItems = [{ id: "cancelled", name: this.$tr("cancel") }];
          break;
      }
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    onFilter() {
      this.requestFilterDialog = !this.requestFilterDialog;
    },

    taskPrograss() {
      if (this.selectedItems.length === 1) {
        this.dialogKey++;
        this.profileData = this.selectedItems[0];
        this.TaskPrograssiveDialog = !this.TaskPrograssiveDialog;
      } else {
        this.$toasterNA("red", this.$tr("only_one_item_is_allowed_for_this_operation"));
      }
    },
    // getTotal: (state) => (tabKey) => {
    //   switch (tabKey) {
    //     case "removed":
    //       return state.extraData?.removedTotal || 0;
    //     case "inactive":
    //       return state.extraData?.inactiveTotal || 0;
    //     case "blocked":
    //       return state.extraData?.blockedTotal || 0;
    //     case "active":
    //       return state.extraData?.activeTotal || 0;
    //     case "pending":
    //       return state.extraData?.pendingTotal || 0;
    //     case "tracing":
    //       return state.extraData?.tracingTotal || 0;
    //     case "all":
    //       return state.extraData?.allTotal || 0;
    //     case "warning":
    //       return state.extraData?.warningTotal || 0;
    //     case "deleted":
    //       return state.extraData?.removedTotal || 0;
    //   }
    // },

    getTotals(tabKey) {
      let total = 0;
      switch (tabKey) {
        case "in storyboard":
          total = this.extraData?.instorybordTotal || 0;
          break;
        case "storyboard review":
          total = this.extraData?.storybordreviewTotal || 0;
          break;
        case "in design":
          total = this.extraData?.indesignTotal || 0;
          break;
        case "design review":
          total = this.extraData?.designReviewTotal || 0;
          break;
        case "all":
          total = this.extraData?.allTotal || 0;
          break;
        case "in programming":
          total = this.extraData?.inprogrammingTotal || 0;
          break;

        case "programming review":
          total = this.extraData?.programingReviewTotal || 0;
          break;
      }
      return total;
    },
    // search and filter functions
    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.fetchDataAccordingtoStatus();
    },
    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post(
          `order_requests_search`,
          options
        );
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    onSearchTypeChange() {
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },
    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.fetchDataAccordingtoStatus();
    },

    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter((row) =>
        !isNaN(code) ? row.code !== "LDR000" + code : row.code !== code
      );
    },
    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.design_request_data = this.design_request_data.filter(
            (item) => item.id !== data.id
          );
          this.design_request_data?.unshift(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },
    fetchOptions() {
      let data = {
        key: this.getTabKey(),
      };
      if (!this.$isObjectEmpty(this.filterData))
        Object.assign(data, this.filterData);

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;

      return data;
    },
  },
};
</script>

<style scoped>
.custompriority {
  max-width: 190px;
  border-radius: 20px;
  color: white;
  text-align: center;
  padding: 2px 6px !important;
}
</style>
