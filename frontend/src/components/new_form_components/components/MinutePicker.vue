<template>
	<div class="hour w-full text-center d-flex flex-column align-center">
		<div>
			<v-btn small text icon color="primary" @click="plus">
				<v-icon>mdi-chevron-up</v-icon>
			</v-btn>
		</div>
		<div class="before-after-numbers cursor-pointer" @click="change(value - 1)">
			{{ value > 0 ? pad(value - 1) : "" }}
		</div>
		<div style="width: 42px">
			<v-text-field
				class="text-center number-input form-new-item"
				background-color="var(--new-input-bg)"
				dense
				type="number"
				:value="pad(value)"
				outlined
				hide-details
				@change="change"
				min="0"
				:max="numbers"
			></v-text-field>
		</div>
		<div class="before-after-numbers cursor-pointer" @click="change(value + 1)">
			{{ value < numbers ? pad(value + 1) : "" }}
		</div>
		<div>
			<v-btn small text icon color="primary" @click="minus">
				<v-icon>mdi-chevron-down</v-icon>
			</v-btn>
		</div>
	</div>
</template>
<script>
export default {
	props: {
		value: {
			type: Number,
			default: 0,
		},
	},
	data() {
		return {
			numbers: 59,
		};
	},
	methods: {
		pad(d) {
			return d < 10 ? "0" + d.toString() : d.toString();
		},
		minus() {
			if (this.value > 0) {
				this.$emit("input", this.value - 1);
			}
		},
		plus() {
			if (this.value < this.numbers) {
				this.$emit("input", this.value + 1);
			}
		},
		change(value) {
			if (value >= 0 && value <= this.numbers && !isNaN(value)) {
				this.$emit("input", parseInt(value));
			} else {
				this.$emit("input", 0);
			}
		},
	},
};
</script>
