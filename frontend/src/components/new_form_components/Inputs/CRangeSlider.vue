<template>
	<InputCard pbNone v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			<div class="form-custom-range-slider-wrapper d-flex">
				<v-range-slider
					v-bind="attrs"
					v-on="listeners"
					class="align-center form-custom-range-slider"
				>
					<template v-slot:prepend>
						<div class="slider-value-wrapper">
							<div class="slider-value me-auto">{{ attrs.value[0] }}</div>
						</div>
					</template>
					<template v-slot:append>
						<div class="slider-value-wrapper">
							<div class="slider-value ms-auto">{{ attrs.value[1] }}</div>
						</div>
					</template>
					<template v-slot:thumb-label="props">
						<span style="color:white">{{ props.value }}</span>
					</template>
				</v-range-slider>
			</div>
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
.form-custom-range-slider-wrapper {
	height: 88px;
}
.form-custom-range-slider .slider-value-wrapper {
	width: 40px;
}
.form-custom-range-slider .slider-value {
	height: 26px;
	width: 26px;
	font-size: 12px;
	border-radius: 6px;
	background-color: var(--new-input-bg);
	display: flex;
	align-items: center;
	justify-content: center;
}
.form-custom-range-slider .v-slider__track-container {
	height: 6px;
	border-radius: 3px;
}
.form-custom-range-slider .v-slider__track-background.primary.lighten-3 {
	background-color: var(--new-input-bg) !important;
}
.form-custom-range-slider .v-slider.v-slider--horizontal {
	margin-top: 22px;
}
.form-custom-range-slider .v-slider__thumb-container .v-slider__thumb {
	height: 16px;
	width: 16px;
}
.form-custom-range-slider
	.v-slider__thumb-container.v-slider__thumb-container--active
	.v-slider__thumb,
.form-custom-range-slider
	.v-slider__thumb-container.v-slider__thumb-container--focused
	.v-slider__thumb {
	height: 22px;
	width: 22px;
	border: 4px solid var(--v-background-base) !important;
	box-shadow: 0 0 3px var(--v-secondary-darken4);
}
.form-custom-range-slider .v-slider__thumb::after,
.form-custom-range-slider .v-slider__thumb::before {
	display: none;
}
.form-custom-range-slider .v-slider__thumb-label {
	transform: translateY(-20%) translateY(-12px) translateX(-40%) rotate(0) !important;
	background: var(--v-background-base) !important;
	color: #777 !important;
	border: 1px solid var(--gray) !important;
	border-radius: 6px !important;
	height: 22px !important;
	width: auto !important;
	padding: 0 6px !important;
	display: flex !important;
	font-size: 10px !important;
	align-items: center;
	justify-content: center;
}
.form-custom-range-slider .v-slider__thumb-label div {
	transform: rotate(0) !important;
}
</style>
