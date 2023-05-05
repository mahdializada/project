<template>
	<div>
		<Toaster
			@close="close"
			v-for="(item, i) in arrayOfTosater"
			:key="i"
			:item="item"

			class="haidar">
		</Toaster>
	</div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
import Toaster from "./Toaster.vue";

export default {
	components: { Toaster },
	computed: {
		...mapState("nas_toaster", ["arrayOfTosater"]),
	},
	methods: {
		...mapMutations("nas_toaster", ["arrayOfTosaterSet"]),

		close(item) {
			const data = this.arrayOfTosater.filter((row) => row.id != item.id);
			this.arrayOfTosaterSet(data);
		},
	},
};
</script>

<style>
.nasimToaster .v-alert__border {
	border-style: solid;
	border-width: unset;
	content: "";
	position: absolute;
}
.haidar {
	transition: all 3s ease;
}
</style>
