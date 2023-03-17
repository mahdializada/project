<template>
	<div class="mt-5" style="height: 90%" :key="i">
		<InputCard
			height="100"
			class="mt-2 overflow-auto expand"
			:title="$tr('add_product')"
			:sub_title="$tr('create_label_text')"
		>
			<v-expansion-panels flat accordion focusable mandatory>
				<v-timeline class="customTimeLine2" dense flat>
					<v-timeline-item
						class="pb-0"
						v-for="(item, ind) in form.products.$each.$iter"
						:key="ind"
					>
						<template v-slot:icon>
							<v-icon light size="15" color="white">{{
								parseInt(ind) + 1
							}}</v-icon>
						</template>
						<v-expansion-panel>
							<v-expansion-panel-header
								disable-icon-rotate
								class="py-0 ps-0 pe-2 my-1 text-subtitle-2"
							>
								<div>
									<p class="mb-0 primary--text text-h6 font-weight-medium">
										{{ $tr("product_name") }}
									</p>
								</div>

								<template v-slot:actions>
									<span class="d-flex justify-center align-center">
										<v-icon
											@click.stop="closeLabel(item)"
											color="primary"
											size="20"
										>
											mdi-close
										</v-icon>
									</span>
								</template>
							</v-expansion-panel-header>

							<v-expansion-panel-content>
								<div class="mb-3 d-flex flex-column flex-md-row">
									<CComboBox
										:canAddNewItem="false"
										px="px-0"
										v-model="item.product_code.$model"
										@blur="item.product_code.$touch()"
										:rules="
											validateRule(item.product_code, $root, $tr('product'))
										"
										:invalid="item.product_code.$invalid"
										:placeholder="$tr('code')"
										:loading="isFetchingproducts"
										:items="products"
										item-value="pcode"
										item-text="name"
										prepend-inner-icon="mdi-package-variant"
										:title="$tr('label_required', $tr('product_code'))"
									/>
									<!-- <CFileInputSingle
										:title="'upload_icon'"
										v-model="item.product_image.$model"
										@blur="item.product_image.$touch()"
										:rules="
											validateRule(
												item.product_image,
												$root,
												$tr('product_image')
											)
										"
										:invalid="item.product_image.$invalid"
										:placeholder="$tr('upload_your_icon_here')"
										prepend-inner-icon="mdi-cloud-upload"
										accept="image/*"
									></CFileInputSingle> -->
								</div>

								<div class="d-flex flex-column flex-md-row">
									<CNumber
										py="py-0"
										v-model="item.product_quantity.$model"
										@blur="item.product_quantity.$touch()"
										:rules="
											validateRule(
												item.product_quantity,
												$root,
												$tr('product_quantity')
											)
										"
										:invalid="item.product_quantity.$invalid"
										:title="$tr('Product_quantity')"
										:placeholder="$tr('quantity')"
										prepend-inner-icon="fa fa-atom"
										px="px-0"
									/>
									<CTextField
										py="py-0"
										v-model="item.product_size.$model"
										@blur="item.product_size.$touch()"
										:rules="
											validateRule(
												item.product_size,
												$root,
												$tr('product_size')
											)
										"
										:invalid="item.product_size.$invalid"
										:title="$tr('size')"
										:placeholder="$tr('size')"
										prepend-inner-icon="fa fa-atom"
									/>

									<CColorPicker
										py="py-0"
										v-model="item.product_color.$model"
										@blur="item.product_color.$touch()"
										:rules="
											validateRule(
												item.product_color,
												$root,
												$tr('product_color')
											)
										"
										:closeClickContent="true"
										:invalid="item.product_color.$invalid"
										:title="$tr('label_required', $tr('color'))"
										:currentColor.sync="item.product_color.$model"
										:placeholder="$tr('color')"
									/>
								</div>
							</v-expansion-panel-content>
						</v-expansion-panel>
						<v-divider></v-divider>
					</v-timeline-item>
				</v-timeline>
			</v-expansion-panels>
			<v-icon
				v-if="!isEdit"
				size="20"
				class="primary rounded-circle pa-1"
				color="white"
				style="left: 30px !important; top: 30px !important"
				@click="addOneMore"
				>mdi-plus</v-icon
			>
		</InputCard>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CAutoComplete from "../new_form_components/Inputs/CAutoComplete.vue";
import CComboBox from "../new_form_components/Inputs/CComboBox.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
import CColorPicker from "../new_form_components/Inputs/CColorPicker.vue";
import CTextArea from "../new_form_components/Inputs/CTextArea.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import CFileInputSingle from "../new_form_components/Inputs/CFileInputSingle.vue";
import CSelect from "../new_form_components/Inputs/CSelect.vue";
import CNumber from "../new_form_components/Inputs/CNumber.vue";
export default {
	props: {
		form: Object,
		isEdit: Boolean,
	},
	data() {
		return {
			i: 0,
			products: [],
			isFetchingproducts: false,
			switch1: false,
			validateRule: GlobalRules.validate,
		};
	},
	created() {
		this.fetchItems({ type: "products", id: 1 });
	},
	methods: {
		async validate() {
			this.form.products.$touch();

			let isValid = !this.form.products.$invalid;
			return isValid;
		},

		async loaded() {
			// this.fetchItems({ type: "products", id: 1 });
		},

		async fetchItems({ type, id }) {
			try {
				this["isFetching" + type] = true;
				const url = `orders/fetch-items/${type}/${id}`;
				const { data } = await this.$axios.get(url);
				this[data.type] = data.items;
			} catch (error) {}
			this["isFetching" + type] = false;
		},

		closeLabel(item) {
			if (this.form.$model.products.length > 1) {
				this.form.$model.products = this.form.$model.products.filter(
					(row) => row.id != item.$model.id
				);
			}
		},
		addOneMore() {
			this.form.products.$model.push({
				id: this.generateID(),
				category: "",
				label_name: "",
				color: "#FF0000FF",
				title: "",
				tab: [],
				status: "",
			});
		},
		clickswitch(item) {
			this.form.$model.labels = this.form.$model.labels.map((row) => {
				if (row.id === item.id) {
					row.status = !row.status;
				}
				return row;
			});
			console.log(this.form.$model.labels);
		},

		generateID() {
			return (
				"Id" +
				Math.floor(
					(Date.now() *
						Math.floor(
							Math.random() * Math.floor(Math.random() * Date.now())
						)) /
						(Math.random() * 1000000000)
				)
			);
		},
	},
	components: {
		CAutoComplete,
		CTextField,
		CColorPicker,
		CTextArea,
		InputCard,
		CComboBox,
		CFileInputSingle,
		CSelect,
		CNumber,
	},
};
</script>
<style>
.customTimeLine2 .v-timeline-item__dot {
	box-shadow: unset !important;
	position: absolute;
	top: 20px;
	left: calc(50% - 20px);
	border: 1px solid var(--v-primary-base);
	display: flex;
	align-items: center;
	justify-content: center;
	height: 40px;
	width: 40px;
}
.customTimeLine2.v-timeline::before {
	top: 50px !important;
}
.customTimeLine2 .v-expansion-panel-content__wrap {
	padding: 0 !important;
}
.customTimeLine2 .v-expansion-panels {
	z-index: 0 !important;
}
.expand .v-expansion-panels {
	display: block !important;
	z-index: 0 !important;
}
.expand .v-expansion-panel::before {
	box-shadow: none !important;
}
.expand .v-expansion-panel-header:before {
	background-color: var(--v-primary-base) !important;
	border-radius: 10px !important;
	left: -90px !important;
}
</style>
