<template>
	<span class="position-relative">
		<span
			class="tooltip-item"
			@mouseover="show = true"
			@mouseleave="show = false"
		>
			<v-btn text icon>
				<v-icon class="primary--text" size="18">
					mdi-information-outline
				</v-icon>
			</v-btn>
		</span>
		<transition :name="'fade'">
			<div class="tooltip-content text-center d-flex" v-if="show">
				<div class="tooltip-content-inner">
					{{ text }}
				</div>
			</div>
		</transition>
	</span>
</template>
<script>
export default {
	props: {
		text: String,
	},
	data() {
		return {
			show: false,
		};
	},
};
</script>
<style scoped>
.tooltip-content {
	width: 300px;
	position: absolute;
	bottom: 170%;
	left: 50%;
	transform: translateX(-20%);
	z-index: 100000000;
	display: none;
}
.tooltip-content-inner {
	position: relative;
	background: var(--v-primary-base);
	padding: 12px 20px;
	color: #fff;
	border-radius: 8px;
	font-size: 14px !important;
}
.tooltip-content-inner::after {
	display: block;
	content: "";
	position: absolute;
	left: 54px;
	bottom: -18px;
	border-left: 6px solid transparent;
	border-right: 6px solid transparent;
	border-top: 12px solid var(--v-primary-base);
	height: 20px;
	width: 12px;
	transition: all 0.5s;
}
</style>
