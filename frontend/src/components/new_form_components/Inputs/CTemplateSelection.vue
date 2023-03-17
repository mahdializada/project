<template>
	<div class="d-flex template-container Scroll flex-wrap overflow-auto mt-3">
		<div
			v-for="(item, i) in items"
			:key="i"
			:class="`single-template cursor-pointer ${
				multi
					? value
						? value.includes(item.id)
						: false
					: value == item.id
					? 'selected'
					: ''
			}`"
			@click="select(item)"
		>
			<div class="single-template-img-wrapper">
				<div
					class="single-template-img"
					:style="`
						background-image: url('${item.image}');
					`"
				></div>
				<div class="selected-mark d-flex align-center justify-center">
					<v-icon dark size="14">mdi-check-bold</v-icon>
				</div>
			</div>
			<div class="single-template-text text-center py-1">
				{{ item.name }}
			</div>
		</div>
	</div>
</template>
<script>
export default {
	props: {
		items: Array,
		multi: Boolean,
		loading: Boolean,
		no_data: Boolean,
		value: null,
	},
	methods: {
		select(item) {
			if (this.multi) {
				if (this.value ? this.value.includes(item.id) : false) {
					this.$emit(
						"input",
						this.value.filter((id) => id != item.id)
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
<style scoped>
.template-container {
	height: 340px;
	margin-left: -16px;
}
.single-template {
	width: 206px;
	height: 170px;
	padding: 0 16px;
}
.single-template-img-wrapper {
	padding: 6px;
	border-radius: 14px;
	border: 1px solid var(--gray);
	margin-bottom: 4px;
	position: relative;
	transition: all 0.1s;
}
.single-template:hover .single-template-img-wrapper {
	box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
}
.single-template-img {
	border-radius: 8px;
	height: 100px;
	width: 100%;
	background-color: red;
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
}
.single-template-text {
	transition: all 0.4s;
}
.selected .single-template-img-wrapper {
	border: 1px solid #2e7be680;
	box-shadow: 0 0 10px #2e7be633;
}
.selected .single-template-text {
	color: var(--v-primary-base);
}
.selected-mark {
	height: 24px;
	width: 24px;
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
