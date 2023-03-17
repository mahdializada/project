<template>
  <v-container fluid class="pa-0">
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
        :page_name="'business_locations_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>
    <!-- Bs locations Bulk Upload -->
    <v-dialog v-model="bsLocationBulkUpload" width="1300">
      <Dialog
        persistent
        max-width="1300"
        @closeDialog="bsLocationBulkUpload = false"
      >
        <BusinessLocationBulkupload
          :tabKey="getTabKey()"
          v-if="bsLocationBulkUpload"
        />
      </Dialog>
    </v-dialog>
    <!-- BusinessLocation Profile Section -->
    <v-dialog v-model="showProfile" persistent width="1300" scrollable>
      <business-location-profile
        :key="viewKey"
        :dialog.sync="showProfile"
        :profileData="profileData"
        @closeProfile="showProfile = false"
        @onEdit="onEdit"
      />
    </v-dialog>

    <!--    Register  Dialog-->
    <RegisterBusinessLocation
      v-if="registerDialog"
      :tableKey="tabItems[tabIndex].key"
      :registerDialog.sync="registerDialog"
    />

    <EditBusinessLocation
      v-if="editDialog"
      :tabKey="tabItems[tabIndex].key"
      :edit-dialog.sync="editDialog"
      :key="editKey"
      :editItems="editItems"
      :is-auto-edit.sync="isAutoEdit"
    />

    <v-dialog
      v-model="filterDialog"
      persistent
      max-width="1300"
      width="1300"
      v-if="$isInScope('blv')"
    >
      <BusinessLocationFilter
        @close="filterDialog = false"
        @getRecord="onFilterRecords"
      />
    </v-dialog>
    <v-row no-gutters>
      <ProgressBar v-if="showProgressBar" />
      <v-col cols="12">
        <PageHeader
          :Icon="'business_locations'"
          :Title="$tr('business_locations')"
          :Breadcrumb="breadcrumb"
        />
      </v-col>

      <v-col cols="12">
        <PageFilters
          @onFilter="filterDialog = true"
          @onBulkUpload="bsLocationBulkUpload = !bsLocationBulkUpload"
          :show-add-note="false"
          :show-bulk-upload="$isInScope('blc')"
          :showDownload="$isInScope('ble')"
          :selected_header="selected_headers"
          :header.sync="selected_headers"
          :downloadContent="getItems"
          :downloadDialog="downloadDialog"
          :filename="$tr('business_locations')"
          @onDownload="downloadDialog = !downloadDialog"
          @onColumn="columnDialog = true"
          @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
        >
          <CustomButton
            @click="registerDialog = !registerDialog"
            icon="mdi-map-marker-plus"
            :text="$tr('create_business_location')"
            type="primary"
          />
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :selectedItems="selectedItems"
          :tab-key.sync="tabKey"
          @onEdit="onEdit"
          @onView="viewSelectedBusinessLocation"
          @onAutoEdit="onAutoEdit"
          :routeName="'business-locations'"
          :subSystemName="'Business Locations'"
          @fetchNewData="fetchNewData"
          :showView="$isInScope('blv')"
          :showEdit="$isInScope('blu')"
          :showAutoEdit="$isInScope('blu')"
          :showDelete="$isInScope('bld')"
          :showBlock="$isInScope('blu')"
          :showSelect="$isInScope('blu')"
          :showApply="$isInScope('blu')"
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
                    v-text="getTotals(item.key)"
                  />
                </v-tab>
              </client-only>
            </v-tabs>
          </v-col>
        </v-row> -->

        <TableTabs
          @getSelectedTabRecords="tabKeyGetter"
          :tabData="tabItems"
          :extraData="extraData"
        />

        <DataTable
          ref="tableRef"
          :headers="all_headers[0].selected_headers"
          :items="getItems"
          :ItemsLength="extraData.total"
          v-model="selectedItems"
          :loading="apiCalling"
          @onTablePaginate="onTableChanges"
          @viewSelectedItem="viewSelectedBusinessLocation"
          @click:row="onRowClicked"
        >
          <template v-slot:[`item.status`]="{ item }">
            <span v-if="item.status === 'inactive'">Deactive</span>
            <span v-else>{{ item.status }}</span>
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
import PageHeader from "~/components/design/PageHeader";
import PageFilters from "~/components/design/PageFilters";
import PageActions from "~/components/design/PageActions";
import DataTable from "~/components/design/DataTable";
import ProgressBar from "~/components/common/ProgressBar";
import CustomButton from "~/components/design/components/CustomButton";
import BusinessLocations from "~/configs/pages/business_locations";
import RegisterBusinessLocation from "~/components/masters/business_locations/RegisterBusinessLocation";
import EditBusinessLocation from "~/components/masters/business_locations/EditBusinessLocation";
import CustomizeColumn from "../../components/customizeColumn/CustomizeColumn2.vue";
import HandleException from "../../helpers/handle-exception";
import TableTabs from "~/components/common/TableTabs.vue";
import Dialog from "../../components/design/Dialog/Dialog.vue";
import BusinessLocationFilter from "~/components/masters/business_locations/BusinessLocationFilter";
import BusinessLocationProfile from "../../components/masters/business_locations/BusinessLocationProfile.vue";
import BusinessLocationBulkupload from "../../components/masters/business_locations/BusinessLocationBulkupload.vue";
export default {
  meta: {
    hasAuth: true,
    scope: "blv",
  },
  components: {
    BusinessLocationProfile,
    RegisterBusinessLocation,
    EditBusinessLocation,
    CustomButton,
    ProgressBar,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    CustomizeColumn,
    BusinessLocationFilter,
    BusinessLocationBulkupload,
    Dialog,
    TableTabs,
  },
  data() {
    return {
      tabItems: BusinessLocations(this).tabItems,
      breadcrumb: BusinessLocations(this).breadcrumb,
      selectedItems: [],
      tabIndex: 0,
      tabKey: "all",
      extraData: [],
      //bulk upload
      bsLocationBulkUpload: false,
      // insert section
      showProgressBar: false,
      registerDialog: false,

      //edit section
      editItems: [],
      editDialog: false,
      editKey: 0,
      isAutoEdit: false,

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

      viewKey: 0,
      showProgressBar: false,
      // profile section
      showProfile: false,
      profileData: {},

      downloadDialog: false,
      filterDialog: false,
      filterData: {},
      options: {},
      searchContent: "",
      searchId: "",
    };
  },
  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },

  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      apiCalling: "business_locations/isApiCalling",
      getTotals: "business_locations/getTotal",
      getItems: "business_locations/getItems",
    }),
  },
  async created() {
    await this.fetchHeaders();
    this.getRecord();
  },

  methods: {
    // vuex state methods
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
      fetchItems: "business_locations/fetchItems",
      insertItem: "business_locations/insertNewItem",
      deleteItem: "business_locations/deleteItem",
    }),

    tabKeyGetter(key) {
      this.tabKey = key;
      this.getRecord();
    },

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "business_locations_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    // fired on auto edit button clicked to edit multiple business locations
    async onAutoEdit() {
      if (this.selectedItems.length > 0) {
        this.editItems = [];
        const itemIds = this.selectedItems.map((item) => item.id);
        const items = await this.fetchBusinessLocation(itemIds);
        if (items) {
          this.editItems = items;
          this.editDialog = true;
          this.isAutoEdit = true;
          this.editKey++;
          this.selectedItems = [];
        }
      }
    },

    viewSelectedBusinessLocation(item) {
      this.viewKey++;
      const data = item || this.selectedItems[0];
      data.status_transformations = this.$sortStatusTransformationData(data);
      this.profileData = data;
      this.showProfile = true;
    },

    // fired on edit button clicked to edit business locations
    async onEdit() {
      if (this.selectedItems.length > 0) {
        this.editItems = [];
        const itemId = this.selectedItems[0].id;
        const items = await this.fetchBusinessLocation(itemId);
        if (items) {
          this.editItems[0] = items[0];
          this.editDialog = true;
          this.editKey++;
          this.selectedItems.splice(0, 1);
        }
      }
    },

    // fetch business locations details
    async fetchBusinessLocation(businessLocationId) {
      try {
        this.showProgressBar = true;
        const url = `business-locations/${businessLocationId}?details=true`;
        const response = await this.$axios.get(url);
        const items = response?.data?.data;
        const filteredItems = [];

        for (const item of items) {
          const selectedCountries = [];
          for (const country of item.company.countries) {
            selectedCountries.push(country);
          }

          item.selectedCountries = selectedCountries;
          filteredItems.push(item);
        }

        this.showProgressBar = false;
        return filteredItems;
      } catch (e) {
        this.$toasterNA("red", this.$tr('something_went_wrong'));
        this.showProgressBar = false;
      }
    },

    // add or remove item from selected items
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    // return table tab key
    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
    },

    // active selected tab item when tab changed
    checkSelectedTab(value) {
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },

    // END::     ******************      Change Status      ******************

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
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },
    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
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
    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post(
          "business-locations/search",
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
          this.deleteItem(data.id);
          this.insertItem(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    async getRecord() {
      try {
        const data = this.fetchOptions();
        const data2 = await this.fetchItems(data);
        this.extraData = data2.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
  },
};
</script>
