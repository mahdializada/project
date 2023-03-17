<template>
	<!-- main components in a page -->
	<v-container fluid class="pa-0">
		<CategoryStepper ref="CategoryStepper" @pushRecord="pushRecord" />
		<categoryUpdateStepper ref="categoryUpdateStepper" @updateRecord="updateRecord"/>
    <CategoryProfileView ref="categoryViewProfileRef"/>
		<CategoriesFilter
			ref="CategoriesFilterRef"
			@filterRecords="onFilterRecords"
		/>
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
				:page_name="'categories_all'"
				@closeColumnDialog="columnDialog = false"
			/>
		</v-dialog>

    <client-only>
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`catogories`"
            :Title="breadcrumb[1].text"
            :Breadcrumb="breadcrumb"
          >
          </PageHeader>
        </v-col>

        <!--start  page filter  -->
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="tableValues"
          :downloadDialog="downloadDialog"
          :filename="$tr('categories')"
          :show-add-note="true"
          @onFilter="openFilterDialog"
          :showDownload="$isInScope('adve')"
          note-title="add_categories_ssp"
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
        >
          <CustomButton
            v-if="$isInScope('advc')"
            icon="fa-plus"
            @click="openInsertDialog"
            :text="$tr('create_item', $tr('category'))"
            type="primary"
          />
        </PageFilters>
        <!--end  page filter  -->

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('advv')"
            :showEdit="true"
            :showAutoEdit="false"
            :showRestore="showRestore"
            :showDelete="$isInScope('advd')"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            :showBlock="false"
            :statusItems="statusItems"
            :routeName="'single-sales/categories-ssp'"
            :subSystemName="'Categories'"
            :noReasonSubmitOperations="'active,inactive, removed'"
            @fetchNewData="fetchNewData"
            @onEdit="onEditCategory"
            @onView="toggleViewDialog"
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
            :extraData="extraData"
          />
          <DataTable
            ref="projectTableRef"
            :headers="all_headers[0].selected_headers"
            :items="tableValues"
            v-model="selectedItems"
            :ItemsLength="extraData.total"
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

            <template v-slot:[`item.description`]="{ item }">
              <v-tooltip bottom max-width="200">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="mx-auto d-flex justify-center">
                      {{ item.description?.substring(0, 30) + " ..." }}
                    </span>
                  </span>
                </template>

                {{ item.description }}
              </v-tooltip>
            </template>
            <template v-slot:[`item.attributes`]="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    {{ $tr("attributes") }}
                  </span>
                </template>
                <span v-for="attributes in item.attributes" :key="attributes.id"
                  >{{ $tr(attributes.name) }} <br
                /></span>
              </v-tooltip>
            </template>
          </DataTable>
        </v-col>
        <progress-bar v-if="isFetchCancels" />
      </v-row>
    </client-only>
  </v-container>
</template>

<script>
import { mapMutations } from "vuex";

import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import PageActions from "~/components/design/PageActions.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import categories_ssp from "~/configs/pages/categories_ssp";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";

import TableTabs from "~/components/common/TableTabs.vue";
import CategoriesFilter from "~/components/single_sales_management_system/CategoriesFilter.vue";
import CategoryUpdateStepper from "../../components/single_sales_management_system/categories/CategoryUpdateStepper.vue";
import CategoryStepper from "../../components/single_sales_management_system/categories/CategoryStepper.vue";
import CategoryProfileView from "../../components/single_sales_management_system/categories/CategoryProfileView.vue";

