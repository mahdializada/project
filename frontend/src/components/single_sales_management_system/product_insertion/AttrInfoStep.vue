<template>
	<div class="w-full mt-5">
		<CAttributeSelection
			:title="'Attributes'"
			:sub_title="'Please Select what attributes does your product have.'"
			@AddAtrr="AddAtrr"
			ref="CAttributeSelectionRefs"
			:titleItems="titleItems"
			:selectedAttr="selectedAttr"
			:loading="loading"
		/>

		<v-expand-transition>
			<div v-if="selectedAttr.length > 0" class="mt-3">
				<InputCard :title="''" px="px-5">
					<v-simple-table class="table">
						<template v-slot:default>
							<thead>
								<tr class="text-body-1">
									<th v-for="(item, index) in selectedAttr" :key="index" class="text-left px-0">
										<div
											class="pe-1 ps-3 red w-full blue lighten-5 h-full d-flex justify-space-between align-center"
										>
											<p class="mb-0 text-uppercase">{{ item.name }}</p>
											<v-icon
												v-if="item.name != 'quantity' && item.name != 'price'"
												size="18"
												color="primary"
												@click="AddAtrr(item)"
												>mdi-close</v-icon
											>
										</div>
									</th>
									<th class="text-left px-0">
										<div
											class="pe-1 ps-3 red w-full blue lighten-5 h-full d-flex justify-space-between align-center"
										></div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr class="pb-1" v-for="(item, i) in modelText" :key="i">
									<td v-for="(it, ind) in item" :key="ind" class="px-1 w_custom">
										<CTextField
											v-if="!it.values"
											:placeholder="it.name"
											v-model="modelText[i][ind].model"
											hide-details
											:type="it.name == 'quantity' || it.name == 'price' ? 'number' : 'text'"
											class="pt-0 mt-1"
											style="min-width: 170px !important"
											px="px-0"
											dense
											:title="''"
										/>

										<CSelect
											v-else
											:title="''"
											v-model="modelText[i][ind].model"
											:items="JSON.parse(it.values)"
											:placeholder="it.name"
											dense
											hide-details
											class="pt-0 mt-1"
											style="min-width: 170px !important"
											px="px-0"
										/>
									</td>
									<td style="vertical-align: center">
										<v-btn icon depressed class="blue lighten-5 mt-1" small>
											<v-icon color="primary" @click="removeRow(item, i)" size="20"
												>mdi-close</v-icon
											>
										</v-btn>
									</td>
								</tr>
							</tbody>
						</template>
					</v-simple-table>
					<v-divider class="mt-2"></v-divider>
					<v-icon @click="addRow()" class="POS-CUST rounded-circle" size="30" color="primary"
						>mdi-plus</v-icon
					>
				</InputCard>
			</div>
		</v-expand-transition>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CAttributeSelection from "../../new_form_components/Inputs/CAttributeSelection.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CSelect from "../../new_form_components/Inputs/CSelect.vue";

