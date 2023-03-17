<template>
	<v-container fluid class="pa-0">
		<v-row no-gutters>
			<v-col cols="12">
				<PageHeader :Icon="`translation`" Title="translation" :Breadcrumb="breadcrumb"> </PageHeader>
			</v-col>
			<v-col cols="12">
				<PageFilters
					ref="pageFilterRef"
					:selected_header="[]"
					:downloadContent="[]"
					:downloadDialog="false"
					:filename="$tr('translation')"
					:show-bulk-upload="false"
					:showFilter="false"
					@searchByContent="searchByContent"
					:showDownload="$isInScope('lne')"
					@onColumn="
						() => {
							dialogKey++;
							columnDialog = true;
						}
					">
				</PageFilters>
			</v-col>
			<v-col cols="12">
				<v-card class="w-full mb-2 px-2 py-4" elevation="1" outlined>
					<div style="height: 1016px; overflow: scroll">
						<table class="lang-table w-full">
							<thead>
								<tr>
									<th>
										<div>
											<span class="fa fa-2x fa-globe"></span>
										</div>
									</th>
									<th>
										<div>{{ $tr("language") }}</div>
									</th>
									<th>
										<div>{{ $tr("frontend") }}</div>
									</th>
									<th>
										<div>{{ $tr("backend") }}</div>
									</th>
									<th>
										<div>{{ $tr("status") }}</div>
									</th>
									<th>
										<div>{{ $tr("completed") }}</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(lang, i) in languages"
									:key="i"
									@click="
										() => {
											$router.push('/languages/' + lang.code);
										}
									">
									<td>
										<div>
											<FlagIcon :flag="lang.country_language.country.iso2.toLowerCase()" round large />
										</div>
									</td>
									<td>
										<div class="ps-4" style="min-width: 240px">
											{{ `${lang.country_language.language.name} (${lang.country_language.country.name})` }}
										</div>
									</td>
									<td>
										<div>{{ lang.frontendCount }}/{{ getTotals("frontendWords") }}</div>
									</td>
									<td>
										<div>{{ lang.backendCount }}/{{ getTotals("backendWords") }}</div>
									</td>
									<td>
										<div>{{ $tr(lang.status) }}</div>
									</td>
									<td>
										<div>{{ Number.parseFloat(((lang.translatedCount / getTotals("totalWords")) * 100).toFixed(2)) }}%</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<v-row class="pb-3 align-center ma-0 py-2 tbl-bottom">
						<v-col cols="0" md="4" class="pa-0"></v-col>
						<v-col class="py-0" cols="12" md="4">
							<div class="text-center text-center mx-auto d-flex align-center justify-center">
								<p class="ma-0 me-2">
									{{
										$tr(
											"showing_items",
											this.itemsPerPage * (this.page - 1),
											this.itemsPerPage == -1 ? this.ItemsLength : this.itemsPerPage * this.page > getTotals("all") ? getTotals("all") : this.itemsPerPage * this.page,
											getTotals("all"),
										)
									}}
								</p>
								<CustomButton icon="fa-sync-alt fa-3x" text="" type="primary" class="customDisabled" />
								<div style="width: 90px !important; margin: 0.16rem 0.16rem">
									<v-select
										v-model="itemsPerPage"
										:items="perpageDropdown"
										label=""
										item-text="text"
										item-value="value"
										dense
										outlined
										:menu-props="{ bottom: true, offsetY: true }"
										hide-details="auto"
										@input="perPageSelect">
									</v-select>
								</div>
							</div>
						</v-col>
						<v-col class="py-1 py-md-0 d-flex" cols="12" md="4">
							<div class="mx-auto mx-md-0 ms-md-auto">
								<CustomPagination @paginate="paginateFunc" :pageCount="parseInt(Math.ceil(getTotals('all') / itemsPerPage))" />
							</div>
						</v-col>
					</v-row>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>
<script>
import PageHeader from "~/components/design/PageHeader";
import PageFilters from "~/components/design/PageFilters";
import CustomButton from "~/components/design/components/CustomButton";
import FlagIcon from "~/components/common/FlagIcon.vue";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomPagination from "~/components/design/components/CustomPagination.vue";
import { mapActions, mapGetters } from "vuex";
import Icons from "~/configs/menus/menuIcons";


