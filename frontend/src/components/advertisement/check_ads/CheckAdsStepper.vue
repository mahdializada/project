<template>
	<Stepper
		:key="key"
		ref="hello"
		:title="$tr('check_ad_and_order')"
		cookieName="create_product_list"
		:show="show"
		:steps="steps"
		:form="form"
		:validateRules="formRules"
		:submit="onDownloadExcel"
		@close="onClose"
		@reset="reset"
		:submitText="
			this.form.check_type == 'order'
				? $tr('download_orders')
				: $tr('download_ads_id_not_exists')
		"
	/>
</template>

<script>
import { saveExcel } from "@progress/kendo-vue-excel-export";
import Stepper from "../../horizontal_stepper/Stepper.vue";
import UploadStep from "./UploadStep.vue";
import CheckStep from "./CheckStep.vue";
import Validations from "~/validations/validations";
import CheckType from "./CheckType.vue";
import moment from "moment";

export default {
	components: { Stepper },

	data() {
		return {
			key: 1,
			show: false,
			steps: [
				{
					title: this.$tr("check_type"),
					component: CheckType,
				},
				{
					title: this.$tr("upload"),
					component: UploadStep,
				},
				{
					title: this.$tr("check"),
					component: CheckStep,
				},
			],
			isSubmitting: false,
			form: {
				check_type: "ad",
				file: [],
				dateRange: {
					start_date: moment().format("YYYY-MM-DD"),
					end_date: moment().format("YYYY-MM-DD"),
				},
			},

			formRules: {
				form: {
					file: Validations.requiredValidation,
					check_type: Validations.requiredValidation,
					dateRange: {},
				},
			},
		};
	},

	methods: {
		reset() {
			this.key++;
			this.form = {
				check_type: "ad",
				file: [],
				dateRange: {
					start_date: moment().format("YYYY-MM-DD"),
					end_date: moment().format("YYYY-MM-DD"),
				},
			};
		},
		onClose() {
			this.show = false;
			this.reset();
		},
		toggleCheckAds(item) {
			this.show = !this.show;
		},

		async onDownloadExcel() {
			try {
				if (this.form.check_type == "order") {
					this.downloadOrders();
					return true;
				}
				saveExcel({
					data: this.form?.file,
					fileName: new Date().toLocaleDateString() + " - ads id not exists",
					columns: [
						{
							field: "id",
							title: "ID",
							autoWidth: true,
							vAlign: "left",
						},
						{
							field: "ad_id",
							title: "Ad Id",
							autoWidth: true,
							vAlign: "left",
						},
					],
				});
				this.reset();
				return true;
			} catch (error) {
				console.log(error);
			}
		},
		downloadOrders() {
			try {
				saveExcel({
					data: this.form?.file,
					fileName:
						this.form?.dateRange?.start_date +
						"/" +
						this.form?.dateRange?.end_date +
						"-UnSavedOrders",
					columns: [
						{
							field: "exist",
							title: "Exist",
							autoWidth: true,
							vAlign: "left",
						},
						{
							field: "pcode",
							title: "product",
							autoWidth: true,
							vAlign: "left",
						},
						{
							field: "name",
							title: "Customer Name",
							autoWidth: true,
							vAlign: "left",
						},
						{
							field: "phone",
							title: "Phone",
							autoWidth: true,
							vAlign: "left",
						},
						{
							field: "qty",
							title: "Quantity",
							autoWidth: true,
							vAlign: "left",
						},
						{
							field: "price",
							title: "Price",
							autoWidth: true,
							vAlign: "left",
						},
						{
							field: "city",
							title: "City",
							autoWidth: true,
							vAlign: "left",
						},
						{
							field: "address",
							title: "Address",
							autoWidth: true,
							vAlign: "left",
						},
					],
				});
				this.reset();
			} catch (error) {
				console.log(error);
				return false;
			}
		},
	},
};
</script>

<style></style>
