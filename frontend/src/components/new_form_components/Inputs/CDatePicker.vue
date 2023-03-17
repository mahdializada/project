<template>
	<InputCard v-bind="$attrs" v-on="$listeners" pbNone>
		<v-menu
			ref="menu"
			v-model="dateMenu"
			:close-on-content-click="false"
			transition="scale-transition"
			offset-y
			min-width="auto"
		>
			<template v-slot:activator="{ on, attrs }">
				<v-text-field
					:value="value"
					v-bind="attrs"
					v-on="on"
					:placeholder="placeholder"
					readonly
					background-color="var(--new-input-bg)"
					outlined
					rounded
					:class="`form-new-item`"
					:prepend-inner-icon="icon"
					:rules="rules"
				></v-text-field>
			</template>
			<v-date-picker no-title @input="input" :min="minDate"> </v-date-picker>
		</v-menu>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
	props: {
		value: String,
		icon: String,
		rules: Array,
		placeholder: String,
		minDate: {
			type: String,
			default: null,
		},
	},
	data() {
		return {
			dateMenu: false,
		};
	},
	methods: {
		input(value) {
			this.dateMenu = false;
			this.$emit("input", value);
		},
	},
	components: { InputCard },
};
</script>
