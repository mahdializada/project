<template>
  <v-container fluid class="pa-0">
    <!-- <v-btn @click="viewVideoProfile()">show profile</v-btn> -->
    <VideoAdProfile ref="videoProfileRefs" />
    <!-- view canva video 1  -->
    <CanvaVideoView ref="canvaVideoViewRefs" />
    <ProductProfileInfoStepper
      ref="productCreationRefs"
      @addProductProfile="addProductProfile"
    />
    <ChangeStatusMa @changeStatus="changeItemStatus" ref="changeStatusMaRef" />
    <ShowProductProfileInfoImage ref="ShowProductProfileInfoImageRef" />
    <CMahdi ref="target" />

    <ProductProfileStatistics
      ref="productProfile"
      @updateAttachment="updateAttachmentProductProfile"
    />

    <AdvertisementGraphProfile
      ref="advertisementRefs"
      :selected_item.sync="currentTab.selectedItems"
      :date_range.sync="filterByDate"
      :current_tab="currentTab.key"
    />
    <BudgetForm ref="historyRefs"></BudgetForm>
    <updateBidBudget ref="updateBidBudgetRef" @submit="onUpdateBidBudget" />
    <BulkUploadStepper
      :selectedTabData.sync="selectedTabData"
      :sheets="sheets"
      :show="adBulkUpload"
      @close="adBulkUpload = false"
    />
    <BankAccount ref="bankModal" />
    <BalanceModal ref="balanceModal" @addBalance="addBalance" />

    <AdvertismentFilter
      ref="advertismentFilterRefs"
      @filterRecords="onFilterRecords"
      :currentTab="currentTab.key"
    />
    <LabelForm
      ref="labelRef"
      subsystem_name="advertisement"
      :model_name="currentTab.key"
      :selected_item.sync="currentTab.selectedItems"
      @applyLabelChanges="applyLabelChanges($event)"
    />
    <RemarkForm
      ref="remarkRef"
      :tab="currentTab.key"
      @addRemark="addRemark"
      :selected_items.sync="currentTab.selectedItems"
    />
    <AdReasons
      @onSubmit="submitReasons"
      @closeDialog="closeReasonDialog()"
      :tabName="currentTab.name"
      ref="reasonRef"
    />

    <client-only>
      <MultiSelectionStatusReason
        ref="multiSelectionStatusReasonRef"
        :currentTab="currentTab"
        @MultiStatusChange="MultiStatusChange"
      />
      <GenerateConnection
        v-if="$isInScope('advc')"
        ref="genereteConnectionRef"
      />
      <GenerateLinkCurrentAdAccount
        v-if="$isInScope('advc')"
        ref="GenerateLinkCurrentAdAccountRef"
        :adAccounts.sync="adAccounts"
      />
      <CheckAdsStepper v-if="$isInScope('advc')" ref="CheckAdsref" />
      <GenerateLinkCreation
        v-if="$isInScope('advc')"
        ref="generateLinkCreationRef"
      />
      <v-dialog v-model="tabDialog" width="1300" persistent>
        <CustomizeTab
          v-if="tabDialog"
          @saveChanges="
            (data) => {
              all_headers[selectedIndex] = data;
              tabDialog = false;
            }
          "
          :tabs.sync="systemTabs"
          :page_name="'advertisement_' + currentTab.key"
          @closeColumnDialog="tabDialog = false"
          :key="selectedIndex"
        />
      </v-dialog>
      <v-dialog v-model="columnDialog" width="1300" persistent>
        <CustomizeColumn
          v-if="columnDialog"
          @saveChanges="
            (data) => {
              all_headers[selectedIndex] = data;
              columnDialog = false;
            }
          "
          :page_headers.sync="all_headers[selectedIndex]"
          :page_name="'advertisement_' + currentTab.key"
          @closeColumnDialog="columnDialog = false"
          :key="selectedIndex"
        />
      </v-dialog>
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
            :filename="'advertisement-' + currentTab.key"
            note-title=""
            @onFilter="openFilterDialog"
            :showBulkUpload="$isInScope('advc')"
            :showDownload="$isInScope('adve')"
            @searchById="searchById"
            @onBulkUpload="adBulkUpload = !adBulkUpload"
            @onDownload="downloadDialog = !downloadDialog"
            @clearAndUnselectId="clearAndUnselectId"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            @onColumn="columnDialog = true"
            @onTab="tabDialog = true"
            :showTab="true"
            @onProfile="showAdvertisementProfile()"
            :selected_item.sync="currentTab.selectedItems"
          >
            <CustomButton
              v-if="currentTab.key == 'ad' && $isInScope('advc')"
              icon="fa-copy"
              @click="openInsertDialog(2)"
              :text="$tr('duplications')"
              type="remark"
              :color="'#2dccff'"
            />
            <CustomButton
              v-if="$isInScope('advc')"
              icon="fa-plus"
              @click="openCheckAdsDialog"
              text="checking_ads"
              type="warning"
            />
            <CustomButton
              v-if="$isInScope('advc')"
              icon="fa-plus"
              @click="openInsertDialog(1)"
              :text="$tr('create_item', $tr('link'))"
              type="primary"
            />
            <!-- tartgeting design  -->
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
                  class="white rounded d-flex align-center justify-center"
                  style="height: 80px; min-width: 30px; max-width: 30px"
                >
                  <v-icon @click="scrollTo('left')" class="primary--text"
                    >mdi-chevron-left</v-icon
                  >
                </div>
                <div
                  id="scroll-box2"
                  class="d-flex align-center px-1"
                  style="
                    overflow-x: hidden;
                    scroll-behavior: smooth;
                    width: 100%;
                  "
                >
                  <!-- toggleBankModal -->
                  <CustomButton
                    icon="mdi-target-account"
                    @click="targetDialog()"
                    :text="$tr('target')"
                    type="primary"
                    v-if="currentTab.key == 'ad_set'"
                  />
                  <CustomButton
                    @click="toggleBankModal"
                    icon="fa-sync"
                    text="Bank Card"
                    type="primary"
                    v-if="currentTab.key == 'ad_account'"
                  />
                  <!-- toggleBalanceModal -->
                  <CustomButton
                    @click="toggleBalanceModal"
                    icon="fa-sync"
                    text="balance"
                    type="primary"
                    :selected_item="balanceData"
                    v-if="currentTab.key == 'ad_account'"
                  />
                  <CustomButton
                    v-if="
                      currentTab.key == 'item_code' &&
                      !currentTab?.selectedItems[0]?.product_profile_info?.id &&
                      currentTab.selectedItems.length > 0
                    "
                    @click="addProductInfo()"
                    icon="fa-plus"
                    text="Product Details"
                    type="primary"
                    :class="
                      currentTab.selectedItems.length == 0
                        ? 'customDisabled'
                        : ''
                    "
                  />
                  <!-- v-if="currentTab.key == 'item_code'" -->
                  <!-- snapchat Referesh -->
                  <!-- <RefreshPercentage
                v-if="
                  selectedPlatformRefresh == 'snapchat' &&
                  (platform_rf_percentage > 0 || platform_rf_loading)
                "
                :percentage="platform_rf_percentage"
                type="warning"
                :textColor="refresh_percentage_color"
              /> -->
                  <!-- v-else -->
                  <div
                    v-click-outside="
                      () => {
                        showSnapchatRefresh = false;
                      }
                    "
                    class="refresh-btn-wrapper postion-relative"
                  >
                    <AdvRefreshPropmpt
                      :show="showSnapchatRefresh"
                      @close="showSnapchatRefresh = false"
                      @refresh="refreshPlatformAd('snapchat')"
                      :canRefresh="canRefresh"
                      :refreshTime="getTimeHour(this.refreshTime)"
                      :refreshTimeAgo="getTimeAgo(this.refreshTime)"
                      :color="'rgb(255 214 51)'"
                      :type="'warning'"
                    />
                    <CustomButton
                      @click="toggleRefresh('snapchat')"
                      icon="fa-sync"
                      text="snapchat"
                      type="warning"
                      v-show="$isInScope('advrfsnapchat')"
                    />
                  </div>
                  <!-- tiktok refresh -->
                  <!-- <RefreshPercentage
                v-if="
                  selectedPlatformRefresh == 'tiktok' &&
                  (platform_rf_percentage > 0 || platform_rf_loading)
                "
                :percentage="platform_rf_percentage"
                type="label"
                :textColor="refresh_percentage_color"
              /> -->
                  <!-- v-else -->
                  <div
                    v-click-outside="
                      () => {
                        showtiktokRefresh = false;
                      }
                    "
                    class="refresh-btn-wrapper postion-relative"
                  >
                    <AdvRefreshPropmpt
                      :show="showtiktokRefresh"
                      @close="showtiktokRefresh = false"
                      @refresh="refreshPlatformAd('tiktok')"
                      :canRefresh="canRefresh"
                      :refreshTime="getTimeHour(this.refreshTime)"
                      :refreshTimeAgo="getTimeAgo(this.refreshTime)"
                      :color="'#2dccff'"
                      :type="'label'"
                    />
                    <CustomButton
                      @click="toggleRefresh('tiktok')"
                      icon="fa-sync"
                      text="tiktok"
                      type="label"
                      v-show="$isInScope('advrftiktok')"
                    />
                  </div>

                  <!-- <CustomButton icon="fa-sync" text="CRM Refresh" type="primary" /> -->
                  <!-- <refresh-percentage
                :percentage="crm_refresh_percentage"
                v-if="crm_refresh"
              /> -->
                  <!-- v-show="!crm_refresh" -->
                  <RefreshPopOver
                    v-model="showCrmPopOver"
                    color="#00bc81"
                    @refresh="getCrmPercentage($event)"
                    @showPercentage="crm_refresh = true"
                    :position="true"
                  >
                    <template v-slot:activator>
                      <CustomButton
                        @click="refreshCrm()"
                        icon="fa-sync"
                        text="CRM"
                        type="refresh"
                        v-if="$isInScope('advrfcrm')"
                      />
                    </template>
                    <template v-slot:body>
                      <div>refresh crm</div>
                    </template>
                  </RefreshPopOver>
                  <RefreshPercentage
                    v-if="refresh_percentage > 0 || isRefreshing"
                    :percentage="refresh_percentage"
                    :textColor="refresh_percentage_color"
                  />
                  <div
                    v-else
                    v-click-outside="
                      () => {
                        showRefresh = false;
                      }
                    "
                    class="refresh-btn-wrapper postion-relative"
                  >
                    <AdvRefreshPropmpt
                      :show="showRefresh"
                      @close="showRefresh = false"
                      @refresh="refreshAds"
                      :canRefresh="canRefresh"
                      :refreshTime="getTimeHour(this.refreshTime)"
                      :refreshTimeAgo="getTimeAgo(this.refreshTime)"
                    />
                    <CustomButton
                      @click="toggleRefresh('all')"
                      :loading="isRefreshing"
                      icon="fa-sync"
                      text="refresh"
                      type="refresh"
                    />
                  </div>

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
                  <CustomButton
                    @click="toggleItemStatus"
                    v-if="
                      currentTab.key == 'item_code' &&
                      currentTab.selectedItems &&
                      currentTab.selectedItems.length == 1
                    "
                    icon="fa-toggle-off"
                    text="Item Status"
                    type="refresh"
                  />
                  <FilterDateRange
                    :dateRangeProp="filterByDate"
                    :hide-title="true"
                    @dateChanged="onDateRangeSubmit"
                  />
                  <!-- test -->
                  <CustomButton
                    @click="toggleSelectedStatus"
                    icon="fa-toggle-off"
                    text="Toggle Status"
                    type="danger2"
                  />
                  <CustomButton
                    @click="refreshDatatable"
                    icon="fa-sync"
                    text="refresh_table"
                    type="refresh"
                  />
                  <v-menu
                    ref="menu"
                    v-model="menu"
                    :close-on-content-click="false"
                    :return-value.sync="refreshDates"
                    transition="scale-transition"
                    offset-y
                    nudge-left="140"
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <div v-bind="attrs" v-on="on" class="">
                        <RefreshPercentage
                          v-if="date_rf_percentage > 0 || showDateRefresh"
                          :percentage="date_rf_percentage"
                          type="label"
                          :textColor="refresh_percentage_color"
                        />
                        <CustomButton
                          v-else
                          icon="fa-sync"
                          text="refresh_by_date"
                          type="label"
                          v-show="$isInScope('advrfdate')"
                        />
                      </div>
                    </template>
                    <v-date-picker
                      v-if="!showDateRefresh"
                      :min="minDate"
                      :max="maxDate"
                      v-model="refreshDates"
                      no-title
                      scrollable
                    >
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="menu = false">
                        Cancel
                      </v-btn>
                      <v-btn text color="primary" @click="refreshAdsByDate()">
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-menu>

                  <CustomButton
                    icon="fa-trash"
                    text="delete"
                    type="danger2"
                    @click="deleteItemCode()"
                    v-if="currentTab.key == 'item_code'"
                  />
                </div>
                <div
                  class="white rounded d-flex align-center justify-center"
                  style="height: 80px; min-width: 30px; max-width: 30px"
                >
                  <v-icon @click="scrollTo('right')" class="primary--text"
                    >mdi-chevron-right</v-icon
                  >
                </div>
              </div>
            </template>
          </CustomPageActions>
        </v-col>

        <v-col cols="12">
          <v-card class="w-full mb-2 px-2 py-1" elevation="1" outlined>
            <AdvertisementGraph
              :dateRange="filterByDate"
              ref="advertisementGraph"
            />
          </v-card>
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
                @showPrompt="showPrompt"
                @closePrompt="showPromptActive = false"
              />
              <AdvertisemenItemCodeTab
                v-show="currentTab.key == 'item_code'"
                :tabs="tabItems"
                @onChange="onItemCodeTabChange($event)"
                ref="AdvertisemenItemCodeTabRef"
              />
              <!-- <div
								v-if="sumWithInitialLeft > 0"
								class="left position-absolute"
								style="top: -58px; left: 0px"
							>
								<div class="position-relative" style="width: 100px">
									<v-chip
										class="ma-2 position-absolute d-flex align-center chip6"
										style="width: 65%; z-index: 20; left: 10px"
										color="primary"
										text-color="white"
									>
										<span>{{ sumWithInitialLeft }}</span>
										<v-icon size="15" @click="clearLeft">mdi-close</v-icon>
									</v-chip>
									<span class="position-absolute leftTab"></span>
								</div>
							</div> -->
              <transition :name="'fade'">
                <div
                  class="position-absolute"
                  v-show="showPromptActive"
                  :style="`top: -68px; left: ${promptLeft}px`"
                >
                  <div class="position-relative" style="width: 150px">
                    <div
                      class="mt-1 pa-1 prompt white--text primary"
                      style=""
                      color="primary"
                      text-color="white"
                    >
                      <div
                        style="font-size: 12px !important; padding: 0px"
                        class="d-flex"
                      >
                        <div style="min-width: 30%">
                          {{ totalActiveInactive?.active || 0 }}
                        </div>
                        {{ $tr("active") }}
                      </div>
                      <v-divider></v-divider>
                      <div
                        style="font-size: 12px !important; padding: 0px"
                        class="d-flex"
                      >
                        <div style="min-width: 30%">
                          {{ totalActiveInactive?.inactive || 0 }}
                        </div>
                        {{ $tr("inactive") }}
                      </div>
                    </div>
                    <span class="position-absolute rightTab2"></span>
                  </div>
                </div>
              </transition>
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
            <template v-slot:[`item.code`]="{ item }">
              <span class="mx-1 primary--text font-weight-bold">
                {{ item.code }}
              </span>
            </template>
            <template v-slot:[`item.spend_control`]="{ item }">
              <span
                class="text-center black--text"
                v-html="calculateSpendControl(item)"
              >
              </span>
            </template>
            <template v-slot:[`item.total_ad_account_balance`]="{ item }">
              <span>
                {{
                  item.total_ad_account_balance % 1 != 0
                    ? item.total_ad_account_balance?.toFixed(2)
                    : item.total_ad_account_balance
                }}
              </span>
            </template>
            <template v-slot:[`item.pmac`]="{ item }">
              <v-chip class="px-2" color="info" small text-color="white">
                {{
                  item?.product_profile_info?.prod_max_adver_cost ??
                  item?.connection?.product_profile_info?.prod_max_adver_cost
                }}
              </v-chip>
            </template>
            <template v-slot:[`item.objective`]="{ item }">
              <v-chip
                class="px-2"
                :color="
                  item.objective == 'LANDING_PAGE' ? 'teal' : 'light-blue'
                "
                small
                text-color="white"
              >
                {{ item.objective }}
              </v-chip>
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
                    :fill="item?.product_profile_info?.id ? '#00bf19' : 'red'"
                    stroke="rgba(0,0,0,0)"
                    stroke-width="1"
                  />
                </svg>
              </span>
            </template>
            <template v-slot:[`item.product_images`]="{ item }">
              <span style="white-space: nowrap" class="mb-1">
                <v-img
                  class="rounded"
                  :key="item?.product_profile_info?.attachments?.length"
                  width="30"
                  height="30"
                  @click="
                    showProductProfileInfo(
                      item?.product_profile_info?.attachments
                    )
                  "
                  lazy-src="/images/Group 2721.png"
                  :src="
                    item?.product_profile_info?.attachments?.length > 0
                      ? item?.product_profile_info?.attachments[0]?.path
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
            <template v-slot:[`item.prod_new_product_source`]="{ item }">
              <div>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      v-bind="attrs"
                      v-on="on"
                      style="white-space: nowrap"
                      class="primary--text"
                    >
                      {{ $tr("prod_new_product_source") }}
                    </span>
                  </template>
                  <div v-if="2 > 1">
                    <div
                      v-if="
                        item.product_profile_info?.prod_new_product_source
                          ?.length > 0
                      "
                    >
                      <p
                        v-for="(i, index) in item.product_profile_info
                          ?.prod_new_product_source"
                        :key="index"
                        class="pb-0 mb-0"
                      >
                        {{ $tr(i) }}
                      </p>
                    </div>
                    <div v-else>
                      <p class="pb-0 mb-0">
                        {{ $tr("no_item", $tr("prod_new_product_source")) }}
                      </p>
                    </div>
                  </div>
                </v-tooltip>
              </div>
            </template>
            <template v-slot:[`item.prod_import_source`]="{ item }">
              <div>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      v-bind="attrs"
                      v-on="on"
                      style="white-space: nowrap"
                      class="primary--text"
                    >
                      {{ $tr("prod_import_source") }}
                    </span>
                  </template>
                  <div
                    v-if="
                      item.product_profile_info?.prod_import_source?.length > 0
                    "
                  >
                    <p
                      v-for="(i, index) in item.product_profile_info
                        ?.prod_import_source"
                      :key="index"
                      class="pb-0 mb-0"
                    >
                      {{ $tr(i) }}
                    </p>
                  </div>
                  <div v-else>
                    <p class="pb-0 mb-0">
                      {{ $tr("no_item", $tr("prod_import_source")) }}
                    </p>
                  </div>
                </v-tooltip>
              </div>
            </template>
            <template v-slot:[`item.logo-avatar`]="{ item }">
              <v-avatar :size="30" color="blue-grey darken-3">
                <span class="white--text text-h6 pa-1">
                  {{ generateAvatar(item) }}
                </span>
              </v-avatar>
            </template>
            <!-- view canva video 2  -->
            <template v-slot:[`item.video_profile`]="{ item }">
              <v-icon
                color="primary"
                v-if="
                  item?.connection?.media_link &&
                  item?.connection?.media_link != '' &&
                  item?.connection?.media_link != null &&
                  item?.connection?.media_link != 'null'
                "
                @click="viewVideoProfile(item?.connection?.media_link)"
                >mdi-eye</v-icon
              >
              <v-icon color="secondary" v-else>mdi-eye-off</v-icon>
            </template>

            <!----------    DATATABLE SYSTEMS SECTION        ---------->
            <template v-slot:[`item.connection.generated_link`]="{ item }">
              <div class="link">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      @click.stop.prevent="
                        clickchild($event, item.connection.generated_link)
                      "
                      v-bind="attrs"
                      v-on="on"
                      style="
                        white-space: nowrap;
                        padding-top: 1.5px;
                        padding-bottom: 1.5px;
                      "
                      class="white--text px-5 rounded-pill primary"
                    >
                      {{ $tr("link") }}
                    </span>
                  </template>
                  <div v-if="item.connection.generated_link">
                    <div v-if="item.connection.generated_link.length > 0">
                      <p class="pb-0 mb-0">
                        {{ item.connection.generated_link }}
                      </p>
                    </div>
                    <div v-else>
                      <p class="pb-0 mb-0">{{ $tr("no_item", $tr("link")) }}</p>
                    </div>
                  </div>
                </v-tooltip>
              </div>
            </template>
            <template v-slot:[`item.connection.landing_page_link`]="{ item }">
              <div class="link">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      @click.stop.prevent="
                        clickchild($event, item.connection.landing_page_link)
                      "
                      v-bind="attrs"
                      v-on="on"
                      style="
                        white-space: nowrap;
                        padding-top: 1.5px;
                        padding-bottom: 1.5px;
                      "
                      class="white--text px-5 rounded-pill info"
                    >
                      {{ $tr("landing_page_link") }}
                    </span>
                  </template>
                  <div v-if="item.connection.landing_page_link">
                    <p class="pb-0 mb-0">
                      {{ item.connection.landing_page_link }}
                    </p>
                  </div>

                  <div v-else>
                    <p class="pb-0 mb-0">{{ $tr("no_item", $tr("link")) }}</p>
                  </div>
                </v-tooltip>
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
                        @click.stop="showAdvertisementProfile(item)"
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
            <template v-slot:[`item.iso2`]="{ item }">
              <FlagIcon
                v-if="item.iso2"
                :flag="item.iso2?.toLowerCase()"
                :round="true"
              />
            </template>
            <template v-slot:[`item.status`]="{ item }">
              <v-switch
                @click.stop="onItemStatusClicked(item)"
                :input-value="item.status?.toLowerCase() == 'active'"
                inset
                :color="primaryColor"
                :loading="updateStatusItemId == item.id"
                :disabled="
                  updateStatusItemId == item.id || checkPlatforms(item)
                "
                hide-details
                readonly
                class="mt-0"
                :title="
                  checkPlatforms(item)
                    ? item.status
                    : $tr(
                        'status_is_not_available_for',

                        item.platform !== undefined
                          ? $tr(item.platform.platform_name)
                          : ''
                      )
                "
              >
              </v-switch>
            </template>

            <template v-slot:[`item.remarks_count`]="{ item }">
              <div
                @click.stop="showAdvertisementProfile(item)"
                class="rounded-pill d-flex align-center justify-space-around pink lighten-5"
                style="min-width: 45px; max-width: 80px; height: 80%"
              >
                <v-avatar
                  class="d-flex justify-center align-center"
                  size="21"
                  style="background-color: rgba(255, 0, 112)"
                >
                  <span style="height: 80%; width: 80%; padding-top: 1px">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="11.294"
                      height="11.277"
                      viewBox="0 0 11.294 11.277"
                    >
                      <path
                        id="remark-icon"
                        d="M176.188,248.219c.924,0,1.849-.006,2.773,0a2.806,2.806,0,0,1,2.8,2.227,3.317,3.317,0,0,1,.071.707c.007,1.224,0,2.447,0,3.671a2.823,2.823,0,0,1-2.959,2.947c-1.126,0-.888-.122-1.58.78-.128.168-.253.338-.38.506a.816.816,0,0,1-1.439.007c-.234-.308-.472-.614-.7-.929a.846.846,0,0,0-.737-.373,6.334,6.334,0,0,1-1.489-.125,2.746,2.746,0,0,1-2-2.675c-.014-1.312-.018-2.623,0-3.935a2.815,2.815,0,0,1,2.862-2.807C174.339,248.211,175.264,248.219,176.188,248.219Zm-.01,3.8c.923,0,1.847,0,2.77,0,.279,0,.445-.133.47-.358.035-.3-.161-.485-.538-.485q-2.691,0-5.382,0c-.355,0-.543.155-.54.434s.181.409.528.409Q174.833,252.024,176.178,252.022Zm-1.133,2.831c.546,0,1.091,0,1.637,0a.451.451,0,0,0,.5-.434.428.428,0,0,0-.486-.408q-1.637,0-3.273,0a.578.578,0,0,0-.295.083.377.377,0,0,0-.146.467.432.432,0,0,0,.455.29C173.971,254.855,174.508,254.853,175.045,254.853Z"
                        transform="translate(-170.54 -248.216)"
                        fill="#fff"
                      />
                    </svg>
                  </span>
                </v-avatar>
                <span style="color: rgba(255, 0, 112)">
                  {{ item.remarks_count }}
                </span>
              </div>
              <!-- <div
                class="position-relative mx-auto text-left"
                style="width: 40px"
                @click.stop="toggleRemarkForm(item)"
              >
                <v-icon color="#999" size="22">mdi-comment</v-icon>
                <div class="primary text-center remarks-count" style="">
                  {{ item.remarks_count }}
                </div>
              </div> -->
            </template>
            <template v-slot:[`item.advertisement_status`]="{ item }">
              <v-switch
                @click.stop="changeRecordStatus(item)"
                v-model="item.advertisement_status"
                inset
                @click.native.stop
                :color="primaryColor"
                class="mt-0"
                hide-details
                true-value="active"
                false-value="inactive"
                :title="'active'"
                :loading="status_loading && item.id == status_id"
              ></v-switch>
            </template>

            <template v-slot:[`item.generated_link_date2`]="{ item }">
              {{ localeHumanReadableTime2(item.connection_date?.created_at) }}
            </template>

            <!-- history status -->
            <template v-slot:[`item.history_status`]="{ item }">
              <div
                @click.stop="toggleHistory(item, 'status_history')"
                class="rounded-pill d-flex align-center justify-space-around blue lighten-4"
                style="min-width: 50px; max-width: 80px; height: 80%"
              >
                <v-avatar
                  class="d-flex justify-center align-center"
                  style="background-color: #115598"
                  size="21"
                >
                  <span style="height: 80%; width: 80%">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24.905"
                      height="30.354"
                      viewBox="0 0 24.905 30.354"
                    >
                      <path
                        id="status-icon"
                        d="M269.015-3533.646H251.892a3.9,3.9,0,0,1-3.891-3.891v-22.571a3.893,3.893,0,0,1,2.342-3.572,6.22,6.22,0,0,0,6.219,5.906h7.783a6.221,6.221,0,0,0,6.218-5.906,3.893,3.893,0,0,1,2.343,3.572v22.571A3.9,3.9,0,0,1,269.015-3533.646ZM255.08-3548.1a1.935,1.935,0,0,0-1.376.57,1.948,1.948,0,0,0,0,2.752l3.358,3.358a1.959,1.959,0,0,0,1.376.57,1.934,1.934,0,0,0,1.376-.57l7.387-7.388a1.933,1.933,0,0,0,.57-1.376,1.933,1.933,0,0,0-.57-1.376,1.932,1.932,0,0,0-1.375-.57,1.934,1.934,0,0,0-1.376.57l-6.012,6.012-1.982-1.982A1.935,1.935,0,0,0,255.08-3548.1Zm9.265-12.01h-7.783A3.9,3.9,0,0,1,252.67-3564h15.566A3.9,3.9,0,0,1,264.345-3560.108Z"
                        transform="translate(-248 3564)"
                        fill="white"
                      />
                    </svg>
                  </span>
                </v-avatar>
                <span style="color: #115598">
                  {{ item.reasonables_count }}
                </span>
              </div>
            </template>
            <!-- end history -->
            <!-- budget status -->
            <template v-slot:[`item.budget_history`]="{ item }">
              <div
                @click.stop="toggleHistory(item, 'budget_history')"
                class="rounded-pill d-flex align-center justify-space-around orange lighten-5"
                style="min-width: 50px; max-width: 80px; height: 80%"
              >
                <v-avatar
                  class="d-flex justify-center align-center orange"
                  size="21"
                >
                  <span style="height: 80%; width: 80%">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24.999"
                      height="25.346"
                      viewBox="0 0 34.999 35.346"
                    >
                      <path
                        id="budget-icon"
                        d="M267.113-3529.552a11.4,11.4,0,0,1-3.635-2.451,11.4,11.4,0,0,1-2.451-3.635,11.353,11.353,0,0,1-.9-4.451,11.352,11.352,0,0,1,.9-4.45,11.4,11.4,0,0,1,2.451-3.635,11.4,11.4,0,0,1,3.635-2.451,11.35,11.35,0,0,1,4.451-.9,11.351,11.351,0,0,1,4.451.9,11.4,11.4,0,0,1,3.635,2.451,11.4,11.4,0,0,1,2.451,3.635,11.353,11.353,0,0,1,.9,4.45,11.354,11.354,0,0,1-.9,4.451A11.4,11.4,0,0,1,279.65-3532a11.4,11.4,0,0,1-3.635,2.451,11.371,11.371,0,0,1-4.451.9A11.37,11.37,0,0,1,267.113-3529.552Zm1.935-7.492-1.024,1a.458.458,0,0,0-.14.36.492.492,0,0,0,.193.361,4.158,4.158,0,0,0,2.545.883v1.413a.472.472,0,0,0,.471.471h.941a.472.472,0,0,0,.472-.471v-1.418a3.322,3.322,0,0,0,3.109-2.139,3.2,3.2,0,0,0-.172-2.544,3.241,3.241,0,0,0-1.961-1.625l-3.178-.93a.871.871,0,0,1-.624-.833.869.869,0,0,1,.868-.868H272.5a1.8,1.8,0,0,1,1.006.31.445.445,0,0,0,.248.075.47.47,0,0,0,.327-.134l1.024-1a.46.46,0,0,0,.139-.36.492.492,0,0,0-.193-.361,4.164,4.164,0,0,0-2.545-.883v-1.413a.472.472,0,0,0-.472-.471h-.941a.472.472,0,0,0-.471.471v1.413h-.074a3.231,3.231,0,0,0-2.379,1.051,3.192,3.192,0,0,0-.829,2.468,3.371,3.371,0,0,0,2.466,2.842l3.017.884a.874.874,0,0,1,.624.833.87.87,0,0,1-.868.869h-1.951a1.8,1.8,0,0,1-1.006-.31.445.445,0,0,0-.247-.075A.469.469,0,0,0,269.048-3537.044Zm-10.468,3.539h-7.114A3.469,3.469,0,0,1,248-3536.97v-18.366a3.469,3.469,0,0,1,3.465-3.465h1.386v-2.079A3.122,3.122,0,0,1,255.97-3564a3.122,3.122,0,0,1,3.118,3.119v2.079h9.01v-2.079a3.123,3.123,0,0,1,3.119-3.119,3.123,3.123,0,0,1,3.118,3.119v2.079h1.039a3.469,3.469,0,0,1,3.465,3.465v2.64a14.548,14.548,0,0,0-7.276-1.947,14.571,14.571,0,0,0-14.555,14.554,14.4,14.4,0,0,0,1.57,6.583h0Z"
                        transform="translate(-248 3564)"
                        fill="white"
                      />
                    </svg>
                  </span>
                </v-avatar>

                <span class="orange--text">
                  {{
                    Array.isArray(item?.adsets)
                      ? item?.adsets.length
                      : item?.adsets
                  }}
                </span>
              </div>
            </template>
            <!-- end history -->

            <template v-slot:[`item.total_bid`]="{ item }">
              {{ getTotalBid(item) }}
            </template>
            <template v-slot:[`item.payment_method`]="{ item }">
              {{ getPaymentMethod(item) }}
            </template>

            <template v-slot:[`item.total_balance`]="{ item }">
              {{
                item.ad_account_balance % 1 != 0
                  ? item.ad_account_balance?.toFixed(2)
                  : item.ad_account_balance
              }}
            </template>
            <template v-slot:[`item.delivery_status`]="{ item }">
              <v-menu open-on-hover right offset-x nudge-right="15px">
                <template v-slot:activator="{ on, attrs }">
                  <div
                    v-bind="attrs"
                    v-on="on"
                    class="rounded-pill d-flex align-center justify-space-around primary lighten-5"
                    style="min-width: 45px; max-width: 80px; height: 80%"
                  >
                    <v-avatar
                      class="d-flex justify-center align-center"
                      size="22"
                    >
                      <SvgIcon
                        :icon="'delivery-icon'"
                        style="height: 80%; width: 80%"
                      >
                      </SvgIcon>
                    </v-avatar>
                    <span style="color: #115598">
                      {{ item.delivery_status.length }}
                    </span>
                  </div>
                </template>
                <v-list v-if="item.delivery_status">
                  <v-list-item v-if="item.delivery_status.length < 1" dense>
                    <v-list-item-icon>
                      <v-icon large>mdi-circle-small</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title class="custom-card-title-2">
                        {{ $tr("not_available") }}</v-list-item-title
                      >
                    </v-list-item-content>
                  </v-list-item>
                  <v-list-item
                    v-else
                    v-for="(status, index) in item.delivery_status"
                    :key="index"
                    dense
                  >
                    <v-list-item-icon>
                      <v-icon large>mdi-circle-small</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title class="custom-card-title-2">{{
                        status
                      }}</v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
            <template v-slot:[`item.ad_account_pixels`]="{ item }">
              <v-menu open-on-hover right offset-x nudge-right="15px">
                <template v-slot:activator="{ on, attrs }">
                  <div v-bind="attrs" v-on="on">Account Pixels</div>
                </template>
                <v-list v-if="item.ad_account_pixels">
                  <v-list-item v-if="item.ad_account_pixels.length < 1" dense>
                    <v-list-item-icon>
                      <v-icon large>mdi-circle-small</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title class="custom-card-title-2">
                        {{ $tr("not_available") }}</v-list-item-title
                      >
                    </v-list-item-content>
                  </v-list-item>
                  <v-list-item
                    v-else
                    v-for="(pixel_item, index) in item.ad_account_pixels"
                    :key="index"
                    dense
                  >
                    <v-list-item-icon>
                      <v-icon large>mdi-circle-small</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title class="custom-card-title-2">{{
                        pixel_item.pixel_id
                      }}</v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
            <template v-slot:[`item.pcode_landing_urls`]="{ item }">
              <v-menu right offset-x nudge-right="15px">
                <template v-slot:activator="{ on, attrs }">
                  <!-- <div v-bind="attrs" v-on="on">Product Landing Page Links</div> -->
                  <v-btn
                    rounded
                    color="primary"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    height="28px "
                  >
                    product landing page urls
                  </v-btn>
                </template>
                <v-list v-if="item.pcode_landing_urls">
                  <v-list-item
                    v-if="item.pcode_landing_urls.split(',').length < 1"
                    dense
                  >
                    <v-list-item-icon>
                      <v-icon large>mdi-circle-small</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title class="custom-card-title-2">
                        {{ $tr("not_available") }}</v-list-item-title
                      >
                    </v-list-item-content>
                  </v-list-item>
                  <v-list-item
                    v-else
                    v-for="(link_item, index) in item.pcode_landing_urls.split(
                      ','
                    )"
                    :key="index"
                    dense
                  >
                    <v-list-item-icon>
                      <v-icon large>mdi-circle-small</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title
                        class="custom-card-title-2"
                        @click="copy_url(link_item)"
                        >{{ link_item }}
                      </v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
            <template v-slot:[`item.avg_bids`]="{ item }">
              {{ item.campaign_adsets_avg_bid }}
            </template>
            <template v-slot:[`item.ad_id`]="{ item }">
              <div @click="copy_url(item.ad_id)" class="custom-card-title-3">
                {{ item.ad_id }}
              </div>
            </template>
            <template
              v-slot:[`item.name`]="{ item }"
              v-if="currentTab.key == 'campaign'"
            >
              <div @click="copy_url(item.name)" class="custom-card-title-3">
                {{ item.name }}
              </div>
            </template>
            <template v-slot:[`item.generated_link_date`]="{ item }">
              {{ localeHumanReadableTime(item.connection_date?.created_at) }}
            </template>

            <template v-slot:[`item.first_generated_date`]="{ item }">
              {{ localeHumanReadableTime(item.first_generated_date) }}
            </template>

            <template v-slot:[`item.first_generated_date2`]="{ item }">
              {{ localeHumanReadableTime2(item.first_generated_date) }}
            </template>
            <template v-slot:[`item.last_updated_date`]="{ item }">
              {{ localeHumanReadableTime(item.last_updated_date) }}
            </template>

            <!-- bid  istory  -->
            <template v-slot:[`item.bid_history`]="{ item }">
              <div
                @click.stop="toggleHistory(item, 'bid_history')"
                class="rounded-pill d-flex align-center justify-space-around purple lighten-5"
                style="min-width: 45px; max-width: 80px; height: 80%"
              >
                <v-avatar
                  class="d-flex justify-center align-center purple"
                  size="22"
                >
                  <SvgIcon
                    :icon="'bid-icon'"
                    style="height: 80%; width: 80%"
                    :fill="'#fff'"
                  >
                  </SvgIcon>
                </v-avatar>
                <span class="purple--text">
                  {{
                    currentTab.key == "ad_set"
                      ? 1
                      : currentTab.key == "campaign"
                      ? item.campaign_adsets.length
                      : item.adsets.length
                  }}
                </span>
              </div>
            </template>

            <template v-slot:[`item.index`]="{ index }">
              <v-avatar color="primary" size="22" class="white--text">
                {{ index + 1 }}
              </v-avatar>
            </template>
            <template v-slot:[`item.country`]="{ item }">
              <div class="d-flex">
                <FlagIcon
                  v-if="item.country.iso2"
                  :flag="item.country.iso2?.toLowerCase()"
                  :round="true"
                />
                <span style="padding: 4px">{{ item.country.name }}</span>
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
                    :src="item.logo"
                  ></v-img>
                </span>
                <span style="padding: 4px">{{ item.company.name }}</span>
              </div>
            </template>
            <template v-slot:[`item.bid`]="{ item }">
              <div
                class="rounded-pill d-flex align-center justify-space-around purple lighten-5"
                style="min-width: 110px; max-width: 120px; height: 80%"
              >
                <span class="purple--text">
                  {{ item.bid }}
                </span>
                <v-avatar
                  class="d-flex justify-center align-center purple"
                  size="20"
                  @click="toggleUpdateBidBudget(item, 'bid')"
                >
                  <v-icon size="13" color="white">mdi-pencil</v-icon>
                </v-avatar>
              </div>
            </template>
            <template v-slot:[`item.daily_budget`]="{ item }">
              <div
                class="rounded-pill d-flex align-center justify-space-around orange lighten-5"
                style="min-width: 110px; max-width: 120px; height: 80%"
              >
                <span class="orange--text">
                  {{ item.daily_budget }}
                </span>
                <v-avatar
                  class="d-flex justify-center align-center orange"
                  size="20"
                  @click="toggleUpdateBidBudget(item, 'budget')"
                >
                  <v-icon size="13" color="white">mdi-pencil</v-icon>
                </v-avatar>
              </div>
            </template>
            <template v-slot:[`item.bank_details`]="{ item }">
              <v-tooltip bottom style="">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="
                      white-space: nowrap;
                      padding-top: 1.5px;
                      padding-bottom: 1.5px;
                    "
                    class="px-5"
                  >
                    {{ $tr("bank_details") }}
                  </span>
                </template>

                <div style="width: 350px !important">
                  <v-card
                    style="
                      background: none !important;
                      position: relative !important;
                      z-index: 100;
                      width: 100% !important;
                      height: 100% !important;
                    "
                  >
                    <v-card-text
                      style="
                        background-image: linear-gradient(
                          to bottom,
                          #2e7be6,
                          #00b3ff
                        ) !important;
                        border-radius: 23px !important;
                      "
                    >
                      <v-row class="pb-0">
                        <v-spacer></v-spacer>
                        <v-avatar
                          class="me-5 pa-4"
                          style="background-color: #c3bfbf82 !important"
                          ><v-icon class="white--text" size="35px">{{
                            "mdi-bank"
                          }}</v-icon></v-avatar
                        >
                      </v-row>
                      <v-row class="mt-0">
                        <v-col cols="12 py-0">
                          <label for="" class="white--text label-size">{{
                            $tr("label_required", $tr("card_number"))
                          }}</label>
                          <v-text-field
                            solo
                            class="mt-1"
                            :value="item?.bank_account?.card_number"
                            color="black"
                            prepend-inner-icon="mdi-credit-card-settings"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="6 pt-0 pe-1">
                          <label for="" class="white--text label-size">{{
                            $tr("label_required", $tr("owner"))
                          }}</label>
                          <v-text-field
                            solo
                            class="mt-1"
                            prepend-inner-icon="mdi-account"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="6 pt-0 ps-1">
                          <label for="" class="white--text label-size">{{
                            $tr("label_required", $tr("bank_name"))
                          }}</label>
                          <v-text-field
                            solo
                            class="mt-1"
                            prepend-inner-icon="mdi-bank"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </div>
              </v-tooltip>
            </template>

            <!-- <template v-slot:activator="{ on, attrs }">
									<span
										v-bind="attrs"
										v-on="on"
										style="
											white-space: nowrap;
											padding-top: 1.5px;
											padding-bottom: 1.5px;
										"
										class="px-5"
									>
										{{ $tr("bank_details") }}
									</span>
								</template>
								<div class="white">
									<div v-if="item.bank_account.length > 0">
										<p class="pb-0 mb-0">
											card_number: {{ item.bank_account[0].card_number }}
										</p>
										<p class="pb-0 mb-0">
											owner: {{ item.bank_account[0].owner }}
										</p>
									</div>
								</div> -->
            <!-- <template v-slot:[`item.product_history`]="{ item }">
              <div @click.stop="toggleHistory(item, 'product_status_history')"
                class="rounded-pill d-flex align-center justify-space-around blue lighten-5"
                style="min-width: 45px; max-width: 80px; height: 80%">
                <v-avatar class="d-flex justify-center align-center blue" size="22">
                  <span style="height: 80%; width: 80%">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24.905" height="30.354" viewBox="0 0 24.905 30.354">
                      <path id="status-icon"
                        d="M269.015-3533.646H251.892a3.9,3.9,0,0,1-3.891-3.891v-22.571a3.893,3.893,0,0,1,2.342-3.572,6.22,6.22,0,0,0,6.219,5.906h7.783a6.221,6.221,0,0,0,6.218-5.906,3.893,3.893,0,0,1,2.343,3.572v22.571A3.9,3.9,0,0,1,269.015-3533.646ZM255.08-3548.1a1.935,1.935,0,0,0-1.376.57,1.948,1.948,0,0,0,0,2.752l3.358,3.358a1.959,1.959,0,0,0,1.376.57,1.934,1.934,0,0,0,1.376-.57l7.387-7.388a1.933,1.933,0,0,0,.57-1.376,1.933,1.933,0,0,0-.57-1.376,1.932,1.932,0,0,0-1.375-.57,1.934,1.934,0,0,0-1.376.57l-6.012,6.012-1.982-1.982A1.935,1.935,0,0,0,255.08-3548.1Zm9.265-12.01h-7.783A3.9,3.9,0,0,1,252.67-3564h15.566A3.9,3.9,0,0,1,264.345-3560.108Z"
                        transform="translate(-248 3564)" fill="white" />
                    </svg>
                  </span>
                </v-avatar>
                <span class="green--text">
                  {{
            item.active_products
                  }}
                </span>- <span class="pink--text">
                  {{
            item.inactive_products
                  }}
                </span>
              </div>
            </template> -->
            <template v-slot:[`item.view_profile`]="{ item }">
              <span
                class="d-flex align-center justify-center"
                style="width: 24px"
              >
                <svg
                  width="30.059"
                  height="30.059"
                  viewBox="0 0 30.059 30.059"
                  @click.stop="showAdvertisementProfile(item)"
                >
                  <path
                    id="graph"
                    d="M20.02,30.059H10.04A10.051,10.051,0,0,1,0,20.02V10.04A10.051,10.051,0,0,1,10.04,0h9.98A10.051,10.051,0,0,1,30.059,10.04v9.98A10.051,10.051,0,0,1,20.02,30.059Zm-9.4-13.8h0c.366.367.719.72,1.062,1.063.771.771,1.5,1.5,2.209,2.229A1.533,1.533,0,0,0,15,20.078,1.506,1.506,0,0,0,15.461,20a2.328,2.328,0,0,0,.832-.589c.985-.969,1.951-1.937,2.974-2.962l.012-.012,1.174-1.175.283.313c.335.37.65.72.969,1.064a1.219,1.219,0,0,0,.892.479,1.068,1.068,0,0,0,.4-.084,1.118,1.118,0,0,0,.7-1.161V14.446c0-1.166,0-2.372,0-3.568,0-.6-.286-.9-.874-.9H22.82c-.753,0-1.554.006-2.45.006-.926,0-1.842,0-2.616,0a1.086,1.086,0,0,0-1.1.672,1.02,1.02,0,0,0,.323,1.244c.333.312.677.614,1.01.907l.4.357-3.416,3.406L14,15.6l-.016-.016c-.692-.7-1.406-1.417-2.112-2.118a1.718,1.718,0,0,0-1.191-.6,1.763,1.763,0,0,0-1.213.613c-.5.5-1.012,1.005-1.5,1.5l-.007.007-.651.653c-.666.666-.96.973-.956,1.295s.313.633,1.013,1.3c.327.312.533.433.733.433s.411-.129.719-.447c.419-.43.815-.869,1.234-1.333.184-.2.37-.41.561-.618Z"
                    fill="#2d7ae5"
                  />
                </svg>
              </span>
            </template>
            <template v-slot:[`item.prod_sale_goal`]="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    class="d-flex align-center justify-center"
                    style="width: 26px"
                  >
                    <svg
                      v-if="
                        item.product_profile_info?.prod_sale_goal ==
                        'for_stock_disposal'
                      "
                      xmlns="http://www.w3.org/2000/svg"
                      width="26.444"
                      height="25.995"
                      viewBox="0 0 26.444 25.995"
                    >
                      <path
                        fill="red"
                        id="stock-disposal"
                        d="M-1521.1-4069.776a5.778,5.778,0,0,1,5.771-5.77,5.777,5.777,0,0,1,5.769,5.77,5.776,5.776,0,0,1-5.769,5.77A5.777,5.777,0,0,1-1521.1-4069.776Zm2.679,1h6.183v-2h-6.183Zm-2.706,3.209h-8.806v-2h7.788a7.139,7.139,0,0,0,1.019,2h0Zm-11.835-.046a1.943,1.943,0,0,1-2.032-2.036c-.012-.764-.01-1.541-.007-2.293v-.02c0-.337,0-.676,0-1.015V-4082.8a2.045,2.045,0,0,1,1.551-2.285l3.1-1.242.215-.085c1.957-.784,3.981-1.595,5.966-2.4a2.432,2.432,0,0,1,.927-.192,2.383,2.383,0,0,1,.9.185c1.955.789,3.982,1.6,5.769,2.313l.091.036,3.61,1.445a1.979,1.979,0,0,1,1.372,2.053c0,1.874,0,3.766,0,5.595v1.55a7.132,7.132,0,0,0-3.837-1.115c-.181,0-.363.007-.543.021v-.865c0-.844-.087-.932-.941-.932h-12.9c-.806,0-.907.1-.907.914v11.311c0,.718-.153.873-.859.877l-.245,0c-.218,0-.431,0-.646,0C-1532.541-4065.607-1532.75-4065.607-1532.959-4065.612Zm10.516-3.335h-7.486v-2h7.535a7.246,7.246,0,0,0-.1,1.17,7.386,7.386,0,0,0,.047.827h0Zm.421-3.38h-7.907v-2h9.072a7.162,7.162,0,0,0-1.164,2Zm2.68-3.38h-10.587v-2h13.4v.87a7.076,7.076,0,0,0-2.811,1.129Z"
                        transform="translate(1535.5 4089.5)"
                        stroke="rgba(0,0,0,0)"
                        stroke-width="1"
                      />
                    </svg>

                    <svg
                      v-else-if="
                        item.product_profile_info?.prod_sale_goal ==
                        'for_profit'
                      "
                      xmlns="http://www.w3.org/2000/svg"
                      width="25.507"
                      height="29.011"
                      viewBox="0 0 25.507 29.011"
                    >
                      <path
                        fill="orange"
                        id="profit"
                        d="M-1528.63-4062.3a8.282,8.282,0,0,1-4.763-7.975,11.347,11.347,0,0,1,2.951-7.254c.823-.945,1.593-1.937,2.416-2.883a1.028,1.028,0,0,1,.674-.32c1.527-.028,3.054-.014,4.582-.014s3.055-.015,4.581.015a1.062,1.062,0,0,1,.714.346,49.015,49.015,0,0,1,3.78,4.851,9.489,9.489,0,0,1,.527,9.746,8.013,8.013,0,0,1-4.907,3.983,15.491,15.491,0,0,1-4.792.8A13.559,13.559,0,0,1-1528.63-4062.3Zm5.565-4.6v1.081h1.258v-1.089a2.376,2.376,0,0,0,2.243-2.438c0-1.128-.437-2.11-2.675-2.632-1.259-.292-1.554-.6-1.554-1.117,0-.372.21-.861,1.132-.861,1.106,0,1.341.57,1.452,1.02h1.458a2.381,2.381,0,0,0-2.173-2.258v-.9h-1.255v.9a2.3,2.3,0,0,0-2.1,2.376c0,1.149.622,2,2.467,2.454,1.483.363,1.7.719,1.7,1.244,0,.546-.392.959-1.228.959a1.524,1.524,0,0,1-1.67-1.2h-1.464A2.678,2.678,0,0,0-1523.064-4066.9Zm-10.322-13.055a.782.782,0,0,1,.855-.775.776.776,0,0,1,.749.825.776.776,0,0,1-.8.776A.779.779,0,0,1-1533.386-4079.958Zm20.359.812c-.5-.013-.606-.256-.773-1.561-.6-.144-1.6.193-1.548-.917.043-.979.93-.686,1.541-.8.163-.605-.184-1.576.89-1.561,1.022.014.727.929.84,1.559,1.3.125,1.567.272,1.583.832.017.589-.268.754-1.569.886-.159.592.2,1.565-.911,1.565Zm-13.876-3.333a14.591,14.591,0,0,0-1.424-5.242,1.221,1.221,0,0,1,.1-.989c.219-.369.642-.34,1.024-.148.76.383,1.529.753,2.281,1.153a.454.454,0,0,0,.655-.108,11.137,11.137,0,0,1,.885-.888.807.807,0,0,1,1.253,0,11.15,11.15,0,0,1,.886.888.455.455,0,0,0,.654.113c.736-.392,1.491-.749,2.233-1.13a.838.838,0,0,1,1.095.107.852.852,0,0,1,.08,1.06,13.68,13.68,0,0,0-1.431,5.187Zm-6.533-3.209c-.634-.1-1.58.2-1.564-.864.011-.806.586-.8,1.148-.8.14,0,.279,0,.409-.012.121-1.3.3-1.622.861-1.619s.725.308.856,1.61c.621.1,1.592-.2,1.556.887-.027.778-.578.776-1.111.774a3.226,3.226,0,0,0-.453.019c-.1.657.173,1.57-.827,1.589h-.032C-1533.6-4084.111-1533.333-4085.043-1533.435-4085.688Z"
                        transform="translate(1535.5 4089.526)"
                        stroke="rgba(0,0,0,0)"
                        stroke-width="1"
                      />
                    </svg>
                    <span v-else> N/A </span>
                  </span>
                </template>
                {{ $tr(item.product_profile_info?.prod_sale_goal) }}
              </v-tooltip>
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
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import Advertisement from "~/configs/pages/advertisement/advertisement";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import AdvertisementTab from "../../../components/advertisement/AdvertisementTab.vue";
import GenerateLinkCreation from "../../../components/advertisement/GenerateLinkCreation.vue";
import AdvertismentFilter from "../../../components/advertisement/AdvertismentFilter.vue";
import FlagIcon from "../../../components/common/FlagIcon.vue";
// import AdBulkUpload from "../../../components/advertisement/bulk-upload/AdBulkUpload.vue";
import Dialog from "../../../components/design/Dialog/Dialog.vue";
import LabelForm from "../../../components/advertisement/label/LabelForm.vue";
import RemarkForm from "../../../components/advertisement/remarks/RemarkForm.vue";
import AdvRefreshPropmpt from "../../../components/advertisement/AdvRefreshPropmpt.vue";
import PopOver from "../../../components/design/PopOver.vue";
import FilterDateRange from "../../../components/advertisement/FilterDateRange.vue";
import RefreshPercentage from "../../../components/advertisement/RefreshPercentage.vue";
import GenerateConnection from "../../../components/advertisement/Connections/GenerateConnection.vue";
import AdvertisementGraph from "../../../components/advertisement/AdvertisementGraph.vue";
import BudgetForm from "../../../components/advertisement/budget/BudgetForm.vue";
import moment from "moment";

