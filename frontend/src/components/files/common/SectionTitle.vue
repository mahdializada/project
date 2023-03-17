<template>
	<div class="section-title pa-2 bg-light-gray">
		<h3 class="text-uppercase ma-0">{{ $tr(title) }}</h3>
	</div>
</template>
<script>
export default {
	props: {
		title: {
			type: String,
		},
	},
};
</script>
<style>
.section-title {
	position: sticky;
	top: -18px;
	z-index: 1;
}
.section-title h3 {
	font-size: 16px !important;
	font-weight: 500 !important;
	white-space: nowrap;
	display: flex;
}
.section-title h3:after {
	content: "";
	display: block;
	height: 1px;
	width: 100%;
	background: #aaa;
	margin-top: 18px;
	margin-left: 10px;
}
.v-application--is-rtl .section-title h3:after {
	margin-left: unset;
	margin-right: 10px;
}
</style>
