<template>
	<InputCard
		px="px-0"
		py="py-0"
		title=""
		:borderStyle="
			!checkBox
				? '1px solid var(--v-primary-lighten3) !important'
				: '1px solid var(--v-surface-darken1) !important'
		"
		:boxShadow="!checkBox ? '0px 0px 35px -10px grey' : 'none'"
	>
		<div class="px-1" :class="addedItem.length > 0 ? 'pt-2' : 'py-2'">
			<div @click="clickedItem()">
				<div class="d-flex justify-end">
					<v-icon v-if="checkBox" color="grey"
						>mdi-checkbox-blank-circle-outline</v-icon
					>
					<v-icon v-else color="primary">mdi-check-circle</v-icon>
				</div>

				<div class="d-flex justify-center mt-n2">
					<v-avatar color="blue lighten-5" size="75">
						<v-icon color="primary" size="45">fa fa-flag-checkered</v-icon>
					</v-avatar>
				</div>

				<div class="d-flex justify-center mt-2 mb-1 titleClass flex-nowrap">
					{{ $tr(title) }}
				</div>
			</div>

			<div class="d-flex">
				<v-tabs class="tabsForCard" v-if="addedItem.length > 0" v-model="tab">
					<v-tab
						@click="tabItemClick(index, item)"
						v-for="(item, index) in addedItem"
						:key="index"
						>{{ item.quantityAmount }}</v-tab
					>
				</v-tabs>
				<v-icon
					@click="clearItem()"
					v-if="!checkBox"
					class="ms-1"
					:style="addedItem.length > 0 ? 'padding:0 5px' : ''"
					color="primary"
					>mdi-plus-circle-outline</v-icon
				>
			</div>
		</div>

		<v-divider v-if="!checkBox"></v-divider>

		<v-expand-transition>
			<div v-if="!checkBox" class="mb-1">
				<CTextField
					v-model="quantity"
					hide-details
					dense
					px="px-2"
					py="py-0"
					type="number"
					:min="0"
					title="Quantity"
					placeholder="Quantity"
				/>
				<CSelect
					v-if="title == 'Gift style'"
					v-model="quantityAmount"
					title="Gift"
					placeholder="Gift"
					hide-details
					dense
					px="px-2"
					py="py-0"
					:items="selectItems"
				/>
				<CTextField
					v-else
					v-model="quantityAmount"
					hide-details
					dense
					px="px-2"
					py="py-0"
					:title="title == 'Free quantity style' ? 'Num. of Free' : 'Discount'"
					:placeholder="
						title == 'Free quantity style' ? 'Num. of Free' : 'Discount'
					"
				/>
				<div class="d-flex justify-center px-2">
					<v-btn
						outlined
						v-if="!removeItem"
						@click="addItem"
						class="mt-1 rounded-pill"
						color="primary"
						right
						>add</v-btn
					>
					<v-btn
						outlined
						v-if="removeItem"
						@click="removeItemClick"
						class="mt-1 rounded-pill"
						color="error"
						right
						>remove</v-btn
					>
				</div>
			</div>
		</v-expand-transition>
	</InputCard>
</template>
<script>
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CSelect from "../../new_form_components/Inputs/CSelect.vue";

export default {
	components: {
		InputCard,
		CTextField,
		CSelect,
	},
	props: {
		title: String,
		form: Object,
	},
	data() {
		return {
			removeItem: true,
			checkBox: true,
			addedItem: [],
			indexForSelected: null,
			tab: null,
			selectItems: ["Foo", "Bar", "Fizz", "Buzz"],
			quantityAmount: "",
			quantity: "",
		};
	},
	watch: {
		addedItem(val) {
			this.tab = val.length - 1;
			if (val.length == 0) {
				this.clearItem();
			}
			if (val.length > 0) {
				this.quantityAmount = val[this.tab].quantityAmount;
				this.quantity = val[this.tab].quantity;
			}
		},
	},
	methods: {
		clickedItem() {
			this.checkBox = !this.checkBox;
		},
		addItem() {
			if (this.quantity.length > 0 && this.quantityAmount.length > 0) {
				this.addedItem.push({
					quantity: this.quantity,
					quantityAmount: this.quantityAmount,
				});
				this.removeItem = true;

				this.$emit("addPricePattern", this.addedItem);
			}
		},
		clearItem() {
			this.removeItem = false;
			this.quantityAmount = "";
			this.quantity = "";
		},
		tabItemClick(index, item) {
			this.removeItem = true;
			this.indexForSelected = index;
			this.quantityAmount = item.quantityAmount;
			this.quantity = item.quantity;
		},
		removeItemClick() {
			this.addedItem.splice(this.indexForSelected, 1);
			this.$emit("addPricePattern", this.addedItem);
		},
	},
	created() {
		if (this.addedItem.length > 0) {
			this.quantityAmount = this.addedItem[0].quantityAmount;
			this.quantity = this.addedItem[0].quantity;
		} else {
			this.removeItem = false;
		}
	},
};
</script>
<style scoped>
.titleClass {
	font-size: 18px;
	font-weight: 500;
	color: #777;
}
</style>
<style>
.tabsForCard .v-slide-group__next,
.v-slide-group__prev {
	align-items: center;
	display: flex;
	flex: 0 1 17px;
	justify-content: center;
	min-width: 17px;
}
.tabsForCard .v-tab {
	min-width: 20px;
}
.tabsForCard .v-tabs-bar {
	height: 35px;
}
.tabsForCard.v-tabs {
	flex: 0 0;
	width: 85%;
}
</style>
