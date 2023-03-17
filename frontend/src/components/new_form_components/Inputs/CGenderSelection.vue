<template>
	<InputCard pbNone v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			<div class="gender-selection-wrapper">
				<v-radio-group
					@change="valueChange"
					:value="value"
					v-bind="attrs"
					v-on="listeners"
					row
				>
					<v-radio
						v-for="(item, i) in items"
						:key="i"
						class="me-4"
						:label="$tr(item.label)"
						:value="item.value"
					></v-radio>
				</v-radio-group>
			</div>
		</template>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
	props: {
		items: Array,
		value: null,
	},
	data() {
		return {
			gender: "",
		};
	},
	methods: {
		valueChange(value) {
			this.$emit("input", value);
		},
	},
	components: { InputCard },
};
</script>
<style scoped>
.gender-selection-wrapper {
	height: 70px;
}
</style>
