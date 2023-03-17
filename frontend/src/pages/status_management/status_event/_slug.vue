<template>
  <v-container fluid class="pa-0">
    <v-row no-gutters>
      <!-- customize column dialog -->
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
          :page_name="'status_events_all'"
          @closeColumnDialog="columnDialog = false"
        />
      </v-dialog>

      <ProgressBar v-if="showProgressBar" />
      <!-- register reason status steppers -->
      <RegisterReasonStatusNew ref="reasonStatusInsertRef" @pushRecord="pushRecord" :slug="slug"/>
      <!-- register reason status steppers -->

      <!-- profile dialog -->
      <v-dialog
        v-model="showProfile"
        persistent
        width="1300"
        scrollable
        v-if="$isInScope('se' + $route.params.slug.charAt(0) + 'v')"
      >
        <ReasonStatusProfile
          :key="profileKey"
          @onEdit="editSelectedReason"
          :profileData="profileData"
          :dialog.sync="showProfile"
        />
      </v-dialog>

      <!-- filter Dialog -->
      <v-dialog
        v-model="reasonFilter"
        persistent
        max-width="1300"
        width="1300"
        v-if="$isInScope('se' + $route.params.slug.charAt(0) + 'v')"
      >
        <ReasonStatusFilter
          @getRecord="onFilterRecords"
          @close="onFilter"
          :subSystems="subSystems"
          :slug="slug"
        />
      </v-dialog>

      <!-- edit dialog -->
      <v-dialog v-model="editDialog" persistent width="1300">
        <EditReasonStatus
          :subSystems="subSystems"
          :edit-dialog.sync="editDialog"
          :key="editKey"
          :editItems="editItems"
          :is-auto-edit.sync="isAutoEdit"
          :slug="slug"
        />
      </v-dialog>
      <v-col cols="12">
        <PageHeader
          :Icon="`status_event_list`"
          Title="status_event_list"
          :Breadcrumb="breadcrumb"
        />
      </v-col>

      <v-col cols="12">
        <PageFilters
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="status_event"
          :downloadDialog="downloadDialog"
          :filename="$tr('status_event_list')"
          :showBulkUpload="false"
          @onDownload="downloadDialog = !downloadDialog"
          @onFilter="onFilter"
          @onColumn="columnDialog = true"
          :showDownload="$isInScope('se' + $route.params.slug.charAt(0) + 'e')"
          @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
        >
          <CustomButton
            @click="toggleReasonStatusRegister"
            icon="fa-plus"
            text="add_status_event"
            type="primary"
          />
        </PageFilters>
      </v-col>

      <v-col cols="12">
        <PageActions
          :selectedItems="selected"
          :showSelect="false"
          :showBlock="false"
          :showApply="false"
          :showDelete="false"
          :defaultAction="false"
          @onView="onView"
          @onEdit="Edit"
          :routeName="'status-events'"
          @onAutoEdit="autoEdit"
          :showView="$isInScope('se' + $route.params.slug.charAt(0) + 'v')"
          :showEdit="$isInScope('se' + $route.params.slug.charAt(0) + 'u')"
          :showAutoEdit="$isInScope('se' + $route.params.slug.charAt(0) + 'u')"
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
                    class="ms-1 tab-chip"
                    :color="item.isSelected ? 'primary' : 'white'"
                    :text-color="item.isSelected ? 'white' : 'primary'"
                    small
                    v-text="total"
                  />
                </v-tab>
              </client-only>
            </v-tabs>
          </v-col>
        </v-row>
        <DataTable
          ref="tableRef"
          :headers="all_headers[0].selected_headers"
          :items="status_event"
          :ItemsLength="total"
          v-model="selected"
          @click:row="onRowClicked"
          @onTablePaginate="onTableChanges"
        >
          <template v-slot:[`item.code`]="{ item }">
            <span
              @click="() => viewSelectedReason(item)"
              class="mx-1 primary--text font-weight-bold"
            >
              {{ item.code }}
            </span>
          </template>
          <template v-slot:[`item.title`]="{ item }">
            <v-tooltip bottom max-width="500">
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on">
                  <span v-if="item.reason.title.length >= 50"
                    >{{ item.reason.title.substring(0, 49) }} ...
                  </span>
                  <span v-else>{{ item.reason.title.substring(0, 49) }} </span>
                </span>
              </template>
              <span>{{ item.reason.title }}</span>
            </v-tooltip>
          </template>
          <template v-slot:[`item.types`]="{ item }">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{ $tr("types") }}
                </span>
              </template>
              <span v-for="types in item.reason_types" :key="types.id">
                {{ $tr(type.type) }} <br />
              </span>
            </v-tooltip>
          </template>
          <template v-slot:[`item.sub_systems`]="{ item }">
            <span>{{ $tr(item.subsystem.phrase) }} <br /></span>
          </template>
        </DataTable>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import ProgressBar from "~/components/common/ProgressBar";
