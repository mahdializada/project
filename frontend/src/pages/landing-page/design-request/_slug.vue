<template>
  <v-container fluid class="pa-0">
    <client-only>
      <v-dialog
        v-model="reviewDialog"
        persistent
        width="1300"
        class="rounded-0"
      >
        <DesignRequestAutoReview
          :request_data="selectedItems"
          @closeDialog="reviewDialog = false"
          :key="reviewDialogKey"
        />
      </v-dialog>
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
          :page_name="'design_request_all'"
          @closeColumnDialog="columnDialog = false"
        />
      </v-dialog>

      <v-dialog v-model="showRejectionDialog" width="1000">
        <rejection-review
          :requestDetails="requestDetails"
          :key="dialogKey"
          @closeDialog="showRejectionDialog = false"
        />
      </v-dialog>
      <v-dialog v-model="showNoteDialog" width="1300" persistent>
        <LandingNoteView
          :title="note_title"
          :note="landing_note"
          @closeDialog="showNoteDialog = false"
        />
      </v-dialog>

      <!-- Design Request Operation Section-->

      <DesignRequestOperation ref="designRequestOperationRef" />

      <v-dialog v-if="$isInScope('dra')" v-model="assignUser" width="1000">
        <Dialog
          persistent
          max-width="1000"
          @closeDialog="
            () => {
              selectedItems = [];
              assignUser = false;
            }
          "
        >
          <AssignUserStepper
            :selected="this.selectedItems"
            :tabKey="getTabKey()"
            v-if="assignUser"
            @close="assignUser = false"
            @fetchNewData="fetchDataAccordingtoStatus()"
          />
        </Dialog>
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

      <v-dialog
        v-model="isEdit"
        persistent
        width="1300"
        scrollable
        v-if="$isInScope('dru')"
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
        :profileData="profileData"
        @fetchNewData="fetchDataAccordingtoStatus"
        :hasDrawer="true"
        ref="desingRequestProfile"
      >
        <v-icon size="20" color="white" @click="onEdit(), toggleDrawerEdit()"
          >mdi-pencil</v-icon
        >
      </Profile>

      <!-- End DesignRequest Profile Section -->

      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`design_request`"
            Title="design_request"
            :Breadcrumb="breadcrumb"
          >
            <template slot="button">
              <CustomButton
                :icon="
                  this.showChart
                    ? 'mdi-chevron-triple-up'
                    : 'mdi-chevron-triple-down'
                "
                class="toggle-chart"
                text=""
                @click="showChart = !showChart"
              />
            </template>
          </PageHeader>
        </v-col>
        <v-expand-transition>
          <v-col cols="12" class="mb-2" v-if="showChart">
            <DesignRequestChart />
          </v-col>
        </v-expand-transition>

        <v-col cols="12">
          <PageFilters
            ref="pageFilterRef"
            :selected_header.sync="all_headers[0].selected_headers"
            :downloadContent="design_request_data"
            :downloadDialog="downloadDialog"
            :filename="'Design Request'"
            @onDownload="
              () => {
                downloadDialog = !downloadDialog;
              }
            "
            @searchById="searchById"
            @clearAndUnselectId="clearAndUnselectId"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            @onFilter="requestFilterDialog = true"
            @onColumn="columnDialog = true"
            note-title="buttons.add_project_note"
            :showBulkUpload="false"
            :showDownload="$isInScope('dre')"
          >
            <CustomButton
              v-if="$isInScope('drc')"
              icon="fa-plus"
              @click="$refs.designRequestOperationRef.openDialog()"
              :text="$tr('create_item', $tr('design_request'))"
              type="primary"
            />
          </PageFilters>
        </v-col>

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('drv')"
            :showEdit="false"
            :showAutoEdit="false"
            :showDelete="$isInScope('drd')"
            :showSelect="$isInScope('dru')"
            :showApply="$isInScope('dru')"
            :showAssign="$isInScope('dra')"
            :showApprove="$isInScope('draprj')"
            :showUnAssign="$isInScope('dra')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabItems[tabIndex].key"
            :showBlock="false"
            :showRestore="showRestore"
            @onUnAssign="onUnAssign"
            @onAssign="onAssign"
            @onView="onView"
            @onAutoEdit="onAutoEdit"
            @onEdit="onEdit"
            @onApprove="showAutoReviewDialog"
            :statusItems="statusItems"
            :routeName="'design-request'"
            :subSystemName="'Design Request'"
            :noReasonSubmitOperations="'in storyboard, storyboard review, in design, design review, in programming, completed'"
          />
        </v-col>
        <v-col cols="12">
          <!-- <v-row class="mx-0">
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
										<span :class="`${item.isSelected ? 'selected' : 'not-selected'} tab-icon`">
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
					</v-row> -->

          <TableTabs
            @getSelectedTabRecords="
              (key) => {
                tabKey = key;
                fetchDataAccordingtoStatus();
              }
            "
            :tabData="tabItems"
            :extraData="extraData"
          />

          <DataTable
            ref="projectTableRef"
            :headers="all_headers[0].selected_headers"
            :items="design_request_data"
            :ItemsLength="extraData.total"
            v-model="selectedItems"
            :loading="apiCalling"
            @onTablePaginate="onTableChanges"
            @click:row="onRowClicked"
            :key="tableKey"
          >
            <template v-slot:[`item.code`]="{ item }">
              <span
                @click="() => viewSelectedDesignRequest(item)"
                class="mx-1 primary--text font-weight-bold"
              >
                {{ item.code }}
              </span>
            </template>
            <template v-slot:[`item.company`]="{ item }">
              {{ item.company ? item.company.name : "" }}
            </template>
            <template v-slot:[`item.download_status`]="{ item }">
              <div
                v-if="item.download_status.status == 'not_downloaded'"
                class="text-center rounded-lg px-1"
                style="background: #00b411"
              >
                {{ $tr("not_downloaded") }}
              </div>
              <div
                v-else-if="item.download_status.status == 'downloaded'"
                class="text-center px-1"
              >
                <v-icon :title="$tr('downloaded')" color="success">
                  mdi-check-bold
                </v-icon>
                <v-avatar
                  :title="`${item.download_status.percentage}% ${$tr(
                    'downloaded'
                  )}`"
                  color="primary"
                  size="30"
                  class="ms-1 text-center"
                >
                  <span
                    class="white--text"
                    style="
                      font-size: 10px;
                      text-align: center;
                      line-height: 2px;
                    "
                  >
                    {{ item.download_status.percentage }}%</span
                  >
                </v-avatar>
              </div>

              <div
                v-else
                class="text-center rounded-lg px-1"
                style="background: #00b411bf"
              >
                {{ $tr("no_media") }}
              </div>
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
              {{ item.user ? item.user.firstname + item.user.lastname : "" }}
            </template>
            <template v-slot:[`item.assignedUser`]="{ item }">
              <div v-if="item.design_request_order != null" :key="customKey">
                <div
                  v-if="
                    item.design_request_order.design_request_order_user.length >
                    0
                  "
                >
                  <v-tooltip bottom color="white primary--text">
                    <template v-slot:activator="{ on, attrs }">
                      <span
                        v-for="(user, key) in item.design_request_order
                          .design_request_order_user"
                        :key="key"
                      >
                        <v-avatar
                          v-bind="attrs"
                          v-on="on"
                          v-if="key < 3"
                          color="white"
                          size="24"
                          class="ml-n1"
                          outlined
                        >
                          <img :src="user.user.image" alt="user" />
                        </v-avatar>
                      </span>
                    </template>
                    <span
                      v-for="(user, key) in item.design_request_order
                        .design_request_order_user"
                      :key="key"
                    >
                      <v-avatar size="20" outlined>
                        <img :src="user.user.image" alt="user" />
                      </v-avatar>
                      {{ user.user.firstname }} {{ user.user.lastname }} <br />
                    </span>
                  </v-tooltip>
                  <span
                    class="primary--text"
                    v-if="
                      item.design_request_order.design_request_order_user
                        .length > 3
                    "
                    >+{{
                      item.design_request_order.design_request_order_user
                        .length - 3
                    }}</span
                  >
                </div>
              </div>
              <div v-else class=""></div>
            </template>

            <template v-slot:[`item.order_type`]="{ item }">
              <div
                class="text-center text-capitalize rounded-lg px-1"
                :style="
                  getOrderType(item) == 'copy'
                    ? 'background:#00B411BF'
                    : getOrderType(item) == 'scratch'
                    ? 'background:#00B41180'
                    : getOrderType(item) == 'mix'
                    ? 'background:#00B411'
                    : ''
                "
              >
                {{ getOrderType(item) }}
              </div>
            </template>

            <template v-slot:[`item.priority`]="{ item }">
              <div
                class="text-center rounded-lg px-1"
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
                {{ $tr(item.priority) }}
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
                        @click="
                          viewNote(
                            item.design_request_order
                              ? item.design_request_order.sales_note
                              : '',
                            'sales_note'
                          )
                        "
                      />
                    </span>
                  </span>
                </template>
                {{ $tr("view") + " " + $tr("note") }}
              </v-tooltip>
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

            <template v-slot:[`item.design_note`]="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="d-flex justify-center mx-auto">
                      <img
                        v-bind="attrs"
                        width="30px"
                        src="/icons/LANDING_NOTE_ICONS/design-icon.svg"
                        alt="icon"
                        @click="
                          viewNote(
                            item.design_request_order
                              ? item.design_request_order.design_note
                              : '',
                            'design_note'
                          )
                        "
                      />
                    </span>
                  </span>
                </template>
                {{ $tr("view") + " " + $tr("note") }}
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
                        @click="
                          viewNote(
                            item.design_request_order
                              ? item.design_request_order.storyboard_note
                              : '',
                            'storyboard_note'
                          )
                        "
                      />
                    </span>
                  </span>
                </template>
                {{ $tr("view") + " " + $tr("note") }}
              </v-tooltip>
            </template>

            <template v-slot:[`item.landing_page_link`]="{ item }">
              <div
                v-if="item.type == 'landing page design'"
                class="info status rounded-lg text-center mx-auto"
                style="width: 100px"
              >
                <a
                  v-if="
                    item.design_request_order
                      ? item.design_request_order.landing_page_link != ''
                        ? true
                        : false
                      : false
                  "
                  :href="
                    item.design_request_order
                      ? item.design_request_order.landing_page_link
                      : ''
                  "
                  target="blank"
                  class="white--text ma-0 pa-0"
                  style="cursor: pointer"
                >
                  <span>{{ $tr("link") }}</span>
                  <v-icon class="ms-1" color="white">mdi-link</v-icon>
                </a>

                <span
                  v-else
                  @click="() =>$toasterNA('orange',this.$tr('sub_system_not_found'))"
                  class="white--text ma-0 pa-0"
                  style="cursor: pointer"
                >
                  <span>{{ $tr("link") }}</span>
                  <v-icon class="ms-1" color="white">mdi-link</v-icon>
                </span>
              </div>
            </template>
            <template v-slot:[`item.design_link`]="{ item }">
              <div
                class="info status rounded-lg text-center mx-auto"
                style="width: 100px"
              >
                <a
                  v-if="
                    item.design_request_order
                      ? item.design_request_order.design_link != ''
                        ? true
                        : false
                      : false
                  "
                  :href="
                    item.design_request_order
                      ? item.design_request_order.design_link
                      : ''
                  "
                  target="blank"
                  class="white--text ma-0 pa-0"
                  style="cursor: pointer"
                >
                  <span>{{ $tr("link") }}</span>
                  <v-icon class="ms-1" color="white">mdi-link</v-icon>
                </a>
                <span
                  v-else
                  @click="() => $toastr.w($tr('no_link_added'))"
                  class="white--text ma-0 pa-0"
                  style="cursor: pointer"
                >
                  <span>{{ $tr("link") }}</span>
                  <v-icon class="ms-1" color="white">mdi-link</v-icon>
                </span>
              </div>
            </template>
            <template v-slot:[`item.template`]="{ item }">
              {{ item.template && item.template.name }}
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
                @click="showRejectReason(item, 'storyboard rejected')"
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
                class="
                  red
                  lighten-1
                  rounded-lg
                  d-flex
                  justify-space-between
                  px-2
                "
                style="width: 100px; padding: 3px"
                @click="showRejectReason(item, 'design rejected')"
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
            <template v-slot:[`item.cancels`]="{ item }">
              <div
                class="
                  red
                  white--text
                  lighten-1
                  rounded-lg
                  d-flex
                  justify-center
                  px-1
                "
                style="width: 90px; padding: 1px"
                @click="showRejectReason(item, 'cancelled')"
              >
                {{ $tr("cancel") }}
              </div>
            </template>

            <template v-slot:[`item.label`]="{ item }">
              <div v-if="item.label">
                {{ item.label.label }}
              </div>
            </template>

            <template v-slot:[`item.requestType`]="{ item }">
              <div v-if="item.status == 'in storyboard'">
                <div
                  :class="`white--text rounded-lg d-flex justify-center px-1 ${
                    item.storyboard_reject_count > 0 ? 'primary' : ' success'
                  }`"
                  style="width: 90px; padding: 1px"
                >
                  {{
                    item.storyboard_reject_count > 0
                      ? $tr("repeate")
                      : $tr("new")
                  }}
                </div>
              </div>
              <div v-else-if="item.status == 'in design'">
                <div
                  :class="`white--text  rounded-lg justify-center d-flex px-1 ${
                    item.design_reject_count > 0 ? 'primary' : ' success'
                  }`"
                  style="width: 90px; padding: 1px"
                >
                  {{
                    item.design_reject_count > 0 ? $tr("repeate") : $tr("new")
                  }}
                </div>
              </div>
              <div v-else>
                <div
                  class="
                    white--text
                    rounded-lg
                    d-flex
                    justify-center
                    px-1
                    success
                  "
                  style="width: 90px; padding: 1px"
                >
                  {{ $tr("new") }}
                </div>
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
        <progress-bar v-if="isFetchCancels" />
      </v-row>
      <br />
      <br />
      <br />
      <br />
      <br />
    </client-only>
  </v-container>
</template>

<script>
import { mapActions, mapGetters, mapState } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import DesignRequestOperation from "~/components/landing/DesignRequestOperation";
import AssignUserStepper from "~/components/assign/AssignUserStepper";
import design_requests from "~/configs/pages/design_requests";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import CompanyBulkUpload from "~/components/companies/CompanyBulkUpload";
import ColumnHelper from "~/helpers/column-helper";
import DesignRequestFilter from "~/components/landing/DesignRequestFilter.vue";
import Profile from "~/components/landing/profile.vue";
import LandingNoteView from "~/components/landing/Orders/LandingNoteView.vue";
import RejectionReview from "~/components/landing/Orders/RejectionReview.vue";
import DesignRequestChart from "~/components/landing/DesignRequestChart.vue";
import DesignRequestAutoReview from "../../../components/landing/DesignRequestAutoReview.vue";
import HandleException from "../../../helpers/handle-exception";
import TableTabs from "~/components/common/TableTabs.vue";

export default {
  meta: {
    hasAuth: true,
    scope: "drv",
  },

  components: {
    CustomPageActions,
    CustomButton,
    DesignRequestEdit,
    PageHeader,
    PageFilters,
    Dialog,
    DataTable,
    CustomizeColumn,
    CompanyBulkUpload,
    DesignRequestOperation,
    AssignUserStepper,
    DesignRequestFilter,
    Profile,
    LandingNoteView,
    RejectionReview,
    DesignRequestChart,
    DesignRequestAutoReview,
    ProgressBar,
    TableTabs,
  },

  data() {
    return {
      customKey: 0,
      reviewDialog: false,
      reviewDialogKey: 0,
      showChart: false,
      showRejectionDialog: false,
      tabChipKey: 0,
      landing_note: "",
      note_title: "",
      showNoteDialog: false,
      design_request_data: [],

      tabItems: design_requests(this).tabItems,
      breadcrumb: design_requests(this).breadcrumb,
      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      options: {},
      showRestore: false,
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
      isFetchCancels: false,
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
      cancelReasons: [],
      requestDetails: {},
      filterDialog: false,
      dialogKey: 0,

      tabKey: "all",
      extraData: [],
    };
  },

  watch: {
    tabIndex: function () {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.fetchDataAccordingtoStatus();
    },

    "user.selectedCompanies"(_) {
      this.fetchDataAccordingtoStatus();
    },
  },
  asyncData({ params, query }) {
    return {
      request: params.slug,
      profileCode: query.profile,
    };
  },

  async created() {
    await this.fetchHeaders();
    if (this.request != undefined) {
      this.filterData.ids = [this.request];
      this.filterData.include = 1;
    }
    this.fetchDataAccordingtoStatus();
    await this.openParamsProfile();

    this.$root.$on("closeEdit", this.toggleEdit);
    //customize columns
  },
  mounted() {
    if (process.client) {
      this.limitedInterval = window.setInterval(() => {
        this.setTotalTimeSpent();
      }, 1000);
    }
    let allowedTabs = [
      "all",
      "assigned",
      "not assigned",
      "waiting",
      "storyboard rejected",
      "design rejected",
      "completed",
      "cancelled",
      "removed",
    ];
    let new_tab_items = [];
    this.tabItems.forEach((item) => {
      if (
        this.$isInScope(this.prepareTabKey(item.key)) ||
        allowedTabs.includes(item.key)
      ) {
        new_tab_items.push(item);
      }
    });
    this.tabItems = new_tab_items;

    this.$echo.private(`update.design-request`).listen("Updated", async (e) => {
      if (e.action == "created") {
        if (this.getTabKey() == "waiting" || this.getTabKey() == "all") {
          let res = await this.fetchDesignRequest(e.data);
          this.design_request_data.unshift(res);
        }
        this.extraData.waitingTotal = this.extraData.waitingTotal + 1;
        this.extraData.allTotal = this.extraData.allTotal + 1;
        this.extraData.notAssignedTotal = this.extraData.notAssignedTotal + 1;
        this.tableKey++;
      } else if (e.action == "updated") {
        let res = await this.fetchDesignRequest(e.data);
        this.design_request_data = this.design_request_data?.map((item) => {
          if (item?.id === res?.id) {
            return res;
          }
          return item;
        });
      } else if (e.action == "status-changed") {
        if (this.getTabKey() == e.data.current_status) {
          this.design_request_data = this.design_request_data.filter(
            (design_request) => {
              if (design_request.id !== e.data.item) {
                return design_request;
              }
            }
          );
          await this.fetchNewRealtime();
        } else if (this.getTabKey() == e.data.new_status) {
          let res = await this.fetchDesignRequest(e.data.item);
          this.design_request_data.unshift(res);
          await this.fetchNewRealtime(0);
        } else if (
          this.getTabKey() == "all" ||
          this.getTabKey() == "assigned" ||
          this.getTabKey() == "not_assigned"
        ) {
          this.design_request_data = this.design_request_data.map((item) => {
            if (item.id == e.data.item) {
              return {
                ...item,
                status: e.data.new_status,
              };
            }
            return item;
          });
          await this.fetchNewRealtime(0);
        } else {
          await this.fetchNewRealtime(0);
        }
        this.tabChipKey++;
      } else if (e.action == "deleted") {
        if (
          this.getTabKey() == "removed" ||
          this.getTabKey() == "all" ||
          this.getTabKey() == "not assigned"
        ) {
          this.design_request_data = this.design_request_data.filter((item) => {
            if (item.id !== e.data) {
              return item;
            }
          });
        }
        this.extraData.removedTotal = this.extraData.removedTotal - 1;
        this.extraData.allTotal = this.extraData.allTotal - 1;
        await this.fetchNewRealtime();
      } else if (e.action == "multiple-assignment") {
        let design_request_ids = e.data.design_request_ids;
        let res = await this.getDesignRequests(design_request_ids);
        this.design_request_data = this.design_request_data.map((item) => {
          for (let i = 0; i < res.length; i++) {
            if (item.id == res[i].id) {
              return res[i];
            }
          }
          return item;
        });
        await this.fetchNewRealtime();
        this.tabChipKey++;
        this.customKey++;
      }
    });
  },

  beforeDestroy() {
    this.$echo.leave("update.design-request");
    clearInterval(this.limitedInterval);
  },
  beforeMount() {},
  computed: {
    ...mapState("auth", ["user"]),
    ...mapGetters({
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
              tab_name: "design_request_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.fetchDataAccordingtoStatus();
    },
    prepareTabKey(key) {
      switch (key) {
        case "in storyboard":
          return "drisb";
        case "storyboard review":
          return "drisbr";
        case "design review":
          return "dridr";
        case "in design":
          return "drid";
        case "in programming":
          return "drip";
      }
    },
    async fetchDesignRequest(id) {
      try {
        const response = await this.$axios.get(`design-request/${id}`);
        if (response?.status === 200) {
          return response?.data;
        }
      } catch (err) {}
    },
    showAutoReviewDialog() {
      this.reviewDialogKey++;
      this.reviewDialog = true;
    },

    async getDesignRequests(ids) {
      let options = {
        ids: ids,
        include: 1,
      };
      const response = await this.$axios.get(
        `design-request?key=${this.getTabKey()}`,
        {
          params: options,
        }
      );
      return response.data.data;
    },

    async fetchNewRealtime(itemsPerPage = 1) {
      const data = {
        key: this.getTabKey(),
        options: {
          itemsPerPage: itemsPerPage,
          page: this.design_request_data.length + 1,
        },
      };
      const response = await this.$axios.get(`design-request?key=${data.key}`, {
        params: data.options,
      });
      this.design_request_data = [
        ...this.design_request_data,
        ...response.data.data,
      ];
      this.extraData = response?.data;
      this.selectedItems = [];
    },

    async openParamsProfile() {
      if (this.profileCode !== null && this.profileCode !== undefined) {
        let item = null;
        try {
          const response = await this.$axios.get(
            `design-request/${this.profileCode}`,
            {
              params: {
                with_code: true,
              },
            }
          );
          if (response?.status === 200) {
            item = response.data;
            this.viewSelectedDesignRequest(item);
          }
        } catch (err) {}
      }
    },

    completedStyle(item) {
      return item.status == "completed" ? "completedAnimation" : "";
    },
    viewNote(note, title) {
      this.landing_note = note;
      this.note_title = title;
      this.showNoteDialog = true;
      // this.note=this.note;
    },

    async showRejectReason(item, status) {
      if (status == "storyboard rejected") {
        if (item.storyboard_reject_count == 0) {
          // this.$toastr.w(this.$tr("no_reject_reasons"));
				this.$toasterNA("orange",this.$tr('no_reject_reasons'));
          return 0;
        }
      } else if (status == "design rejected") {
        if (item.design_reject_count == 0) {
          // this.$toastr.w(this.$tr("no_reject_reasons"));
				this.$toasterNA("orange",this.$tr('no_reject_reasons'));
          return 0;
        }
      } else {
        try {
          const data = {
            status: status,
            design_request_id: item.id,
          };
          this.isFetchCancels = true;
          const response = await this.$axios.post(
            "designRequests/reasonReject",
            data
          );
          if (response?.status == 200) {
            this.requestDetails = {
              status: status,
              reasons: response.data.data,
            };
            if (this.requestDetails.reasons.length == 0) {
              this.isFetchCancels = false;
              // this.$toastr.w(this.$tr("no_cancel_reasons"));
				this.$toasterNA("orange",this.$tr('no_cancel_reasons'));

              return;
            }
            this.dialogKey++;
            this.showRejectionDialog = true;
          } else {
            this.$toasterNA("red", this.$tr('something_went_wrong'));
          }
          this.isFetchCancels = false;
        } catch (error) {
          this.isFetchCancels = false;
          this.$toasterNA("red", this.$tr('something_went_wrong'));
        }
      }
    },

    async onUnAssign() {
      if (this.selectedItems.length) {
        let orderIds = this.selectedItems.map(
          (item) => item.design_request_order.id
        );
        try {
          await this.$axios.post("design-request/assign-user?type=unassign", {
            orderIds: orderIds,
          });
          this.fetchDataAccordingtoStatus();
        } catch (error) {
          this.$toasterNA("red", this.$tr('something_went_wrong'));
        }
      }
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
          d = new Date(element?.completed_date);
          d.toString();
        } else {
          d = new Date();
          d.toString();
        }
        let cancelledTotalSecond = 0;
        if (element.status_times?.length > 0) {
          element.status_times.forEach((e) => {
            if (e.status == "cancelled") {
              let cancelledDate = new Date(e.created_at);
              cancelledDate.toString();
              if (e.end_date) {
                let endDate = new Date(e.end_date);
                endDate.toString();
                let diff = Math.abs(
                  cancelledDate.getTime() / 1000 - endDate.getTime() / 1000
                );
                cancelledTotalSecond += diff;
              } else {
                d = new Date(e?.created_at);
                d.toString();
              }
            }
          });
        }
        if (element.status_times?.length > 0) {
          element.status_times.forEach((e) => {
            if (e.status == "removed") {
              let removedDate = new Date(e.created_at);
              removedDate.toString();
              if (e.end_date) {
                let endDate = new Date(e.end_date);
                endDate.toString();
                let diff = Math.abs(
                  removedDate.getTime() / 1000 - endDate.getTime() / 1000
                );
                cancelledTotalSecond += diff;
              } else {
                d = new Date(e?.created_at);
                d.toString();
              }
            }
          });
        }
        let created_date = element.created_at;
        let f = new Date(created_date);
        f.toString();
        let diff = Math.abs(d.getTime() / 1000 - f.getTime() / 1000);
        diff = diff - cancelledTotalSecond;
        let total_time_spent = this.checkSpentTime(diff);

        this.total_times_spent.push({
          code: element.code,
          diff: diff,
          total_time: total_time_spent,
        });
      });
    },
    viewSelectedDesignRequest(item) {
      this.showProfile = true;
      this.$refs.desingRequestProfile.toggleDrawer();
      this.profileData = item;
    },
    toggleDrawerEdit() {
      this.$refs.desingRequestProfile.toggleDrawer();
    },

    async fetchDataAccordingtoStatus() {
      this.apiCalling = true;
      const data = this.fetchOptions();
      const response = await this.$axios.get(`design-request`, {
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

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.fetchDataAccordingtoStatus();
    },

    onSearchTypeChange() {
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },

    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter((row) =>
        !isNaN(code) ? row.code !== "LDR000" + code : row.code !== code
      );
    },

    checkSelectedTab(value) {
      // this.tableKey++;    // Commented due to pagination bug
      this.selectedItems = [];
      let currentTab = "";
      this.tabItems = this.tabItems.map((item, index) => {
        index === value
          ? ((item.isSelected = 1), (currentTab = item))
          : (item.isSelected = 0);
        return item;
      });
      this.setStatusItems(currentTab.key);
    },

    setStatusItems(status) {
      this.showRestore = false;

      switch (status) {
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
              id: "in design",
              name: this.$tr("approve"),
            },
            {
              id: "storyboard rejected",
              name: this.$tr("reject"),
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
              id: "in programming",
              name: this.$tr("approve"),
            },
            {
              id: "design rejected",
              name: this.$tr("reject"),
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
        case "removed":
          this.statusItems = [];
          this.showRestore = true;
        case "cancelled":
          this.statusItems = [
            { id: "in storyboard", name: this.$tr("in_storyboard") },
          ];
          break;
        default:
          this.statusItems = [];
          break;
      }
    },

    getTabKey() {
      return this.tabItems[this.tabIndex].key;
    },

    onView() {
      // this.showProfile = true;
      this.$refs.desingRequestProfile.toggleDrawer();
      this.profileData = this.selectedItems[0];
    },

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post(
          "design-requests/search",
          options
        );
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
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
        key: this.tabKey,
      };
      if (!this.$isObjectEmpty(this.filterData))
        Object.assign(data, this.filterData);

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;

      return data;
    },

    fetchNewData() {
      this.selectedItems = [];
      this.fetchDataAccordingtoStatus();
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
        case "assigned":
          return this.extraData?.assignedTotal || 0;
        case "not assigned":
          return this.extraData?.notAssignedTotal || 0;
      }
    },
    getOrderType(order) {
      return (
        order.design_request_order.order_type ??
        order.design_request_order.order_type
      );
    },
  },
};
</script>

<style scoped>
.toggle-chart {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: 20px;
}
@media only screen and (min-width: 800px) {
  .toggle-chart {
    right: unset;
    left: 50%;
    transform: translate(-50%, -50%);
  }
}
.custompriority {
  text-align: center !important;
}
</style>
<style>
.completedAnimation {
  -webkit-animation: colorChange 5s infinite; /* Chrome, Safari, Opera */
  animation: 5s infinite colorChange;
}

@keyframes colorChange {
  0% {
    color: red;
  }
  25% {
    color: green;
  }
  50% {
    color: rgb(39, 3, 201);
  }
  75% {
    color: rgb(7, 196, 0);
  }
  100% {
    color: red;
  }
}
/* Chrome, Safari, Opera */
@-webkit-keyframes colorChange {
  0% {
    color: red;
  }
  25% {
    color: green;
  }
  50% {
    color: rgb(39, 3, 201);
  }
  75% {
    color: rgb(7, 196, 0);
  }
  100% {
    color: red;
  }
}
</style>
