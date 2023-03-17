<template>
	<div class="w-full d-flex justify-space-around align-center">
		<div class="next-prev">
			<v-btn
				class="mx-2"
				fab
				small
				text
				color="primary"
				@click="carouselPrev"
				v-if="
					headers.length > 4 ||
					(this.$vuetify.breakpoint.width < 700 && headers.length > 2)
				"
			>
				<v-icon dark>
					{{
						this.$vuetify.rtl
							? "mdi-chevron-double-right"
							: "mdi-chevron-double-left"
					}}
				</v-icon>
			</v-btn>
		</div>
		<div class="w-full">
			<div class="stepper-header-owl owl-carousel owl-theme w-full" ref="owl">
				<div :class="`item w-full `" v-for="(header, i) in headers" :key="i">
					<div
						:class="`stepper-header-item-outer text-center ${
							current == i + 1 ? 'current' : ''
						} ${current > i + 1 ? 'finished' : ''} ${
							i == headers.length - 1 ? 'last' : ''
						}`"
						:key="i + 5 * headers.length"
						@click="changeToIndexValidate(i + 1)"
					>
						<div
							:class="`stepper-header-item-inner rounded-circle d-flex justify-center align-center mx-auto ${
								current >= i + 1 ? 'current' : ''
							} ${
								current == headers.length && i < headers.length - 1
									? 'not-allowed'
									: ''
							}`"
						>
							<v-icon style="font-size: 18px">{{ header.icon }}</v-icon>
						</div>
						<div class="stepper-header-title">{{ $tr(header.title) }}</div>
					</div>
				</div>
			</div>
		</div>
		<div class="next-prev">
			<v-btn
				class="mx-2"
				fab
				text
				small
				color="primary"
				@click="carouselNext"
				v-if="
					headers.length > 4 ||
					(this.$vuetify.breakpoint.width < 700 && headers.length > 2)
				"
			>
				<v-icon dark>
					{{
						this.$vuetify.rtl
							? "mdi-chevron-double-left"
							: "mdi-chevron-double-right"
					}}
				</v-icon>
			</v-btn>
		</div>
	</div>
</template>
<script>
export default {
	created() {
		let length = this.headers.length;
		let rtl = this.$vuetify.rtl;
		setTimeout(function () {
			carousel(length, rtl);
			window.dispatchEvent(new Event("resize"));
		}, 0);
	},
	props: {
		headers: {
			type: Array,
		},
		current: {
			type: Number,
		},
	},
	date() {
		return {};
	},
	methods: {
		changeToIndexValidate(i) {
			this.$emit("changeToIndexValidate", i);
		},
		carouselNext() {
			let owl = $(".stepper-header-owl");
			owl.owlCarousel();
			owl.trigger("next.owl.carousel");
		},
		carouselPrev() {
			let owl = $(".stepper-header-owl");
			owl.owlCarousel();
			owl.trigger("prev.owl.carousel");
		},
		carouselTo(to, speed) {
			let owl = $(".stepper-header-owl");
			owl.owlCarousel();
			owl.trigger("to.owl.carousel", [to, speed]);
		},
	},
};
</script>
<style scoped>
.stepper-header-item-outer {
	position: relative;
	cursor: pointer;
	min-width: 64px;
}
.stepper-header-item-inner {
	height: 42px;
	width: 42px;
	border: 2px solid var(--stepper-header-item-border);
	background: var(--bg-white);
	transition: all 0.3s;
	z-index: 3;
	position: relative;
}
.not-allowed.stepper-header-item-inner {
	cursor: not-allowed;
}
.current.stepper-header-item-outer .stepper-header-item-inner {
	background: var(--v-primary-base) !important;
	border-color: var(--v-primary-base) !important;
	color: var(--color-white);
}
.finished.stepper-header-item-outer .stepper-header-item-inner {
	background: var(--v-primary-base) !important;
	border-color: var(--v-primary-base) !important;
	color: var(--color-white);
}
.stepper-header-item-inner .v-icon {
	color: var(--stepper-header-item-border);
	transition: all 0.3s;
}
.current .stepper-header-item-inner .v-icon,
.finished .stepper-header-item-inner .v-icon {
	color: var(--color-white) !important;
}
.stepper-header-item-outer {
	position: relative;
}
.stepper-header-item-outer::after {
	display: block;
	content: "";
	height: 2px;
	width: 100%;
	background: var(--stepper-header-item-border);
	position: absolute;
	top: 20px;
	left: 50%;
}
.v-application--is-rtl .stepper-header-item-outer::after,
.v-application--is-rtl .stepper-header-item-outer::before {
	left: unset;
	right: 50%;
}
.stepper-header-item-outer::before {
	display: block;
	content: "";
	height: 2px;
	width: 0%;
	background: var(--spacer-active-current);
	position: absolute;
	top: 20px;
	transition: all 0.5s;
	left: 50%;
	z-index: 1;
}
.stepper-header-item-outer.last::after,
.stepper-header-item-outer.last::before {
	display: none;
}
.stepper-header-item-outer.finished::before {
	width: 100%;
}
.stepper-header-title {
	color: var(--input-placeholder-color);
}
.current .stepper-header-title {
	color: var(--v-primary-base);
}
.finished .stepper-header-title {
	color: var(--v-primary-base);
}
.next-prev {
	width: 60px !important;
	transform: translateY(-12px);
}
</style>
