<template>
  <v-container fluid class="pa-0">
    <v-dialog v-model="showNoteDialog" width="1300" persistent>
      <LandingNoteView
        :title="note_title"
        :note="landing_note"
        @closeDialog="showNoteDialog = false"
      />
    </v-dialog>

    <!-- filter dilaog -->
    <v-dialog
      v-if="$isInScope('drov')"
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
        :page_name="'history_CMS_all'"
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

    <HistoryProfile ref="designRequestHistory" :profileData="profileData" />

    <!-- End DesignRequest Profile Section -->

    <v-row no-gutters>
      <ProgressBar v-if="showProgressBar" />
      <v-col cols="12">
        <PageHeader
          :Icon="`history`"
          Title="history"
          :Breadcrumb="breadcrumb"
        />
      </v-col>

      <v-col cols="12">
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="companies"
          :downloadDialog="downloadDialog"
          :filename="'design-request-histories'"
          @onDownload="
            () => {
              downloadDialog = !downloadDialog;
            }
          "
          @onFilter="onFilter"
          @onColumn="columnDialog = true"
          :showBulkUpload="false"
          :showDownload="$isInScope('droc')"
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
          :showDelete="$isInScope('drod')"
          :showSelect="false"
          :showApply="false"
          :showAssign="false"
          :selectedItems.sync="selectedItems"
          :tab-key.sync="tabItems[tabIndex].key"
          :showBlock="false"
          @onView="onView"
          :statusItems="statusItems"
          :routeName="'design-request-histories'"
          :subSystemName="'History'"
          @fetchNewData="fetchDataAccordingtoStatus"
          @onDelete="confirmDelation()"
        />
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
                    {{ $tr(item.text) }}
                  </p>
                  <v-chip
                    :key="tabChipKey"
                    class="ms-1 tab-chip"
                    :color="item.isSelected ? 'primary' : 'white'"
                    :text-color="item.isSelected ? 'white' : 'primary'"
                    small
                    v-text="getTotals(item.key)"
                  />
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
              @click="toggleDrawer(item)"
              class="mx-1 primary--text font-weight-bold"
            >
              {{ item.code }}
            </span>
          </template>
          <template v-slot:[`item.company`]="{ item }">
            {{ item.company ? item.company.name : "" }}
          </template>
          <template v-slot:[`item.total_time_spent`]="{ item }">
            <div
              v-for="total_time in total_times_spent"
              :key="total_time.index"
            >
              <span v-if="total_time.code == item.code">
                {{ total_time.total_time }}
              </span>
            </div>
          </template>
          <template v-slot:[`item.updatedBy`]="{ item }">
            {{
              item.updatedBy
                ? item.updatedBy.firstname + item.updatedBy.lastname
                : ""
            }}
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

          <template v-slot:[`item.order_type`]="{ item }">
            <div
              v-if="item.order_type"
              class="custompriority text-capitalize"
              :style="
                item.order_type == 'copy'
                  ? 'background:#00B411BF'
                  : item.order_type == 'scratch'
                  ? 'background:#00B41180'
                  : item.order_type == 'mix'
                  ? 'background:#00B411'
                  : ''
              "
            >
              {{ $tr(item.order_type) }}
            </div>
          </template>

          <template v-slot:[`item.sales_note`]="{ item }">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  <span class="mx-auto d-flex justify-center">
                    <img
                      width="30px"
                      src="/icons/LANDING_NOTE_ICONS/sales-icon.svg"
                      alt="icon"
                      @click="viewNote(item.sales_note, 'sales_note')"
                    />
                  </span>
                </span>
              </template>
              {{ $tr("view_note") }}
            </v-tooltip>
          </template>
          <template v-slot:[`item.design_note`]="{ item }">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  <span class="d-flex justify-center mx-auto">
                    <img
                      width="30px"
                      src="/icons/LANDING_NOTE_ICONS/design-icon.svg"
                      alt="icon"
                      @click="viewNote(item.design_note, 'design_note')"
                    />
                  </span>
                </span>
              </template>
              {{ $tr("view_note") }}
            </v-tooltip>
          </template>
          <template v-slot:[`item.storyboard_note`]="{ item }">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  <span class="mx-auto d-flex justify-center">
                    <img
                      width="30px"
                      src="/icons/LANDING_NOTE_ICONS/storyboard-icon.svg"
                      alt="icon"
                      @click="viewNote(item.storyboard_note, 'storyboard_note')"
                    />
                  </span>
                </span>
              </template>
              {{ $tr("view_note") }}
            </v-tooltip>
          </template>

          <template v-slot:[`item.landing_page_link`]="{ item }">
            <div
              class="info status rounded-lg text-center mx-auto"
              style="width: 100px"
            >
              <a
                :href="item.landing_page_link"
                target="blank"
                class="white--text ma-0 pa-0"
                style="cursor: pointer"
              >
                <span>{{ $tr("link") }}</span>
                <v-icon class="ms-1" color="white">mdi-link</v-icon>
              </a>
            </div>
          </template>

          <!----------    DATATABLE Percentage SECTION        ---------->
          <template v-slot:[`item.percentage`]="{ item }">
            <v-avatar color="primary" size="30" class="text-center">
              <span
                class="white--text"
                style="font-size: 10px; text-align: center; line-height: 2px"
              >
                {{ item.task_prograss }}%</span
              >
            </v-avatar>
          </template>

          <template v-slot:[`item.design_link`]="{ item }">
            <div
              class="info status rounded-lg text-center mx-auto"
              style="width: 100px"
            >
              <a
                :href="item.design_link"
                target="blank"
                class="white--text ma-0 pa-0"
                style="cursor: pointer"
              >
                <span>{{ $tr("link") }}</span>
                <v-icon class="ms-1" color="white">mdi-link</v-icon>
              </a>
            </div>
          </template>
          <template v-slot:[`item.template`]="{ item }">
            <div v-if="item.template">
              {{ item.template.name }}
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
import { mapActions, mapGetters } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import PageActions from "~/components/design/PageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import CompanyProfile from "~/components/masters/CompanyProfile.vue";
import RegisterDesignRequest from "~/components/landing/RegisterDesignRequest";
import AssignUserStepper from "~/components/assign/AssignUserStepper";
import design_request_history from "~/configs/pages/design-request-history";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import CompanyBulkUpload from "~/components/companies/CompanyBulkUpload";
import ColumnHelper from "~/helpers/column-helper";
import DesignRequestFilter from "~/components/landing/DesignRequestFilter.vue";
import HistoryProfile from "~/components/landing/HistoryProfile.vue";
import LandingNoteView from "~/components/landing/Orders/LandingNoteView.vue";
import RejectionReview from "~/components/landing/Orders/RejectionReview.vue";
import Alert from "../../helpers/alert";