export default {
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
    CategoriesFilter,
    CustomizeColumn,
    CategoryUpdateStepper,
    CategoryStepper,
    CategoryProfileView
},
	data() {
		return {
			tabKey: "all",
			tableValues: [],

      tabItems: categories_ssp(this).tabItems,
      breadcrumb: categories_ssp(this).breadcrumb,

      selectedItems: [],
      tableKey: 0,
      show: false,
      tabIndex: 0,
      options: {},
      extraData: {},
      showRestore: false,
      //register section
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
      // download dialog
      downloadDialog: false,
      // search
      searchId: "",
      searchContent: "",
      filterData: {},

      editCategory: false,
    };
  },

  watch: {
    tabKey: function (index) {
      this.checkSelectedTab(index);
    },
  },

  async created() {
    //customize columns
    await this.fetchHeaders();
    this.checkSelectedTab(this.tabKey);
  },

  async fetch() {
    await this.getRecord();
  },

  methods: {
    ...mapMutations({ setEditData: "single_sales/SET_EDIT_CATEGORIES" }),
    getColor(status) {
      console.log(status);
      if (status == "active") return "#2ebce6";
      else if (status == "inactive") return "#e6af2e";
      else return "#e83c59";
    },
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "categories_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },
    toggleViewDialog() {
      console.log(this.selectedItems);
      if (this.selectedItems.length == 1) {
        const item = this.selectedItems[0]
        this.$refs.categoryViewProfileRef.showDialog(item);
      }
    },

		fetchNewData() {
			this.selectedItems = [];
			this.getRecord();
		},
		viewSelectedDesignRequest(item) {
			this.showProfile = true;
		},
		async pushRecord(data) {
			if (this.tabKey === "all" || this.tabKey === "inactive")
				await this.tableValues.unshift(data);
			this.extraData.allTotal += 1;
			this.extraData.inactiveTotal += 1;
			this.extraData.total += 1;
		},
		updateRecord(updateData){
			this.tableValues = this.tableValues.map((item)=>{
				if(item.id == updateData.id){
					return updateData;
				}
				return item;
			})
		},
		async getRecord() {
			try {
				const data = this.$fetchOptions(
					this.tabKey,
					this.filterData,
					this.options,
					this.searchContent,
					this.searchId
				);
				// console.log("asdfgsdf", data);
				// data.fetch_for_form = true;
				this.apiCalling = true;
				const response = await this.$axios.get("single-sales/categories-ssp", {
					params: data,
				});
				console.log("get categories", response);
				if (response.status == 200) {
					// this.tableValues = response?.data;
					this.tableValues = response?.data?.data;
					this.extraData = response?.data;
					// console.log("ex data", this.extraData);
				}
				this.apiCalling = false;
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
		setStatusItems(status) {
			switch (status) {
				case "active":
					this.statusItems = [{ id: "inactive", name: "inactive" }];
					this.showRestore = false;
					break;
				case "inactive":
					this.statusItems = [{ id: "active", name: "active" }];
					this.showRestore = false;
					break;
				case "removed":
					this.statusItems = [];
					this.showRestore = true;
					break;
				default:
					this.statusItems = [];
					this.showRestore = false;
					break;
			}
		},
		checkSelectedTab(value) {
			// this.tableKey++;    // Commented due to pagination bug
			this.selectedItems = [];
			let currentTab = "";
			this.tabItems = this.tabItems.map((item) => {
				item.key === value
					? ((item.isSelected = 1), (currentTab = item.key))
					: (item.isSelected = 0);
				return item;
			});
			this.setStatusItems(currentTab);
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
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.$fetchOptions(
          this.tabKey,
          this.filterData,
          this.options,
          this.searchContent,
          this.searchId
        );
        const response = await this.$axios.post(
          "single-sales/categories/search",
          options
        ); // Change the route
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

    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.tableValues = this.tableValues.filter(
            (row) => row.id !== data.id
          );
          this.tableValues.unshift(data);
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

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.getRecord();
    },

    openFilterDialog() {
      this.$refs.CategoriesFilterRef.changeModel();
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
    },
    openInsertDialog() {
      this.$refs.CategoryStepper.show = true;
    },

    async onEditCategory() {
      if (this.selectedItems.length != 1) {
        //  console.log('jjust select one item',this.selectedItems);
        // this.$toastr.w(this.$tr("please_select_one_option"));
        this.$toasterNA("orange", this.$tr("please_select_one_option"));

        return;
      }
      await this.setEditData(this.selectedItems[0]);
      this.editCategory = true;

      this.$refs.categoryUpdateStepper.show = true;
      this.$refs.categoryUpdateStepper.onEditategory(this.selectedItems[0]);
    },
  },
};
</script>

<style></style>
