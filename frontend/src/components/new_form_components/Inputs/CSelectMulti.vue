<template>
	<InputCard InputCard pbNone v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			<v-select
				:class="`form-new-item form-custom-select ${
					hasItems ? 'has-append' : ''
				}`"
				background-color="var(--new-input-bg)"
				outlined
				:rounded="rounded"
				:menu-props="{ bottom: true, offsetY: true }"
				v-bind="attrs"
				v-on="listeners"
				:multiple="multi"
			>
				<template v-slot:[`selection`]="{ item }">
					<div class="v-select__selection--comma">
						{{ itemText ? item[itemText] : item }},
					</div>
				</template>
				<template v-slot:[`item`]="{ item, on, attrs }">
					<v-list-item
						class="select-list-item-custom pe-1"
						v-on="on"
						v-bind="attrs"
					>
						<div
							:class="`select-item-custom w-full d-flex justify-space-around align-center`"
							style="font-weight: 500"
						>
							{{ itemText ? item[itemText] : item }}
							<v-checkbox
								v-model="attrs.inputValue"
								hide-details
								color="primary"
								class="ms-auto mt-0 pt-0"
							></v-checkbox>
						</div>
					</v-list-item>
				</template>
				<template slot="append" class="pe-0" v-if="hasItems">
					<v-btn fab small color="primary" class="ma-0" @click.stop="add">
						<v-icon dark size="20"> mdi-plus </v-icon>
					</v-btn>
				</template>
				<template v-slot:append-item v-if="hasAddNew">
					<v-list-item>
						<v-btn color="primary" class="ma-0" @click.stop="add">
							<v-icon dark size="20"> mdi-plus </v-icon>
							{{ addNewTitle }}
						</v-btn>
					</v-list-item>
				</template>
			</v-select>
			<div class="d-flex flex-wrap" v-if="hasItems">
				<div v-for="(item, i) in selectedItems" :key="i" class="me-2">
					<InputItem
						:label="item[label]"
						:icon="attrs['prepend-inner-icon']"
						@removeItem="removeItem(item)"
					/>
				</div>
			</div>
		</template>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
import InputItem from "../components/InputItem.vue";
export default {
	props: {
		hasItems: Boolean,
		multi: Boolean,
		hasAddNew: Boolean,
		selectedItems: Array,
		addNewTitle: "",
		itemText: String,
		rounded:{
			type:Boolean,
			default:true
		},
		label:{
			type:String,
			default:'name'
		}
	},
	data() {
		return {};
	},
	methods: {
		add() {
			this.$emit("add");
		},
		removeItem(item) {
			this.$emit("removeItem", item);
		},
	},
	components: {
		InputCard,
		InputItem,
	},
};
</script>
<style>
.form-custom-select .v-input__prepend-inner {
	margin-top: 16px;
	margin-right: 8px !important;
}
.form-custom-select.has-append .v-input__slot {
	padding-right: 8px !important;
}
.v-application--is-rtl .form-custom-select.has-append .v-input__slot {
	padding-right: 24px !important;
	padding-left: 8px;
}
.form-custom-select.has-append .v-input__append-inner {
	margin-top: 8px !important;
}
.form-custom-select .v-select__selections {
	flex-wrap: nowrap;
}
</style>
