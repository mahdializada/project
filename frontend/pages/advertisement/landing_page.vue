<template>
  <v-container fluid class="pa-0">
    <client-only>
      <Landing_Page_Operations
        v-if="$isInScope('advprojectsc') || $isInScope('advprojectsu')"
        ref="landingPageRef"
        @getRecord="fetchDataAccordingtoStatus"
      />
      <ProjectFilter ref="projectFilter" @filterRecords="onFilterRecords" />

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
          :page_name="'projects_all'"
          @closeColumnDialog="columnDialog = false"
        />
      </v-dialog>

      <v-row no-gutters>
        <v-col cols="12">
          <PageHeader
            Icon="projects"
            :Title="breadcrumb[1].text"
            :show_ad_img="true"
            :Breadcrumb="breadcrumb"
          >
          </PageHeader>
        </v-col>
        <v-col cols="12">
          <PageFilters
            ref="pageFilterRef"
            :selected_header.sync="all_headers[0].selected_headers"
            :downloadContent="projectRecords"
            :downloadDialog="downloadDialog"
            @onFilter="openFilterDialog"
            filename="projects"
            @onDownload="
              () => {
                downloadDialog = !downloadDialog;
              }
            "
            @searchById="searchById"
            @clearAndUnselectId="clearAndUnselectId"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            @onColumn="columnDialog = true"
            note-title=""
            :showBulkUpload="false"
            :showDownload="$isInScope('advprojectse')"
          >
            <CustomButton
              v-if="$isInScope('advprojectsc')"
              icon="fa-plus"
              @click="InsertDialog"
              :text="$tr('create_item', $tr('project'))"
              type="primary"
            />
          </PageFilters>
        </v-col>

        <v-col cols="12">
          <CustomPageActions
            :showView="false"
            :showEdit="$isInScope('advprojectsu')"
            :showAutoEdit="false"
            :showDelete="$isInScope('advprojectsd')"
            :ignoreReason="false"
            :showSelect="false"
            :showApply="false"
            :showAssign="false"
            :showApprove="false"
            :showUnAssign="false"
            :selectedItems.sync="selectedItems"
            :tab-key="tabItems[tabIndex].key"
            :showBlock="false"
            :showRestore="$isInScope('advprojectsd')"
            :routeName="'advertisement/projects'"
            :subSystemName="'Projects'"
            @onEdit="onEdit"
            @onDelete="onDelete"
            @onRestore="onRestore"
            :statusItems="[]"
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
            :items="projectRecords"
            :ItemsLength="getTotals(tabItems[tabIndex].key)"
            v-model="selectedItems"
            :loading="apiCalling"
            @onTablePaginate="onTableChanges"
            @click:row="onRowClicked"
            :key="tableKey"
          >
            <template v-slot:[`item.code`]="{ item }">
              <span class="mx-1 primary--text font-weight-bold">
                {{ item.code }}
              </span>
            </template>
          </DataTable>
        </v-col>
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
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import Project from "~/configs/pages/advertisement/project";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import HandleException from "~/helpers/handle-exception";
import ProjectFilter from "../../components/advertisement/ProjectFilter.vue";
import Landing_Page_Operations from "../../components/advertisement/landing_page/Landing_Page_Operations.vue";

export default {
  meta: {
    hasAuth: true,
    scope: "advprojectsv",
  },

  components: {
    CustomPageActions,
    CustomButton,
    PageHeader,
    PageFilters,
    DataTable,
    CustomizeColumn,
    ProjectFilter,
    Landing_Page_Operations,
},

  data() {
    return {
      customKey: 0,
      reviewDialog: false,
      reviewDialogKey: 0,
      showChart: false,
      tabChipKey: 0,
      landing_note: "",
      note_title: "",
      showNoteDialog: false,
      projectRecords: [],

      tabItems: Project(this).tabItems,
      breadcrumb: Project(this).breadcrumb,
      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      options: {},

      downloadDialog: false,
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
      autoEdit: false,
      editKey: 0,
      autoEditData: [],
      selectedItems: [],
      content: [],
      contentData: "",
      filterData: {},
      note_title: "",
      isEdit: false,
      isAutoEdit: false,
      editData: {},
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
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "projects_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    async onRestore() {
      if (this.getTabKey() == "removed") {
        try {
          const ids = this.selectedItems.map(({ id }) => id);
          const url = `advertisement/projects/update?restore=true`;
          const { data } = await this.$axios.put(url, { ids });
          if (data.result) {
            // this.$toastr.s(this.$tr("successfully_restored"));
            this.$toasterNA("green", this.$tr("successfully_restored"));
          } else {
            this.$toasterNA("red", this.$tr("something_went_wrong"));
          }
        } catch (error) {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } else {
        this.$toasterNA("red", this.$tr("cant_do_operation_on_current_tab"));
      }
    },

    async onDelete() {
      try {
        const ids = this.selectedItems.map(({ id }) => id);
        const url = `advertisement/projects/${ids}`;
        const { data } = await this.$axios.delete(url);
        if (data.result) {
          // 				this.$toasterNA("green", this.$tr('successfully_deleted'));
          this.$toasterNA("green", this.$tr("successfully_deleted"));
        } else {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },

	InsertDialog() {
      this.$refs.landingPageRef.showDialog();
    },
    openFilterDialog() {
      this.$refs.projectFilter.changeModel();
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.fetchDataAccordingtoStatus();
    },

    async fetchDataAccordingtoStatus() {
      this.apiCalling = true;
      this.selectedItems = [];
      this.checkSelectedTab(this.tabIndex);
      const data = this.fetchOptions();
      const response = await this.$axios.get(`advertisement/projects`, {
        params: data,
      });
      if (response?.status === 200) {
        this.projectRecords = response.data?.data;
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

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.get(
          "advertisement/projects/search",
          {
            params: options,
          }
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
          this.projectRecords = this.projectRecords.filter(
            (item) => item.id !== data.id
          );
          this.projectRecords?.unshift(data);
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

    fetchNewData() {
      this.selectedItems = [];
      this.fetchDataAccordingtoStatus();
    },

    async onEdit() {
      if (this.selectedItems.length == 1) {
        const [firstItem] = this.selectedItems;

        this.$refs.landingPageRef.toggle(firstItem);
      } else {
        this.$toasterNA(
          "red",
          this.$tr("only_one_item_is_allowed_for_this_operation")
        );
      }
    },

    // add or remove item from selected items
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    getTotals(tabKey) {
      switch (tabKey) {
        case "all":
          return this.extraData?.allTotal || 0;
        case "current":
          return this.extraData?.currentTotal || 0;
        case "removed":
          return this.extraData?.removedTotal || 0;
      }
    },
  },
};
</script>