import PageHeader from "~/components/design/PageHeader";
import PageFilters from "~/components/design/PageFilters";
import PageActions from "~/components/design/PageActions";
import DataTable from "~/components/design/DataTable";
import Reasons from "~/configs/pages/reasons";
import CustomButton from "~/components/design/components/CustomButton.vue";
import { mapGetters, mapActions } from "vuex";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import HandleException from "~/helpers/handle-exception";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import RegisterReasonStatus from "../../../components/statusManagement/reasonStatus/RegisterReasonStatus.vue";
import RegisterReasonStatusNew from "../../../components/statusManagement/reasonStatus/RegisterReasonStatusNew.vue";
import ReasonStatusProfile from "../../../components/statusManagement/reasonStatus/ReasonStatusProfile.vue";
import EditReasonStatus from "../../../components/statusManagement/reasonStatus/EditReasonStatus.vue";
import ReasonStatusFilter from "../../../components/statusManagement/reasonStatus/ReasonStatusFilter.vue";
import ColumnHelper from "~/helpers/column-helper";

export default {
  meta: {
    hasAuth: true,
    scope: ["seuv", "semv", "secv",'seav'],
  },
  name: "reasons",
  async asyncData({ store, params }) {
    //   await store.dispatch("statusEvents/fetchStatusEvent", {
    //     slug: params.slug,
    //   });
    //   await store.dispatch("reasons/fetchReasons", {
    //     slug: params.slug,
    //   });

    //   await store.dispatch("customColumns/fetchCustomColumns", {
    //     view_name: "reason_" + params.slug,
    //   });
    return { slug: params.slug };
  },

  components: {
    CustomButton,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    ProgressBar,
    Dialog,
    CustomizeColumn,
    RegisterReasonStatus,
    ReasonStatusProfile,
    EditReasonStatus,
    ReasonStatusFilter,
    RegisterReasonStatusNew
  },

  data() {
    return {
      selected: [],
      tabItems: Reasons(this).tabItems,

      breadcrumb:
        this.$route.params.slug === "user"
          ? Reasons(this).breadcrumb_user
          : this.$route.params.slug === "master"
          ? Reasons(this).breadcrumb_master
          : this.$route.params.slug === "content"
          ? Reasons(this).breadcrumb_landing
          : this.$route.params.slug === "personal"
          ? Reasons(this).breadcrumb_personal
          : this.$route.params.slug === "advertisement"
					? Reasons(this).breadcrumb_advertisement
          : [],
      registerDialog: false,
      showProgressBar: false,
      tabIndex: 0,
      key: 0,
      subSystems: [],

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

      showProgressBar: false,

      // Edit and Auto Edit
      editKey: 0,
      editItems: [],
      editDialog: false,
      isAutoEdit: false,
      reasonFilter: false,

      // Profile
      showProfile: false,
      profileData: {},
      profileKey: 0,

      downloadDialog: false,

      filterData: {},
      options: {},
      searchId: "",
      searchContent: "",
    };
  },

  async validate({ params }) {
    return !params.slug ? false : true;
  },

  computed: {
    ...mapGetters({
      status_event: "statusEvents/get_status_event",
      total: "statusEvents/get_total",
    }),
  },

  async fetch() {
    await this.getRecord();
  },
  async created() {
    await this.fetchHeaders();
    const slug = this.$route.params.slug;
    // this.fetchStatusEvent({
    //   slug: slug,
    // });
    // this.fetchReasons({
    //   slug: slug,
    // });
  },

  methods: {
    ...mapActions({
      fetchStatusEvent: "statusEvents/fetchStatusEvent",
      fetchReasons: "reasons/fetchReasons",
      reason: "reasons/fetchStatusEvent",
      filterReasonStore: "reasons/filterReason",
      fetchStatusEventByID: "statusEvents/fetchStatusEventByID",
    }),

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "status_events_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    // add or remove item from selected items
    onRowClicked(item) {
      let items = new Set(this.selected);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selected = Array.from(items);
    },

    async viewSelectedReason(item) {
      this.profileData = item;
      this.showProfile = true;
    },

    async fetchSubSystems() {
      // const element = [];
      try {
        const response = await this.$axios.get(
          `systems_sub_systems?system_name=${this.slug}`
        );
        if (response?.status === 200 && response?.data?.result) {
          this.subSystems = response?.data?.data;
        } else {
          // this.$toastr.w(this.$tr("something_went_wrong"));
				this.$toasterNA("orange",this.$tr('something_went_wrong'));

        }
      } catch (error) {
        if (error?.response?.status === 404 && !error?.response?.data?.result) {
          // this.$toastr.w(this.$tr("sub_system_not_found"));
				this.$toasterNA("orange",this.$tr('sub_system_not_found'));

        } else {
          HandleException.handleApiException(this, error);
        }
      }
    },

    async Edit() {
      this.showProgressBar = true;
      await this.fetchSubSystems();
      this.editKey++;
      this.editDialog = true;
      this.editItems[0] = this._.clone(this.selected[0]);
      this.showProgressBar = false;
    },

    async editSelectedReason(item) {
      if (this.subSystems.length === 0) {
        await this.fetchSubSystems();
      }
      this.showProfile = false;
      this.editKey++;
      this.editItems[0] = this._.clone(item);
      this.editDialog = true;
    },

    async autoEdit() {
      this.showProgressBar = true;
      if (this.subSystems.length === 0) {
        await this.fetchSubSystems();
      }
      this.editDialog = true;
      this.editKey++;
      this.isAutoEdit = true;
      this.editItems = this.selected.map((row) => {
        return this._.clone(row);
      });
      this.showProgressBar = false;
    },

    onView() {
      this.profileKey++;
      if (this.selected.length == 1) {
        this.profileData = this.selected[0];
        this.showProfile = true;
      } else {
        // this.$toastr.w(this.$tr("view_item_max_limit_text"));
				 this.$toasterNA("orange", this.$tr("view_item_max_limit_text"));

      }
    },

    async onFilter() {
      if (this.subSystems.length === 0) {
        await this.fetchSubSystems();
      }
      this.reasonFilter = !this.reasonFilter;
    },

    async onAutoEdit() {
      this.isAutoEdit = true;
      this.editKey++;
      this.editDialog = true;
      this.editItems = this._.clone(this.selected);
      this.selected = [];
    },

    async onEdit() {
      this.editKey++;
      this.editDialog = true;
      this.editItems[0] = this._.clone(this.selected[0]);
      this.selected = [];
    },

    // new search and filter functions
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.getRecord();
    },

    onSearchTypeChange() {
      this.selected = [];
      this.searchContent = "";
      this.searchId = "";
    },
    clearAndUnselectId(id) {
      this.selected = this.selected.filter((row) => row?.id != id);
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;

      this.getRecord();
    },

    fetchOptions() {
      let data = {
        slug: this.slug,
      };

      if (!this.$isObjectEmpty(this.filterData))
        Object.assign(data, this.filterData);

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;
      return data;
    },

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.fetchStatusEventByID(options);
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    selectData(response) {
      if (response != null) {
        const data = response.data;
        this.selected?.unshift(response);
        // this.onRowClicked(data);
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    async getRecord() {
      try {
        const data = this.fetchOptions();
        await this.fetchStatusEvent(data);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    toggleReasonStatusRegister(){
      this.$refs.reasonStatusInsertRef.showDialog()
    },

    async pushRecord(data) {
      if (this.tabKey == "inprocess" || this.tabKey == "all") {
        await this.status_event.unshift(data);
      }
      // this.extraData.allTotal += 1;
      // this.extraData.inprocessTotal += 1;
      // this.extraData.total += 1;
    },
  },
  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },
};
</script>

<style scoped></style>
