<template>
	<div class="input-item-single mb-2 pa-2 d-flex position-relative">
		<div class="item-icon-outer">
			<div class="item-icon-inner">
				<v-icon color="primary" size="18">{{ icon }}</v-icon>
			</div>
		</div>
		<div class="w-full ps-5 pe-4">
			{{ label }}
		</div>
		<v-btn
			fab
			x-small
			text
			color="primary"
			class="ma-0 item-close-btn"
			@click="$emit('removeItem')"
		>
			<v-icon dark size="20"> mdi-close </v-icon>
		</v-btn>
	</div>
</template>
<script>
export default {
	props: {
		label: String,
		icon: String,
	},
};
</script>
<style scoped>
.input-item-single {
	border: 1px solid var(--gray);
	border-radius: 10px;
}
.item-icon-outer {
	position: absolute;
	top: 12px;
	left: 12px;
}
.v-application--is-rtl .item-icon-outer {
	left: unset;
	right: 12px;
}
.item-icon-inner {
	display: flex;
	height: 28px;
	width: 28px;
	border-radius: 50%;
	background: #2e7be60f !important;
	align-items: center;
	justify-content: center;
}
.item-close-btn {
	position: absolute;
	top: 12px;
	right: 10px;
}
.v-application--is-rtl .item-close-btn {
	right: unset;
	left: 10px;
}
</style>