import AdReasons from "../../../components/advertisement/AdReasons.vue";
import AdHistory from "../../../components/advertisement/AdHistory.vue";
import CompaignBidHistory from "../../../components/companies/CompaignBidHistory.vue";
import BidHistory from "../../../components/advertisement/BidHistory.vue";
import BulkUploadStepper from "../../../components/advertisement/bulk-upload2/BulkUploadStepper.vue";
import BalanceModal from "../../../components/advertisement/balance/BalanceModal.vue";
import RefreshPopOver from "../../../components/design/RefreshPopOver.vue";
import MultiSelectionStatusReason from "../../../components/advertisement/MultiSelectionStatusReason.vue";
import CustomizeTab from "../../../components/customizeColumn/CustomizeTab.vue";
import AdvertisementGraphProfile from "../../../components/advertisement/graph/AdvertisementGraphProfile.vue";
import BankAccount from "../../../components/advertisement/balance/BankAccount.vue";
import updateBidBudget from "../../../components/advertisement/updateBidBudget.vue";
import SvgIcon from "../../../components/design/components/SvgIcon.vue";
import CheckAdsStepper from "../../../components/advertisement/check_ads/CheckAdsStepper.vue";
import VideoAdProfile from "../../../components/advertisement/video-ad-profile/VideoAdProfile.vue";
import ProductProfileStatistics from "../../../components/advertisement/product-statistics-profile/ProductProfileStatistics.vue";
import CanvaVideoView from "../../../components/advertisement/CanvaVideoView.vue";
import ProductProfileInfoStepper from "../../../components/advertisement/product-profile-info/ProductProfileInfoStepper.vue";
import ShowProductProfileInfoImage from "../../../components/advertisement/product-profile-info/ShowProductProfileInfoImage.vue";
import GenerateLinkCurrentAdAccount from "~/components/advertisement/Connections/GenerateLink/GenerateLinkCurrentAdAccount.vue";
import CMahdi from "../../../components/advertisement/CMahdi.vue";
import ChangeStatusMa from "../../../components/alert/ChangeStatusMa.vue";
import AdvertisemenItemCodeTab from "../../../components/advertisement/AdvertisemenItemCodeTab.vue";
export default {
  meta: {
    hasAuth: true,
    scope: "advv",
  },

  components: {
    CustomPageActions,
    CustomButton,
    PageHeader,
    PageFilters,
    DataTable,
    CustomizeColumn,
    AdvertisementTab,
    GenerateLinkCreation,
    FlagIcon,
    AdvertismentFilter,
    Dialog,
    // AdBulkUpload,
    LabelForm,
    RemarkForm,
    AdvRefreshPropmpt,
    PopOver,
    FilterDateRange,
    RefreshPercentage,
    GenerateConnection,
    AdvertisementGraph,
    AdReasons,
    AdHistory,
    BudgetForm,
    CompaignBidHistory,
    BidHistory,
    BulkUploadStepper,
    BalanceModal,
    RefreshPopOver,
    MultiSelectionStatusReason,
    CustomizeTab,
    AdvertisementGraphProfile,
    BankAccount,
    updateBidBudget,
    SvgIcon,
    CheckAdsStepper,
    VideoAdProfile,
    ProductProfileStatistics,
    CanvaVideoView,
    ProductProfileInfoStepper,
    ShowProductProfileInfoImage,
    GenerateLinkCurrentAdAccount,
    CMahdi,
    ChangeStatusMa,
    AdvertisemenItemCodeTab,
  },

  data() {
    return {
      prev_index: null,
      sortItem: {},
      tabDialog: false,
      crm_refresh: false,
      crm_refresh_percentage: 0,
      scroll_left: 0,
      scroll_right: 0,
      sheets: {
        campaign: [],
        ad_set: [],
        ad: [],
        connection_data: [],
      },
      selectedTabData: {},
      adBulkUpload: false,
      copy: false,
      tableRecords: [],
      totalRecords: 0,
      headers: [],
      systemTabs: Advertisement(this).productTab,
      tabItems: Advertisement(this).productTab,
      breadcrumb: Advertisement(this).breadcrumb,
      currentTab: Advertisement(this).productTab[0],
      apiCalling: false,
      axiosSource: null,
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
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },
      downloadDialog: false,
      showRefresh: false,
      canRefresh: false,
      refreshTime: null,
      isRefreshing: false,
      refresh_percentage: 0,
      platform_rf_percentage: 0,
      platform_rf_loading: false,

      showLabelPopOver: false,
      showRemarkPopOver: false,
      lableRemarkErrMessage: "",
      // for custom columns
      dialogKey: 0,
      columnDialog: false,
      selected_column: [],
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
        {
          key: "ispp_code",
          name: "Ispp Code",
          selected_headers: [],
          headers: [],
        },
        {
          key: "platform",
          name: "Platform",
          selected_headers: [],
          headers: [],
        },
        {
          key: "organization",
          name: "organization",
          selected_headers: [],
          headers: [],
        },
        {
          key: "ad_account",
          name: "Ad Account",
          selected_headers: [],
          headers: [],
        },
        {
          key: "campaign",
          name: "campaign",
          selected_headers: [],
          headers: [],
        },
        {
          key: "ad_set",
          name: "Adset",
          selected_headers: [],
          headers: [],
        },
        {
          key: "ad",
          name: "ad",
          selected_headers: [],
          headers: [],
        },
      ],

      statusChangeItem: [],
      selectedCampaignBudget: [],
      fetchBudget: false,
      balanceData: null,
      showHistory: false,
      showCrmPopOver: false,
      selectedPlatformRefresh: "",
      sumWithInitialLeft: 0,
      sumWithInitialRight: 0,
      showSnapchatRefresh: false,
      showtiktokRefresh: false,
      menu: false,
      refreshDates: null,
      showDateRefresh: false,
      date_rf_percentage: 0,
      minDate: null,
      updateItems: [
        { key: "bid", text: this.$tr("add_bid_value"), svg: "bid-icon" },
        {
          key: "budget",
          text: this.$tr("add_budget_value"),
          svg: "budget-icon",
        },
      ],
      refresh_percentage_color: "white",
      refresh_error: 0,
      showPromptActive: false,
      promptLeft: 0,
      totalActiveInactive: {},
      menu2: false,
      refreshDates2: null,
      showDateRefresh2: false,
      adAccounts: [],
      item_code_tab: "all",
    };
  },

  async created() {
    // this.tabItems = JSON.stringify(this.systemTabs);
    this.getFirstConnectionDate();
    await this.fetchHeaders();
    this.fetchItemsAccordingToTabKey(this.currentTab.key, {
      query: null,
      body: null,
    });

    this.fetchAdAccounts();
  },

  mounted() {
    this.$root.$on("apply_label_changes", this.applyLabelChanges);
    this.getRefreshProgress();
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
    updateAttachmentProductProfile(data) {
      this.tableRecords = this.tableRecords.map((item) => {
        if (item.pcode == data.item_code)
          item.product_profile_info.attachments = data.attachments;
        return item;
      });
      this.tableRecords = [...this.tableRecords];
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
    calculateSpendControl(item) {
      var result =
        parseFloat(
          item?.product_profile_info?.prod_max_adver_cost != "N/A"
            ? item?.product_profile_info?.prod_max_adver_cost
            : 0
        ) *
          parseFloat(
            item?.crm_total_orders ??
              item?.item_code_history_ads?.crm_total_orders ??
              item?.ad_through_connection?.crm_total_orders
          ) -
        parseFloat(
          item?.spend ??
            item?.item_code_history_ads?.spend ??
            item?.ad_through_connection?.spend
        );
      if (result > 0) {
        return (
          parseFloat(result).toFixed(2) +
          '<i class="ml-1 fa fa-arrow-up" style="color:#00bc81"></i>'
        );
      } else if (result < 0) {
        return (
          parseFloat(result).toFixed(2) +
          '<i class="ml-1 fa fa-arrow-down" style="color:#EE4F12"></i>'
        );
      } else {
        return '<i class="ml-2 fa fa-infinity" style="color:#2E7BE6"></i>';
      }
    },
    ...mapActions({
      fetchCustomColumns: "customColumns/fetchCustomColumns",
    }),
    toggleItemStatus() {
      this.$refs.changeStatusMaRef.toggleStatusDialog(
        this.currentTab.selectedItems[0]
      );
    },
    async changeItemStatus(item) {
      try {
        const result = await this.$axios.post(
          "advertisement/item-status",
          item
        );
        if (result.status == 201) {
          setTimeout(() => {
            this.$refs.AdvertisemenItemCodeTabRef.changeCount(item);
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
            this.$refs.changeStatusMaRef.close();
          }, 1000);
        } else {
          this.$refs.changeStatusMaRef.reset();
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch {
        this.$refs.changeStatusMaRef.reset();
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },

    deleteItemCode(fromEmit = false) {
      if (fromEmit) {
        const pcode = this.currentTab.selectedItems[0].pcode;
        this.tableRecords = this.tableRecords.filter(
          (row) => row.pcode != pcode
        );
        this.currentTab.selectedItems = [];
        try {
          this.tabItems[4].count.total = this.tabItems[4].count.total - 1;
        } catch (error) {
          console.log("error", error);
        }
        return;
      }
      if (this.currentTab.selectedItems.length == 0) {
        this.$toasterNA("red", this.$tr("select adset first"));
        return;
      }

      if (this.currentTab.selectedItems.length != 1) {
        this.$toasterNA(
          "red",
          this.$tr("only_one_item_is_allowed_for_this_operation")
        );
        return;
      }

      this.$TalkhAlertNA(
        "Are you sure ?", //text
        "delete", //icon
        async () => {
          this.$refs.itemCodeDeleteRefs.toggleDialog(
            this.currentTab.selectedItems[0]
          );
        }, // callback function
        "yes_delete" // btn text
      );
    },

    async fetchAdAccounts() {
      try {
        const url = `advertisement/connection/generate_link/allAdAccounts/1`;
        const { data } = await this.$axios.get(url);
        if (data.items) this.adAccounts = data.items;
      } catch (error) {
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
    async getFirstConnectionDate() {
      try {
        const response = await this.$axios.post(
          "advertisement/connection-first-date"
        );
        this.minDate = response.data.date;
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
        this.closeReasonDialog();
      }
    },

    toggleSelectedStatus() {
      const items = this.currentTab.selectedItems;
      const currentTab = this.currentTab.key;
      const statusTabs = ["campaign", "ad_set", "ad"];

      if (Array.isArray(items) && items.length > 0 && items.length <= 100) {
        let status = "";
        let status_col = "advertisement_status";
        if (statusTabs.includes(currentTab)) {
          status_col = "status";
        }
        for (let index = 0; index < items.length; index++) {
          const row = items[index];
          if (row[status_col] != status && status != "") {
            this.$toasterNA(
              "primary",
              this.$tr("all item status must be the same type")
            );

            return;
          }
          status = row[status_col];
        }
        let newStatus = "";
        if (statusTabs.includes(currentTab)) {
          newStatus = status == "ACTIVE" ? "inactive" : "active";
        } else {
          newStatus = status == "active" ? "inactive" : "active";
        }

        if (this.$refs.multiSelectionStatusReasonRef) {
          this.$refs.multiSelectionStatusReasonRef.toggleDialog(
            items,
            newStatus
          );
        }
      } else if (Array.isArray(items) && items.length > 100) {
        this.$toasterNA(
          "primary",
          this.$tr("cant_do_operation_on_more_than_100_items_at_the_same_time")
        );
      } else {
        this.$toasterNA(
          "primary",
          this.$tr("please_select_at_least_one_item_to_share")
        );
      }
    },
    MultiStatusChange() {
      for (let i = 0; i < this.currentTab.selectedItems.length; i++) {
        this.tableRecords = this.tableRecords.map((item) => {
          if (item.id == this.currentTab.selectedItems[i].id) {
            item.advertisement_status =
              item.advertisement_status == "active" ? "inactive" : "active";
          }
          return item;
        });
      }
    },
    async refreshDatatable() {
      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);
      if (this.$refs.advertisementGraph) {
        this.$refs.advertisementGraph.fetchGraphTabs(filterData);
        this.$refs.advertisementGraph.fetchGraphData(filterData);
      }
      await this.resetPagination();
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: this.options,
        body: filterData,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
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
      if (this.$refs.advertisementGraph) {
        this.$refs.advertisementGraph.fetchGraphTabs(body);
        this.$refs.advertisementGraph.fetchGraphData(body);
      }
      await this.resetPagination();
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: this.options,
        body: body,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
    },
    async clearRight() {
      // clear fake column
      if (this.selectedIndex == 4) {
        this.all_headers[4].selected_headers =
          this.all_headers[4].selected_headers?.filter(
            (item) => item.id != "cc"
          );
      }
      const currentTabIndex = this.getCurrentTabIndex();
      // const rightArray = this.tabItems.slice(
      // 	currentTabIndex + 1,
      // 	this.tabItems.length,
      // );
      // for (let i = 0; i < rightArray.length; i++) {
      // 	const el = rightArray[i].key;
      this.tabItems = this.tabItems.map((row) => {
        row.selectedItems = [];
        return row;
      });
      this.sumWithInitialRight = 0;
      // }
      await this.resetPagination();
      const body = this.filterOptions(currentTabIndex);
      if (this.$refs.advertisementGraph) {
        this.$refs.advertisementGraph.fetchGraphTabs(body);
        this.$refs.advertisementGraph.fetchGraphData(body);
      }
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

    onItemStatusClicked(item) {
      if (this.currentTab.selectedItems.length > 1) {
        this.toggleSelectedStatus();
      } else {
        if (this.checkPlatforms(item)) {
          this.status_loading = true;
          this.statusChangeItem = item;
          const newStatus = item.status == "ACTIVE" ? "inactive" : "active";
          this.$refs.reasonRef.fetchAllReasons(null, newStatus);
        }
      }
    },

    async changeAdvertisementStatus(reasons) {
      try {
        const tab = this.currentTab.key;
        const id = this.statusChangeItem.id;
        this.updateStatusItemId = id;
        const body = {
          section: tab,
          item_id: id,
          status: this.statusChangeItem.status,
          reason_ids: reasons,
        };
        const url = `/advertisement/change-status`;
        const { data } = await this.$axios.put(url, body);
        if (data.status == "success") {
          const currentStatus = this.statusChangeItem.status;
          const newStatus = currentStatus == "ACTIVE" ? "INACTIVE" : "ACTIVE";
          if (this.updateStatusItemId == this.statusChangeItem.id) {
            this.statusChangeItem.status = newStatus;
          }
          // const message = this.$tr(
          //   "record_item_status_changed",
          //   this.$tr(data.type)
          // );
          // this.$toastr.s(message);
          this.$refs.reasonRef.submitResult(true);

          // this.$toasterNA("green", this.$tr(message));
        } else {
          // this.$toastr.e(this.$tr(data.status));
          this.$refs.reasonRef.submitResult(false);
          this.$toasterNA("red", this.$tr(data.status));
        }
      } catch (error) {
        this.$refs.reasonRef.submitResult(false);

        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
      this.updateStatusItemId = null;
    },

    async submitReasons(reasons) {
      const currentTab = this.currentTab.key;
      const statusTabs = ["campaign", "ad_set", "ad"];
      if (statusTabs.includes(currentTab)) {
        this.changeAdvertisementStatus(reasons);
        this.closeReasonDialog();
        return;
      }
      const data = {
        model: this.currentTab.key,
        id: this.statusChangeItem.id,
        status: this.statusChangeItem.advertisement_status,
        reason_ids: reasons,
      };
      try {
        const response = await this.$axios.post("assign-reason", data);
        this.$refs.reasonRef.submitResult(true);
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
        this.$refs.reasonRef.submitResult(false);
        this.closeReasonDialog();
      }

      this.status_loading = false;
    },
    closeReasonDialog() {
      this.status_loading = false;
      const currentTab = this.currentTab.key;
      const statusTabs = ["campaign", "adset", "ads"];
      if (statusTabs.includes(currentTab)) {
        return;
      }

      const index = this.tableRecords.findIndex(
        (row) => row == this.statusChangeItem
      );
      this.tableRecords[index].advertisement_status =
        this.tableRecords[index].advertisement_status == "active"
          ? "inactive"
          : "active";
    },

    changeRecordStatus(item) {
      this.status_id = item.id;

      this.status_loading = true;
      this.statusChangeItem = item;
      this.$refs.reasonRef.fetchAllReasons(
        null,
        this.statusChangeItem.advertisement_status
      );
    },

    async getRefreshProgress() {
      this.$echo
        .private(`refresh-ads.${this.$auth.user.id}`)
        .listen("SendRefreshAdsEvent", ({ data }) => {
          this.refresh_percentage_color = "white";
          if (data?.status && data?.status == "faild") {
            this.refresh_error++;
            this.refresh_percentage_color = "red";
            console.log(
              "refresh_error[" + data.item_index + 1 + "]",
              data.faild_message
            );
          }
          if (data.platform) {
            this.platform_rf_loading = true;
          } else if (data.dates) {
            this.showDateRefresh = true;
          } else {
            this.isRefreshing = true;
          }

          const total = parseInt(data.total);
          const current = parseInt(data.item_index) + 1;
          if (total == current) {
            this.resetPagination();
            this.fetchItemsAccordingToTabKey(this.currentTab.key, {
              query: null,
              body: null,
            });
            setTimeout(() => {
              if (data.platform) {
                this.platform_rf_loading = false;
                this.platform_rf_percentage = 0;
              } else if (data.dates) {
                this.showDateRefresh = false;
                this.date_rf_percentage = 0;
              } else {
                this.isRefreshing = false;
                this.refresh_percentage = 0;
              }
              this.refresh_percentage_color = "white";
              // this.$toastr.s(this.$tr("successfully_refreshed"));
              if (this.refresh_error > 0)
                this.$toasterNA(
                  "orange",
                  this.$tr("refreshed_but_with_some_failds")
                );
              else this.$toasterNA("green", this.$tr("successfully_refreshed"));
            }, 3000);
          }
          const percentage = (100 / total) * current;
          const twoDecimal = Math.round(percentage);
          if (data.platform) {
            this.platform_rf_percentage = twoDecimal == 100 ? 99 : twoDecimal;
          } else if (data.dates) {
            this.date_rf_percentage = twoDecimal == 100 ? 99 : twoDecimal;
          } else {
            if (twoDecimal == 100) {
              this.refresh_percentage = 99;
            } else {
              this.refresh_percentage = twoDecimal;
            }
          }
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

    async fetchHeaders() {
      try {
        if (this.ColumnAxiosSource)
          this.ColumnAxiosSource.cancel("Request-Cancelled");
        this.ColumnAxiosSource = this.$axios.CancelToken.source();

        if (this.all_headers[this.selectedIndex].headers.length == 0) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "advertisement_" + this.currentTab.key,
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

    async toggleRefresh(platform = "") {
      try {
        if (platform == "snapchat")
          this.showSnapchatRefresh = !this.showSnapchatRefresh;
        else if (platform == "tiktok")
          this.showtiktokRefresh = !this.showtiktokRefresh;
        else this.showRefresh = !this.showRefresh;

        let param = { platform: platform };
        if (
          this.showRefresh ||
          this.showtiktokRefresh ||
          this.showSnapchatRefresh
        ) {
          let { data } = await this.$axios.get(`advertisement/last-refresh`, {
            params: param,
          });
          // if (data.no_record) {
          // 	this.canRefresh = false;
          // 	return;
          // }
          // if (data.date == null && param.platform) {
          // 	this.canRefresh = true;
          // 	return;
          // }
          let now = moment().format("DD/MM/YYYY HH:mm:ss");
          let ms = moment(now, "DD/MM/YYYY HH:mm:ss").diff(moment(data.date));
          this.refreshTime = data.date;
          // if (ms > 40 * 60000) {
          // 	// 40 minsp;
          // 	this.canRefresh = true;
          // } else {
          // 	this.canRefresh = false;
          // }

          this.canRefresh = true;
        }
      } catch (e) {}
    },
    getTimeAgo(time) {
      return moment(new Date(`${time} UTC`))
        .local(true)
        .fromNow(true);
    },
    getTimeHour(time) {
      return moment(new Date(`${time} UTC`))
        .local(true)
        .format("LT");
    },
    async refreshAds() {
      this.refresh_error = 0;
      try {
        // let url = `advertisement/connection/refresh`;
        // await this.$axios.get(url);
        let data = { frereshAll: true, dates: moment().format("YYYY-MM-DD") };
        let url = `advertisement/connection/refresh-platform`;
        let count = await this.$axios.post(url, data);
        if (count.data == 0) {
          this.isRefreshing = false;
          this.$toasterNA("red", this.$tr("no_active_ads_to_refresh"));
          return;
        } else {
          this.$toasterNA(
            "primary",
            this.$tr("your Data Wil Refresh until 2 Min")
          );
          this.isRefreshing = false;
          return;
        }
        this.refresh_percentage = 1;
      } catch (_) {
        this.isRefreshing = false;
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    async refreshAdsByDate() {
      this.refresh_error = 0;
      try {
        if (this.refreshDates != null) {
          this.menu = false;
          this.showDateRefresh = true;
          let data = { dates: this.refreshDates };
          let url = `advertisement/connection/refresh-platform`;
          let count = await this.$axios.post(url, data);

          if (count.data == 0) {
            this.showDateRefresh = false;
            this.$toasterNA("red", this.$tr("no_active_ads_to_refresh"));
            return;
          } else {
            this.$toasterNA(
              "blue",
              this.$tr("your Data Wil Refresh until 2 Min")
            );
            this.showDateRefresh = false;
            return;
          }
          this.date_rf_percentage = 0;
        }
        this.menu = false;
      } catch (error) {
        console.log(error);
        this.showDateRefresh = false;
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },

    async refreshPlatformAd(platform) {
      this.refresh_error = 0;
      try {
        this.selectedPlatformRefresh = platform;
        this.platform_rf_loading = true;
        let data = { platform: platform };
        let url = `advertisement/connection/refresh-platform`;
        await this.$axios.post(url, data);
        this.$toasterNA(
          "primary",
          this.$tr("refresh will take about one minites")
        );
        this.platform_rf_loading = false; //clean it
      } catch (_) {
        this.platform_rf_loading = false;
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    togglePlatformReferesh() {
      this.showSnapchatRefresh = true;
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

    async searchById(id) {
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
      // this.selectData(response);
      // } catch (error) {
      // 	HandleException.handleApiException(this, error);
      // }
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

    async onFilterRecords(filterData) {
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
      this.$refs.advertismentFilterRefs.changeModel(this.currentTab.key);
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
      if (this.$refs.advertisementGraph) {
        const items = Object.values(filterData);
        const filteredItems = items.filter((item) => {
          if (Array.isArray(item)) return item.length > 0;
          return false;
        });
        if (filteredItems.length > 0) {
          this.$refs.advertisementGraph.fetchGraphTabs(filterData);
          this.$refs.advertisementGraph.fetchGraphData(filterData);
        }
      }

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

    filterOptions(tabIndex) {
      const prevTabItem = this.currentTab;
      let options = { prev: prevTabItem.key };
      const items = this.tabItems;
      // const items = this.tabItems.slice(0, tabIndex);
      const eachItem = (tab) => {
        const ids = tab.selectedItems.map(({ id }) => id);
        options[tab.key] = ids;
      };
      items.forEach(eachItem);
      options = { ...options, ...this.filterByDate };
      return options;
    },

    async fetchItemsAccordingToTabKey(tabkey, options, item_code_tab = false) {
      try {
        if (!options.searchValue) this.apiCalling = true;
        if (this.axiosSource) this.axiosSource.cancel("Request-Cancelled");
        this.axiosSource = this.$axios.CancelToken.source();
        let url = `advertisement/data/${tabkey}`;
        const header = {
          cancelToken: this.axiosSource.token,
          params: { ...options.query, search_by_id: options.search_by_id },
        };
        if (!item_code_tab) {
          this.fetchDataCounts({
            ...options.body,
            searchContent: options.query?.searchContent,
            ...this.filterData,
          });
        }
        // for item code tab
        if (this.currentTab.key == "item_code") {
          options.body.item_code_tab = this.item_code_tab;
        }

        const { data } = await this.$axios.post(
          url,
          {
            ...options.body,
            searchValue: options?.searchValue,
            ...this.filterData,
          },
          header
        );

        if (data.result && data.items) {
          this.setSelectedTabWithData(data.items);
          this.tableRecords = data.items;
          this.totalRecords = data.total;
          if (this.isSearched) {
            // change search 4
            this.tabItems = this.tabItems.map((item) => {
              if (item.key == this.isSearched.key) {
                this.isSearched.total =
                  this.isSearched.total ?? data.total ?? "0";
                item.count = this.isSearched.total;
              }
              return item;
            });
          }
        } else if (data.result && data.item) {
          this.totalRecords = data.total;
          this.tableRecords = this.tableRecords.filter(
            (item) => item.id != data.item.id
          );
          this.tableRecords.unshift(data.item);
          this.currentTab.selectedItems.push(data.item);
          if (this.isSearched) {
            // change search 5
            this.tabItems = this.tabItems.map((item) => {
              if ((item.key == this.isSearched.key) == tabkey) {
                item.count = data.total ? data.total : "0";
              }
              return item;
            });
          }
        } else if (!data.item) {
          this.$root.$emit("removeSearchId", this.searchId);
        } else if (data.result == false) {
          // this.$toastr.e(this.$tr(data.code));
          this.$toasterNA("red", this.$tr(data.code));
        }
        this.apiCalling = false;
      } catch ({ message }) {
        if (message == "Request-Cancelled") {
          if (!options.searchValue) this.apiCalling = true;
        } else {
          this.apiCalling = false;
        }
      }
    },
    addProductProfile(item) {
      this.tableRecords = this.tableRecords.map((i) => {
        if (i.pcode == item.item_code) i.product_profile_info = item;
        return i;
      });
    },
    async fetchDataCounts(passedData) {
      try {
        if (passedData.searchContent) {
          // change search 6
          passedData.searchContent = "";
        }
        if (this.axiosSourceCount)
          this.axiosSourceCount.cancel("Request-Cancelled");
        this.axiosSourceCount = this.$axios.CancelToken.source();

        const { data } = await this.$axios.post(
          "advertisement/counts",
          {
            ...passedData,
          },
          {
            cancelToken: this.axiosSourceCount.token,
          }
        );
        if (data.result) {
          let counts = data.counts;
          // reset item status count
          this.$refs.AdvertisemenItemCodeTabRef.resetItem();
          this.tabItems = this.tabItems.map((item) => {
            if (item.key in counts) {
              if (this.isSearched && this.isSearched.key != item.key)
                // change search 7
                item.count = counts[item.key];
            } else if (this.isSearched) {
              // change search 8
              item.count = this.isSearched.total ?? "0";
            }
            return item;
          });
        }
      } catch (error) {
        console.log("error count", error);
      }
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
      if (this.$refs.advertisementGraph) {
        this.$refs.advertisementGraph.fetchGraphTabs(body);
        this.$refs.advertisementGraph.fetchGraphData(body);
      }
      body.prev = prevTabItem.key;
      await this.resetPagination();
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: this.options,
        body: body,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
    },
    async copy_url(item) {
      await navigator.clipboard.writeText(item);
      this.$toasterNA("green", this.$tr("successfully_copied"));
    },
    openInsertDialog(value) {
      if (value == 2) {
        if (this.currentTab.selectedItems.length != 1)
          this.$toasterNA(
            "red",
            this.$tr("only_one_item_is_allowed_for_this_operation")
          );
        else
          this.$refs["GenerateLinkCurrentAdAccountRef"].toggleConnection(
            this.currentTab.selectedItems[0]
          );
        return;
      }
      this.$refs["genereteConnectionRef"].toggleConnection();

      // this.$refs.generateLinkCreationRef.toggle();
    },
    openCheckAdsDialog() {
      this.$refs.CheckAdsref.toggleCheckAds();

      // this.$refs.generateLinkCreationRef.toggle();
    },
    targetDialog() {
      if (this.currentTab.selectedItems.length == 0) {
        this.$toasterNA("red", this.$tr("select adset first"));
        return;
      }

      if (this.currentTab.selectedItems.length != 1) {
        this.$toasterNA(
          "red",
          this.$tr("only_one_item_is_allowed_for_this_operation")
        );
        return;
      }
      this.$refs.target.toggleDialog(this.currentTab.selectedItems[0]);
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
    addRemark(id) {
      this.tableRecords = this.tableRecords.map((item) => {
        if (item.id == id) {
          item.remarks_count++;
        }
        return item;
      });
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
    generateAvatar(item) {
      switch (this.currentTab.key) {
        case "project":
          return item.name[0].toUpperCase();
        case "item_code":
          return item.pcode[0].toUpperCase();
        case "organization":
          return item.name[0].toUpperCase();
        case "ad_account":
          return item.name[0].toUpperCase();
        case "campaign":
          return item.name[0].toUpperCase();
        case "ad_set":
          return item.name[0].toUpperCase();
        case "ad":
          return item.ad_name[0].toUpperCase();
        default:
          break;
      }
      return "C";
    },

    getTotalBid(item) {
      let total = 0;
      let adsets = Array.isArray(item.adsets)
        ? item.adsets
        : item.campaign_adsets;
      adsets?.forEach((adset) => {
        total = total + parseFloat(adset.bid);
      });
      return total;
    },
    getPaymentMethod(item) {
      let method = "";
      if (item.connection !== undefined || item?.platform_name) {
        const platform =
          item?.platform_name || item.connection?.platform?.platform_name;
        switch (platform) {
          case "facebook":
            method = "Credit Card";
            break;
          case "tiktok":
            method = "Debit Card";
            break;
          case "snapchat":
            method = "Credit Card";
            break;
          default:
            method = "unkown";
            break;
        }
      }
      return method;
    },

    toggleBankModal() {
      if (
        this.currentTab?.selectedItems?.length < 1 ||
        this.currentTab?.selectedItems?.length > 1
      ) {
        this.$toasterNA("red", this.$tr("please_select_one_option"));
        return false;
      }

      this.$refs.bankModal.toggleBankModal(this.currentTab?.selectedItems[0]);
    },
    toggleBalanceModal() {
      if (
        this.currentTab?.selectedItems?.length < 1 ||
        this.currentTab?.selectedItems?.length > 1
      ) {
        this.$toasterNA("red", this.$tr("please_select_one_option"));
        return false;
      }
      this.balanceData = this.currentTab?.selectedItems;

      this.$refs.balanceModal.toggleDialog(this.currentTab?.selectedItems[0]);
    },

    toggleUpdateBidBudget(item, type) {
      this.$refs.updateBidBudgetRef.toggleDialog(item, type);
    },
    onUpdateBidBudget(result) {
      if (result.status === 200) {
        this.toggleUpdateBidBudget();
        this.$toasterNA("green", this.$tr("successfully_updated"));

        const index = this.tableRecords.findIndex(
          (row) => row.adset_id == result.data.adset_id
        );

        this.tableRecords[index].bid = result.data.bid;
        this.tableRecords[index].daily_budget = result.data.daily_budget;
      } else {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },

    localeHumanReadableTime2(date) {
      return moment(date).fromNow();
    },
    async refreshCrm() {
      let { data } = await this.$axios.get(`advertisement/last-refresh`, {
        params: { platform: "crm" },
      });
      this.showCrmPopOver = true;

      if (data.no_record) {
        this.canRefresh = false;
        return;
      }
      let now = moment().format("DD/MM/YYYY HH:mm:ss");
      let ms = moment(now, "DD/MM/YYYY HH:mm:ss").diff(moment(data.date));
      this.refreshTime = data.date;
      if (ms > 40 * 60000) {
        // 40 minsp;
        this.canRefresh = true;
      } else {
        this.canRefresh = false;
      }
    },
    toggleHistory(item, tab) {
      this.$refs.historyRefs.openDialog({
        campaign_id: item.id,
        item: item,
        model: this.currentTab.key,
        tab: tab,
        dateRange: this.filterByDate,
      });
    },

    getCrmPercentage(percentage) {
      this.$toasterNA(
        "primary",
        this.$tr("refresh will take about one minites")
      );
      return 10;

      this.crm_refresh_percentage = percentage;

      this.crm_refresh = true;
      setTimeout(() => {
        if (this.crm_refresh_percentage == 100) {
          this.crm_refresh_percentage = 0;
          this.crm_refresh = false;

          this.$toasterNA("green", this.$tr("successfully_refreshed"));
        }
      }, 3000);
    },
    showAdvertisementProfile(item) {
      if (this.currentTab.key == "item_code") {
        this.currentTab.selectedItems = [item];
        this.$refs.productProfile.openDialog(item);
        return;
      }
      this.currentTab.selectedItems = [item];
      this.$refs.advertisementRefs.showProfile([item]);
    },

    addBalance(value) {
      this.tableRecords.map((row) => {
        if (row == this.currentTab.selectedItems[0])
          row.ad_account_balance = value;
      });
    },
    showPrompt(data) {
      this.showPromptActive = true;
      this.totalActiveInactive = data.data;

      this.promptLeft = data.ofset;
    },
    // view canva video 3
    viewVideoProfile(item) {
      if (item && item?.includes("https://www.canva.com")) {
        this.$refs.canvaVideoViewRefs.openVideo(
          item.slice(0, 41) + "view?embed"
        );
      }
      // this.$refs.videoProfileRefs.openDialog(item);
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
        this.currentTab.selectedItems[0].pcode
      );
    },

    refreshMissedAds() {
      let data = { dates: this.refreshDates2 };
      let url = `advertisement/get-missed-ads`;
      let count = this.$axios.post(url, data);
      console.log(count);
    },
    showProductProfileInfo(item) {
      if (item) this.$refs.ShowProductProfileInfoImageRef.showDialog(item);
    },
  },
};
</script>

<style>
.link:hover {
  cursor: copy;
}

.link .primary:hover {
  background-color: var(--v-primary-lighten1) !important;
}

.chip6 .v-chip__content {
  width: 100% !important;
  justify-content: space-between !important;
}
</style>
<style scoped>
.custom-card-title-2 {
  font-size: 16px;
  font-weight: 400;
  cursor: copy;
  color: #777;
}

.custom-card-title-3 {
  cursor: copy;
}

.remarks-count {
  padding: 0px 4px;
  position: absolute;
  top: -5px;
  left: 12px;
  border: 2px solid rgb(255, 255, 255) !important;
  color: rgb(255, 255, 255) !important;
  height: 16px;
  line-height: 16px;
  font-size: 10px;
  border-radius: 5px !important;
  min-width: 20px;
}

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
