<template>
	<InputCard pbNone v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			<v-autocomplete
				class="form-new-item form-custom-auto-complete"
				background-color="var(--new-input-bg)"
				outlined
				:rounded="rounded"
				:menu-props="{ bottom: true, offsetY: true }"
				v-bind="attrs"
				v-on="listeners"
				:item-text="itemText"
			>
				<template v-slot:[`selection`]="{ item }">
					<div class="v-select__selection--comma">
						{{ itemText ? item[itemText] : item }}{{ multiple ? "," : "" }}
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
							<v-spacer></v-spacer>
							<v-checkbox
								v-model="attrs.inputValue"
								hide-details
								color="primary"
								:class="`${isCheckNeeded?'d-none':''} ms-auto mt-0 pt-0`"
							></v-checkbox>
						</div>
					</v-list-item> </template
			></v-autocomplete>
		</template>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
	props: {
		itemText: String,
		multiple: Boolean,
		isCheckNeeded: Boolean,
		rounded: {
			type: Boolean,
			default: false,
		},
	},
	components: { InputCard },
};
</script>
<style>
.form-custom-auto-complete .v-input__prepend-inner {
	margin-top: 16px;
	margin-right: 8px !important;
}
</style>
