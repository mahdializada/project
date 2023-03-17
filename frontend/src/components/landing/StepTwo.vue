<template>
	<div>
		<!-- <div class="flex-column d-flex align-center mb-3">
			<v-avatar size="100">
				<svg
					data-name="Layer 1"
					id="Layer_1"
					viewBox="0 0 512 512"
					xmlns="http://www.w3.org/2000/svg"
				>
					<defs>
						<style>
							.cls-1 {
								fill: #ffd34e;
							}
							.cls-2 {
								fill: #4b525f;
							}
							.cls-3 {
								fill: #304269;
							}
							.cls-4 {
								fill: #91bed4;
							}
							.cls-5 {
								fill: #ea6045;
							}
						</style>
					</defs>
					<title />
					<rect
						class="cls-1"
						height="512"
						rx="256"
						ry="256"
						width="512"
					/>
					<path
						class="cls-2"
						d="M181.76,114.66V164h21.42V125.7a4.75,4.75,0,0,1,4.75-4.75h96.14a4.75,4.75,0,0,1,4.75,4.75V164H328.3V114.66a14.59,14.59,0,0,0-14.59-14.59H196.36A14.59,14.59,0,0,0,181.76,114.66Z"
					/>
					<rect
						class="cls-3"
						height="260.71"
						rx="16.88"
						ry="16.88"
						width="332.76"
						x="89.62"
						y="151.22"
					/>
					<path
						class="cls-4"
						d="M215.73,250a65.3,65.3,0,0,0,80.55,0L416.8,155c-3-2.71-6.95-5-11.3-5h-299c-4.35,0-8.31,2.27-11.3,5Z"
					/>
					<rect
						class="cls-5"
						height="26.59"
						rx="10.7"
						ry="10.7"
						transform="translate(244.77 -111.74) rotate(45)"
						width="26.59"
						x="243.97"
						y="226.3"
					/>
				</svg>
			</v-avatar>

			<v-btn @click="addNewRow" color="primary" class="rounded-sm mt-2">
				{{ $tr('add_order') }}
			</v-btn>
		</div> -->
		<div
			v-for="(order, index) in designRequest.design_request_orders.$each.$iter"
			:key="index"
		>
			<!-- <div class="d-flex align-center w-full justify-space-between">
				<h2 class="mb-2">{{ $tr('order') }} {{ index * 1 + 1 }}</h2>
				<CloseBtn class="pt-0" @click="removeRow(index)" />
			</div> -->
			<div class="w-full">
				<CustomInput
					@blur="order.order_type.$touch()"
					:items="orderTypes"
					:label="$tr('label_required', $tr('order_type'))"
					:placeholder="$tr('choose_item', $tr('order_type'))"
					v-model.trim="order.order_type.$model"
					:rules="validate(order.order_type, $root, 'order_type')"
					type="autocomplete"
					item-text="name"
					item-value="id"
				/>
			</div>

			<div class="w-full">
				<Editor
					:key="salesNoteKey"
					v-model.trim="order.sales_note.$model"
					:rules="validate(order.sales_note, $root, 'sales_note')"
					:label="$tr('sales_note')"
				/>
			</div>
		</div>
	</div>
</template>

<script>
import Editor from "../design/Editor.vue";
import CustomInput from "~/components/design/components/CustomInput";
import CloseBtn from "~/components/design/Dialog/CloseBtn";
import GlobleRules from "~/helpers/vuelidate";
import designRequestValidations from "~/validations/design-request-validations";
export default {
	components: { CustomInput, CloseBtn, Editor },
	props: {
		salesNoteKey: {
			type: Number,
			required: true,
		},
	},

	data() {
		return {
			validate: GlobleRules.validate,
			orderTypes: [
				{ id: "scratch", name: this.$tr("Scratch") },
				{ id: "copy", name: this.$tr("Copy") },
				{ id: "mixed", name: this.$tr("Mixed") },
			],
		};
	},

	methods: {
		removeRow(index) {
			this.designRequestSchema.design_request_orders.splice(index, 1);
		},

		addNewRow() {
			this.designRequestSchema.design_request_orders.push(
				JSON.parse(
					JSON.stringify(designRequestValidations.designRequestOrderSchema)
				)
			);
		},
	},
};
</script>
