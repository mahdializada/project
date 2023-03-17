<template>
	<InputCard pbNone v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			<v-text-field
				class="form-new-item form-custom-number"
				background-color="var(--new-input-bg)"
				outlined
				rounded
				v-bind="attrs"
				v-on="listeners"
				type="number"
			>
				<template slot="append" class="pe-0">
					<v-btn
						class="number-btn ma-0"
						fab
						small
						color="#2e7be60f"
						@click="$emit('input', parseFloat(attrs.value) + 1)"
					>
						<v-icon dark size="20"> mdi-plus </v-icon>
					</v-btn>
				</template>
				<template slot="prepend-inner" class="pe-0">
					<v-btn
						class="number-btn ma-0"
						fab
						small
						color="#2e7be61c"
						@click="$emit('input', parseFloat(attrs.value) - 1)"
					>
						<v-icon dark size="20"> mdi-minus </v-icon>
					</v-btn>
				</template>
			</v-text-field>
		</template>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
export default {
	components: { InputCard },
};
</script>

<style>
.form-custom-number .v-input__slot {
	padding: 0 8px !important;
}
.form-custom-number .v-input__append-inner,
.form-custom-number .v-input__prepend-inner {
	margin-top: 8px;
	margin-right: 8px !important;
}
.form-custom-number input {
	text-align: center;
}
.number-btn {
	color: var(--v-primary-base) !important;
}
.form-custom-number input[type="number"]::-webkit-inner-spin-button,
.form-custom-number input[type="number"]::-webkit-outer-spin-button {
	-webkit-appearance: none;
	margin: 0;
}
</style>
