<template>
	<v-dialog
		v-if="showDialog"
		v-model="showDialog"
		width="800"
		persistent
		height="500"
	>
		<v-card class="white">
			<v-card-title class="px-2 pt-2 pb-2" style="color: #777">
				<div class="dialog-title d-flex align-center">
					{{ $tr("balance") }}
					<div
						class="remarks-number ms-1"
						:style="`background: ${$vuetify.theme.defaults.light.primary}33`"
					></div>
				</div>
				<v-spacer></v-spacer>
				<div class="d-flex align-center">
					<v-btn
						class="me-1"
						fab
						dark
						x-small
						color="primary"
						@click="
							() => {
								expand = !expand;
								title = 'Add New Balance';
								setDefaultValues();
							}
						"
					>
						<v-icon dark size="20"> mdi-plus </v-icon>
					</v-btn>
					<svg
						@click="toggleDialog()"
						width="42px"
						height="42px"
						viewBox="0 0 700 560"
						fill="currentColor"
						style="transform: scaleY(-1); cursor: pointer"
					>
						<g>
							<path
								d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
							/>
							<path
								d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
							/>
						</g>
					</svg>
				</div>
			</v-card-title>
			<v-card-text>
				<v-card class="blue lighten-5 my-2">
					<v-card-title class="primary--text">
						<span>{{ $tr("current_balance") + ":  " + currencyBalance }}</span>
						<v-spacer></v-spacer>
						<v-avatar @click="update()" color="primary" size="23">
							<v-icon
								v-text="'mdi-pencil'"
								size="15"
								class="white--text"
							></v-icon>
						</v-avatar>
					</v-card-title>
				</v-card>
				<v-expand-transition>
					<v-card
						class="pa-4"
						elevation="20"
						v-show="expand"
						style="
							position: absolute !important;
							z-index: 10000;
							right: 20px;
							left: 20px;
						"
					>
						<v-form ref="form" lazy-validation @submit.prevent="onSubmit">
							<div
								style="font-size: 16px"
								class="font-weight-medium primary--text"
							>
								{{ title }}
							</div>
							<div class="d-flex">
								<span><small>You can add and delete balance</small></span>
								<v-spacer></v-spacer>
								<span @click="closeExpand">
									<v-icon small>mdi-close</v-icon></span
								>
							</div>
							<div class="d-flex justify-space-between align-center mt-3">
								<div class="me-1">
									<v-select
										v-model="$v.balance.payment_type.$model"
										:rules="
											validateRule(
												$v.balance.payment_type,
												$root,
												$tr('payment_type')
											)
										"
										class="form-new-item form-custom-select"
										:invalid="balance.payment_type.$invalid"
										background-color="var(--new-input-bg)"
										outlined
										:items="paymentItem"
										disabled
										item-text="name"
										item-value="name"
										placeholder="select payment type"
										dense
									>
									</v-select>
								</div>
								<div class="me-1">
									<v-select
										v-model="$v.balance.currency.$model"
										:rules="
											validateRule($v.balance.currency, $root, $tr('currency'))
										"
										class="form-new-item form-custom-select"
										background-color="var(--new-input-bg)"
										outlined
										:items="currencyItem"
										item-text="name"
										item-value="name"
										placeholder="select currency"
										dense
										disabled
									>
									</v-select>
								</div>
								<div class="me-1">
									<v-text-field
										:class="`form-new-item form-custom-text-field small`"
										background-color="var(--new-input-bg)"
										outlined
										dense
										:rounded="false"
										type="number"
										placeholder="Balance amount"
										v-model="$v.balance.amount.$model"
										@keyup="changeAmount"
										:min="platform == 'tiktok' ? 0 : -1000000000000000000000"
										:max="platform == 'snapchat' ? 0 : 1000000000000000000000"
										:rules="
											validateRule(
												$v.balance.amount,
												$root,
												$tr('balance amount')
											)
										"
										:invalid="balance.amount.$invalid"
									>
										>
									</v-text-field>
								</div>

								<div class="me-1">
									<v-btn
										class="mb-3"
										style="font-weight: 400"
										hide-details
										dark
										color="primary"
										@click="onSubmit"
									>
										+ Add
									</v-btn>
								</div>
							</div>
						</v-form>
					</v-card>
				</v-expand-transition>

				<!-- body -->
				<v-card-text class="overflow-auto my-2 mr-3" :style="`height:60vh;  `">
					<div v-if="!api_loading">
						<v-timeline
							dense
							v-if="balanceData.length > 0"
							:class="`custom-timeLine`"
							color="red"
							:style="`margin-top:${expand ? '210px !important' : ''}`"
						>
							<v-timeline-item
								v-for="(data, index) in balanceData"
								:key="index"
								color="primary"
								icon="mdi-book"
							>
								<template v-slot:icon>
									<v-badge avatar bordered overlap bottom>
										<template v-slot:badge>
											<v-avatar>
												<v-img :src="data.created_by.image"></v-img>
											</v-avatar>
										</template>

										<v-avatar size="30">
											<v-icon color="white" size="15">mdi-book</v-icon>
										</v-avatar>
									</v-badge>
								</template>
								<div class="d-flex py-2 ps-1">
									<div class=" ">
										<h4>
											{{ adAccount != null ? adAccount?.name : "account" }}
										</h4>

										<span class="text-caption">
											{{
												data.created_by.firstname +
												" " +
												data.created_by.lastname
											}}
										</span>
									</div>
									<v-chip
										class="mx-2 px-1 primary--text"
										color="blue lighten-5"
										small
										label
									>
										{{ data.payment_type }}
									</v-chip>
									<span>{{ data.type }}</span>
									<v-spacer></v-spacer>
									<div class="px-1">
										<div class="text-body-1 font-weight-regular green--text">
											{{ data.balance + " " + data.currency }}
										</div>

										<div class="text-caption">
											{{ localeHumanReadableTime(data.updated_at) }}
										</div>
									</div>
									<div>
										<!-- <v-menu bottom left>
											<template v-slot:activator="{ on, attrs }">
												<v-icon color="primary" v-bind="attrs" v-on="on"
													>mdi-dots-horizontal</v-icon
												>
											</template>
											<v-list>
												<v-list-item @click="update(data)" v-if="index == 0">
													<v-list-item-icon class="grey--text">
														<v-icon v-text="'mdi-pencil'" size="20"></v-icon>
													</v-list-item-icon>
													<v-list-item-title class="text-caption">{{
														$tr("update")
													}}</v-list-item-title>
												</v-list-item>
												<v-list-item @click="deleteView(data.id)">
													<v-list-item-icon class="grey--text">
														<v-icon v-text="'mdi-delete'" size="20"></v-icon>
													</v-list-item-icon>
													<v-list-item-title class="text-caption">{{
														$tr("delete")
													}}</v-list-item-title>
												</v-list-item>
											</v-list>
										</v-menu> -->
									</div>
								</div>
								<v-divider></v-divider>
							</v-timeline-item>
						</v-timeline>

						<div v-else class="text-center">
							<NoRemark
								:style="`margin-top:${expand ? '210px !important' : '150px'}`"
							/>
						</div>
					</div>

					<div v-else class="px-5">
						<v-skeleton-loader
							v-for="i in 2"
							:key="i"
							type=" list-item-avatar, list-item-three-line"
						></v-skeleton-loader>
					</div>
				</v-card-text>
				<v-card-actions class="justify-end">
					<v-btn class="primary" @click="onClose">{{ $tr("close") }}</v-btn>
				</v-card-actions>
			</v-card-text>
		</v-card>
	</v-dialog>
