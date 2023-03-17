<template>
	<div>
		<v-dialog v-model="bankviewdialog" width="1000" persistent>
			<v-card>
				<v-card elevation="24" style="margin-bottom: 10px">
					<v-card-title style="color: #777" class="px-2 pt-2 pb-2">
						<div class="dialog-title d-flex align-center">
							{{ $tr("bank_details") }}
							<div
								class="remarks-number ms-1"
								:style="`background: ${$vuetify.theme.defaults.light.primary}`"
							></div>
						</div>
						<v-spacer></v-spacer>
						<DialogCloseBtn
							class="close-dialog-btn"
							@click="closeBankViewDialog"
						/>
					</v-card-title>
				</v-card>
				<v-card-text
					style="
						box-shadow: none;
						overflow-y: scroll;
						overflow-x: hidden;
						height: 500px;
					"
				>
				<div v-if="bankskeleton">
					<v-skeleton-loader
					v-for="i in 4"
					:key="i"
					:loading="load"
					v-bind="attrs"
					height="70px"
					style="margin-bottom: -0.5rem !important"
					type="list-item-avatar, divider"
					></v-skeleton-loader>
				</div>
				<div v-if="items.length > 0 && bankskeleton == false">
					<template>
							<v-expansion-panels  v-model="panel" multiple>
								<v-expansion-panel v-for="item in items" :key="item.id">
									<v-expansion-panel-header
										hide-actions
										@click.native.stop="isEdit = false"
										style="margin-bottom: -1rem; margin-top: -2rem"
									>
										<template v-slot:default="{ open }">
											<v-col cols="12" md="12">
												<svg
													class="primary"
													style="
														margin-bottom: -1.8rem;
														width: 50px;
														height: 50px;
														padding: 10px;
														border-radius: 50pc;
														color: white;
													"
													viewBox="0 0 24 24"
												>
													<path
														fill="currentColor"
														d="M11.5,1L2,6V8H21V6M16,10V17H19V10M2,22H21V19H2M10,10V17H13V10M4,10V17H7V10H4Z"
													/>
												</svg>
												&nbsp;&nbsp;
												<span style="font-size: 1.5rem">
													{{ item.bank_name }}
												</span>
												<br /><br />
												<span
													:class="`${$vuetify.rtl ? 'numberRtl' : 'number'}`"
												>
													{{ item.bank_account_number }}
												</span>
											</v-col>
											<p style="z-index: 2">
												<v-btn
													:class="`${$vuetify.rtl ? 'editbtnRtl' : 'editbtn'}`"
													@click.native.stop="onEdit(item)"
													v-if="open && !isEdit"
													class="primary sm"
													>Edit</v-btn
												>
												<v-btn
													:class="`${$vuetify.rtl ? 'savebtnRtl' : 'savebtn'}`"
													v-if="open && isEdit"
													@click.native.stop="update(item.id)"
													:disabled="!validForm"
													type="submit"
													:loading="isLoading"
													class="success"
													>Save</v-btn
												>
												<v-btn
													:class="`${
														$vuetify.rtl ? 'cancelbtnRtl' : 'cancelbtn'
													}`"
													class="error sm"
													v-if="open && isEdit"
													@click="isEdit = false"
													>Cancel</v-btn
												>
											</p>
											<v-icon
												:class="`${$vuetify.rtl ? 'chevronRtl' : 'chevron'}`"
												>{{
													open
														? "mdi-chevron-up-circle-outline"
														: "mdi-chevron-down-circle-outline"
												}}</v-icon
											>
										</template>
									</v-expansion-panel-header>
									<v-expansion-panel-content>
										<div v-if="isEdit">
											<v-col cols="12" style="margin-bottom: -4rem !important">
												<h3 style="color: #777">
													{{ $tr("bank_information") }}
												</h3>
											</v-col>
											<v-form
												ref="formNames"
												v-model="validForm"
												lazy-validation
											>
												<v-container>
													<v-row
														style="
															margin: 0px !important;
															padding: 0px !important;
															height: 5rem !important;
														"
													>
														<v-col cols="5">
															<v-subheader>{{ $tr("bank_name") }}</v-subheader>
														</v-col>
														<v-col cols="5">
															<v-text-field
																dense
																:placeholder="
																	$tr('label_required', $tr('bank_name'))
																"
																v-model="form.bank_name"
																outlined
																:rules="nameRules"
																required
															></v-text-field>
														</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row
														style="
															background-color: #faf8faff;
															margin: 0px !important;
															padding: 0px !important;
															height: 5rem !important;
														"
													>
														<v-col cols="5">
															<v-subheader>{{
																$tr("bank_account_number_iban")
															}}</v-subheader>
														</v-col>
														<v-col cols="5">
															<v-text-field
																:placeholder="
																	$tr(
																		'label_required',
																		$tr('bank_account_number_iban')
																	)
																"
																dense
																v-model="form.bank_account_number_iban"
																outlined
																:rules="bankaccountnumberibanRules"
																required
															></v-text-field>
														</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row
														style="
															margin: 0px !important;
															padding: 0px !important;
															height: 5rem !important;
														"
													>
														<v-col cols="5">
															<v-subheader>{{
																$tr("bank_account_number")
															}}</v-subheader>
														</v-col>
														<v-col cols="5">
															<v-text-field
																:placeholder="
																	$tr(
																		'label_required',
																		$tr('bank_account_number')
																	)
																"
																dense
																v-model="form.bank_account_number"
																outlined
																:rules="bankAccountNumberRules"
																required
															></v-text-field>
														</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row
														style="
															background-color: #faf8faff;
															margin: 0px !important;
															padding: 0px !important;
															height: 5rem !important;
														"
													>
														<v-col cols="5">
															<v-subheader>{{ $tr("swift_code") }}</v-subheader>
														</v-col>
														<v-col cols="5">
															<v-text-field
																:placeholder="
																	$tr('label_required', $tr('swift_code'))
																"
																dense
																v-model="form.swift_code"
																outlined
																:rules="swiftcodeRules"
																required
															></v-text-field>
														</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row
														style="
															margin: 0px !important;
															padding: 0px !important;
															height: 5rem !important;
														"
													>
														<v-col cols="5">
															<v-subheader>{{ $tr("address") }}</v-subheader>
														</v-col>
														<v-col cols="5">
															<v-text-field
																:placeholder="
																	$tr('label_required', $tr('address'))
																"
																dense
																v-model="form.address"
																outlined
																:rules="addressRules"
																required
															></v-text-field>
														</v-col>
														<v-col cols="2"></v-col>
													</v-row>
												</v-container>
											</v-form>
										</div>

										<div v-else>
											<v-col cols="12" style="margin-bottom: -4rem !important">
												<h3 style="color: #777">
													{{ $tr("bank_information") }}
												</h3>
											</v-col>
											<v-container style="margin-left: 1rem !important">
												<v-row style="">
													<v-col cols="5"
														><strong style="color: #777">{{
															$tr("bank_name")
														}}</strong>
													</v-col>
													<v-col cols="5" id="span_right_side_styles">{{
														item.bank_name
													}}</v-col>
													<v-col cols="2"></v-col>
												</v-row>
												<v-row style="background-color: #faf8faff">
													<v-col cols="5"
														><strong style="color: #777">
															{{ $tr("bank_account_number_iban") }}</strong
														>
													</v-col>
													<v-col cols="5" id="span_right_side_styles">{{
														item.bank_account_number_iban
													}}</v-col>
													<v-col cols="2"></v-col>
												</v-row>
												<v-row style="">
													<v-col cols="5"
														><strong style="color: #777">
															{{ $tr("bank_account_number") }}
														</strong></v-col
													>
													<v-col cols="5" id="span_right_side_styles">{{
														item.bank_account_number
													}}</v-col>
													<v-col cols="2"></v-col>
												</v-row>
												<v-row style="background-color: #faf8faff">
													<v-col cols="5"
														><strong style="color: #777">{{
															$tr("swift_code")
														}}</strong>
													</v-col>
													<v-col cols="5" id="span_right_side_styles">{{
														item.swift_code
													}}</v-col>
													<v-col cols="2"></v-col>
												</v-row>
												<v-row style="">
													<v-col cols="5"
														><strong style="color: #777">{{
															$tr("supplier_name")
														}}</strong>
													</v-col>
													<v-col cols="5" id="span_right_side_styles">{{
														item.supplier.supplier_name
													}}</v-col>
													<v-col cols="2"></v-col>
												</v-row>
												<v-row style="background-color: #faf8faff">
													<v-col cols="5"
														><strong style="color: #777"
															>{{ $tr("address") }}
														</strong></v-col
													>
													<v-col cols="5" id="span_right_side_styles">{{
														item.address
													}}</v-col>
													<v-col cols="2"></v-col>
												</v-row>
											</v-container>
										</div>
									</v-expansion-panel-content>
								</v-expansion-panel>
							</v-expansion-panels>
					</template>
				</div>
					<div v-else-if="items.length == 0 && bankskeleton == false">
						<h3 style="text-align: center ">No Data</h3>
					</div>
				</v-card-text>
			</v-card>
		</v-dialog>
	</div>
</template>

<script>
import DialogCloseBtn from "../design/Dialog/CloseBtn.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
export default {
	components: { DialogCloseBtn, CTextField },
	data() {
		return {
			panel:[0],
			attrs: {
        class: 'mb-6',
        boilerplate: true,
        elevation: 2,
      },
			validForm: true,
			rules: {
				required: [(value) => !!value || "Required."],
			},
			form: {
				bank_name: "",
				bank_account_number: "",
				bank_account_number_iban: "",
				swift_code: "",
				address: "",
			},
			bankviewdialog: false,
			items: [],
			supplierupdate: [],
			isEdit: false,
			supplier_id: "",
			isLoading: false,
			nameRules: [
				(v) => !!v || "Bank Name is required",
				(v) =>
					(v && v.length <= 30) || "Bank Name must be less than 30 characters",
			],
			bankAccountNumberRules: [
				(v) => v.length > 0 || "Bank Account Number is required",
				(v) =>
					Number.isInteger(Number(v)) || "The value must be an integer number",
				(v) => v > 0 || "The value must be greater than zero",
			],
			bankaccountnumberibanRules: [
				(v) => !!v || "Bank Account Number Iban is required",
			],
			swiftcodeRules: [(v) => !!v || "Swift Code is required"],
			addressRules: [(v) => !!v || "Address is required"],
			bankskeleton:false,
			load : false,
		};
	},
	methods: {
		onEdit(item) {
			console.log(item);
			this.isEdit = true;
			this.form = {
				bank_name: item.bank_name,
				bank_account_number: item.bank_account_number,
				bank_account_number_iban: item.bank_account_number_iban,
				swift_code: item.swift_code,
				address: item.address,
			};
		},
		togglleBankViewDialog(id) {
			
			this.bankviewdialog = true;
			this.getSupplierBankAccount(id);
			
		},
		closeBankViewDialog() {
			this.bankviewdialog = false;
			this.items = [];
			this.panel = [0];
			this.bankskeleton = false;
		},
		async getSupplierBankAccount(id) {
			this.bankskeleton = true;
			const result = await this.$axios.get(`get-supplier-bank-account/${id}`);
			console.log(result);
			this.items = result.data;
			this.bankskeleton = false;
		},
		async update(id) {
			try {
				this.form["id"] = id;
				if (this.$refs.formNames[0].validate()) {
					this.isLoading = true;
					const result = await this.$axios.put(
						"update-supplier-bank-account",
						this.form
					);
					if (result) {
						this.items = this.items.map((item) => {
							if (item.id == id) {
								item.bank_name = this.form.bank_name;
								item.bank_account_number = this.form.bank_account_number;
								item.bank_account_number_iban =
									this.form.bank_account_number_iban;
								item.swift_code = this.form.swift_code;
								item.address = this.form.address;
							}
							return item;
						});
						this.$toasterNA(
							"green",
							" The Bank Account has successfully updated  "
						);
						this.isLoading = false;
						this.isEdit = false;
					}
					this.isEdit = false;
				}
			} catch (error) {
				this.$toasterNA("red", " Something went wrong");
				this.isLoading = false;
			}
		},
	},
};
</script>
<style scoped>
#span_left_side_styles {
	font-size: 20px;
	color: #777;
}
#span_right_side_styles {
	font-size: 18px;
}
.chevron {
	position: absolute;
	right: 30px;
}
.chevronRtl {
	position: absolute;
	left: 30px;
}
.editbtnRtl {
	margin-left: 4rem;
	margin-top: 2rem;
}
.editbtn {
	margin-right: 3rem !important;
	margin-top: 2rem;
}
.savebtnRtl {
	position: absolute;
	left: 5rem;
}
.cancelbtnRtl {
	position: absolute;
	left: 10rem;
}
.savebtn {
	/* margin-right: 3rem !important;
	*/
	position: absolute !important;
	right: 70px !important;
}
.cancelbtn {
	position: absolute !important;
	right: 150px !important;
	z-index: 1 !important;
}
.numberRtl {
	position: absolute;
	right: 8rem;
}
.number {
	position: absolute;
	left: 8rem;
}
</style>
