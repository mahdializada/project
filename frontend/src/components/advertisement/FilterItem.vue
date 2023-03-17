<template>
	<div v-if="type == 'country'">
		<FilterInput
			v-model="vmodel"
			type="autocomplete"
			:items="items"
			label=""
			:placeholder="$tr(placeholder)"
			@change="onItemChanged"
			item-text="name"
			:hasFlag="hasFlag"
			:hasIcon="hasIcon"
		/>
		<SelectCard :items="selectedItems" @removeByItem="onRemoveItem" />
	</div>

	<div v-else-if="type == 'autocomplete'">
		<FilterInput
			v-model="vmodel"
			type="autocomplete"
			:items="items"
			label=""
			:placeholder="$tr(placeholder)"
			@change="onItemChanged"
			item-text="name"
			has-avatar
		/>
		<SelectCard :items="selectedItems" @removeByItem="onRemoveItem" />
	</div>
	<div v-else-if="type == 'autocomplete_single'">
		<FilterInput
			v-model="vmodel"
			type="autocomplete"
			:items="items"
			label=""
			:placeholder="$tr(placeholder)"
			@change="onSingleItemChanged"
			item-text="name"
			has-avatar
		/>
	</div>
	<div v-else-if="type == 'number_condition'">
		<FilterInput
			v-model="form.condition"
			type="autocomplete"
			:items="numeric_conditions"
			label=""
			:placeholder="$tr('condition')"
			item-text="name"
			:clearable="false"
			has-avatar
		/>
		<div class="d-flex">
			<FilterInput
				v-model="form.min"
				type="number"
				label=""
				:placeholder="$tr('value')"
				class="w-full"
				:class="{
					'me-1': form.condition == 'IS_BETWEEN',
				}"
			/>
			<FilterInput
				v-if="form.condition == 'IS_BETWEEN'"
				v-model="form.max"
				type="number"
				label=""
				:placeholder="$tr('value')"
				class="w-full ms-1"
			/>
		</div>
	</div>
</template>

<script>
import FilterInput from "~/components/design/components/FilterInput.vue";
import SelectCard from "../new_form_components/Inputs/SelectCard.vue";
import GlobalRules from "~/helpers/vuelidate";

export default {
	components: {
		FilterInput,
		SelectCard,
	},
	props: {
		type: { type: String, default: "autocomplete" },
		selectedItems: { type: Array, default: () => [] },
		form: { type: Object, default: () => ({}) },
		items: { type: Array, default: () => [] },
		placeholder: { type: String, default: "placeholder" },
		hasFlag: {
			type: Boolean,
			default: false,
		},
		hasIcon: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			validate: GlobalRules.validate,
			vmodel: "",
			numeric_conditions: [
				{ id: "IS_EQUAL_TO", name: this.$tr("is_equal_to") },
				{ id: "IS_GREATER_THAN", name: this.$tr("is_greater_than") },
				{ id: "IS_LESS_THAN", name: this.$tr("is_less_than") },
				{ id: "IS_BETWEEN", name: this.$tr("is_between") },
			],
		};
	},

	methods: {
		onItemChanged(itemId) {
			if (itemId) {
				const find = (item) => item.id == itemId;
				const index = this.items.findIndex(find);
				const selected = this.items[index];
				this.$emit("update:selectedItems", this.selectedItems.concat(selected));
				this.items.splice(index, 1);
			}
		},
		onSingleItemChanged(itemId) {
			this.$emit("update:value", itemId);
		},
		onRemoveItem(selectedItem) {
			if (selectedItem) {
				const find = (item) => item.id == selectedItem.id;
				const index = this.selectedItems.findIndex(find);
				this.items.push(selectedItem);
				this.selectedItems.splice(index, 1);
			}
		},
	},
};
</script>
