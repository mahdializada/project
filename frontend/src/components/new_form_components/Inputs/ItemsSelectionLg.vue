<template>
	<div
		class="selection-items-container d-flex flex-wrap align-center justify-center"
	>
		<div
			v-for="(item, i) in items"
			:key="i"
			:class="`selection-item cursor-pointer px-2 pb-2 ${
				multi ? value.includes(item.id) : value == item.id ? 'selected' : ''
			}`"
			@click="select(item)"
		>
			<div class="selection-item-icon d-flex align-center justify-center">
				<div class="selection-item-icon-bg" v-html="item.icon"></div>
			</div>
			<div :class="`selection-item-text text-center ${paddingBottom}`">
				{{ $tr(item.title) }}
			</div>
			<div class="selected-mark d-flex align-center justify-center">
				<v-icon dark size="14">mdi-check-bold</v-icon>
			</div>
		</div>
	</div>
</template>
<script>
export default {
	props: {
		value: null,
		items: Array,
		multi: Boolean,
		paddingBottom: String,
	},
	methods: {
		select(item) {
			if (this.multi) {
				if (this.value.includes(item.id)) {
					this.$emit(
						"input",
						this.value.filter((id) => item.id !== id)
					);
				} else {
					this.$emit("input", [...this.value, item.id]);
				}
			} else {
				if (this.value == item.id) {
					this.$emit("input", "");
				} else {
					this.$emit("input", item.id);
				}
			}
		},
	},
};
</script>
<style>
.selection-item-icon-bg svg {
	width: 52px !important;
	display: block;
	margin: 0 auto;
}
</style>
<style scoped>
.selection-item {
	position: relative;
	width: 220px;
	border-radius: 12px;
	margin: 8px;
	background: var(--v-background-base);
	border: 1px solid transparent;
	transition: all 0.1s;
}
.selection-item:hover {
	box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
}
.selection-item.selected {
	border: 1px solid var(--v-primary-base);
	box-shadow: 0 0 10px #2e7be633;
}
.selection-item-icon {
	height: 110px;
}
.selection-item-icon-bg {
	height: 70px;
	width: 70px;
	border-radius: 50%;
	background: #2e7be617 !important;
}
.selection-item-text {
	color: #777;
	font-size: 14px !important;
	font-weight: 500;
}
.selection-item.selected .selection-item-text {
	color: var(--v-primary-base);
}
.selected-mark {
	height: 18px;
	width: 18px;
	border-radius: 50%;
	background: var(--v-primary-base);
	position: absolute;
	bottom: 0;
	left: 50%;
	transform: translate(-50%, 50%) scale(0);
	transition: all 0.1s;
}
.selected .selected-mark {
	transform: translate(-50%, 50%) scale(1);
}
</style>
