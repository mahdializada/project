<template>
	<span :class="
		'flag-icon ' +
		`flag-icon-${flag}` +
		`${round ? ' flag-round' : ''} ${large ? 'large' : ''} ${
			medium ? 'medium' : ''
		} `
	"></span>
</template>

<script>
import "flag-icon-css/sass/flag-icons.scss";

/*
|---------------------------------------------------------------------
| Flag Icon Component
|---------------------------------------------------------------------
|
| Show by country code the respective flag for that country
| https://github.com/lipis/flag-icon-css
|
*/
export default {
	props: {
		// Country ISO
		flag: {
			type: String,
			default: "us",
		},
		// Circle flag or square
		round: {
			type: Boolean,
			default: false,
		},

		large: {
			type: Boolean,
			default: false,
		},
		medium: {
			type: Boolean,
			default: false,
		},
	},
};
</script>

<style lang="scss" scoped>
.flag-icon {
	height: 22px;
	width: 22px;

	&.flag-round {
		background-size: cover;
		border-radius: 100%;
		height: 26px;
		width: 26px;
	}

	&.medium {
		height: 40px;
		width: 40px;
	}

	&.large {
		height: 70px;
		width: 70px;
	}
}
</style>
