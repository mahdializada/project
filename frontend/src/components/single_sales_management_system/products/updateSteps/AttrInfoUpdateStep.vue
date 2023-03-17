<template>
	<div class="w-full mt-5">
		<CAttributeSelection
			:title="'Attributes'"
			:sub_title="'Please Select what attributes does your product have.'"
			@AddAtrr="AddAtrr"
			ref="CAttributeSelectionRefs"
			:items='[		{ id: 1, name: "Color" },
				{ id: 2, name: "RAM" },{ id: 4, name: "Size" },
				{ id: 5, name: "Xl" },
				{ id: 6, name: "Lg" },
				{ id: 7, name: "Sm" },]'

			:selectedAttr='selectedAttr'
		/>

		<v-expand-transition>
			<div v-if="selectedAttr.length > 0" class="mt-3">
				<InputCard :title="''">
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
												@click="itemRemove(item)"
												>mdi-close</v-icon
											>
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr class="pb-1" v-for="(item, i) in modelText" :key="i">
									<td v-for="(it, ind) in item" :key="ind" class="px-1 w_custom">
										<CTextField
											:placeholder="it.name"
											hide-details
											:type="it.name == 'quantity' || it.name == 'price' ? 'number' : 'text'"
											class="pt-0 mt-1"
											style="min-width: 170px !important"
											px="px-0"
											dense
											:title="''"
										/>
									</td>
									<v-btn icon depressed class="blue lighten-5" small>
										<v-icon color="primary" @click="removeRow(item,i)" size="20">mdi-close</v-icon>
									</v-btn>
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
import CAttributeSelection from "../../../new_form_components/Inputs/CAttributeSelection.vue";
import InputCard from "../../../new_form_components/components/InputCard.vue";
import CTextField from "../../../new_form_components/Inputs/CTextField.vue";
 

export default {
	props: {
		form: Object, // default prop
	},
	 
	data() {
		return {
			validateRule: GlobalRules.validate, // gloabl function fro validate
			attrItems: [],
			selectedAttr:[],
			modelText: [],
			defaultArray: [
				{ id: 10, name: "quantity" },
				{ id: 11, name: "price" },
			],
		};
	},
	watch: {
		selectedAttr: function (val) {
			if (val.length == 2) {
				this.selectedAttr = [];
				this.modelText = [];
			}
		},
	 
	},
	methods: {
		async loaded() {
			// calls when stepper is shown
			this.getCategoryAttributes();
			this.AddAtrr( [{ id: 1, name: "Color" }])
		 
		},
		async validate() {
			console.log('ss',this.editData);
			// validate function must validate this step here and return true of false
			this.form.name.$touch();
			return !this.form.name.$invalid;
		},
		AddAtrr(AttrArray) {
			this.selectedAttr = [...AttrArray, ...this.defaultArray];
			this.modelText[0] = [...AttrArray, ...this.defaultArray];

			for (let index = 0; index < this.modelText.length; index++) {
				this.modelText[index] = this.selectedAttr;
			}
		},
		itemRemove(item) {
			this.selectedAttr = this.selectedAttr.filter((r) => r.id != item.id);
			for (
				let index = 0;
				index < this.$refs.CAttributeSelectionRefs.$children[0].$children.length;
				index++
			) {
				const element = this.$refs.CAttributeSelectionRefs.$children[0].$children[index];
				if (element.titleItem == item.name) {
					element.Atclick();
					break;
				}
			}
			for (let index = 0; index < this.modelText.length; index++) {
				this.modelText[index] = this.selectedAttr;
			}
		},
		addRow() {
			const firstRow = this.modelText[0];
			this.modelText.push(firstRow);
		},
		removeRow(item,index){
			console.log(index);
			console.log(item);
			this.modelText = this.modelText.filter((r,i)=> i !=index);
		},

		async getCategoryAttributes(){
        console.log('category',this.form.category_id.$model);

		const data = await this.$axios.post('bnsadfdf', {category_id:this.form.category_id.$model});
				 
				console.log('response',data.data);
		// 		if (data.data.result) {
		}
	},
 
	components: { CAttributeSelection, InputCard, CTextField },
};
</script>
<style>
.table thead {
	border-radius: 50%;
}
.POS-CUST {
	position: absolute;
	left: -30px;
	bottom: 17px;
	box-shadow: 0px 0px 45px 0px grey;
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
