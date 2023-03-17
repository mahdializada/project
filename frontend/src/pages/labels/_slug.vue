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
          :page_name="'labels_all'"
          @closeColumnDialog="columnDialog = false"
        />
      </v-dialog>

      <!-- /** filter label dialog */ -->

      <v-dialog v-model="filterDialog" persistent width="1300">
        <labelFilter
          :subSystems="subSystems"
          @close="filterDialog = false"
          @getRecord="onFilterRecords"
        />
      </v-dialog>

      <!-- /**end filter dialog */ -->

      <ProgressBar v-if="showProgressBar" />
      <!-- <v-dialog v-model="registerDialog" persistent scrollable width="1300"> -->
        <!-- v-if="
          $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'c')
        " -->
        <!-- <Dialog @closeDialog="registerDialog = false">
          <RegisterLabel
            :systemName="$route.params.slug"
            :subSystems="subSystems"
            :labelCategory="this.labelCategory"
            @closeDialog="registerDialog = false"
            :edit_data="selectedItems"
            :key="key"
          />
        </Dialog> -->
      <!-- </v-dialog> -->
      <LabelStepper ref="labelStepperRef" :tabKey.sync="tabKey" />

      <v-dialog
        v-model="showProfile"
        persistent
        width="1300"
        scrollable
        v-if="
          $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'v')
        "
      >
        <LabelProfile
          :key="profileKey"
          @onEdit="editSelectedLabel"
          :labelCategory="labelCategory"
          :profileData="profileData"
          :dialog.sync="showProfile"
          :subSystems="subSystems"
          :systemName="$route.params.slug"
        />
      </v-dialog>

      <v-dialog
        v-model="editDialog"
        persistent
        width="1300"
        v-if="
          $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'u')
        "
      >
        <EditLabel
          :systemName="$route.params.slug"
          :subSystems="subSystems"
          :labelCategory="this.labelCategory"
          :edit-dialog.sync="editDialog"
          :key="editKey"
          :editItems="editItems"
          :is-auto-edit.sync="isAutoEdit"
        />
      </v-dialog>
      <v-col cols="12">
        <PageHeader :Icon="`labels`" Title="labels" :Breadcrumb="breadcrumb" />
      </v-col>
      <v-col cols="12">
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="labels"
          :downloadDialog="downloadDialog"
          :filename="$tr('labels')"
          @onDownload="toggleDownload"
          @onFilter="onFilter"
          @onColumn="columnDialog = true"
          :show-bulk-upload="
            $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'c')
          "
          :showDownload="
            $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'e')
          "
          @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
        >
          <!-- <CustomButton @click="addLabel" icon="fa-plus" text="add_label" type="primary" /> -->
          <CustomButton
            @click="toggleRegisterDialog"
            icon="fa-user-plus"
            :text="$tr('create_item', $tr('label'))"
            type="primary"
          />
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :selectedItems.sync="selectedItems"
          :tabKey.sync="tabKey"
          :showBlock="false"
          @onView="onView"
          @onEdit="Edit"
          @onAutoEdit="autoEdit"
          :routeName="'labels'"
          :subSystemName="`Labels (${$route.params.slug})`"
          @fetchNewData="fetchNewData"
          :statusItems="[
            { id: 'archive', name: 'archive' },
            { id: 'live', name: 'live' },
          ]"
          :showView="
            $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'v')
          "
          :showEdit="
            $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'u')
          "
          :showAutoEdit="
            $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'u')
          "
          :showDelete="
            $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'd')
          "
          :showSelect="
            $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'u')
          "
          :showApply="
            $isInScope('l' + $route.params.slug.charAt(0).toLowerCase() + 'u')
          "
        />
      </v-col>
      <v-col cols="12">
        <TableTabs
          @getSelectedTabRecords="tabKeyGetter"
          :tabData="tabItems"
          :extraData="extraData"
        />
        <DataTable
          v-model="selectedItems"
          :headers="all_headers[0].selected_headers"
          :key="tableKey"
          :loading="apiCalling"
          @click:row="onRowClicked"
          :items="labels"
          @viewSelectedItem="viewSelectedLabel"
          :ItemsLength="getTotals(tabKey)"
          @onTablePaginate="onTableChanges"
          ref="dataTableRef"
        >
          <template v-slot:[`item.title`]="{ item }">
            <v-tooltip bottom max-width="800">
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on" style="white-space: nowrap">
                  <span v-if="item.title.length >= 90"
                    >{{ item.title.substring(0, 89) }} ...
                  </span>
                  <span v-else>{{ item.title.substring(0, 89) }} </span>
                </span>
              </template>
              <span>{{ item.title }} </span>
            </v-tooltip>
          </template>
          <template v-slot:[`item.sub_systems`]="{ item }">
            <v-tooltip bottom v-if="item.sub_systems">
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{ $tr("sub_systems") }}
                </span>
              </template>
              <span v-for="sub in item.sub_systems" :key="sub.id"
                >{{ sub.name }} <br
              /></span>
            </v-tooltip>
          </template>
          <template v-slot:[`item.label`]="{ item }">
            <div class="d-flex align-center">
              <div>{{ item.label }}</div>

              <svg
                id="Layer_1"
                height="13"
                class="ms-1"
                style="transform: scaleX(-1)"
                :fill="item.color"
                data-name="Layer 1"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 434.76793 294.202"
              >
                <path
                  d="M148.4,303.52l86.238,94.641a67.39788,67.39788,0,0,0,49.84,21.84h239.12a44.93217,44.93217,0,0,0,44.801-44.801l-.00391-201.6a44.93211,44.93211,0,0,0-44.801-44.801h-239.68a68.7035,68.7035,0,0,0-49.281,21.281l-86.242,92.957c-15.68,16.801-15.68,43.121,0,60.48Z"
                  transform="translate(-135.13112 -127.29898)"
                  style="
                    stroke: #fff;
                    stroke-miterlimit: 10;
                    stroke-width: 20px;
                  "
                />
              </svg>
            </div>
          </template>
        </DataTable>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import ProgressBar from "../../components/common/ProgressBar";