export default {
	meta: {
		hasAuth: true,
		scope: "lnv",
	},
	// moved functionality to created function
	// async async Data({ store }) {
	// 	await store.dispatch("languages/fetchLanguages", {
	// 		key: "all",
	// 	});
	// 	await store.dispatch("customColumns/fetchCustomColumns", {
	// 		view_name: "languages",
	// 	});
	// },
	async created() {
		this.getRecord();
		await this.fetchCuctomColumns({ view_name: "languages" });
	},
	components: {
		ProgressBar,
		PageHeader,
		PageFilters,
		CustomButton,
		FlagIcon,
		CustomPagination,
	},
	computed: {
		...mapGetters({
			languages: "languages/getLanguages",
			itemsTotal: "languages/itemsTotal",
			apiCalling: "languages/isApiCalling",
			getTotals: "languages/getTotal",
			custom_columns: "customColumns/get_custom_columns",
		}),
	},
	mounted() {},
	data() {
		return {
			breadcrumb: [
				{
					text: "master_management_system",
					disabled: true,
					to: "",
					icon: Icons.master_management_system,
				},
				{
					text: "language_setting",
					disabled: true,
					to: "",
					icon: Icons.language_setting,
				},
				{
					text: "translation",
					disabled: true,
					to: "",
					icon: Icons.translation,
				},
			],
			searchContent: "",
			dialogKey: 0,
			columnDialog: false,
			ItemsLength: 100,
			page: 1,
			itemsPerPage: 10,
			perpageDropdown: [
				{
					text: "2",
					value: 2,
				},
				{
					text: "10",
					value: 10,
				},
				{
					text: "20",
					value: 20,
				},
				{
					text: "50",
					value: 50,
				},
				{
					text: "100",
					value: 100,
				},
				{
					text: "200",
					value: 200,
				},
				{
					text: "500",
					value: 500,
				},
				{
					text: "1000",
					value: 1000,
				},
				{
					text: this.$tr("all"),
					value: -1,
				},
			],
		};
	},
	methods: {
		...mapActions({
			fetchCuctomColumns: "customColumns/fetchCustomColumns",
			fetchLanguages: "languages/fetchLanguages",
		}),

		paginateFunc(page) {
			this.fetchLanguages({
				key: "all",
				itemsPerPage: this.itemsPerPage,
				page,
			});
		},
		perPageSelect() {
			this.fetchLanguages({
				key: "all",
				itemsPerPage: this.itemsPerPage,
				page: this.page,
			});
		},

		searchByContent(searchContent) {
			this.searchContent = searchContent;
			this.getRecord();
		},

		async getRecord() {
			try {
				await this.fetchLanguages({ key: "all", searchContent: this.searchContent });
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
	},
};
</script>
<style scoped>
.lang-table {
	--table-height: 70px;
	border: 0 !important;
	border-collapse: collapse;
}
.lang-table * {
	border: 0 !important;
}
.lang-table thead {
	border-radius: 50%;
}

.lang-table thead th div,
.lang-table tbody td div {
	height: var(--table-height);
	color: white;
	display: flex;
	align-items: center;
	justify-content: center;
	text-align: center;
	margin-bottom: 20px;
}
.lang-table thead th div {
	font-size: 24px !important;
	background: #a3dbe6 !important;
}
.lang-table tbody td div {
	font-size: 20px !important;
	font-weight: 600;
	background: #ccc !important;
}
.lang-table tr {
	background: transparent !important;
	border-spacing: 5em !important;
}
.lang-table tbody tr {
	cursor: pointer;
}

td div {
	margin-bottom: 1em;
}

.lang-table thead tr :first-child div,
.lang-table tbody tr :nth-child(2) div {
	border-top-left-radius: calc(var(--table-height) / 2);
	border-bottom-left-radius: calc(var(--table-height) / 2);
}
.lang-table tr :last-child div {
	border-top-right-radius: calc(var(--table-height) / 2);
	border-bottom-right-radius: calc(var(--table-height) / 2);
}
.v-application--is-rtl .lang-table thead tr :first-child div,
.v-application--is-rtl .lang-table tbody tr :nth-child(2) div {
	border-top-right-radius: calc(var(--table-height) / 2);
	border-bottom-right-radius: calc(var(--table-height) / 2);
	border-top-left-radius: 0;
	border-bottom-left-radius: 0;
}
.v-application--is-rtl .lang-table tr :last-child div {
	border-top-left-radius: calc(var(--table-height) / 2);
	border-bottom-left-radius: calc(var(--table-height) / 2);
	border-top-right-radius: 0;
	border-bottom-right-radius: 0;
}
.lang-table tbody tr :first-child div {
	background: transparent !important;
}
</style>