</template>

<script>
import moment from "moment-timezone";
import GlobalRules from "~/helpers/vuelidate";
import Validations from "../../../validations/validations";
import Alert from "~/helpers/alert";
import NoRemark from "../remarks/NoRemark.vue";

export default {
	props: {
		selected_item: Object,
	},
	validations: {
		balance: {
			name: Validations.name100Validation,
			amount: Validations.requiredValidation,
			currency: Validations.requiredValidation,
			payment_type: Validations.requiredValidation,
		},
	},
	data() {
		return {
			showDialog: false,
			api_loading: false,
			date: "",
			balance: { name: null, amount: 0, currency: "USD", payment_type: "" },
			validateRule: GlobalRules.validate,
			adAccountId: null,
			balanceData: [],
			expand: false,
			title: "Add New Balance",
			platform: null,
			currencyBalance: 0,
			adAccount: null,

			currencyItem: [
				{
					name: "USD",
				},
				{
					name: "AED",
				},
			],
			paymentItem: [
				{
					name: "Dibit Card",
				},
				{
					name: "Credit Card",
				},
			],
		};
	},
	created() {},
	watch: {},

	methods: {
		async toggleDialog(adAccount) {
			this.closeExpand();
			this.balance = { currency: "", payment_type: "", amount: 0, id: null };
			console.log("this.balance", this.balance);
			if (adAccount !== undefined) {
				this.adAccountId = adAccount.id;
				this.platform = adAccount.connection.platform.platform_name;
			}
			this.showDialog = !this.showDialog;
			if (this.showDialog) {
				this.api_loading = true;
				await this.fetchAllData();
				this.api_loading = false;
			} else {
				this.remark = "";
			}

			this.setDefaultValues();
		},
		setDefaultValues() {
			if (this.platform == "snapchat") {
				this.balance.currency = "USD";
				this.balance.payment_type = "Credit Card";
			} else if (this.platform == "tiktok") {
				this.balance.currency = "AED";
				this.balance.payment_type = "Dibit Card";
			}
			this.balance.id = null;
		},
		async fetchAllData() {
			try {
				const response = await this.$axios.get(
					`advertisement/balance/${this.adAccountId}`
				);
				if (response.data.result) {
					this.balanceData = response.data.data;
					this.currencyBalance = response.data.adAccount.ad_account_balance;
					this.adAccount = response.data.adAccount;
				}
			} catch (error) {}
		},
		onClose() {
			this.showDialog = !this.showDialog;
		},
		addBlance() {
			this.balance = { currency: "", payment_type: "", amount: 0 };
		},
		clearBalance() {
			this.closeExpand();

			this.$refs.form.resetValidation();
		},
		closeExpand() {
			this.expand = false;
			this.balance.amount = 0;
		},
		async onSubmit() {
			try {
				this.$refs.form.validate();
				this.$v.balance.$touch();
				if (
					this.$v.balance.currency.$invalid ||
					this.$v.balance.amount.$invalid ||
					this.$v.balance.payment_type.$invalid
				) {
					return false;
				}

				this.balance.ad_account_id = this.adAccountId;
				console.log(this.balance);
				const response = await this.$axios.post(
					"advertisement/balance",
					this.balance
				);
				if (response.data.result) {
					let message = this.balance.id
						? this.$tr("successfully_updated")
						: this.$tr("successfully_inserted");
					this.$toasterNA("green", message);

					this.clearBalance();
					this.balanceData.unshift(response.data.data);
					this.currencyBalance = response.data.current_balance;
					this.$emit("addBalance", this.currencyBalance);
				}
			} catch (error) {
				this.$toasterNA("red", error);
			}
		},
		localeHumanReadableTime(date) {
			return moment(date)
				.locale(this.$vuetify.lang.current)
				.format("YYYY-MM-DD h:mm: A");
		},
		update() {
			this.title = "Update Balance";
			this.setDefaultValues();
			this.balance.amount = this.currencyBalance;
			this.balance.id = this.adAccount.id;

			this.expand = true;
		},
		deleteView(id) {
			Alert.confirmAlert(
				this,
				() => this.delete(id),
				"",
				"warning",
				"are_you_sure"
			);
		},
		async delete(id) {
			try {
				const response = await this.$axios.delete(
					"advertisement/balance/" + id
				);
				if (response.data.result) {
					let message = this.$tr("successfully_deleted");
					this.$toasterNA("orange", message);
					this.clearBalance();
				}
			} catch (error) {
				this.$toasterNA("red", error);
			}
		},
		changeAmount(value) {
			if (this.platform == "snapchat") {
				if (this.balance.amount > 0)
					this.balance.amount = this.balance.amount * -1;
			} else if (this.platform == "tiktok") {
				if (this.balance.amount < 0)
					this.balance.amount = this.balance.amount * -1;
			}
		},
	},
	components: { NoRemark },
};
</script>

<style>
.dialog-title {
	font-size: 20px;
	font-weight: 600;
	color: #777;
}
.custom-timeLine::before {
	width: 0.5px !important;
	background: #edebeb !important;
}
</style>
