<template>
	<div class="d-flex justify-center align-center">
		<div :class="`percentage__${type} percentage__btn__size me-1 ${position ? '' :'position-relative'}`">
			<div :class="`percentage__container percentage__${type}`">
				<div :class="`${textColor}--text`">{{ percentage }}%</div>
			</div>
			<lottie-player
				src="/assets/refresh_percentage.json"
				background="transparent"
				speed="1"
				style="width: 40px"
				loop
				autoplay
			>
			</lottie-player>
		</div>
	</div>
</template>

<script>
export default {
	props: ["percentage"],
	props: {
		percentage: Number,
		type: {
			type: String,
			default: "primary",
		},

		textColor: {
			type: String,
			default: "white",
		},
		position:Boolean,
	},
};
</script>

<style scoped>
.percentage__primary {
	background: #00bc81 !important;
	border-color: #00bc81 !important;
	color: #fff !important;
}
.percentage__label {
	background: #2dccff !important;
	border-color: #2dccff !important;
	color: #fff !important;
}

.percentage__warning {
	background: rgb(255 214 51);
	border-color: rgb(255 214 51);
	color: #fff !important;
}
.percentage__btn__size {
	cursor: not-allowed;
	height: 38px;
	width: 90px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 4px;
	padding: 0 16px;
}

.percentage__container {
	position: absolute;
	bottom: 60px;
	height: 35px;
	width: 35px;
	transform: translateY(-20%) translateY(-12px) translateX(0%) rotate(45deg);
	border-radius: 50% 50% 0;
	display: flex;
	justify-items: center;
	justify-content: center;
}

.percentage__container > div {
	transform: rotate(-45deg);
	text-align: center;
	display: flex;
	align-items: center;
	justify-content: center;
	letter-spacing: normal;
	line-height: 1;
	position: relative;
	text-indent: 0;
	transition: 0.3s cubic-bezier(0.25, 0.8, 0.5, 1), visibility 0s;
	vertical-align: middle;
}
</style>
