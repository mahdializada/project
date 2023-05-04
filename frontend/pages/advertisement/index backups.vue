<template>
	<v-container fluid class="pa-0">
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
		<BalanceModal ref="balanceModal" />

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
			@applyChanges="applyLabelChanges($event)"
		/>
		<RemarkForm
			ref="remarkRef"
			:tab="currentTab.key"
			@addRemark="addRemark"
			:selected_items.sync="currentTab.selectedItems"
		/>
		<AdReasons
			:showReasonDialog="showReasonDialog"
			@onSubmit="submitReasons"
			@closeDialog="closeReasonDialog()"
			:tabName="currentTab.name"
			ref="reasonRef"
		/>

		<client-only>
			<MultiSelectionStatusReason ref="multiSelectionStatusReasonRef" />
			<GenerateConnection
				v-if="$isInScope('advc')"
				ref="genereteConnectionRef"
			/>
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
							v-if="$isInScope('advc')"
							icon="fa-plus"
							@click="openInsertDialog"
							:text="$tr('create_item', $tr('link'))"
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
					>
						<template slot="afterDelete">
							<!-- toggleBankModal -->
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
							<!-- snapchat Referesh -->
							<RefreshPercentage
								v-if="
									selectedPlatformRefresh == 'snapchat' &&
									(platform_rf_percentage > 0 || platform_rf_loading)
								"
								:percentage="platform_rf_percentage"
								type="warning"
								:textColor="refresh_percentage_color"
							/>
							<div
								v-else
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
								/>
							</div>
							<!-- tiktok refresh -->
							<RefreshPercentage
								v-if="
									selectedPlatformRefresh == 'tiktok' &&
									(platform_rf_percentage > 0 || platform_rf_loading)
								"
								:percentage="platform_rf_percentage"
								type="label"
								:textColor="refresh_percentage_color"
							/>
							<div
								v-else
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
								/>
							</div>

							<!-- <CustomButton icon="fa-sync" text="CRM Refresh" type="primary" /> -->
							<refresh-percentage
								:percentage="crm_refresh_percentage"
								v-if="crm_refresh"
							/>
							<RefreshPopOver
								v-model="showCrmPopOver"
								color="#00bc81"
								@refresh="getCrmPercentage($event)"
								v-show="!crm_refresh"
								@showPercentage="crm_refresh = true"
							>
								<template v-slot:activator>
									<CustomButton
										@click="refreshCrm()"
										icon="fa-sync"
										text="CRM"
										type="refresh"
										v-if="$isInScope('advrefresh')"
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
							<PopOver v-model="showLabelPopOver" color="#2dccff">
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
							<PopOver v-model="showRemarkPopOver" color="#ff0070">
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
							<FilterDateRange
								:dateRangeProp="filterByDate"
								:hide-title="true"
								@dateChanged="onDateRangeSubmit"
							/>
							<!-- test -->

							<v-menu
								ref="menu"
								v-model="menu"
								:close-on-content-click="false"
								:return-value.sync="refreshDates"
								transition="scale-transition"
								offset-y
								min-width="auto"
							>
								<template v-slot:activator="{ on, attrs }">
									<div
										v-bind="attrs"
										v-on="on"
										class="d-flex ml-1 align-center"
									>
										<RefreshPercentage
											v-if="date_rf_percentage > 0 || showDateRefresh"
											:percentage="date_rf_percentage"
											type="label"
										/>
										<CustomButton
											v-else
											icon="fa-sync"
											text="refresh_by_date"
											type="label"
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
							<!-- end -->
							<CustomButton
								@click="refreshDatatable"
								icon="fa-sync"
								text="refresh_table"
								type="refresh"
							/>
							<!--
							<CustomButton @click="toggleSelectedStatus" icon="fa-toggle-off" text="Toggle Status"
								type="danger2" /> -->
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
								:items="tabItems"
								@onChange="onTabChange"
								@unselect="onUnselected"
								:totalRecords="totalRecords"
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
						ref="projectTableRef"
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
						<template v-slot:[`item.logo-avatar`]="{ item }">
							<v-avatar :size="30" color="blue-grey darken-3">
								<span class="white--text text-h6 pa-1">
									{{ generateAvatar(item) }}
								</span>
							</v-avatar>
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
												@click.stop="toggleHistory(item, 'label')"
											>
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
								:flag="item.iso2.toLowerCase()"
								:round="true"
							/>
						</template>
						<template v-slot:[`item.status`]="{ item }">
							<v-switch
								@click.stop="onItemStatusClicked(item)"
								:input-value="item.status.toLowerCase() == 'active'"
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
								@click.stop="toggleHistory(item, 'remark')"
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
							{{ localeHumanReadableTime2(item.connection_date.created_at) }}
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
							{{ item.balances_sum_balance }}
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
							{{ localeHumanReadableTime(item.connection_date.created_at) }}
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
									:flag="item.country.iso2.toLowerCase()"
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
							<v-tooltip bottom>
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
								<div class="white">
									<div v-if="item.bank_account.length > 0">
										<p class="pb-0 mb-0">
											card_number: {{ item.bank_account[0].card_number }}
										</p>
										<p class="pb-0 mb-0">
											owner: {{ item.bank_account[0].owner }}
										</p>
									</div>
								</div>
							</v-tooltip>
						</template>
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
					</DataTable>
				</v-col>
			</v-row>
		</client-only>
	</v-container>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import Advertisement from "~/configs/pages/advertisement/advertisement";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import AdvertisementTab from "../../components/advertisement/AdvertisementTab.vue";
