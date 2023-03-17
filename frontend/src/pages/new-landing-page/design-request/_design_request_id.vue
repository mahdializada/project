<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0">
    <client-only>
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="breadcrumb[1].icon"
            :Title="breadcrumb[1].text"
            :Breadcrumb="breadcrumb"
          >
          </PageHeader>
        </v-col>

        <!--start  page filter  -->

        <!--end  page filter  -->

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
            :statusItems="statusItems"
            :routeName="'design-request'"
            :subSystemName="'Design Request'"
            :noReasonSubmitOperations="'in storyboard, storyboard review, in design, design review, in programming, completed'"
          />
        </v-col>
        <v-col cols="12">
          <TableTabs
            @getSelectedTabRecords="
              (key) => {
                tabKey = key;
                getRecord();
              }
            "
            :tabData="tabItems"
            :extraData="'countries/getTotal'"
          />

          <DataTable
            ref="projectTableRef"
            :headers="selected_header"
            :items="design_request_data"
            :ItemsLength="getTotals(tabItems[tabIndex].key)"
            v-model="selectedItems"
            :loading="apiCalling"
            @onTablePaginate="onTableChanges"
            @click:row="onRowClicked"
            :item-class="completedStyle"
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
import PageActions from "~/components/design/PageActions.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import new_landing_page from "~/configs/pages/new_landing_page";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn.vue";
import ColumnHelper from "~/helpers/column-helper";
import TableTabs from "~/components/common/TableTabs.vue";

export default {
  	asyncData ({ redirect }) {
    return redirect('/not-found')
  },
  components: {
    CustomPageActions,
    PageActions,
    CustomButton,
    DesignRequestEdit,
    PageHeader,
    PageFilters,
    DataTable,
    ProgressBar,
    TableTabs,
  },
  data() {
    return {
      tabKey: "all",
      design_request_data: [],
      headers: new_landing_page(this).headers,
      tabItems: this.$getTabItems(["all", "active", "inactive", "blocked"]),
      breadcrumb: new_landing_page(this).breadcrumb,
      selectedItems: [],
      tableKey: 0,
      tabIndex: 0,
      options: {},
      //register section

      statusItems: [],

      // profile section
      showProfile: false,

      // for custom columns
      selected_header: [],
      apiCalling: false,
      isFetchCancels: false,

      showProgressBar: false,
    };
  },

  computed: {
    ...mapGetters({
      getTotals: "countries/getTotal",
    }),
  },

  methods: {
    viewSelectedDesignRequest(item) {
      this.showProfile = true;
    },
    getRecord() {
      return [];
    },

    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    completedStyle(item) {
      return item.status == "completed" ? "completedAnimation" : "";
    },

    onTableChanges(options) {
      return 1;
    },
  },
};
</script>

<style>
</style>