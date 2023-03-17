<template>
	<v-dialog v-model="model" persistent max-width="500" width="1300">
		<v-card class="white">
			<v-form ref="form" lazy-validation @submit.prevent="onSubmit">
				<v-card-title>
					<SvgIcon
						:icon="icon"
						:fill="'var(--v-primary-base)'"
						class="mt-1"
					></SvgIcon>
					<div
						style="font-size: 18px"
						class="font-weight-medium px-1 primary--text input-title"
					>
						{{ title }}
					</div>
				</v-card-title>
				<v-card-text>
					<div class="me-1">
						<p class="px-1">{{ label + $tr("value") }}</p>
						<v-text-field
							:class="`form-new-item form-custom-text-field`"
							:title="$tr('label_required', $tr('value'))"
							background-color="var(--new-input-bg)"
							outlined
							dense
							:rounded="false"
							type="number"
							placeholder="Balance amount"
							v-model="$v.form.value.$model"
							:rules="validateRule($v.form.value, $root, $tr('balance amount'))"
						>
							<template v-slot:prepend-inner>
								<SvgIcon :icon="icon"></SvgIcon>
							</template>
						</v-text-field>
					</div>
					<div style="height: 40px" class="">
						<v-checkbox
							v-if="showCheckBox"
							class="reason pt-0 ps-2 blue lighten-5"
							hide-details
							v-model="autoBid"
							label="this adset bid is auto ,change it to custom?"
						/>
					</div>
				</v-card-text>
				<v-card-actions class="pa-3">
					<v-spacer></v-spacer>
					<v-btn style="font-weight: 400" hide-details @click="model = false">
						{{ $tr("cancel") }}
					</v-btn>
					<v-btn
						style="font-weight: 400"
						hide-details
						dark
						color="primary"
						@click="validateFunc"
						:loading="loading"
					>
						{{ $tr("add") }}
					</v-btn>
				</v-card-actions>
			</v-form>
		</v-card>
	</v-dialog>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import Validations from "../../validations/validations";
import SvgIcon from "~/components/design/components/SvgIcon.vue";
export default {
	components: { SvgIcon },
	data() {
		return {
			validateRule: GlobalRules.validate,
			model: false,
			title: this.$tr("add_budget_value"),
			label: this.$tr("budget_value"),
			type: null,
			defaultValue: null,
			form: {
				platform: null,
				adset_id: null,
				value: "",
				type: null,
			},
			icon: "budget-icon",
			autoBid: false,
			showCheckBox: false,
			loading: false,
		};
	},
	validations: {
		form: {
			value: Validations.decimalValidation,
		},
	},

	methods: {
		toggleDialog(data, type) {
			this.reset();
			this.model = !this.model;
			if (data && type) {
				this.form.adset_id = data.adset_id;
				this.form.platform = data.platform.platform_name;
				this.form.type = type;
				console.log(data);
				if (type == "bid") {
					this.icon = "bid-icon";
					this.title = this.$tr("add_item_value", this.$tr("bid"));
					this.label = this.$tr("bid");
					this.form.value = parseFloat(data.bid);
					this.defaultValue = parseFloat(data.bid);
				} else {
					this.icon = "budget-icon";
					this.title = this.$tr("add_item_value", this.$tr("budget"));
					this.label = this.$tr("budget");
					this.form.value = parseFloat(data.daily_budget);
					this.defaultValue = parseFloat(data.daily_budget);
				}
			}
		},
		reset() {
			this.loading = false;
			this.showCheckBox = false;
			this.autoBid = false;
			this.form = {
				platform: null,
				adset_id: null,
				value: "",
				type: null,
			};
		},
		validateFunc() {
			this.$refs.form.validate();
			this.$v.form.$touch();
			if (
				this.defaultValue == this.form.value ||
				(this.showCheckBox == true && this.autoBid == false)
			) {
				console.log(this.defaultValue, this.form.value);

				this.model = !this.model;
				return;
			}
			if (!this.$v.form.value.$invalid) this.onSubmit();
		},
		async onSubmit() {
			try {
				this.loading = true;

				if (this.showCheckBox == true && this.autoBid == true)
					this.form.auto = "custom";
				let data = await this.$axios.post(
					`advertisement/update-adset-bid-budget`,
					this.form
				);
				this.loading = false;
				// if (this.form.type == "budget" && this.form.platform == "tiktok") {
				// 	this.model = false;
				// 	this.$toasterNA("green", data.data);
				// } else
				this.$emit("submit", data);
			} catch (error) {
				this.loading = false;
				console.log("error", error.response.status);
				if (error.response.status == 501) {
					this.showCheckBox = true;
				} else if (
					error.response.data == "The bid strategy cannot be modified."
				)
					this.$toasterNA("red", " The Adset Bid strategy is Auto  ");
				else this.$toasterNA("red", error.response.data);
			}
		},
	},
};
</script>