import GenerateLinkCreation from "../../components/advertisement/GenerateLinkCreation.vue";
import AdvertismentFilter from "../../components/advertisement/AdvertismentFilter.vue";
import FlagIcon from "../../components/common/FlagIcon.vue";
// import AdBulkUpload from "../../components/advertisement/bulk-upload/AdBulkUpload.vue";
import Dialog from "../../components/design/Dialog/Dialog.vue";
import LabelForm from "../../components/advertisement/label/LabelForm.vue";
import RemarkForm from "../../components/advertisement/remarks/RemarkForm.vue";
import AdvRefreshPropmpt from "../../components/advertisement/AdvRefreshPropmpt.vue";
import PopOver from "../../components/design/PopOver.vue";
import FilterDateRange from "../../components/advertisement/FilterDateRange.vue";
import RefreshPercentage from "../../components/advertisement/RefreshPercentage.vue";
import GenerateConnection from "../../components/advertisement/Connections/GenerateConnection.vue";
import AdvertisementGraph from "../../components/advertisement/AdvertisementGraph.vue";
import BudgetForm from "../../components/advertisement/budget/BudgetForm.vue";
import moment from "moment";

import AdReasons from "../../components/advertisement/AdReasons.vue";
import AdHistory from "../../components/advertisement/AdHistory.vue";
import CompaignBidHistory from "../../components/companies/CompaignBidHistory.vue";
import BidHistory from "../../components/advertisement/BidHistory.vue";
import BulkUploadStepper from "../../components/advertisement/bulk-upload2/BulkUploadStepper.vue";
import BalanceModal from "../../components/advertisement/balance/BalanceModal.vue";
import RefreshPopOver from "../../components/design/RefreshPopOver.vue";
import MultiSelectionStatusReason from "../../components/advertisement/MultiSelectionStatusReason.vue";
import CustomizeTab from "../../components/customizeColumn/CustomizeTab.vue";
import AdvertisementGraphProfile from "../../components/advertisement/graph/AdvertisementGraphProfile.vue";
import BankAccount from "../../components/advertisement/balance/BankAccount.vue";
import updateBidBudget from "../../components/advertisement/updateBidBudget.vue";
import SvgIcon from "../../components/design/components/SvgIcon.vue";

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
	},

	data() {
		return {
			tabDialog: false,
			crm_refresh: false,
			crm_refresh_percentage: 0,
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
			systemTabs: Advertisement(this).tabItems,
			tabItems: Advertisement(this).tabItems,
			breadcrumb: Advertisement(this).breadcrumb,
			currentTab: Advertisement(this).tabItems[0],
			apiCalling: false,
			axiosSource: null,
			status_loading: false,
			updateStatusItemId: null,

			ColumnAxiosSource: null,

			filterData: {},
			type: 1,
			content: [],
			searchContent: "",
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
			showReasonDialog: false,
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
	},

	mounted() {
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
				: "primary";
		},

		maxDate: function () {
			return moment().format("YYYY-MM-DD");
		},
	},

	methods: {
		...mapActions({
			fetchCustomColumns: "customColumns/fetchCustomColumns",
		}),

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
			if (Array.isArray(items) && items.length > 0 && items.length <= 100) {
				if (this.$refs.multiSelectionStatusReasonRef) {
					this.$refs.multiSelectionStatusReasonRef.toggleDialog(items);
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

		refreshDatatable() {
			const currentTabIndex = this.getCurrentTabIndex();
			const filterData = this.filterOptions(currentTabIndex);
			if (this.$refs.advertisementGraph) {
				this.$refs.advertisementGraph.fetchGraphTabs(filterData);
				this.$refs.advertisementGraph.fetchGraphData(filterData);
			}
			if (this.options.page) this.options.page = 1;
			this.fetchItemsAccordingToTabKey(this.currentTab.key, {
				query: this.options,
				body: filterData,
			});
			this.tableRecords = [];
			this.totalRecords = 0;
		},

		clearLeft() {
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
			if (this.options.page) this.options.page = 1;
			this.fetchItemsAccordingToTabKey(this.currentTab.key, {
				query: this.options,
				body: body,
			});
			this.tableRecords = [];
			this.totalRecords = 0;
		},

		clearRight() {
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
			if (this.options.page) this.options.page = 1;
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
			if (this.checkPlatforms(item)) {
				this.status_loading = true;
				this.statusChangeItem = item;
				const newStatus = item.status == "ACTIVE" ? "inactive" : "active";
				this.$refs.reasonRef.fetchAllReasons(null, newStatus);
				this.showReasonDialog = true;
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
					const message = this.$tr(
						"record_item_status_changed",
						this.$tr(data.type)
					);
					// this.$toastr.s(message);
					this.$toasterNA("green", this.$tr(message));
				} else {
					// this.$toastr.e(this.$tr(data.status));
					this.$toasterNA("red", this.$tr(data.status));
				}
			} catch (error) {
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
			} catch (error) {
				this.$toasterNA("red", this.$tr("something_went_wrong"));
				this.closeReasonDialog();
			}
			this.showReasonDialog = false;
			this.status_loading = false;
		},
		closeReasonDialog() {
			this.showReasonDialog = false;
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
			this.showReasonDialog = true;
		},

		getRefreshProgress() {
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
						if (this.options.page) this.options.page = 1;
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

		onDateRangeSubmit(range) {
			const timeRange = {
				start_date: range.start_date,
				end_date: range.end_date,
			};
			this.filterByDate = timeRange;
			if (this.options.page) this.options.page = 1;
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
					this.all_headers[this.selectedIndex].headers = response.data.headers;
					this.all_headers = { ...this.all_headers };
				}
			} catch (error) {}
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
					if (data.no_record) {
						this.canRefresh = false;
						return;
					}
					if (data.date == null && param.platform) {
						this.canRefresh = true;
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
				let data = { date: "today" };
				let url = `advertisement/connection/refresh-platform`;
				await this.$axios.post(url, data);
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
					await this.$axios.post(url, data);
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
			} catch (_) {
				this.platform_rf_loading = false;
				this.$toasterNA("red", this.$tr("something_went_wrong"));
			}
		},
		togglePlatformReferesh() {
			this.showSnapchatRefresh = true;
		},

		getDate(item) {
			console.log("date", item);
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
			const options = this.fetchOptions();
			const currentTabIndex = this.getCurrentTabIndex();
			if (this.options.page) this.options.page = 1;
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

		searchByContent(searchContent) {
			this.searchContent = searchContent;
			const options = this.fetchOptions();

			const currentTabIndex = this.getCurrentTabIndex();
			if (this.options.page) this.options.page = 1;
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

		onFilterRecords(filterData) {
			this.$root.$emit("resetPageNumber");
			const currentTabIndex = this.getCurrentTabIndex();
			if (this.options.page) this.options.page = 1;
			const tabData = this.filterOptions(currentTabIndex);
			this.filterData = filterData;
			this.fetchItemsAccordingToTabKey(this.currentTab.key, {
				body: { ...filterData, ...tabData },
			});
		},
		openFilterDialog() {
			this.$refs.advertismentFilterRefs.changeModel();
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
			if (this.options.page) this.options.page = 1;

			this.fetchItemsAccordingToTabKey(tabItem.key, {
				query: this.options,
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

		async fetchItemsAccordingToTabKey(tabkey, options) {
			try {
				if (!options.searchValue) this.apiCalling = true;
				if (this.axiosSource) this.axiosSource.cancel("Request-Cancelled");
				this.axiosSource = this.$axios.CancelToken.source();
				let url = `advertisement/data/${tabkey}`;
				const header = {
					cancelToken: this.axiosSource.token,
					params: { ...options.query, search_by_id: options.search_by_id },
				};

				this.fetchDataCounts({
					...options.body,
					searchValue: options?.searchValue,
					...this.filterData,
				});

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
				} else if (data.result && data.item) {
					this.totalRecords = data.total;
					this.tableRecords = this.tableRecords.filter(
						(item) => item.id != data.item.id
					);
					this.tableRecords.unshift(data.item);
					this.currentTab.selectedItems.push(data.item);
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

		async fetchDataCounts(passedData) {
			try {
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
					this.tabItems = this.tabItems.map((item) => {
						if (item.key in counts) {
							item.count = counts[item.key];
						}
						return item;
					});
				}
			} catch (error) {
				console.log("error count", error);
			}
		},
		onUnselected(tabIndex) {
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
			if (this.options.page) this.options.page = 1;
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
		openInsertDialog() {
			this.$refs.genereteConnectionRef.toggleConnection();

			// this.$refs.generateLinkCreationRef.toggle();
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
			adsets.forEach((adset) => {
				total = total + parseFloat(adset.bid);
			});
			return total;
		},
		getPaymentMethod(item) {
			let method = "";
			if (item.connection !== undefined || item?.platform_name) {
				const platform =
					item?.platform_name || item.connection.platform.platform_name;
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
				this.currentTab.selectedItems.length < 1 ||
				this.currentTab.selectedItems.length > 1
			) {
				this.$toasterNA("red", this.$tr("please_select_one_option"));
				return false;
			}

			this.$refs.bankModal.toggleBankModal(this.currentTab.selectedItems[0]);
		},
		toggleBalanceModal() {
			if (
				this.currentTab.selectedItems.length < 1 ||
				this.currentTab.selectedItems.length > 1
			) {
				this.$toasterNA("red", this.$tr("please_select_one_option"));
				return false;
			}
			this.balanceData = this.currentTab.selectedItems;

			this.$refs.balanceModal.toggleDialog(this.currentTab.selectedItems[0]);
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
				console.log("resut", index, result);
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
			this.currentTab.selectedItems = [item];
			this.$refs.advertisementRefs.showProfile([item]);
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
</style>
