<template>
  <v-container fluid class="pa-0 social_media">
    <v-row no-gutters>
      <ProgressBar v-if="showProgressBar" />
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
          :page_name="'social_media_all'"
          @closeColumnDialog="columnDialog = false"
        />
      </v-dialog>

      <v-dialog
        v-model="isEdit"
        persistent
        width="1300"
        scrollable
        v-if="$isInScope('smu')"
      >
        <SocialMediaEdit
          v-if="isEdit"
          :key="editKey"
          :editData="editData"
          @closeEdit="toggleEdit"
        />
      </v-dialog>
      <v-dialog
        v-model="autoEdit"
        persistent
        width="1300"
        scrollable
        max-height="800px"
        v-if="$isInScope('smu')"
      >
        <SocialMediaAutoEdit
          v-if="autoEdit"
          :key="editKey"
          :autoEditData="autoEditData"
          @closeEdit="toggleAutoEdit"
        />
      </v-dialog>
      <v-dialog
        v-model="registerDialog"
        persistent
        width="1300"
        scrollable
        v-if="$isInScope('smc')"
      >
        <Dialog @closeDialog="registerDialog = false">
          <SocialMediaRegistration />
        </Dialog>
      </v-dialog>

      <v-col cols="12">
        <PageHeader
          :Icon="`social_media`"
          Title="social_media"
          :Breadcrumb="breadcrumb"
        />
      </v-col>
      <v-col cols="12">
        <PageFilters
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="getItems"
          :downloadDialog="downloadDialog"
          :filename="$tr('social_media')"
          :showFilter="false"
          @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
          @onColumn="columnDialog = true"
        >
          <CustomButton
            @click="registerDialog = true"
            icon="fa-plus"
            :text="$tr('create_item', $tr('social_media'))"
            type="primary"
          />
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :showAutoEdit="$isInScope('smu')"
          :showDelete="$isInScope('smd')"
          :showBlock="$isInScope('smu')"
          :showSelect="$isInScope('smu')"
          :showApply="$isInScope('smu')"
          :tab-key.sync="tabKey"
          @onEdit="onEdit"
          :showView="false"
          :selectedItems.sync="selectedItems"
          @onAutoEdit="onAutoEdit"
          :showEdit="$isInScope('smu')"
          :routeName="'social_media'"
          :subSystemName="'Social Media'"
          @fetchNewData="fetchNewData"
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
          ref="socialTableRef"
          :items="getItems"
          :ItemsLength="getTotals(getTabKey())"
          @select="onItemsSelect"
          @onTablePaginate="onTableChanges"
        >
          <template v-slot:[`item.code`]="{ item }">
            <span class="mx-1 font-weight-bold">
              {{ item.code }}
            </span>
          </template>
          <template v-slot:[`item.website`]="{ item }">
            <span class="primary--text">
              <a :href="item.website" target="_blank"> {{ item.website }} </a>
            </span>
          </template>
        </DataTable>
        <h1></h1>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import HandleException from "../../helpers/handle-exception";
import { mapActions, mapGetters } from "vuex";
import ProgressBar from "../../components/common/ProgressBar";
import PageHeader from "../../components/design/PageHeader";
import PageFilters from "../../components/design/PageFilters";
import PageActions from "../../components/design/PageActions";
import DataTable from "../../components/design/DataTable";
import SocialMedia from "../../configs/pages/social_media";
import CustomButton from "../../components/design/components/CustomButton";
import CloseDialogButton from "../../components/common/buttons/CloseDialogButton";
import TextField from "../../components/common/TextField";
import AutoComplete from "../../components/common/AutoComplete";
import SocialMediaRegistration from "../../components/masters/social_media/SocialMediaRegistration.vue";
import SocialMediaEdit from "../../components/masters/social_media/SocialMediaEdit.vue";
import SocialMediaAutoEdit from "../../components/masters/social_media/SocialMediaAutoEdit.vue";
import Dialog from "../../components/design/Dialog/Dialog";
import TableTabs from "~/components/common/TableTabs.vue";

import CustomizeColumn from "../../components/customizeColumn/CustomizeColumn2.vue";
export default {
  meta: {
    hasAuth: true,
    scope: "smv",
  },
  // functionality moved to created function
  // async async Data({ store }) {
  // 	await store.dispatch("social_media/fetchItems", { key: "all" });
  // },

  name: "SocialMedia",
  components: {
    Dialog,
    AutoComplete,
    TextField,
    CloseDialogButton,
    CustomButton,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    ProgressBar,
    SocialMediaRegistration,
    SocialMediaEdit,
    SocialMediaAutoEdit,
    CustomizeColumn,
    TableTabs,
  },

  data() {
    return {
      tabItems: SocialMedia(this).tabItems,

      breadcrumb: SocialMedia(this).breadcrumb,

      registerDialog: false,

      selectedItems: [],
      tabIndex: 0,
      tableKey: 0,
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
      downloadDialog: false,
      options: {},
      searchId: "",
      searchContent: "",

      autoEdit: false,
      autoEditData: [],
      teamEdit: false,
      systemEditKey: 0,
      editData: {},

      // Single Edit
      isEdit: false,
      editData: {},
      editKey: 0,

      tabKey: "all",
      extraData: [],
    };
  },

  computed: {
    ...mapGetters({
      getItems: "social_media/getItems",
      getTotals: "social_media/getTotal",
      apiCalling: "social_media/isApiCalling",
    }),
  },
  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },
  async created() {
    await this.fetchHeaders();
    this.getRecord();
    this.$root.$on("closeAutoEdit", this.toggleAutoEdit);
    this.$root.$on("closeEdit", this.toggleEdit);
  },

  methods: {
    ...mapActions({
      fetchItems: "social_media/fetchItems",
      insertNewItem: "social_media/insertNewItem",
      deleteItem: "social_media/removeItem",
      insertNewItem: "social_media/insertNewItem",
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
              tab_name: "social_media_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    checkSelectedTab(value) {
      this.tableKey++;
      this.selectedItems = [];
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    // return table tab key
    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
    },
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    onItemsSelect(items) {
      this.selectedItems = items;
    },

    // 	***********************			No changes required		************************
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },

    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
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

    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.deleteItem(data.id);
          this.insertNewItem(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
    },

    fetchOptions() {
      let data = {
        key: this.tabKey,
      };

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;

      return data;
    },

    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },
    // 	***********************			No changes required		************************

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post(
          "social_media/searchSocialMedia",
          options
        );
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
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

    onAutoEdit() {
      this.editKey++;
      this.autoEditData = this.selectedItems;
      this.autoEdit = !this.autoEdit;
    },
    toggleAutoEdit() {
      this.editKey++;
      this.autoEdit = !this.autoEdit;
      if (!this.isEdit) {
        this.selectedItems = [];
      }
    },
    onEdit() {
      if (this.selectedItems.length == 1) {
        this.editKey++;
        this.editData = this.selectedItems[0];
        this.isEdit = !this.isEdit;
      } else {
        // this.$toastr.i(this.$tr("please_select_a_record_first"));
				 this.$toasterNA("primary", this.$tr("please_select_a_record_first"));

      }
    },
    toggleEdit() {
      this.editKey++;
      this.isEdit = !this.isEdit;
      if (!this.isEdit) {
        this.selectedItems = [];
      }
    },
  },
};
</script>

<style>
.social_media a,
a:hover,
a:focus,
a:active {
  text-decoration: none !important;
  color: inherit !important;
}
</style>
