<template>
	<Stepper
		ref="hello"
		:title="$tr('create_new') + ' ' + $tr('order')"
		cookieName="create_new_order"
		:show="show"
		:steps="steps"
		:form="products"
		:validateRules="productRules"
		:submit="validateForm"
		@close="onClose"
		@reset="reset"
		:showBack="showBack"
	/>
</template>

<script>
import Stepper from "../horizontal_stepper/Stepper.vue";

import OrderStep from "./OrderStep.vue";
import ProductStep from "./ProductStep.vue";
import ProductInfoStep from "./ProductInfoStep.vue";
import AddressStep from "./AddressStep.vue";
import Validations from "~/validations/validations";
import HandleException from "~/helpers/handle-exception";
import { requiredIf } from "vuelidate/lib/validators";
import ProductTable from "./ProductTable.vue";

export default {
	components: { Stepper },

	data() {
		return {
			show: false,

			showBack: true,
			steps: [
				{
					title: this.$tr("order"),
					component: OrderStep,
					id: "info",
					props: {
						fetchCountry: false,
					},
				},

				{
					title: this.$tr("address"),
					component: AddressStep,
					id: "info",
				},
				{
					title: this.$tr("product"),
					component: ProductStep,
					id: "info",
				},

				{
					title: this.$tr("product_info"),
					component: ProductInfoStep,
					id: "info",
				},
				{
					title: this.$tr("ProductTable"),
					component: ProductTable,
					id: "info",
				},
			],
			isSubmitting: false,
			products: {
				projectname: null,
				project_id: null,
				source: null,
				solution: null,
				celebrity_code: null,
				landing_link: null,
				phone_number: null,
				city: null,
				area: null,
				address_id: null,
				address: null,
				products: [
					{
						id: 1,
						product_code: null,
						product_image: null,
						product_quantity: null,
						product_size: null,
						product_color: "#FF0000FF",
					},
				],

				price: 0,
				send_brochure: true,
				with_tax: true,
				delay: false,
				start_date: null,
				note: null,
				selectedAddress: null,
				name: null,
			},

			productRules: {
				form: {
					projectname: {},
					project_id: Validations.requiredValidation,
					source: Validations.requiredValidation,
					solution: {
						required: requiredIf(function (value) {
							return this.form.source == 3;
						}),
					},

					celebrity_code: {
						required: requiredIf(function (value) {
							return this.form.source == 6;
						}),
					},
					landing_link: {
						required: requiredIf(function (value) {
							return this.form.source == 7;
						}),
					},
					phone_number: Validations.phoneValidation,
					city: Validations.requiredValidation,
					area: Validations.requiredValidation,
					address: Validations.requiredValidation,
					address_id: {},
					products: {
						$each: {
							id: {},
							product_code: Validations.requiredValidation,
							product_image: {},
							product_quantity: Validations.requiredValidation,
							product_size: Validations.requiredValidation,
							product_color: Validations.requiredValidation,
						},
					},

					price: Validations.numberValidation,
					send_brochure: Validations.requiredValidation,
					with_tax: Validations.requiredValidation,
					delay: Validations.requiredValidation,
					start_date: {
						required: requiredIf(function (value) {
							return !this.form.delay;
						}),
					},

					note: Validations.htmlNoteValidation,
					name: Validations.requiredValidation,
					selectedAddress: {},
				},
			},
		};
	},

	watch: {
		"connection.connection_type": function (type) {},
		"connection.connection_id": function (connection_id) {},
	},

	methods: {
		reset() {
			this.products = {
				projectname: null,
				project_id: null,
				source: null,
				solution: null,
				celebrity_code: null,
				landing_link: null,
				phone_number: null,
				city: null,
				area: null,
				address_id: null,
				address: null,
				products: [
					{
						id: 1,
						product_code: null,
						product_image: null,
						product_quantity: null,
						product_size: null,
						product_color: "#FF0000FF",
					},
				],

				price: 0,
				send_brochure: true,
				with_tax: true,
				delay: false,
				start_date: null,
				note: null,
				selectedAddress: null,
				name: null,
			};
		},
		onClose() {
			this.show = false;
			this.reset();
		},
		toggle() {
			this.show = !this.show;
			this.showBack = true;
		},

		async validateForm() {
			const products = {};

			if (this.products.selectedAddress != null) {
				if (
					this.products?.selectedAddress?.address2 == this.products.address &&
					this.products?.selectedAddress?.emirate_id == this.products.city &&
					this.products?.selectedAddress?.city == this.products.area.name &&
					this.products?.selectedAddress?.name == this.products.name
				)
					products["order"] = this.products.selectedAddress.post_id;
				else {
					products["province"] = this.products.city;
					products["area"] = this.products.area.name;
					products["address"] = this.products.address;
					products["name"] = this.products.name;
				}
			}

			products["pcode"] = this.products.products.map((row) => row.product_code);
			products["pcode"] = products.pcode.map((row) => row.pcode);
			products["qty"] = this.products.products.map(
				(row) => row.product_quantity
			);
			products["prod_size"] = this.products.products.map(
				(row) => row.product_size
			);
			products["prod_color"] = this.products.products.map(
				(row) => row.product_color
			);
			products["delay"] = this.products.start_date;
			products["project"] = this.products.project_id;
			products["withtax"] = this.products.with_tax == true ? 1 : 0;
			products["buroaz"] = this.products.send_brochure == true ? 1 : 0;
			products["ad_id"] = this.$auth.user.username;
			products["source"] = this.products.source;
			products["phone"] = this.products.phone;
			products["price"] = parseFloat(this.products.price);
			products["status"] = this.products.delay == true ? 1 : 5;
			products["celebrity_code"] = this.products.celebrity_code;
			products["solution"] = this.products.solution;
			products["landing_link"] = this.products.landing_link;

			products["notes"] = this.products.note;

			return await this.insertProduct(products);
		},

		async insertProduct(products) {
			console.log("insert", products);
			try {
				const data = await this.$axios.post("crm-orders", products);
				if (data.status) {
					this.$toasterNA("green", this.$tr("successfully_inserted"));
					this.reset();
					return true;
				} else if (data.not_found) {
					// this.$toastr.e(this.$tr("ad_id_not_found"));
					this.$toasterNA("red", this.$tr("ad_id_not_found"));
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
			return false;
		},
	},
};
</script>

<style></style>
