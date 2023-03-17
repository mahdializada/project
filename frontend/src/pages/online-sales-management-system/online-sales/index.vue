<template>
  <v-container fluid class="pa-0">
    <ReasonDialogNew
      ref="reasonRefs"
      :sub_system="'Online Sales'"
      :submitFunction="submitReasons"
    />
    <DesignRequestOperation ref="designRequestOperationRef" />
    <OtpCodeForm
      ref="otpCodeRefs"
      submit_url="online-sales-product"
      @onSuccess="deleteItemCode(true)"
      :subsystem="'online_sales'"
    />

    <OnlineSalesStepper ref="OnlineSalesStepperRef" @pushRecord="pushRecord" />
    <RemarkForm
      ref="remarkRef"
      sub_system="online_sales"
      :tab="currentTab.key"
      @addRemark="addRemark"
      :selected_items.sync="currentTab.selectedItems"
    />
    <ProductProfileInfoStepper
      ref="productCreationRefs"
      @addProductProfile="addProductProfile"
    />
    <ShowProductProfileInfoImage ref="ShowProductProfileInfoImageRef" />

    <v-dialog v-model="columnDialog" width="1300" persistent>
      <CustomizeColumn2
        v-if="columnDialog"
        @saveChanges="
          (data) => {
            all_headers[selectedIndex] = data;
            columnDialog = false;
          }
        "
        :page_headers.sync="all_headers[selectedIndex]"
        :page_name="'online_sales' + currentTab.key"
        @closeColumnDialog="columnDialog = false"
        :key="selectedIndex"
      />
    </v-dialog>
    <OnlineSalesFilter
      ref="OnlineSalesFilterRef"
      @filterRecords="FilterStoreData"
      :currentTab="currentTab.key"
    />
    <ChangeItemCodeStatus
      style="z-index: 10000"
      @changeStatus="changeItemStatus"
      ref="changeItemStatusRef"
    />

    <OnlineSalesProfile
      ref="onlineProfileRefs"
      @changeStatus="toggleItemStatus"
      :current_tab.sync="currentTab.key"
      @updateAttachment="updateOnlineSalesAttachments"
    ></OnlineSalesProfile>
    <AdvertisementGraphProfile
      ref="advertisementRefs"
      :selected_item.sync="currentTab.selectedItems"
      :date_range.sync="filterByDate"
      :current_tab.sync="currentTab.key"
    />

    <GenerateConnection v-if="$isInScope('osc')" ref="genereteConnectionRef" />
    <LabelForm
      ref="labelRef"
      subsystem_name="online_sales"
      :model_name="currentTab.key"
      :selected_item.sync="currentTab.selectedItems"
      @applyLabelChanges="applyLabelChanges($event)"
    />

    <client-only>
      <!-- <MultiSelectionStatusReason
          ref="multiSelectionStatusReasonRef"
          :currentTab="currentTab"
          @MultiStatusChange="MultiStatusChange"
        /> -->
      <v-row no-gutters>
        <v-col cols="12">
          <PageHeader
            Icon="advertisement_management_system"
            :Title="breadcrumb[1].text"
            :Breadcrumb="breadcrumb"
            :show_ad_img="true"
          >
          </PageHeader>
        </v-col>
        <v-col cols="12">
          <PageFilters
            ref="pageFilterRef"
            :selected_header.sync="all_headers[0].selected_headers"
            :show_graph="true"
            :downloadContent="tableRecords"
            :downloadDialog="downloadDialog"
            :filename="'online sales -' + currentTab.key"
            note-title=""
            @onFilter="openFilterDialog"
            :showBulkUpload="false"
            :showDownload="$isInScope('ose')"
            @searchById="searchById"
            @onDownload="downloadDialog = !downloadDialog"
            @clearAndUnselectId="clearAndUnselectId"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            @onColumn="columnDialog = true"
            @onTab="tabDialog = true"
            :showTab="true"
            @onProfile="showOnlineSalesProfile()"
            :selected_item.sync="currentTab.selectedItems"
            :custom_search="true"
          >
            <CustomButton
              v-if="$isInScope('osc')"
              icon="fa-plus"
              @click="openInsertDialog()"
              :text="$tr('create_item', $tr('sales'))"
              type="primary"
            />
          </PageFilters>
        </v-col>

        <v-col cols="12">
          <CustomPageActions
            :showView="false"
            :showEdit="false"
            :showAutoEdit="false"
            :showDelete="false"
            :ignoreReason="false"
            :showSelect="false"
            :showApply="false"
            :showAssign="false"
            :showApprove="false"
            :showUnAssign="false"
            :selectedItems.sync="currentTab.selectedItems"
            :tab-key="'not'"
            :showBlock="false"
            :showRestore="false"
            :statusItems="[]"
            :full="true"
          >
            <template slot="afterDelete">
              <div class="d-flex">
                <div
                  id="scroll-box2"
                  class="d-flex align-center px-1"
                  style="
                    overflow-x: hidden;
                    scroll-behavior: smooth;
                    width: 100%;
                  "
                >
                  <!-- toggleBalanceModal -->
                  <!-- v-if="
                        currentTab.key == 'item_code' &&
                        !currentTab?.selectedItems[0]?.product_product_info?.id &&
                        currentTab.selectedItems.length > 0
                      " -->
                  <CustomButton
                    @click="addProductInfo()"
                    v-if="currentTab.key == 'item_code'"
                    icon="fa-plus"
                    text="Product Details"
                    type="primary"
                    :class="
                      currentTab.selectedItems.length == 0 ||
                      currentTab.selectedItems[0]?.product_info?.id
                        ? 'customDisabled'
                        : ''
                    "
                  />

                  <PopOver
                    v-model="showLabelPopOver"
                    color="#2dccff"
                    :position="true"
                  >
                    <template v-slot:activator>
                      <CustomButton
                        @click="toggleLabelFom()"
                        icon="fa-tag"
                        text="label"
                        type="label"
                      />
                    </template>
                    <template v-slot:body>
                      <div>
                        {{ lableRemarkErrMessage }}
                      </div>
                    </template>
                  </PopOver>

                  <PopOver
                    v-model="showRemarkPopOver"
                    color="#ff0070"
                    :position="true"
                  >
                    <template v-slot:activator>
                      <CustomButton
                        @click="toggleRemarkForm()"
                        icon="fa-comment-alt"
                        text="remark"
                        type="remark"
                      />
                    </template>
                    <template v-slot:body>
                      <div>
                        {{ lableRemarkErrMessage }}
                      </div>
                    </template>
                  </PopOver>
                  <!-- <CustomButton
                      @click="toggleSelectedStatus"
                      icon="fa-toggle-off"
                      text="Toggle Status"
                      type="danger2"
                    /> -->
                  <CustomButton
                    @click="toggleItemStatus"
                    v-if="currentTab.key == 'item_code'"
                    :class="
                      currentTab?.selectedItems?.length == 0
                        ? 'customDisabled'
                        : ''
                    "
                    icon="fa-toggle-off"
                    text="Item Status"
                    type="refresh"
                  />
                  <CustomButton
                    :class="`${
                      currentTab?.selectedItems?.length > 0
                        ? ''
                        : 'customDisabled'
                    }`"
                    @click="deleteItemCode()"
                    icon="fa-trash"
                    text="delete"
                    type="danger2"
                    v-if="currentTab.key == 'item_code'"
                  />
                  <CustomButton
                    v-if="currentTab.key == 'item_code'"
                    @click="openDesignRequestDialog()"
                    :class="`${
                      currentTab?.selectedItems?.length > 0
                        ? ''
                        : 'customDisabled'
                    }`"
                    :text="$tr('create_item', $tr('design_request'))"
                    icon="fa-plus"
                    type="primary"
                  />
                </div>
              </div>
            </template>
          </CustomPageActions>
        </v-col>

        <v-col cols="12">
          <v-row class="mx-0">
            <v-col cols="12 pa-0 position-relative">
              <AdvertisementTab
                @prev_index="prevIndex($event)"
                :items="tabItems"
                @onChange="onTabChange"
                @unselect="onUnselected"
                :totalRecords="totalRecords"
              />
              <OnlineSalesItemCodeTab
                v-show="currentTab.key == 'item_code'"
                :tabs="itemCodeTabCount"
                subsystem="online_sales"
                @onChange="onItemCodeTabChange($event)"
                ref="OnlineSalesItemCodeTabRef"
              />
              <div
                v-if="sumWithInitialRight"
                class="right position-absolute"
                style="top: -58px; right: 0px"
              >
                <div class="position-relative" style="width: 100px">
                  <v-chip
                    class="ma-2 position-absolute d-flex align-center chip6"
                    style="width: 65%; z-index: 20; right: 10px"
                    color="primary"
                    text-color="white"
                  >
                    <span>{{ sumWithInitialRight }}</span>
                    <v-icon size="15" @click="clearRight">mdi-close</v-icon>
                  </v-chip>
                  <span class="position-absolute rightTab"></span>
                </div>
              </div>
            </v-col>
          </v-row>

          <DataTable
            ref="adverTableRef"
            :headers.sync="all_headers[selectedIndex].selected_headers"
            :items="tableRecords"
            :ItemsLength="totalRecords"
            :itemsPerPage="20"
            height="700px"
            @onTablePaginate="onTableChanges"
            v-model="currentTab.selectedItems"
            :loading="apiCalling"
            @click:row="onRowClicked"
            :useDefaltStatus="false"
          >
            <!----------    DATATABLE SYSTEMS SECTION        ---------->

            <template v-slot:[`item.code`]="{ item }">
              <span class="mx-1 primary--text font-weight-bold">
                {{
                  item?.country?.code ||
                  item?.company?.code ||
                  item?.project?.code ||
                  item.code
                }}
              </span>
            </template>

            <template v-slot:[`item.status`]="{ item }">
              <v-switch
                @click.stop="changeRecordStatus(item)"
                :input-value="item?.current_status?.length > 0"
                inset
                :color="primaryColor"
                :loading="status_loading"
                hide-details
                readonly
                class="mt-0"
              >
              </v-switch>
            </template>

            <template v-slot:[`item.country`]="{ item }">
              <div class="d-flex">
                <FlagIcon
                  :flag="item.country?.iso2?.toLowerCase()"
                  :round="true"
                />
                <span style="padding: 4px">{{ item.country?.name }}</span>
              </div>
            </template>
            <template v-slot:[`item.company`]="{ item }">
              <div class="d-flex">
                <span style="white-space: nowrap" class="mb-1">
                  <v-img
                    class="rounded-circle"
                    width="25"
                    height="25"
                    lazy-src="https://picsum.photos/id/11/10/6"
                    :src="item.company?.logo"
                  ></v-img>
                </span>
                <span style="padding: 4px">{{ item.company?.name }}</span>
              </div>
            </template>

            <template v-slot:[`item.view_profile`]="{ item }">
              <span
                class="d-flex align-center justify-center"
                style="width: 24px"
              >
                <svg
                  width="30.059"
                  height="30.059"
                  viewBox="0 0 30.059 30.059"
                  @click.stop="showOnlineSalesProfile(item)"
                >
                  <path
                    id="graph"
                    d="M20.02,30.059H10.04A10.051,10.051,0,0,1,0,20.02V10.04A10.051,10.051,0,0,1,10.04,0h9.98A10.051,10.051,0,0,1,30.059,10.04v9.98A10.051,10.051,0,0,1,20.02,30.059Zm-9.4-13.8h0c.366.367.719.72,1.062,1.063.771.771,1.5,1.5,2.209,2.229A1.533,1.533,0,0,0,15,20.078,1.506,1.506,0,0,0,15.461,20a2.328,2.328,0,0,0,.832-.589c.985-.969,1.951-1.937,2.974-2.962l.012-.012,1.174-1.175.283.313c.335.37.65.72.969,1.064a1.219,1.219,0,0,0,.892.479,1.068,1.068,0,0,0,.4-.084,1.118,1.118,0,0,0,.7-1.161V14.446c0-1.166,0-2.372,0-3.568,0-.6-.286-.9-.874-.9H22.82c-.753,0-1.554.006-2.45.006-.926,0-1.842,0-2.616,0a1.086,1.086,0,0,0-1.1.672,1.02,1.02,0,0,0,.323,1.244c.333.312.677.614,1.01.907l.4.357-3.416,3.406L14,15.6l-.016-.016c-.692-.7-1.406-1.417-2.112-2.118a1.718,1.718,0,0,0-1.191-.6,1.763,1.763,0,0,0-1.213.613c-.5.5-1.012,1.005-1.5,1.5l-.007.007-.651.653c-.666.666-.96.973-.956,1.295s.313.633,1.013,1.3c.327.312.533.433.733.433s.411-.129.719-.447c.419-.43.815-.869,1.234-1.333.184-.2.37-.41.561-.618Z"
                    fill="#2d7ae5"
                  />
                </svg>
              </span>
            </template>
            <template v-slot:[`item.item_status`]="{ item }">
              <div class="text-center">
                <v-menu offset-y>
                  <template v-slot:activator="{}">
                    <v-btn
                      :color="item?.item_status?.color ?? 'primary'"
                      small
                      dark
                      width="120px"
                    >
                      {{
                        $tr(item?.item_status?.item_status ?? "select_status")
                      }}
                      <v-spacer></v-spacer>
                    </v-btn>
                  </template>
                </v-menu>
              </div>
            </template>

            <template v-slot:[`item.labels`]="{ item }">
              <v-menu
                open-on-hover
                right
                offset-x
                v-if="item.labels.length > 0"
                nudge-right="15px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <span v-bind="attrs" v-on="on" class="">
                    <template v-for="(label, i) in item.labels">
                      <v-avatar
                        v-if="i < 3"
                        :key="i"
                        size="27"
                        color="white"
                        class="ml-n1"
                      >
                        <!-- @click.stop="toggleHistory(item, 'label')" -->
                        <v-avatar
                          size="25"
                          :color="label.color"
                          class="white--text text-uppercase"
                          >{{ label.label.charAt(0) }}</v-avatar
                        >
                      </v-avatar>
                    </template>
                    <span v-if="item.labels.length > 3">
                      +{{ item.labels.length - 3 }}
                    </span>
                  </span>
                </template>
                <v-list>
                  <v-list-item
                    v-for="(lab, index) in item.labels"
                    :key="index"
                    dense
                  >
                    <v-list-item-icon>
                      <v-avatar
                        size="22"
                        :color="lab.color"
                        class="white--text text-center text-uppercase"
                        >{{ lab.label.charAt(0) }}</v-avatar
                      >
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title class="custom-card-title-2">{{
                        lab.label
                      }}</v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
            <template v-slot:[`item.iiu`]="{ item }">
              <span class="mx-1 primary--text font-weight-bold">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="17.836"
                  height="19.26"
                  viewBox="0 0 17.836 19.26"
                >
                  <path
                    id="product_info_icon"
                    d="M-5460.13-4474.21a5.5,5.5,0,0,1,5.4-5.5,5.483,5.483,0,0,1,5.562,5.469,5.5,5.5,0,0,1-5.364,5.506h-.044A5.488,5.488,0,0,1-5460.13-4474.21Zm2.988.074c-.281.271-.244.629.111.992.322.328.647.654.977.977a.673.673,0,0,0,1.114-.005q1.095-1.086,2.181-2.181a7.365,7.365,0,0,0,.587-.617.543.543,0,0,0-.052-.772.577.577,0,0,0-.776-.073,1.985,1.985,0,0,0-.3.271l-2.237,2.244c-.215-.246-.4-.478-.609-.69a.884.884,0,0,0-.607-.308A.549.549,0,0,0-5457.143-4474.136Zm-5.5,5.2a3.355,3.355,0,0,1-3.354-3.354v-11.353a3.354,3.354,0,0,1,3.354-3.354h7.741a3.354,3.354,0,0,1,3.354,3.354v2.392a7.647,7.647,0,0,0-3.1-.65,7.676,7.676,0,0,0-7.676,7.675,7.655,7.655,0,0,0,2.114,5.29Z"
                    transform="translate(5466.5 4487.5)"
                    :fill="item?.product_info?.id ? '#00bf19' : 'red'"
                    stroke="rgba(0,0,0,0)"
                    stroke-width="1"
                  />
                </svg>
              </span>
            </template>

            <template v-slot:[`item.prod_available_quantity`]="{ item }">
              {{ $tr(item.product_info?.prod_available_quantity) }}
            </template>
            <template v-slot:[`item.prod_cost`]="{ item }">
              {{ $tr(item.product_info?.prod_cost) }}
            </template>
            <template v-slot:[`item.prod_import_source`]="{ item }">
              <span v-if="!item.product_info?.prod_import_source">
                {{ "N/A" }}
              </span>
              <span v-else>
                <span
                  v-for="(source, index) in item.product_info
                    ?.prod_import_source"
                  :key="index"
                  class="text-uppercase"
                >
                  {{ source }}
                  {{
                    index != item.product_info?.prod_import_source?.length - 1
                      ? ", "
                      : ""
                  }}
                </span>
              </span>
            </template>
            <template v-slot:[`item.prod_new_product_source`]="{ item }">
              {{ $tr(item.product_info?.prod_new_product_source ?? "N/A") }}
            </template>
            <template v-slot:[`item.prod_max_adver_cost`]="{ item }">
              {{ $tr(item.product_info?.prod_max_adver_cost) }}
            </template>
            <template v-slot:[`item.prod_production_type`]="{ item }">
              {{ $tr(item.product_info?.prod_production_type) }}
            </template>
            <template v-slot:[`item.prod_profit`]="{ item }">
              {{ $tr(item.product_info?.prod_profit) }}
            </template>
            <template v-slot:[`item.prod_profitability`]="{ item }">
              {{ $tr(item.product_info?.prod_profitability) }}
            </template>
            <template v-slot:[`item.prod_sale_goal`]="{ item }">
              {{ $tr(item.product_info?.prod_sale_goal) }}
            </template>
            <template v-slot:[`item.prod_sales_stability`]="{ item }">
              {{ $tr(item.product_info?.prod_sales_stability) }}
            </template>
            <template v-slot:[`item.prod_section`]="{ item }">
              {{ $tr(item.product_info?.prod_section) }}
            </template>
            <template v-slot:[`item.prod_source`]="{ item }">
              {{ $tr(item.product_info?.prod_source) }}
            </template>
            <template v-slot:[`item.prod_style`]="{ item }">
              {{ $tr(item.product_info?.prod_style) }}
            </template>

            <template v-slot:[`item.prod_work_type`]="{ item }">
              {{ $tr(item.product_info?.prod_work_type) }}
            </template>
            <template v-slot:[`item.state`]="{ item }">
              {{ $tr(item.product_info?.state) }}
            </template>

            <template v-slot:[`item.product_images`]="{ item }">
              <span style="white-space: nowrap" class="mb-1">
                <v-img
                  class="rounded"
                  :key="item?.product_info?.attachments?.length"
                  width="30"
                  height="30"
                  @click.stop="
                    showProductProfileInfo(item?.product_info?.attachments)
                  "
                  lazy-src="/images/Group 2721.png"
                  :src="
                    item?.product_info?.attachments?.length > 0
                      ? item?.product_info?.attachments[0]?.path
                      : ''
                  "
                ></v-img>
              </span>
            </template>
            <template
              v-slot:[`item.cc`]="{ item }"
              v-if="
                tabItems[0]?.selectedItems?.length > 0 ||
                tabItems[1]?.selectedItems?.length > 0
              "
            >
              <span style="white-space: nowrap" class="mb-1">
                <v-avatar
                  size="27"
                  color="white"
                  class="ml-n1"
                  v-if="tabItems[0]?.selectedItems?.length > 0"
                >
                  <v-avatar size="25" class="white--text text-uppercase">
                    <FlagIcon
                      :flag="item?.country?.iso2?.toLowerCase()"
                      :round="true"
                    />
                  </v-avatar>
                </v-avatar>
                <v-avatar
                  size="27"
                  class="ml-n1"
                  v-if="tabItems[1]?.selectedItems?.length > 0"
                >
                  <v-avatar size="25" class="white--text text-uppercase">
                    <v-img
                      class="rounded-circle"
                      width="30"
                      height="30"
                      lazy-src="https://picsum.photos/id/11/10/6"
                      :src="item?.company?.logo"
                    ></v-img>
                  </v-avatar>
                </v-avatar>
              </span>
            </template>
          </DataTable>
        </v-col>
      </v-row>
    </client-only>
  </v-container>
</template>
  <script>
import { mapActions, mapGetters } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTableAdvertisement.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import CustomizeColumn2 from "~/components/customizeColumn/CustomizeColumn2.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import AdvertisementTab from "../../../components/advertisement/AdvertisementTab.vue";
import FlagIcon from "../../../components/common/FlagIcon.vue";
// import AdBulkUpload from "../../../components/advertisement/bulk-upload/AdBulkUpload.vue";
import LabelForm from "../../../components/advertisement/label/LabelForm.vue";
import PopOver from "../../../components/design/PopOver.vue";
import moment from "moment";

import AdHistory from "../../../components/advertisement/AdHistory.vue";
import MultiSelectionStatusReason from "../../../components/advertisement/MultiSelectionStatusReason.vue";
import CustomizeTab from "../../../components/customizeColumn/CustomizeTab.vue";
import AdvertisementGraphProfile from "../../../components/advertisement/graph/AdvertisementGraphProfile.vue";
import SvgIcon from "../../../components/design/components/SvgIcon.vue";
import CheckAdsStepper from "../../../components/advertisement/check_ads/CheckAdsStepper.vue";
import ProductProfileStatistics from "../../../components/advertisement/product-statistics-profile/ProductProfileStatistics.vue";
import ProductProfileInfoStepper from "../../../components/advertisement/product-profile-info/ProductProfileInfoStepper.vue";
import ShowProductProfileInfoImage from "../../../components/advertisement/product-profile-info/ShowProductProfileInfoImage.vue";
import OtpCodeForm from "../../../components/advertisement/OtpCodeForm.vue";
import GenerateConnection from "../../../components/advertisement/Connections/GenerateConnection.vue";
import OnlineSalesFilter from "../../../components/online_sales_management_system/online-sales-filter/OnlineSalesFilter.vue";
import AdStatusReason from "../../../components/online_sales_management_system/change-status-reason/AdStatusReason.vue";
import FilterDateRange from "../../../components/advertisement/FilterDateRange.vue";
import OnlineSalesStepper from "../../../components/online_sales_management_system/online-sales-stepper/OnlineSalesStepper.vue";
import OnlineSalesItemCodeTab from "../../../components/online_sales_management_system/OnlineSalesItemCodeTab.vue";
import OnlineSalesProfile from "../../../components/online_sales_management_system/online-sales-profile/OnlineSalesProfile.vue";
import DesignRequestOperation from "../../../components/landing/DesignRequestOperation.vue";
import RemarkForm from "../../../components/advertisement/remarks/RemarkForm.vue";
import ReasonDialogNew from "../../../components/common/ReasonDialogNew.vue";
import online_sales_management from "../../../configs/pages/online_sales_management/online_sales_management.js";
import ChangeItemCodeStatus from "../../../components/online_sales_management_system/ChangeItemCodeStatus.vue";
export default {
  meta: {
    hasAuth: true,
    scope: "osv",
  },

  components: {
    CustomPageActions,
    CustomButton,
    PageHeader,
    PageFilters,
    DataTable,
    AdvertisementTab,
    FlagIcon,
    LabelForm,
    PopOver,
    AdHistory,
    MultiSelectionStatusReason,
    CustomizeTab,
    AdvertisementGraphProfile,
    SvgIcon,
    CheckAdsStepper,
    ProductProfileStatistics,
    ProductProfileInfoStepper,
    ShowProductProfileInfoImage,
    OtpCodeForm,
    GenerateConnection,
    OnlineSalesFilter,
    AdStatusReason,
    CustomizeColumn2,
    FilterDateRange,
    OnlineSalesStepper,
    OnlineSalesItemCodeTab,
    OnlineSalesProfile,
    DesignRequestOperation,
    RemarkForm,
    ReasonDialogNew,
    ChangeItemCodeStatus
},

  data() {
    return {
      itemCodeTabCount: [],
      AxiosSource: null,
      prev_index: null,
      sortItem: {},
      tabDialog: false,
      scroll_left: 0,
      scroll_right: 0,
      selectedTabData: {},
      copy: false,
      tableRecords: [],
      totalRecords: 0,
      headers: [],
      systemTabs: online_sales_management(this).itemTabs,
      tabItems: online_sales_management(this).itemTabs,
      breadcrumb: online_sales_management(this).breadcrumb,
      currentTab: online_sales_management(this).itemTabs[0],
      apiCalling: false,
      status_loading: false,
      updateStatusItemId: null,

      ColumnAxiosSource: null,

      filterData: {},
      type: 1,
      content: [],
      searchContent: "",
      isSearched: [], // change search 1
      searchId: "",
      options: {},
      filterByDate: {
        start_date: "2023-01-12",
        end_date: moment().format("YYYY-MM-DD"),
      },
      downloadDialog: false,

      showLabelPopOver: false,
      lableRemarkErrMessage: "",
      // for custom columns
      columnDialog: false,
      selected_headers: [],
      selectedIndex: 0,
      status_id: 0,

      all_headers: [
        {
          key: "country",
          name: "Country",
          selected_headers: [],
          headers: [],
        },
        {
          key: "company",
          name: "Company",
          selected_headers: [],
          headers: [],
        },
        {
          key: "project",
          name: "Project",
          selected_headers: [],
          headers: [],
        },
        {
          key: "sales_type",
          name: "sales_type",
          selected_headers: [],
          headers: [],
        },

        {
          key: "item_code",
          name: "Item Code",
          selected_headers: [],
          headers: [],
        },
      ],

      statusChangeItem: [],
      sumWithInitialLeft: 0,
      sumWithInitialRight: 0,
      item_code_tab: "all",
      showRemarkPopOver: false,
    };
  },

  async created() {
    // this.tabItems = JSON.stringify(this.systemTabs);
    await this.fetchHeaders();
    this.fetchItemsAccordingToTabKey(this.currentTab.key, {
      query: null,
      body: this.filterByDate,
    });
  },

  mounted() {
    // this.$root.$on("apply_label_changes", this.applyLabelChanges);
  },

  beforeDestroy() {
    this.$echo.leave(`refresh-ads.${this.$auth.user.id}`);
  },

  watch: {
    "currentTab.selectedItems": function (value) {
      const changeItem = (item) => {
        if (item.key == this.currentTab.key) {
          item.selectedItems = value;
        }
      };
      this.tabItems.forEach(changeItem);
    },
  },
  computed: {
    ...mapGetters({
      custom_columns: "customColumns/get_custom_columns",
    }),
    primaryColor() {
      return this.$vuetify.theme.themes.light.primary == "#2E7BE6"
        ? "success"
        : "#2E7BE6";
    },

    maxDate: function () {
      return moment().format("YYYY-MM-DD");
    },
  },

  methods: {
    updateOnlineSalesAttachments(data) {
      // this.tableRecords = this.tableRecords.map((item) => {
      //   if (item.pcode == data.item_code)
      //     item.product_product_info.attachments = data.attachments;
      //   return item;
      // });
      this.tableRecords = [...this.tableRecords];
    },
    toggleItemStatus() {
      if (this.currentTab.selectedItems.length > 1) {
        this.$toasterNA(
          "orange",
          this.$tr("only_one_item_is_allowed_for_this_operation")
        );
        return;
      }
      this.$refs.changeItemStatusRef.toggleStatusDialog(
        this.currentTab.selectedItems[0]
      );
    },
    async onItemCodeTabChange(tab) {
      this.item_code_tab = tab;
      if (tab != "all")
        this.all_headers[4].selected_headers =
          this.all_headers[4].selected_headers.filter(
            (item) => item.value != "item_status"
          );
      else {
        const item = {
          category: null,
          category_id: null,
          id: 349,
          sortable: 0,
          tab_id: 5,
          text: "Item status",
          tooltip: "Item status",
          value: "item_status",
          visible: 1,
        };
        // add column in spacific index of array
        this.all_headers[4].selected_headers.splice(1, 0, item);
      }

      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);
      this.resetPagination();
      this.fetchItemsAccordingToTabKey(
        this.currentTab.key,
        {
          query: this.options,
          body: filterData,
        },
        true
      );
      this.tableRecords = [];
      this.totalRecords = 0;
    },
    ...mapActions({
      fetchCustomColumns: "customColumns/fetchCustomColumns",
    }),
    pushRecord() {
      const currentTabIndex = this.getCurrentTabIndex();
      const body = this.filterOptions(currentTabIndex);
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: this.options,
        body: body,
      });
      // this.fetchItemsAccordingToTabKey(this.currentTab.key);
    },
    async changeItemStatus(item) {
      try {
        const result = await this.$axios.post(
          "online-sales/item-status",
          item
        );
        if (result.status == 201) {
          this.$root.$emit("updateItemChangeStatus", item.item_code);
          setTimeout(() => {
            this.$refs.OnlineSalesItemCodeTabRef.changeCount(item);
            if (this.item_code_tab == "all") {
              this.tableRecords = this.tableRecords.map((i) => {
                if (i.id == item.id) {
                  i.item_status = {
                    item_code: item.item_code,
                    color: item.item_status.color,
                    item_status: item.item_status.status,
                  };
                  this.currentTab.selectedItems[0] = i;
                }
                return i;
              });
            } else {
              this.tableRecords = this.tableRecords.filter(
                (data) => data.id != item.id
              );
              this.currentTab.selectedItems = [];
            }

            this.$toasterNA(
              "green",
              this.$tr("record_item_status_changed", this.$tr("item_code"))
            );
            this.$refs.changeItemStatusRef.close();
          }, 1000);
        } else {
          this.$refs.changeItemStatusRef.reset();
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch {
        this.$refs.changeItemStatusRef.reset();
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },

    scrollTo(direction) {
      const scrollBox = document.getElementById("scroll-box2");
      if (direction == "left") {
        scrollBox.scrollLeft -= 300;
      } else {
        scrollBox.scrollLeft += 300;
      }
      this.scroll_left = scrollBox.scrollLeft;
    },
    openInsertDialog() {
      this.$refs.OnlineSalesStepperRef.showDialog();
    },

    async clearLeft() {
      const currentTabIndex = this.getCurrentTabIndex();
      const leftArray = this.tabItems.slice(0, currentTabIndex);
      for (let i = 0; i < leftArray.length; i++) {
        const el = this.tabItems[i];
        el.selectedItems = [];
        this.tabItems[i] = el;
        this.sumWithInitialLeft = 0;
      }
      const body = this.filterOptions(currentTabIndex);

      await this.resetPagination();
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: this.options,
        body: body,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
    },
    async clearRight() {
      if (this.selectedIndex == 4) {
        this.all_headers[4].selected_headers =
          this.all_headers[4].selected_headers?.filter(
            (item) => item.id != "cc"
          );
      }
      const currentTabIndex = this.getCurrentTabIndex();
      this.tabItems = this.tabItems.map((row) => {
        row.selectedItems = [];
        return row;
      });
      this.sumWithInitialRight = 0;
      // }
      await this.resetPagination();
      const body = this.filterOptions(currentTabIndex);

      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: this.options,
        body: body,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
    },
    checkPlatforms(item) {
      const disabledPlatforms = ["google ads", "facebook"];
      if (item.platform) {
        const name = item.platform.platform_name;
        return !disabledPlatforms.includes(name);
      }
      return false;
    },
    changeRecordStatus(item) {
      this.currentTab.selectedItems = [item];
      this.$refs.reasonRefs.fetchAllReasons("active");
    },

    async submitReasons(reasons, status = "active") {
      const item = this.currentTab.selectedItems[0];
      try {
        let data = {
          tab: this.currentTab.key,
          id: item.id,
          primary_key: "id",
          status: status,
          reason_ids: reasons,
          model: "online_sales",
        };
        let primary_key = "id";
        let id = "id";
        switch (this.currentTab.key) {
          case "country":
            id = item.country_id;
            primary_key = "country_id";
            break;
          case "company":
            primary_key = "company_id";
            id = item.company_id;
            break;
          case "project":
            primary_key = "project_id";

            id = item.project_id;
            break;
          case "sales_type":
            primary_key = "sales_type";
            id = item.sales_type;
            break;
          case "item_code":
            primary_key = "product_code";
            id = item.product_code;
            break;
        }
        data.id = id;
        data.primary_key = primary_key;
        data.column = primary_key;

        const response = await this.$axios.post("assign-reason", data);
        this.tableRecords = this.tableRecords.map((row) => {
          if (row.id == item.id) {
            row.current_status.length > 0
              ? (row.current_status = [])
              : (row.current_status = [{}]);
          }
          return row;
        });
        return true;
      } catch (error) {
        console.log(error);
        return false;
      }
    },

    async fetchHeaders() {
      try {
        if (this.ColumnAxiosSource)
          this.ColumnAxiosSource.cancel("Request-Cancelled");
        this.ColumnAxiosSource = this.$axios.CancelToken.source();

        if (this.all_headers[this.selectedIndex].headers.length == 0) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "online_sales_" + this.currentTab.key,
            },
            cancelToken: this.ColumnAxiosSource.token,
          });

          this.all_headers[this.selectedIndex].selected_headers =
            response.data.selected_headers;
          // fake columns
          this.all_headers[this.selectedIndex].headers = response.data.headers;
          this.all_headers = { ...this.all_headers };
        }
        // make fake columns in item code when country or company is selected
        await this.makeFakeColumn();
      } catch (error) {}
    },
    makeFakeColumn() {
      if (
        this.selectedIndex == 4 &&
        (this.tabItems[0].selectedItems.length > 0 ||
          this.tabItems[1].selectedItems.length > 0)
      ) {
        if (
          this.all_headers[4].selected_headers?.find((item) => item.id == "cc")
        )
          return;
        const newData = {
          category: null,
          category_id: null,
          id: "cc",
          sortable: 0,
          tab_id: 5,
          text: "cc",
          tooltip: "Selected Country And Company",
          value: "cc",
          visible: 1,
        };
        this.all_headers[4].selected_headers.splice(1, 0, newData);
      }
    },
    async clickchild(event, item) {
      if (event.ctrlKey) {
        window.open(item, "_blank");
      } else {
        try {
          await navigator.clipboard.writeText(item);
          // this.$toastr.s(this.$tr("successfully_copied"));
          this.$toasterNA("green", this.$tr("successfully_copied"));
        } catch (error) {
          // this.$toastr.e(this.$tr("connot_copy"));
          this.$toasterNA("red", this.$tr("connot_copy"));
        }
      }
    },

    setSelectedTabWithData(items) {
      if (items.length) {
        const key = this.tabItems[this.selectedIndex].key;
        this.selectedTabData[key] = items;
        this.selectedTabData.key = key;
      }
    },

    getTimeHour(time) {
      return moment(new Date(`${time} UTC`))
        .local(true)
        .format("LT");
    },

    async clickchild(event, item) {
      if (event.ctrlKey) {
        window.open(item, "_blank");
      } else {
        try {
          await navigator.clipboard.writeText(item);
          // this.$toastr.s(this.$tr("successfully_copied"));
          this.$toasterNA("green", this.$tr("successfully_copied"));
        } catch (error) {
          // this.$toastr.e(this.$tr("connot_copy"));
          this.$toasterNA("red", this.$tr("connot_copy"));
        }
      }
    },

    clearAndUnselectId(code) {
      this.currentTab.selectedItems = this.currentTab.selectedItems.filter(
        (row) => row.code !== code
      );
    },

    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.removeUser(data.id);
          this.insertUser(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    addRemark(id) {
      // this.tableRecords = this.tableRecords.map((item) => {
      //   if (item.id == id) {
      //     item.remarks_count++;
      //   }
      //   return item;
      // });
    },

    async searchById(id) {
      if (id == "" && this.searchId == "") {
      }
      this.searchId = id;
      // try {

      await this.resetPagination();
      const options = this.fetchOptions();
      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);

      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: options,
        body: filterData,
        searchValue: true,
        search_by_id: true,
      });
    },
    resetPagination() {
      if (this.options.page) this.options.page = 1;
      // if (this.options.itemsPerPage) this.options.itemsPerPage = 20;
      this.$refs.adverTableRef.setPaginationTo(1);
      if (this.currentTab.key == "item_code") {
        this.$refs.adverTableRef.setPerPageTo(50);
      } else {
        this.$refs.adverTableRef.setPerPageTo(20);
      }

      return;
    },

    async searchByContent(searchContent) {
      this.searchContent = searchContent;
      const options = this.fetchOptions();
      this.isSearched = options; // change search 2
      const currentTabIndex = this.getCurrentTabIndex();
      await this.resetPagination();
      const filterData = this.filterOptions(currentTabIndex);
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: options,
        body: filterData,
      });
    },

    onSearchTypeChange() {
      this.currentTab.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },

    fetchOptions() {
      let data = {
        key: this.currentTab.key,
      };
      if (!this.$isObjectEmpty(this.filterData))
        Object.assign(data, this.filterData);

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;

      return data;
    },
    getCurrentTabIndex() {
      return this.tabItems.findIndex((tab) => tab.key == this.currentTab.key);
    },
    onTableChanges(options) {
      this.options = this._.clone(options);
      options = this.fetchOptions();

      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: options,
        body: filterData,
      });
    },

    async FilterStoreData(filterData) {
      this.$root.$emit("resetPageNumber");
      const currentTabIndex = this.getCurrentTabIndex();
      await this.resetPagination();
      const tabData = this.filterOptions(currentTabIndex);
      this.filterData = filterData;
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        body: { ...filterData, ...tabData },
      });
    },
    openFilterDialog() {
      this.$refs.OnlineSalesFilterRef.changeModel(this.currentTab.key);
    },
    onRowClicked(item) {
      let items = this.currentTab.selectedItems;
      let found = items.findIndex((item2) => item.id == item2.id) > -1;
      if (found) {
        items = items.filter((item2) => item2.id != item.id);
      } else {
        items.push(item);
      }
      this.currentTab.selectedItems = items;
      this.setSelectedTabWithData(this.currentTab.selectedItems);
      const changeItem = (item) => {
        if (item.key == this.currentTab.key) {
          item.selectedItems = Array.from(items);
        }
      };
      this.tabItems.forEach(changeItem);
    },

    chipRightLeft(index) {
      // const leftArray = this.tabItems.slice(0, index);
      // const rightArray = this.tabItems.slice(index + 1, this.tabItems.length);
      // const leftArraylength = leftArray.map((row) => row.selectedItems.length);
      const rightArraylength = this.tabItems.map(
        (row) => row.selectedItems.length
      );
      // const initialValueLeft = 0;
      // this.sumWithInitialLeft = leftArraylength.reduce(
      // 	(previousValue, currentValue) => previousValue + currentValue,
      // 	initialValueLeft,
      // );
      const initialValueRight = 0;
      this.sumWithInitialRight = rightArraylength.reduce(
        (previousValue, currentValue) => previousValue + currentValue,
        initialValueRight
      );
    },
    prevIndex(prev_index) {
      this.prev_index = prev_index;
    },

    async onTabChange(tabIndex) {
      this.chipRightLeft(tabIndex);
      this.selectedIndex = tabIndex;
      const tabItem = this.tabItems[tabIndex];
      this.currentTab = tabItem;
      this.fetchHeaders();
      const filterData = this.filterOptions(tabIndex);

      await this.resetPagination();
      let options = this.fetchOptions();
      // const prevTab = this.tabItems[this.prev_index];
      // this.sortItem[prevTab.key] = options.sortBy;
      options.sortBy = [];
      this.$refs.adverTableRef.forceReset();
      // if (this.sortItem[this.currentTab.key]) {
      //   options.sortBy = this.sortItem[this.currentTab.key];
      // }
      if (this.isSearched && this.isSearched.key != options.key) {
        // change search 3
        options.searchContent = "";
      }
      this.fetchItemsAccordingToTabKey(tabItem.key, {
        query: options,
        body: filterData,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
    },

    filterOptions() {
      const prevTabItem = this.currentTab;
      let options = {};
      const eachItem = (tab) => {
        const ids = tab.selectedItems.map((item) => item[tab.id_selector]);
        options[tab.key] = ids;
      };
      this.tabItems.forEach(eachItem);
      options = { ...options, ...this.filterByDate };
      return options;
    },

    async fetchItemsAccordingToTabKey(
      tabkey,
      options = null,
      item_code_tab = false
    ) {
      if (this.AxiosSource) this.AxiosSource.cancel("Request-Cancelled");
      this.AxiosSource = this.$axios.CancelToken.source();
      this.apiCalling = true;

      // for item code tab
      if (this.currentTab.key == "item_code") {
        options.body.item_code_tab = this.item_code_tab;
      }

      const res = await this.$axios.get(`online-sales?key=${tabkey}`, {
        params: options,
        cancelToken: this.AxiosSource.token,
      });
      if (res.data.item_tabs?.all) this.itemCodeTabCount = res.data.item_tabs;
      this.tabItems = this.tabItems.map((item) => {
        item.count = res.data.tab_count[item.key];
        return item;
      });
      this.tableRecords = this.sortData(res.data.data);
      this.totalRecords = res.data.total;
      this.apiCalling = false;
    },
    addProductProfile(item) {
      this.tableRecords = this.tableRecords.map((i) => {
        if (i.product_code == item.item_code) i.product_info = item;
        return i;
      });
    },

    async onUnselected(tabIndex) {
      if (
        this.selectedIndex == 4 &&
        this.tabItems[0].selectedItems.length == 0 &&
        this.tabItems[1].selectedItems.length == 0
      ) {
        this.all_headers[4].selected_headers =
          this.all_headers[4].selected_headers?.filter(
            (item) => item.id != "cc"
          );
      }
      this.chipRightLeft(tabIndex);
      this.currentTab.selectedItems = [];
      let prevTabItem = { key: null, selectedItems: [] };
      if (tabIndex > 0) {
        prevTabItem = this.tabItems[tabIndex - 1];
      } else {
        prevTabItem = this.tabItems[tabIndex];
      }
      const clickedTab = this.tabItems[tabIndex];
      if (clickedTab.key == this.currentTab.key) return;
      const currentTabIndex = this.getCurrentTabIndex();
      const body = this.filterOptions(currentTabIndex);

      body.prev = prevTabItem.key;
      await this.resetPagination();
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: this.options,
        body: body,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
    },
    toggleLabelFom() {
      if (this.checkPossibility("label")) this.$refs.labelRef.toggleDialog();
    },

    applyLabelChanges(labels) {
      this.tableRecords = this.tableRecords.map((row) => {
        if (row.id == this.currentTab.selectedItems[0].id) {
          row.labels = labels;
        }
        return row;
      });
      this.$refs.advertisementRefs.reFetchExtraInfo("label");
    },

    checkPossibility(type) {
      const valid = ["ispp_code"];
      if (this.currentTab.selectedItems.length == 0) {
        this.lableRemarkErrMessage = this.$tr("please_select_one_option");
        this.showLabelRemakrPopOver(type);
        return false;
      }
      if (valid.includes(this.currentTab.key)) {
        this.lableRemarkErrMessage = this.$tr(
          "cant_do_operation_on_current_tab"
        );
        this.showLabelRemakrPopOver(type);
        return false;
      }
      if (this.currentTab.selectedItems.length > 1) {
        this.lableRemarkErrMessage = this.$tr("please_select_one_option");
        this.showLabelRemakrPopOver(type);
        return false;
      }
      return true;
    },
    showLabelRemakrPopOver(type) {
      this.showLabelPopOver = type == "label";
      this.showRemarkPopOver = type == "remark";
    },
    showOnlineSalesProfile(item) {
      this.currentTab.selectedItems = [];
      this.currentTab.selectedItems = [item];
      this.$refs.onlineProfileRefs.openDialog(item);
    },

    async addProductInfo() {
      if (this.currentTab.selectedItems.length == 0) {
        return;
      }
      if (this.currentTab.selectedItems.length > 1) {
        this.$toasterNA(
          "orange",
          this.$tr("only_one_item_is_allowed_for_this_operation")
        );
        return;
      }

      this.$refs.productCreationRefs.openDialog(
        this.currentTab.selectedItems[0].product_code
      );
    },

    showProductProfileInfo(item) {
      if (item) this.$refs.ShowProductProfileInfoImageRef.showDialog(item);
    },

    sortData(data) {
      return data.map((row) => {
        if (this.currentTab.key == "country") {
          row.labels = row.country.labels;
        } else if (this.currentTab.key == "company") {
          row.labels = row.company.labels;
        } else if (this.currentTab.key == "project") {
          row.labels = row.project.labels;
        } else if (this.currentTab.key == "sales_type") {
          row.labels = row.sales_type_labels;
        } else {
          row.labels = row.labels;
        }
        return row;
      });
    },

    async onDateRangeSubmit(range) {
      const timeRange = {
        start_date: range.start_date,
        end_date: range.end_date,
      };
      this.filterByDate = timeRange;
      await this.resetPagination();
      const options = this.fetchOptions();
      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);

      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: options,
        body: {
          ...filterData,
        },
      });
    },

    openDesignRequestDialog() {
      const item = this.currentTab.selectedItems;
      if (item.length > 1) {
        this.$toasterNA(
          "orange",
          this.$tr("only_one_item_is_allowed_for_this_operation")
        );
        return;
      }
      this.$refs.designRequestOperationRef.openDialog(item);
    },

    async toggleRemarkForm(item) {
      if (item) {
        this.currentTab.selectedItems = [];
        await this.onRowClicked(item);
      }
      if (this.checkPossibility("remark")) {
        this.$refs.remarkRef.toggleDialog();
      } else {
        console.log("possibliy not matched");
      }
    },

    deleteItemCode(fromEmit = false) {
    
      try {
        let selectedItem = this.currentTab.selectedItems[0];
        if (fromEmit) {
          this.currentTab.selectedItems = [];
              const currentTabIndex = this.getCurrentTabIndex();
              const body = this.filterOptions(currentTabIndex);
              this.fetchItemsAccordingToTabKey(this.currentTab.key, {
                query: this.options,
                body: body,
              });
          // this.tableRecords = this.tableRecords.filter(
          //   (row) => row.product_code != selectedItem.product_code
          // );
          // this.currentTab.selectedItems = [];
          // // this.tabItems[4].count.total = this.tabItems[4].count.total - 1;

          return;
        }

        if (this.currentTab.selectedItems.length != 1) {
          this.$toasterNA(
            "red",
            this.$tr("only_one_item_is_allowed_for_this_operation")
          );
          return;
        }
        selectedItem.pcode = selectedItem.product_code;
        this.$TalkhAlertNA(
          "Are you sure ?", //text
          "delete", //icon
          async () => {
            this.$refs.otpCodeRefs.toggleDialog(selectedItem);
          }, // callback function
          "yes_delete" // btn text
        );
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
  },
};
</script>
  
  <style>
.chip6 .v-chip__content {
  width: 100% !important;
  justify-content: space-between !important;
}
</style>
  <style scoped>
.leftTab {
  height: 20px;
  width: 20px;
  background: var(--v-primary-base);
  bottom: -52px;
  left: 48px;
  transform: rotate(45deg);
  z-index: 19;
}
.rightTab {
  height: 20px;
  width: 20px;
  background: var(--v-primary-base);
  bottom: -52px;
  right: 47px;
  transform: rotate(45deg);
  z-index: 19;
}
.rightTab2 {
  height: 15px;
  width: 15px;
  background: var(--v-primary-base);
  bottom: -66px;
  right: 47px;
  transform: rotate(45deg);
  z-index: 19;
}
.prompt {
  position: absolute;
  width: 65%;
  z-index: 20;
  right: 10px;
  border-radius: 5px;
}
.item_status:hover {
  background: #2e7be6;
  color: white;
}
</style>
  