export default {
  meta: {
    hasAuth: true,
    scope: "drov",
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
    RegisterDesignRequest,
    AssignUserStepper,
    DesignRequestFilter,
    HistoryProfile,
    LandingNoteView,
    RejectionReview,
  },
  data() {
    return {
      showRejectionDialog: false,
      tabChipKey: 0,
      landing_note: "",
      note_title: "",
      showNoteDialog: false,
      design_request_data: [],

      tabItems: design_request_history(this).tabItems,
      breadcrumb: design_request_history(this).breadcrumb,
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
      note_title: "",
      isEdit: false,
      isAutoEdit: false,
      DesignRequestEditKey: 0,
      editData: {},
      bulkUploadDialog: false,
      assignUser: false,
      // filter
      requestFilterDialog: false,
    };
  },
  watch: {
    tabIndex: function () {
      this.fetchDataAccordingtoStatus();
    },
  },

  async created() {
    await this.fetchHeaders();
    this.fetchDataAccordingtoStatus();
    await this.$root.$on("getData", (data) => {
      this.filterData = data;
    });
    this.$root.$on("closeEdit", this.toggleEdit);
    //customize columns
  },
  mounted: function () {
    this.$echo.private(`update.design-request`).listen("Updated", async (e) => {
      if (e.action == "created") {
        if (this.getTabKey() == "waiting" || this.getTabKey() == "all") {
          this.design_request_data.unshift(e.data);
        }
        this.extraData.waitingTotal = this.extraData.waitingTotal + 1;
        this.extraData.allTotal = this.extraData.allTotal + 1;
      } else if (e.action == "updated") {
        this.design_request_data = this.design_request_data?.map((item) => {
          if (item?.id === e.data?.id) {
            return e.data;
          }
          return item;
        });
      } else if (e.action == "status-changed") {
        if (this.getTabKey() == e.data.current_status) {
          this.design_request_data = this.design_request_data.filter((item) => {
            if (item.id !== e.data.item.id) {
              return item;
            }
          });
        } else if (this.getTabKey() == e.data.new_status) {
          this.design_request_data.unshift(e.data.item);
        } else {
          this.design_request_data = this.design_request_data.map((item) => {
            if (item.id == e.data.item.id) {
              return {
                ...item,
                status: e.data.new_status,
              };
            }
            return item;
          });
        }
        await this.fetchNewRealtime();
        this.tabChipKey++;
      } else if (e.action == "deleted") {
        if (this.getTabKey() == "removed") {
          this.design_request_data = this.design_request_data.filter((item) => {
            if (item.id !== e.data) {
              return item;
            }
          });
        }
        this.extraData.removedTotal = this.extraData.removedTotal - 1;
        this.extraData.allTotal = this.extraData.allTotal - 1;
      }
    });
  },
  beforeDestroy() {
    this.$echo.leave("update.design-request");
  },
  beforeMount() {},
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
              tab_name: "history_CMS_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    viewNote(note, title) {
      this.landing_note = note;
      this.note_title = title;
      this.showNoteDialog = true;
      // this.note=this.note;
    },

    toggleDrawer(item) {
      this.$refs.designRequestHistory.toggleDrawer(item);
      this.profileData = item;
    },

    async fetchDataAccordingtoStatus() {
      this.apiCalling = true;
      this.selectedItems = [];
      this.checkSelectedTab(this.tabIndex);
      const data = this.fetchOptions();
      const response = await this.$axios.get(`design-request-histories`, {
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
      let currentTab = "";
      this.tabItems = this.tabItems.map((item, index) => {
        index === value
          ? ((item.isSelected = 1), (currentTab = item))
          : (item.isSelected = 0);
        return item;
      });
    },

    getTabKey() {
      return this.tabItems[this.tabIndex].key;
    },

    onView() {
      this.toggleDrawer(this.selectedItems[0]);
      this.profileData = this.selectedItems[0];
    },

    async viewSelectedCompany(item) {
      this.profileData = item;
      this.showProfile = true;
    },

    toggleEdit() {
      this.editKey++;
      this.isEdit = !this.isEdit;
    },
    // fired on edit button clicked to edit candidate
    async onEdit() {
      // *HM*    PLEASE DON'T REMOVE THIS LINE *HM*
      // Design Request does not have EDIT and AUTO-EDIT
      return;
      if (this.selectedItems.length > 0) {
        this.editData = this.selectedItems;
        this.isEdit = true;
        this.editKey++;
      }
    },
    // fired on auto edit button clicked to edit multiple candidate
    async onAutoEdit() {
      return;
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
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },
    onFilter() {
      this.requestFilterDialog = !this.requestFilterDialog;
    },

    getTotals(tabKey) {
      switch (tabKey) {
        case "waiting":
          return this.extraData?.waitingTotal || 0;
        case "in storyboard":
          return this.extraData?.instorybordTotal || 0;
        case "storyboard review":
          return this.extraData?.storybordreviewTotal || 0;
        case "storyboard rejected":
          return this.extraData?.storyboardRejectTotal || 0;
        case "in design":
          return this.extraData?.indesignTotal || 0;
        case "design review":
          return this.extraData?.designReviewTotal || 0;
        case "all":
          return this.extraData?.allTotal || 0;
        case "design rejected":
          return this.extraData?.designRejectedTotal || 0;
        case "in programming":
          return this.extraData?.inprogrammingTotal || 0;
        case "cancelled":
          return this.extraData?.cancelledTotal || 0;
        case "completed":
          return this.extraData?.completedTotal || 0;
        case "removed":
          return this.extraData?.removedTotal || 0;
      }
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
          `design_request_histories_search`,
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
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
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
    // ask for confirmation
    confirmDelation() {
      if (this.getTabKey() == "all") {
        this.$toasterNA("red", this.$tr("cant_do_operation_on_all_tab"));
        return;
      }

      Alert.removeDialogAlert(
        this,
        () => {
          this.deleteHistory();
        },
        "swal_remove_text"
      );
    },

    async deleteHistory() {
      try {
        this.itemIds = this.selectedItems.map((item) => item?.id);
        const response = await this.$axios.delete(
          `design-request-histories/${this.itemIds}?tab_key=${this.getTabKey()}`
        );
        if (response?.status === 200 && response?.data?.status) {
          Alert.successAlert(this, this.$tr("successfully", "deleted"));
          this.fetchDataAccordingtoStatus();
        } else {
          this.$toasterNA("red", this.$tr('something_went_wrong'));
        }
      } catch (error) {}
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