import PageHeader from "../../components/design/PageHeader";
import PageFilters from "../../components/design/PageFilters";
import PageActions from "../../components/design/PageActions";
import DataTable from "../../components/design/DataTable";
import Labels from "../../configs/pages/labels";
import CustomButton from "../../components/design/components/CustomButton.vue";
import { mapGetters, mapActions } from "vuex";
import Dialog from "../../components/design/Dialog/Dialog.vue";
import LabelProfile from "../../components/labels/LabelProfile.vue";
import RegisterLabel from "../../components/labels/RegisterLabel.vue";
import EditLabel from "../../components/labels/EditLabel.vue";
import HandleException from "../../helpers/handle-exception";
import CustomizeColumn from "../../components/customizeColumn/CustomizeColumn2.vue";
import labelFilter from "../../components/labels/labelFilter.vue";
import TableTabs from "~/components/common/TableTabs.vue";

import Alert from "../../helpers/alert";
import LabelStepper from "../../components/statusManagement/labelStepper/LabelStepper.vue";

export default {
  meta: {
    hasAuth: true,
    scope: ["lmv", "luv", "lcv"],
  },
  name: "labels",
  async asyncData({ store, params }) {
    // 	await store.dispatch("customColumns/fetchCustomColumns", {
    // 		view_name: "label_" + params.slug,
    // 	});
    // 	await store.dispatch("labels/fetchLabels", {
    // 		slug: params.slug,
    // 		key: "all",
    // 	});
    // 	//  options: {
    // 	//     ...this?.filterData,
    // 	//     type: this?.type,
    // 	//     content: this?.content,
    // 	//     contentData: this?.contentData,
    // 	//   },
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
    LabelProfile,
    RegisterLabel,
    EditLabel,
    CustomizeColumn,
    labelFilter,
    TableTabs,
    LabelStepper,
  },

  data() {
    return {
      tabs: [],
      selectedItems: [],
      tabItems: Labels(this).tabItems,

      breadcrumb:
        this.$route.params.slug === "user"
          ? Labels(this).breadcrumb_user
          : this.$route.params.slug === "master"
          ? Labels(this).breadcrumb_master
          : this.$route.params.slug === "content"
          ? Labels(this).breadcrumb_landing
          : this.$route.params.slug === "personal"
          ? Labels(this).breadcrumb_personal
          : this.$route.params.slug === "advertisement"
          ? Labels(this).breadcrumb_advertisement
          : [],

      registerDialog: false,
      key: 0,
      showProgressBar: false,
      type: 1,
      content: [],
      contentData: "",
      subSystems: [],
      labelCategory: [],

      // Page actions
      subsystemName: "",

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
      tableKey: 0,

      // Edit and Auto Edit
      editKey: 0,
      editItems: [],
      editDialog: false,
      isAutoEdit: false,

      // Profile
      showProfile: false,
      profileData: {},
      profileKey: 0,
      downloadDialog: false,

      // filter
      filterDialog: false,
      filterData: {},

      tabKey: "all",
      extraData: [],
    };
  },

  async validate({ params }) {
    this.subsystemName = `Labels (${params.slug})`;
    return !params.slug ? false : true;
  },

  computed: {
    // get labels from getters
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      getTotals: "labels/getTotal",
      labels: "labels/getLabels",
      apiCalling: "labels/isApiCalling",
    }),
  },
  async created() {
    const slug = this.$route.params.slug;
    await this.fetchHeaders();
    this.fetchLabels({
      slug: slug,
      key: "all",
    });
    if (process.client) {
      this.$root.$on("getData", (data) => {
        this.filterData = data;
      });
    }
  },

  watch: {},

  methods: {
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
      fetchLabels: "labels/fetchLabels",
      fetchLabelsByID: "labels/fetchLabelsByID",
    }),

    tabKeyGetter(key) {
      this.selectedItems = [];
      this.tabKey = key;
      this.getRecord();
    },

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "labels_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },
    async toggleRegisterDialog() {
      this.$refs.labelStepperRef.toggle();
    },

    // add or remove item from selected items
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    toggleDownload() {
      this.downloadDialog = !this.downloadDialog;
    },

    async viewSelectedLabel(item) {
      this.profileKey++;
      await this.fetchLabelCategory();
      if (this.subSystems.length === 0) {
        await this.fetchSubSystems();
      }
      this.profileData = this.selectedItems[0];
      this.profileKey++;
      this.profileData = item;
      this.profileData.status_transformations =
        this.$sortStatusTransformationData(item);
      this.showProfile = true;
    },

    async fetchSubSystems() {
      const element = [];
      try {
        const response = await this.$axios.get(
          `systems_sub_systems?system_name=${this.slug}`
        );
        if (response?.status === 200 && response?.data?.result) {
          const subs = response?.data?.data;
          let i = 0;
          for (let index = 0; index < subs.length; index++) {
            if (
              !(subs[index].name == "Labels" || subs[index].name == "Reasons")
            ) {
              element[i] = subs[index];
              i++;
            }
          }
          this.subSystems = element;
        } else {
          // this.$toastr.w(this.$tr("something_went_wrong"));
          this.$toasterNA("orange", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        if (error?.response?.status === 404 && !error?.response?.data?.result) {
          // this.$toastr.w(this.$tr("sub_system_not_found"));
          this.$toasterNA("orange", this.$tr("sub_system_not_found"));
        } else {
          HandleException.handleApiException(this, error);
        }
      }
    },

    async fetchLabelCategory() {
      try {
        const response = await this.$axios.get(`labels_category`);
        this.labelCategory = response.data;
      } catch (error) {
        if (error?.response?.status === 404 && !error?.response?.data?.result) {
          // this.$toastr.w(this.$tr("sub_system_not_found"));
          this.$toasterNA("orange", this.$tr("sub_system_not_found"));
        } else {
          HandleException.handleApiException(this, error);
        }
      }
    },

    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },

    async addLabel() {
      this.showProgressBar = true;
      if (this.subSystems.length === 0) {
        await this.fetchSubSystems();
      }

      await this.fetchLabelCategory();

      this.key++;
      this.selectedItems = [];
      this.registerDialog = true;
      this.showProgressBar = false;
    },
    async Edit() {
      if (this.selectedItems.length == 1) {
        this.$refs.labelStepperRef.toggle(this.selectedItems[0]);
      }
      // this.showProgressBar = true;
      // if (this.subSystems.length === 0) {
      //   await this.fetchSubSystems();
      // }
      // await this.fetchLabelCategory();
      // this.editKey++;
      // this.editDialog = true;
      // this.editItems[0] = this._.clone(this.selectedItems[0]);
      // this.showProgressBar = false;
    },

    async editSelectedLabel(item) {
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
      await this.fetchLabelCategory();

      this.editDialog = true;
      this.editKey++;
      this.isAutoEdit = true;
      this.editItems = this.selectedItems.map((row) => {
        return this._.clone(row);
      });
      this.showProgressBar = false;
    },

    async onView() {
      await this.fetchLabelCategory();
      if (this.subSystems.length === 0) {
        await this.fetchSubSystems();
      }
      this.profileData = this.selectedItems[0];
      this.profileData.status_transformations =
        this.$sortStatusTransformationData(this.selectedItems[0]);
      this.profileKey++;
      this.showProfile = true;
    },

    // customize columns: start=>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // called from chiled save column changes

    async onFilter() {
      if (this.subSystems.length === 0) {
        await this.fetchSubSystems();
      }
      this.filterDialog = true;
    },

    //customize columns: called from child

    // new search and filter functions
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },

    searchByContent(contentData) {
      this.contentData = contentData;
      this.getRecord();
    },

    onSearchTypeChange() {
      this.selectedItems = [];
      this.contentData = "";
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
        slug: this.slug,
        options: {},
      };
      if (
        !(
          this.filterData &&
          Object.keys(this.filterData).length === 0 &&
          Object.getPrototypeOf(this.filterData) === Object.prototype
        )
      )
        Object.assign(data.options, this.filterData);

      if (
        !(
          this.options &&
          Object.keys(this.options).length === 0 &&
          Object.getPrototypeOf(this.options) === Object.prototype
        )
      )
        Object.assign(data.options, this.options);

      if (this.contentData != "") data.options.contentData = this.contentData;

      if (this.searchId != "") data.options.content = this.searchId;

      return data;
    },
    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        var response = await this.fetchLabelsByID(options);
        this.selectData(response);
      } catch (error) {
        // HandleException.handleApiException(this, error);
      }
    },
    selectData(response) {
      if (response != null) {
        this.onRowClicked(response);
        this.selectedItems?.unshift(response);
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    async getRecord() {
      try {
        const data = this.fetchOptions();
        const data2 = await this.fetchLabels(data);
        this.extraData = data2.data;
      } catch (error) {
        if (error.response.status === 401) {
          redirect("/auth/signin");
        } else {
        }
      }
    },
  },
};
</script>
<style scoped>
/* .v-tooltip__content {
  font-size: 50px !important;
  opacity: 1 !important;
  display: block !important;
} */
.label-tooltip {
  background: var(--v-primary-base) !important;
  opacity: 1;
}

.custom-tooltip:hover + .custom-tooltip-content {
  min-width: 200px;
  min-height: 20px;
  height: auto;
  padding-bottom: 8px;
}

.custom-tooltip-content {
  border-radius: 10px;
  transition: all 0.4s;
  transition-duration: 100ms;
  transition-timing-function: ease;
  position: absolute;
  width: 0px;
  height: 0px;
  transform: translate(20%, -15%);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
  overflow: hidden;
}
</style>