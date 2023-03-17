<template>
	<v-dialog v-model="dialog" persistent max-width="900">
		<v-card class="background">
			<v-card-title class="share-title d-flex justify-space-between">
				Share
				<CloseBtn
					@click="
						() => {
							$emit('toggleShareModal');
						}
					"
				/>
			</v-card-title>
			<div class="px-3 mb-2" v-if="!show_done">
				<div class="elevation-6 mb-2 rounded">
					<div
						class="surface rounded-t font-weight-medium"
						style="padding: 12px 16px"
					>
						{{ $tr("share_with_users_teams_roles_departments_companies") }}
					</div>
					<div class="pa-2 pb-0">
						<div class="d-flex flex-column flex-md-row">
							<div class="w-full">
								<p class="mb-1 font-weight-normal">{{ $tr("share_with") }}</p>
								<CustomInput
									:items="shareWith"
									:hideDetails="true"
									v-model="share_to"
									:placeholder="
										$tr('add_users_teams_roles_departments_companies')
									"
									item-value="value"
									item-text="text"
									type="select"
									class="me-md-1 mb-2"
									:loading="false"
									@change="share_change"
								/>
							</div>
							<div class="w-full">
								<p class="mb-1 ms-md-1 font-weight-normal">
									{{ $tr("permission") }}
								</p>
								<CustomInput
									:items="permissions"
									v-model="permiission_model"
									:hideDetails="true"
									:placeholder="$tr('choose_item', $tr('permission'))"
									item-value="value"
									item-text="text"
									type="select"
									class="ms-md-1 mb-2"
									:loading="false"
								/>
							</div>
						</div>
					</div>
				</div>
				<div class="d-flex justify-space-between align-center mb-2">
					<div v-if="$vuetify.breakpoint.width > 700">
						<v-checkbox
							v-model="selectAll"
							class="mt-0"
							@change="select_all_change"
							:disabled="this.share_to == null"
							hide-details
							:label="$tr('select_all')"
						/>
					</div>
					<div v-if="$vuetify.breakpoint.width > 700" style="min-width: 330px">
						<v-form @submit.prevent="search">
							<v-text-field
								hide-details
								rounded
								background-color="surface "
								class="pa-0 ma-0 custom-search white"
								v-model="searchData"
								color="white"
								@input="search"
								:placeholder="$tr('search') + '...'"
							>
								<template slot="append" class="pe-0">
									<v-btn fab x-small text class="ma-0 me-1" @click="search">
										<v-icon dark size="20"> mdi-magnify </v-icon>
									</v-btn>
								</template>
							</v-text-field>
						</v-form>
					</div>
					<div
						v-if="$vuetify.breakpoint.width <= 700"
						class="d-flex justify-space-between align-center position-relative overflow-hidden w-full"
						style="height: 50px"
					>
						<div
							:class="`d-flex justify-space-between align-center first-one ${
								showSearch ? 'show-search' : ''
							}`"
						>
							<v-checkbox
								v-model="selectAll"
								class="mt-0"
								@change="select_all_change"
								:disabled="this.share_to == null"
								hide-details
								:label="$tr('select_all')"
							/>
							<div>
								<v-btn fab x-small text class="" @click="showSearchMethod">
									<v-icon dark size="20"> mdi-magnify </v-icon>
								</v-btn>
							</div>
						</div>
						<v-text-field
							ref="companySearchInput"
							hide-details
							rounded
							background-color="surface"
							:class="`pa-0 ma-0 custom-search second-one ${
								showSearch ? 'show-search' : ''
							}`"
							v-model="searchData"
							@input="search"
							@blur="showSearch = !showSearch"
							color="white"
							:placeholder="$tr('search') + '...'"
						>
							<template slot="append" class="pe-0">
								<v-btn fab x-small text class="ma-0 me-1" @click="search">
									<v-icon dark size="20"> mdi-magnify </v-icon>
								</v-btn>
							</template>
						</v-text-field>
					</div>
				</div>

				<div
					:class="`surface rounded overflow-y-auto position-relative ${
						share_to == null ? 'd-flex align-center justify-center' : ''
					}`"
					style="height: 270px"
					ref="listContainer"
				>
					<div v-if="loading" class="w-full">
						<ShareListLoading />
					</div>
					<div class="text-center text--disabled" v-else-if="share_to == null">
						<div class="mb-1">
							<svg
								fill="currentColor"
								viewBox="0 0 227.216 227.216"
								style="enable-background: new 0 0 227.216 227.216"
								width="40px"
							>
								<path
									d="M175.897,141.476c-13.249,0-25.11,6.044-32.98,15.518l-51.194-29.066c1.592-4.48,2.467-9.297,2.467-14.317
									c0-5.019-0.875-9.836-2.467-14.316l51.19-29.073c7.869,9.477,19.732,15.523,32.982,15.523c23.634,0,42.862-19.235,42.862-42.879
									C218.759,19.229,199.531,0,175.897,0C152.26,0,133.03,19.229,133.03,42.865c0,5.02,0.874,9.838,2.467,14.319L84.304,86.258
									c-7.869-9.472-19.729-15.514-32.975-15.514c-23.64,0-42.873,19.229-42.873,42.866c0,23.636,19.233,42.865,42.873,42.865
									c13.246,0,25.105-6.042,32.974-15.513l51.194,29.067c-1.593,4.481-2.468,9.3-2.468,14.321c0,23.636,19.23,42.865,42.867,42.865
									c23.634,0,42.862-19.23,42.862-42.865C218.759,160.71,199.531,141.476,175.897,141.476z M175.897,15
									c15.363,0,27.862,12.5,27.862,27.865c0,15.373-12.499,27.879-27.862,27.879c-15.366,0-27.867-12.506-27.867-27.879
									C148.03,27.5,160.531,15,175.897,15z M51.33,141.476c-15.369,0-27.873-12.501-27.873-27.865c0-15.366,12.504-27.866,27.873-27.866
									c15.363,0,27.861,12.5,27.861,27.866C79.191,128.975,66.692,141.476,51.33,141.476z M175.897,212.216
									c-15.366,0-27.867-12.501-27.867-27.865c0-15.37,12.501-27.875,27.867-27.875c15.363,0,27.862,12.505,27.862,27.875
									C203.759,199.715,191.26,212.216,175.897,212.216z"
								/>
							</svg>
						</div>
						<div style="max-width: 250px">
							<p class="ma-0 text--secondary" style="font-size: 14px">
								Please choose users, teams, roles and companies from above.
							</p>
						</div>
					</div>
					<div class="users w-full" v-else-if="share_to == 'users'">
						<ShareModalList
							:items="!search_active ? users : search_items"
							imageProperty="image"
							titleProperty="title"
							type="user"
							v-model="selected_users"
						/>
					</div>
					<div class="companies w-full" v-else-if="share_to == 'companies'">
						<ShareModalList
							v-if="!show_company_users"
							:items="!search_active ? companies : search_items"
							type="company"
							imageProperty="logo"
							titleProperty="name"
							showChevron
							v-model="selected_companies"
							@checkboxChanged="checkboxChanged"
							@itemClick="company_click"
						/>
						<div v-else>
							<div
								class="surface darken-1 w-full d-flex align-center"
								style="position: sticky; top: 0; z-index: 1; padding: 8px 16px"
							>
								<v-btn fab x-small text @click="back" class="me-1">
									<v-icon color="primary" size="22">
										mdi-keyboard-backspace
									</v-icon>
								</v-btn>
								<p class="ma-0 font-weight-medium">
									{{ active_company ? active_company.name : "" }}
								</p>
							</div>
							<ShareListLoading v-if="loading2" />
							<ShareModalList
								v-else
								:items="!search_active ? company_users : search_items"
								hideMeta
								type="user"
								imageProperty="image"
								v-model="selected_company_users"
								@change="active_item_user_change"
							/>
						</div>
					</div>
					<div class="departments w-full" v-else-if="share_to == 'departments'">
						<ShareModalList
							v-if="!show_department_users"
							:items="!search_active ? departments : search_items"
							titleProperty="name"
							type="department"
							noImage
							showChevron
							v-model="selected_departments"
							@checkboxChanged="checkboxChanged"
							@itemClick="dep_team_role_click"
						/>
						<div v-else>
							<div
								class="surface darken-1 w-full d-flex align-center"
								style="position: sticky; top: 0; z-index: 1; padding: 8px 16px"
							>
								<v-btn fab x-small text @click="back" class="me-1">
									<v-icon color="primary" size="22">
										mdi-keyboard-backspace
									</v-icon>
								</v-btn>
								<p class="ma-0 font-weight-medium">
									{{ active_department ? active_department.name : "" }}
								</p>
							</div>
							<ShareListLoading v-if="loading2" />
							<ShareModalList
								v-else
								:items="!search_active ? department_users : search_items"
								hideMeta
								type="user"
								imageProperty="image"
								v-model="selected_department_users"
								@change="active_item_user_change"
							/>
						</div>
					</div>
					<div class="teams w-full" v-else-if="share_to == 'teams'">
						<ShareModalList
							v-if="!show_team_users"
							:items="!search_active ? teams : search_items"
							noImage
							type="team"
							titleProperty="name"
							showChevron
							v-model="selected_teams"
							@checkboxChanged="checkboxChanged"
							@itemClick="dep_team_role_click"
						/>
						<div v-else>
							<div
								class="surface darken-1 w-full d-flex align-center"
								style="position: sticky; top: 0; z-index: 1; padding: 8px 16px"
							>
								<v-btn fab x-small text @click="back" class="me-1">
									<v-icon color="primary" size="22">
										mdi-keyboard-backspace
									</v-icon>
								</v-btn>
								<p class="ma-0 font-weight-medium">
									{{ active_team ? active_team.name : "" }}
								</p>
							</div>
							<ShareListLoading v-if="loading2" />
							<ShareModalList
								v-else
								:items="!search_active ? team_users : search_items"
								hideMeta
								type="user"
								imageProperty="image"
								v-model="selected_team_users"
								@change="active_item_user_change"
							/>
						</div>
					</div>
					<div class="roles w-full" v-else-if="share_to == 'roles'">
						<ShareModalList
							v-if="!show_role_users"
							:items="!search_active ? roles : search_items"
							noImage
							type="role"
							titleProperty="name"
							showChevron
							v-model="selected_roles"
							@checkboxChanged="checkboxChanged"
							@itemClick="dep_team_role_click"
						/>
						<div v-else>
							<div
								class="surface darken-1 w-full d-flex align-center"
								style="position: sticky; top: 0; z-index: 1; padding: 8px 16px"
							>
								<v-btn fab x-small text @click="back" class="me-1">
									<v-icon color="primary" size="22">
										mdi-keyboard-backspace
									</v-icon>
								</v-btn>
								<p class="ma-0 font-weight-medium">
									{{ active_role ? active_role.name : "" }}
								</p>
							</div>
							<ShareListLoading v-if="loading2" />
							<ShareModalList
								v-else
								:items="!search_active ? role_users : search_items"
								hideMeta
								type="user"
								imageProperty="image"
								v-model="selected_role_users"
								@change="active_item_user_change"
							/>
						</div>
					</div>
				</div>
			</div>
			<div
				v-else
				class="d-flex flex-column align-center justify-center"
				style="min-height: 500px"
			>
				<lottie-player
					src="/assets/success.json"
					background="transparent"
					speed="1"
					style="width: 280px; height: 280px"
					loop
					autoplay
				>
				</lottie-player>
				<h2 class="mb-1" style="color: #2cda94">
					{{ $tr("no_file_shared_succssfully", items.length) }}
				</h2>
			</div>
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn
					v-if="!show_done"
					color="primary"
					class="font-weight-medium px-3"
					text
					@click="
						() => {
							$emit('toggleShareModal');
						}
					"
				>
					{{ $tr("cancel") }}
				</v-btn>
				<v-btn
					v-if="!show_done"
					color="primary"
					class="font-weight-medium px-3"
					@click="submit"
					:loading="submitting"
				>
					{{ $tr("share") }}
				</v-btn>
				<v-btn
					v-else
					color="primary"
					class="font-weight-medium px-3"
					@click="
						() => {
							$emit('toggleShareModal');
						}
					"
				>
					{{ $tr("done") }}
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>
<script>
import CustomInput from "~/components/design/components/CustomInput.vue";
import ShareModalList from "./ShareModalList.vue";
import ShareListLoading from "./ShareListLoading.vue";
import CloseBtn from "~/components/design/Dialog/CloseBtn.vue";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
export default {
	components: {
		CustomInput,
		ShareModalList,
		ShareListLoading,
		CloseBtn,
		DoneMessage,
	},
	props: {
		dialog: {
			type: Boolean,
			default: false,
		},
		items: {
			type: Array,
			default: () => [],
		},
	},

	data() {
		return {
			// dialog: true,
			loading: false,
			loading2: false,
			submitting: false,
			show_done: false,
			share_to: null,
			permiission_model: "read",
			shareWith: [],
			permissions: [
				{
					value: "read",
					text: this.$tr("read"),
				},
				{
					value: "read_&_write",
					text: this.$tr("read_&_write"),
				},
			],
			selectAll: false,
			show_company_users: false,
			show_department_users: false,
			show_team_users: false,
			show_role_users: false,
			users: [],
			companies: [],
			departments: [],
			teams: [],
			roles: [],
			company_users: [],
			department_users: [],
			team_users: [],
			role_users: [],
			active_company: null,
			active_department: null,
			active_team: null,
			active_role: null,
			// company properties
			selected_companies: [],
			selected_company_users: [],
			company_selected_users: {},
			// department section
			selected_departments: [],
			selected_department_users: [],
			department_selected_users: {},
			// team section
			selected_teams: [],
			selected_team_users: [],
			team_selected_users: {},
			// role section
			selected_roles: [],
			selected_role_users: [],
			role_selected_users: {},
			// user section
			selected_users: [],
			names: {
				companies: "company",
				users: "user",
				departments: "department",
				teams: "team",
				roles: "role",
			},
			search_active: false,
			searchData: "",
			search_items: [],
			search_items_selected: [],
			typingTimer: null,
			showSearch: false,
		};
	},
	mounted() {
		this.shareWith = [
			{
				value: "companies",
				text: this.$tr("companies"),
			},
			{
				value: "departments",
				text: this.$tr("departments"),
			},
			{
				value: "teams",
				text: this.$tr("teams"),
			},
			{
				value: "roles",
				text: this.$tr("roles"),
			},
			{
				value: "users",
				text: this.$tr("users"),
			},
		];
	},

	methods: {
		async share_change() {
			this.$refs.listContainer.scrollTo(0, 0);
			this.resetSearch();
			this.loading = true;
			switch (this.share_to) {
				case "companies":
					this.companies = this.$auth.user.companies;
					break;
				case "departments":
					// fetch departments
					if (this.departments.length == 0) {
						this.departments = await this.get_dep_team_role("departments");
					}
					break;
				case "teams":
					// fetch teams
					if (this.teams.length == 0) {
						this.teams = await this.get_dep_team_role("teams");
					}
					break;
				case "roles":
					// fetch roles
					if (this.roles.length == 0) {
						this.roles = await this.get_dep_team_role("roles");
					}
					break;
				case "users":
					// fetch users
					if (this.users.length == 0) {
						this.users = await this.get_users_of_companies();
					}
					break;

				default:
					break;
			}
			this.show_company_users = false;
			this.show_department_users = false;
			this.show_team_users = false;
			this.show_role_users = false;
			this.loading = false;
		},
		async company_click(item) {
			this.loading2 = true;
			this.resetSearch();
			this.show_company_users = true;
			this.active_company = item;
			this.company_users = await this.get_users_of_companies(item.id);
			this.selected_company_users = [];
			if (this.selected_companies.includes(item.id)) {
				if (this.company_selected_users[item.id] !== undefined) {
					this.selected_company_users = this.company_selected_users[item.id];
				} else {
					this.selected_company_users = this.company_users.map(
						(item) => item.id
					);
				}
			}
			this.loading2 = false;
		},
		async dep_team_role_click(item) {
			this.loading2 = true;
			this.resetSearch();
			this[`show_${this.get_share_to(true)}_users`] = true;
			this[`active_${this.get_share_to(true)}`] = item;
			this[`${this.get_share_to(true)}_users`] = await this.get_users({
				[`${this.get_share_to(true)}_ids`]: [item.id],
			});
			this[`selected_${this.get_share_to(true)}_users`] = [];
			if (this[`selected_${this.get_share_to()}`].includes(item.id)) {
				if (
					this[`${this.get_share_to(true)}_selected_users`][item.id] !==
					undefined
				) {
					this[`selected_${this.get_share_to(true)}_users`] =
						this[`${this.get_share_to(true)}_selected_users`][item.id];
				} else {
					this[`selected_${this.get_share_to(true)}_users`] = this[
						`${this.get_share_to(true)}_users`
					].map((item) => item.id);
				}
			}
			this.loading2 = false;
		},
		back() {
			this[`show_${this.get_share_to(true)}_users`] = false;
			this[`selected_${this.get_share_to()}_users`] = [];
			this.resetSearch();
		},
		async get_dep_team_role(type) {
			// type : departments, teams, roles
			const response = await this.$axios.get(
				`${type}?key=${type}-of-logged-in-company`
			);
			if (response.data.result == true) {
				return response.data.data;
			}
			return false;
		},
		async get_users_of_companies(id = null) {
			let url = "users?key=users-of-logged-in-company";
			if (id != null) {
				url += `&company_id=${id}`;
			}
			const response = await this.$axios.get(url);
			if (response.data.result == true) {
				let res = response.data.data;
				return res.filter((e) => e.id !== this.$auth.user.id);
			}
			return false;
		},
		async get_users(params) {
			const response = await this.$axios.get(`users`, {
				params: params,
			});
			if (response.data.result == true) {
				let res = response.data.users;
				return res.filter((e) => e.id !== this.$auth.user.id);
			}
			return false;
		},
		add_to_selected_arr(arr, item) {
			if (!arr.includes(item)) {
				arr.push(item);
			}
		},
		filter_selected_arr(arr, passedItem) {
			return arr.filter((item) => {
				if (item !== passedItem) {
					return item;
				}
			});
		},
		checkboxChanged(value, item, property) {
			if (value == false) {
				delete this[property][item.id];
			}
		},
		active_item_user_change(value) {
			let obj = { ...this[`${this.get_share_to(true)}_selected_users`] };
			if (value.length > 0) {
				this.add_to_selected_arr(
					this[`selected_${this.get_share_to()}`],
					this[`active_${this.get_share_to(true)}`].id
				);
				obj[this[`active_${this.get_share_to(true)}`].id] = value;
			} else if (value.length == 0) {
				this[`selected_${this.get_share_to()}`] = this.filter_selected_arr(
					this[`selected_${this.get_share_to()}`],
					this[`active_${this.get_share_to(true)}`].id
				);
				delete obj[this[`active_${this.get_share_to(true)}`].id];
			}
			this[`${this.get_share_to(true)}_selected_users`] = obj;
		},
		select_all_change(value) {
			if (this.share_to) {
				this[value ? "select_all_func" : "un_select_all"]();
			}
		},
		select_all_func() {
			// show_company_users
			if (this[`show_${this.get_share_to(true)}_users`]) {
				if (this.search_active) {
				} else {
					// company_selected_users
					let obj = { ...this[`${this.get_share_to(true)}_selected_users`] };
					// selected_company_users
					// company_users
					this[`selected_${this.get_share_to(true)}_users`] = this[
						`${this.get_share_to(true)}_users`
					].map((item) => item.id);
					this.add_to_selected_arr(
						this[`selected_${this.get_share_to()}`], // selected_companies
						this[`active_${this.get_share_to(true)}`].id // active_company
					);
					// active_company
					// selected_company_users
					obj[this[`active_${this.get_share_to(true)}`].id] =
						this[`selected_${this.get_share_to(true)}_users`];
					this[`${this.get_share_to(true)}_selected_users`] = obj; // company_selected_users
				}
			} else {
				// selected_companies
				this[`selected_${this.get_share_to()}`] = this[this.get_share_to()].map(
					(item) => {
						return item.id;
					}
				);
			}
		},
		un_select_all() {
			let obj = {};
			// show_company_users
			if (this[`show_${this.get_share_to(true)}_users`]) {
				this[`selected_${this.get_share_to(true)}_users`] = []; // selected_company_users
				// selected_companies
				this[`selected_${this.get_share_to()}`] = this.filter_selected_arr(
					this[`selected_${this.get_share_to()}`], // selected_comapnies
					this[`active_${this.get_share_to(true)}`].id // active_company
				);
				obj = { ...this[`${this.get_share_to(true)}_selected_users`] }; // company_selected_users
				delete obj[this[`active_${this.get_share_to(true)}`].id]; // active_company
			} else {
				this[`selected_${this.get_share_to()}`] = []; // selected_companies
			}
			this[`${this.get_share_to(true)}_selected_users`] = obj; // company_selected_users
		},
		get_share_to(single = false) {
			if (single) {
				return this.names[this.share_to];
			}
			return this.share_to;
		},
		selectAllWatch(value, model) {
			if (
				value.length == this[model].length &&
				value.length > 0 &&
				this[model].length > 0
			) {
				this.selectAll = true;
			} else {
				this.selectAll = false;
			}
		},
		changeSelectedCount(value, model) {
			if (value.length > 0) {
				this.shareWith = this.shareWith.map((item) => {
					if (item.value == model) {
						item.text = `${this.$tr(item.value)} ${this.$tr(
							"count_selected",
							value.length
						)}`;
					}
					return item;
				});
			} else if (value.length == 0) {
				this.shareWith = this.shareWith.map((item) => {
					if (item.value == model) {
						item.text = this.$tr(item.value);
					}
					return item;
				});
			}
		},
		search() {
			if (this.searchData.length == 0) {
				this.search_active = false;
				return;
			}
			this.debounce(() => {
				if (this.searchData.length !== 0) {
					this.search_active = true;
					if (this[`show_${this.get_share_to(true)}_users`]) {
						this[`selected_${this.get_share_to(true)}_users`] = [];
						this.search_items = this[`${this.get_share_to(true)}_users`].filter(
							(item) => {
								if (
									`${item.firstname} ${item.lastname}`
										.toLowerCase()
										.indexOf(this.searchData.toLowerCase()) >= 0
								) {
									return item;
								}
							}
						);
					} else {
						if (this.get_share_to() == "users") {
							this.selected_users = [];
							this.search_items = this.users.filter((item) => {
								if (
									`${item.firstname} ${item.lastname}`
										.toLowerCase()
										.indexOf(this.searchData.toLowerCase()) >= 0
								) {
									return item;
								}
							});
						} else {
							this[`selected_${this.get_share_to()}`] = [];
							this.search_items = this[this.get_share_to()].filter((item) => {
								if (
									item.name
										.toLowerCase()
										.indexOf(this.searchData.toLowerCase()) >= 0
								) {
									return item;
								}
							});
						}
					}
				}
			}, 1000);
		},
		debounce(callback, wait) {
			clearTimeout(this.typingTimer);
			this.typingTimer = setTimeout(callback, wait);
		},
		resetSearch() {
			this.search_active = false;
			this.search_items = [];
			this.searchData = "";
		},
		async submit() {
			this.submitting = true;
			if (this.items.length > 0) {
				if (
					this.selected_companies.length > 0 ||
					this.selected_departments.length > 0 ||
					this.selected_teams.length > 0 ||
					this.selected_roles.length > 0 ||
					this.selected_users.length > 0
				) {
					try {
						let res = await this.$axios.post("/files/personal/share", {
							items: this.items,
							selected_companies: this.selected_companies,
							company_selected_users: this.company_selected_users,
							selected_departments: this.selected_departments,
							department_selected_users: this.department_selected_users,
							selected_teams: this.selected_teams,
							team_selected_users: this.team_selected_users,
							selected_roles: this.selected_roles,
							role_selected_users: this.role_selected_users,
							selected_users: this.selected_users,
							permissions: this.permiission_model,
						});
						if (res.status == 201) {
							this.$emit("shareSuccess", res.data.items, res.data.file_shares);
							this.show_done = true;
						}
					} catch (err) {
						console.error(err);
					}
				} else {
					// this.$toastr.e("please_select_at_least_one_item_to_share");
			this.$toasterNA("red", this.$tr('please_select_at_least_one_item_to_share'));

				}
			} else {
				// this.$toastr.e("please_select_at_least_one_file_folder_to_share");
			this.$toasterNA("red", this.$tr('please_select_at_least_one_file_folder_to_share'));

			}
			this.submitting = false;
		},
		showSearchMethod() {
			var app = this;
			this.showSearch = !this.showSearch;
			setTimeout(() => {
				app.$refs.companySearchInput.focus();
			}, 300);
		},
	},
	watch: {
		share_to(value) {
			this.selectAllWatch(this[`selected_${this.get_share_to()}`], value);
		},
		selected_companies(value) {
			this.selectAllWatch(value, "companies");
			this.changeSelectedCount(value, "companies");
		},
		selected_departments(value) {
			this.selectAllWatch(value, "departments");
			this.changeSelectedCount(value, "departments");
		},
		selected_teams(value) {
			this.selectAllWatch(value, "teams");
			this.changeSelectedCount(value, "teams");
		},
		selected_roles(value) {
			this.selectAllWatch(value, "roles");
			this.changeSelectedCount(value, "roles");
		},
		selected_users(value) {
			this.selectAllWatch(value, "users");
			this.changeSelectedCount(value, "users");
		},
		selected_company_users(value) {
			this.selectAllWatch(value, "company_users");
		},
		selected_department_users(value) {
			this.selectAllWatch(value, "department_users");
		},
		selected_team_users(value) {
			this.selectAllWatch(value, "team_users");
		},
		selected_role_users(value) {
			this.selectAllWatch(value, "role_users");
		},
		show_company_users(value) {
			if (!value) {
				this.selectAllWatch(this.selected_companies, "companies");
			}
		},
		companies(value) {
			this.selectAllWatch(value, "selected_companies");
		},
		company_users(value) {
			this.selectAllWatch(value, "selected_company_users");
		},
		show_department_users(value) {
			if (!value) {
				this.selectAllWatch(this.selected_departments, "departments");
			}
		},
		departments(value) {
			this.selectAllWatch(value, "selected_departments");
		},
		department_users(value) {
			this.selectAllWatch(value, "selected_department_users");
		},
		show_team_users(value) {
			if (!value) {
				this.selectAllWatch(this.selected_teams, "teams");
			}
		},
		teams(value) {
			this.selectAllWatch(value, "selected_teams");
		},
		team_users(value) {
			this.selectAllWatch(value, "selected_team_users");
		},
		show_role_users(value) {
			if (!value) {
				this.selectAllWatch(this.selected_roles, "roles");
			}
		},
		roles(value) {
			this.selectAllWatch(value, "selected_roles");
		},
		role_users(value) {
			this.selectAllWatch(value, "selected_role_users");
		},
		users(value) {
			this.selectAllWatch(value, "selected_users");
		},
	},
};
</script>
<style scoped>
.share-title {
	font-size: 18px;
	font-weight: 600 !important;
}
.first-one,
.second-one {
	position: absolute;
	transition: all 0.3s;
}
.first-one {
	left: 0px;
	right: 0px;
}
.v-application--is-rtl .first-one {
	left: 0px;
	right: 0px;
}
.second-one {
	left: 100%;
	right: -100%;
}
.first-one.show-search {
	left: -100%;
	right: 100%;
}
.second-one.show-search {
	left: 16px;
	right: 16px;
}
</style>
