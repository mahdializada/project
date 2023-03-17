<template>
	<div :key="i">
			<v-card>
				<v-form
				@submit.prevent="submit"
				v-model="valid"
				lazy-validation
				ref="form"
				:loading="loading"
				>
				<CustomStepper
				:headers="headers"
				:canNext="false"
				@validate="() => {}"
				:is-submitting="false"
				ref="addedbankaccount"
				@submit="submit"
				:isSubmitting="isSubmitting"
				>
				<template v-slot:step1>
							<!-- <v-card-title >
						<h4>Bank Account</h4>
					</v-card-title> -->
							<v-card-text
								style="max-height: 650px; overflow-y: scroll; margin-top: -90px"
							>
								<MultiInputItem
									:items="form"
									@addMore="addOneMore"
									@removeItem="removeItem"
									:title="'bank_account'"
								>
									<template v-slot:content="{ item }">
										<div
											class="h-full d-flex"
											style="height: fit-content !important"
										>
											<div class="w-full">
												<CTextField
													:title="$tr('label_required', $tr('bank_name'))"
													:placeholder="$tr('label_required', $tr('bank_name'))"
													type="textfield"
													rounded
													:rules="nameRules"
													v-model="item.bank_name"
												/>
											</div>
											<div class="w-full">
												<CTextField
													:title="
														$tr('label_required', $tr('bank_account_name'))
													"
													:placeholder="
														$tr('label_required', $tr('bank_account_name'))
													"
													type="textfield"
													rounded
													:rules="bankaccountnameRules"
													v-model="item.bank_account_name"
												/>
											</div>
										</div>
										<div
											class="h-full d-flex"
											style="height: fit-content !important"
										>
											<div class="w-full">
												<CTextField
													:title="
														$tr('label_required', $tr('bank_account_number'))
													"
													:placeholder="
														$tr('label_required', $tr('bank_account_number'))
													"
													type="textfield"
													rounded
													:rules="bankAccountNumberRules"
													v-model="item.bank_account_number"
												/>
											</div>
											<div class="w-full">
												<CTextField
													:title="
														$tr(
															'label_required',
															$tr('bank_account_number_iban')
														)
													"
													:placeholder="
														$tr(
															'label_required',
															$tr('bank_account_number_iban')
														)
													"
													type="textfield"
													rounded
													:rules="bankaccountnumberibanRules"
													v-model="item.bank_account_number_iban"
												/>
											</div>
										</div>
										<div
											class="h-full d-flex"
											style="height: fit-content !important"
										>
											<div class="w-full">
												<CTextField
													:title="$tr('label_required', $tr('swift_code'))"
													:placeholder="
														$tr('label_required', $tr('swift_code'))
													"
													type="textfield"
													rounded
													v-model="item.swift_code"
													:rules="swiftcodeRules"
												/>
											</div>
											<div class="w-full">
												<CTextField
													:title="$tr('label_required', $tr('address'))"
													:placeholder="$tr('label_required', $tr('address'))"
													type="textfield"
													rounded
													v-model="item.address"
													:rules="addressRules"
												/>
											</div>
										</div>
									</template>
								</MultiInputItem>
							</v-card-text>

							<!-- <v-card-actions>
						<v-spacer></v-spacer>
						<v-btn class="primary" type="submit">{{ $tr("submit") }}</v-btn>
						<v-btn
						class="error"
						@click="close"
						>
						{{ $tr("cancel") }}
					</v-btn>
				</v-card-actions> -->
						</template>
						<template v-slot:step2>
							<DoneMessage
								:title="$tr('item_all_set', $tr('bank_account'))"
								:subTitle="$tr('you_can_access_your_item', $tr('bank_account'))"
							/>
						</template>
					</CustomStepper>
				</v-form>
			</v-card>
	</div>
</template>
<script>
import DoneMessage from "../design/components/DoneMessage.vue";
import DialogCloseBtn from "../design/Dialog/CloseBtn.vue";
import CustomStepper from "../design/FormStepper/CustomStepper.vue";
import MultiInputItem from "../new_form_components/cat_product_selection/MultiInputItem.vue";
import CTextArea from "../new_form_components/Inputs/CTextArea.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
export default {
	components: {
    DialogCloseBtn,
    MultiInputItem,
    CTextField,
    CTextArea,
    CustomStepper,
    DoneMessage,
},
props:{
	id:{
		// type:String,
		required:true
	},
	reset:Boolean,
},
	data() {
		return {
			valid: true,
			dialog: false,
			loading: false,
			addedbankaccount: "",
			BankData: [],
			isSubmitting:false,
			supplier_id: "",
			i:0,
			form: [
				{
					bank_name: "",
					bank_account_name: "",
					bank_account_number: "",
					bank_account_number_iban: "",
					swift_code: "",
					address: "",
				},
			],
			nameRules: [
				(v) => !!v || "Bank Name is required",
				(v) => (v && v.length <= 30) || "Bank Name must be less than 30 characters",
			],
			bankaccountnameRules: [(v) => !!v || "Bank Account Name is required"],
			// bankAccountNumberRules: [(v) => !!v || "Bank Account Number is required"],
			bankAccountNumberRules: [
				(v) => v.length > 0 || "Bank Account Number is required",
				(v) =>
					Number.isInteger(Number(v)) || "The value must be an integer number",
				(v) => v > 0 || "The value must be greater than zero",
			],
			bankaccountnumberibanRules:
				[
					(v) => !!v || "Bank Account Number Iban is required",
				],
			swiftcodeRules: [(v) => !!v || "Swift Code is required"],
			addressRules: [(v) => !!v || "Address is required"],
			headers: [
				{
					icon: "fa-globe",
					title: "bank account",
					slotName: "step1",
				},
				{
					icon: "fa-thumbs-up",
					title: "done",
					slotName: "step2",
				},
			],
		};
	},
	watch:{
		reset: function(){
			this.resetForm();
		}
	},
	methods: {

		// multi item input
		async addOneMore() {
			if (this.$refs.form.validate()) {
				this.form.push({
					bank_name: "",
					bank_account_name: "",
					bank_account_number: "",
					bank_account_number_iban: "",
					swift_code: "",
					address: "",
				});
			}
		},
		async submit() {
			try {
				this.isSubmitting = true;
				if (this.$refs.form.validate()) {
					let bankdata = {
						bank: this.form,
						supplier_id: this.id,
					};

					this.loading = true;
					const result = await this.$axios.post(
						"store-supplier-bank-account",
						bankdata
					);
					if (result.status == 201) {
						this.$refs.addedbankaccount.forceNext();
						this.resetForm();
						// this.dialog = false;
						this.loading = false;
					}
				}
				this.isSubmitting = false;
			} catch (error) {
				console.log(error);
			}
		},
		resetForm() {
			this.form = [
				{
					bank_name: "",
					bank_account_name: "",
					bank_account_number: "",
					bank_account_number_iban: "",
					swift_code: "",
					address: "",
				},
			];
			this.$refs.form.resetValidation()
		},
		async removeItem(item) {
			if (this.form.length > 1) this.form.splice(item, 1);
		},
	},
};
</script>
<style scoped></style>
