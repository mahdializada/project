<template>
	<div>
		<v-card
			class="w-full d-flex position-relative align-center"
			elevation="1"
			outlined
			:class="show_ad_img ? `ad_card_header` : 'card_Page_header'"
		>
			<v-avatar
				size="65"
				color="blue lighten-5"
				class="d-flex justify-center align-center ms-2"
			>
				<span
					v-if="!flag"
					v-html="Icons[Icon]"
					style="width: 25px"
					class="d-flex align-center"
				></span>
				<FlagIcon v-if="flag" :flag="Icon" :round="true" />
			</v-avatar>
			<div class="px-2 py-3">
				<h2 class="title d-flex align-center">
					{{ $tr(Title) }}
					<slot name="title-button"></slot>
				</h2>
				<v-breadcrumbs :items="Breadcrumb" class="ps-0 pb-0 pt-1" divider="»">
					<template v-slot:item="{ item }">
						<v-breadcrumbs-item
							:to="item.to"
							class="breadcrumb-item text--primary d-flex align-center"
							nuxt
							:exact="item.exact"
							:disabled="item.disabled"
						>
							<a class="text--primary d-flex">
								<span
									class="me-1 d-flex align-center"
									v-if="item.icon"
									v-html="item.icon"
									style="width: 15px !important"
								></span>
								{{ $tr(item.text) }}
							</a>
						</v-breadcrumbs-item>
					</template>
				</v-breadcrumbs>
			</div>
			<v-spacer />
			<slot name="button"></slot>
			<v-spacer />
		</v-card>
		<v-card class="w-full mb-2" elevation="1" outlined>
			<slot name="after"></slot>
		</v-card>
		<slot name="content"></slot>
	</div>
</template>
<script>
import FlagIcon from "~/components/common/FlagIcon.vue";
import Icons from "~/configs/menus/menuIcons";

export default {
	components: {
		FlagIcon,
	},
	props: {
		Icon: {
			type: String,
			required: true,
		},
		Title: {
			type: String,
		},
		Breadcrumb: {
			type: Array,
		},
		flag: {
			type: Boolean,
			default: false,
		},
		show_ad_img: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			Icons,
		};
	},
};
</script>
<style scoped>
.rotated-image {
	transform: rotate(180deg);
	-ms-transform: rotate(180deg);
	-moz-transform: rotate(180deg);
	-webkit-transform: rotate(180deg);
	-o-transform: rotate(180deg);
}
.title {
	font-size: 1.7rem !important;
}

.theme--light .breadcrumb-item a,
.theme--light .v-breadcrumbs__divider {
	color: #444 !important;
}

.theme--dark .breadcrumb-item a,
.theme--dark .v-breadcrumbs__divider {
	color: #ccc !important;
}
</style>

<style>
.card_Page_header {
	background-image: url(~/static/images/corner.png);
	background-size: contain;
	background-repeat: no-repeat;
	background-position: 100%;
}

.ad_card_header {
	background-image: url(~/static/images/corner.png),
		url(~/static/images/ads/adPageHeader.png);
	background-size: contain, contain;
	background-repeat: no-repeat, no-repeat;
	background-position: 100%, 80%;
}
</style>