export default {
	props: {
		form: Object, // default prop
	},
	components: { CAttributeSelection, InputCard, CTextField, CSelect },
	data() {
		return {
			validateRule: GlobalRules.validate, // gloabl function fro validate
			attrItems: [],
			selectedAttr: [
				{ id: 10, name: "quantity" },
				{ id: 11, name: "price" },
			],
			modelText: [
				[
					{ id: 10, name: "quantity" },
					{ id: 11, name: "price" },
				],
			],
			defaultArray: [
				{ id: 10, name: "quantity" },
				{ id: 11, name: "price" },
			],
			titleItems: [],
			loading: false,
		};
	},

	watch: {
		modelText: function (params) {
			if (params.length == 0) {
				this.defaultValue();
			}
		},
	},
	methods: {
		async fetchAttr() {
			try {
				const res = await this.$axios.get("single-sales/attribute-ssp", {
					params: {
						fetch_for_form: true,
					},
				});
				if (res.status == 200) {
					this.titleItems = res.data;
					this.loading = true;
				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},

		async loaded() {
			// calls when stepper is shown
			console.log("2 loaded");
			this.fetchAttr();
		},

		async validate() {
			// validate function must validate this step here and return true of false
			this.form.name.$touch();
			return !this.form.name.$invalid;
		},
		AddAtrr(item) {
			if (this.selectedAttr.findIndex((el) => el.id == item.id) > -1) {
				this.selectedAttr = this.selectedAttr.filter((r) => r.id != item.id);
				for (let index = 0; index < this.modelText.length; index++) {
					this.modelText[index] = this.modelText[index].filter((r) => r.id !== item.id);
				}
			} else {
				this.selectedAttr.splice(this.selectedAttr.length - 2, 0, item);

				for (let index = 0; index < this.modelText.length; index++) {
					item = JSON.parse(JSON.stringify(item));
					this.modelText[index].splice(this.modelText[index].length - 2, 0, item);
				}
			}
			if (this.selectedAttr.length == 2) {
				this.defaultValue();
			}
		},

		addRow() {
			if (this.selectedAttr.length > 2) {
				let dataItems = [];
				this.selectedAttr.forEach((element) => {
					const data = this.titleItems.find((r) => r.id == element.id);
					if (data != undefined) {
						dataItems.push(JSON.parse(JSON.stringify(data)));
					}
				});
				const defaultArray = [
					{ id: 10, name: "quantity" },
					{ id: 11, name: "price" },
				];
				dataItems = [...dataItems, ...JSON.parse(JSON.stringify(defaultArray))];
				this.modelText.push(dataItems);
			} else {
				console.log("fasht nako namisha ! ");
			}
		},

		removeRow(items, index) {
			if (this.modelText.length == 1) {
				items.forEach((item) => {
					this.selectedAttr = this.selectedAttr.filter((r) => r.id != item.id);
				});
			}

			this.modelText = this.modelText.filter((r, i) => i != index);
		},

		defaultValue() {
			console.log("defaultValue");
			this.selectedAttr = [
				{ id: 10, name: "quantity" },
				{ id: 11, name: "price" },
			];
			this.modelText = [];
			this.modelText[0] = JSON.parse(JSON.stringify(this.selectedAttr));
		},
	},
};
</script>
<style>
.table thead {
	border-radius: 50%;
}
.POS-CUST {
	position: absolute;
	left: -25px;
	bottom: 17px;
	box-shadow: 0px 0px 25px 0px grey;
}
.table tbody tr:nth-of-type(odd) {
	background-color: inherit !important;
}
.table .v-data-table tbody tr:hover {
	cursor: default !important;
}
.table table tbody tr:not(.v-data-table__selected):not(.not-hover-tr__force):hover {
	background: inherit !important;
}
.table table tbody tr:not(.v-data-table__selected):hover {
	box-shadow: none !important;
}
.v-application--is-rtl .table thead tr :first-child div {
	border-top-right-radius: calc(var(--table-height) / 2);
	border-bottom-right-radius: calc(var(--table-height) / 2);
	border-top-left-radius: 0;
	border-bottom-left-radius: 0;
}
.table thead tr :first-child div {
	border-top-left-radius: calc(var(--table-height) / 2);
	border-bottom-left-radius: calc(var(--table-height) / 2);
}
.table thead tr :last-child div {
	border-top-right-radius: calc(var(--table-height) / 2);
	border-bottom-right-radius: calc(var(--table-height) / 2);
}
.v-application--is-rtl .table thead tr :last-child div {
	border-top-left-radius: calc(var(--table-height) / 2);
	border-bottom-left-radius: calc(var(--table-height) / 2);
	border-top-right-radius: 0;
	border-bottom-right-radius: 0;
}
.table {
	--table-height: 70px;
	border: 0 !important;
	border-collapse: collapse;
}
.table > .v-data-table__wrapper > table > thead > tr:last-child > th {
	border-bottom: none !important;
}
.table table tbody tr:not(.v-data-table__selected):hover .theme--light.v-icon {
	color: inherit !important;
}
</style>
