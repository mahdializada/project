<template>
	<div
		:class="`loading-page d-flex align-center justify-center tabBackground ${
			loading || loadingProps ? 'show' : 'hide'
		}`"
	>
		<div class="svg">
			<lottie-player
				src="/assets/pageLoader.json"
				background="transparent"
				speed="1"
				style="width: 400px; height: 800px"
				loop
				autoplay
			>
			</lottie-player>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		loadingProps: {
			type: Boolean,
			default: false,
		},
	},
	data: () => ({
		loading: false,
	}),
	methods: {
		start() {
			this.loading = true;
		},
		finish() {
			this.loading = false;
		},
	},
};
</script>

<style scoped>
.loading-page {
	position: fixed;
	z-index: 1000000000000000;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0;
	width: 100%;
	height: 100%;
	background: #f2f5f8;
	text-align: center;
	display: flex;
	align-items: center;
	justify-content: center;
}
.dark.loading-page {
	background: #111b27 !important;
}
.svg {
	stroke: var(--v-primary-base);
}
.svg2 {
	stroke: #f8a036;
}
.show {
	opacity: 1;
	transition: all 0.5s;
}
.hide {
	opacity: 0;
	z-index: -100000000;
	transition: all 0.5s, z-index 0.6s;
}
</style>
