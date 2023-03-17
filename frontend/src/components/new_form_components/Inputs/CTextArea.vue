<template>
	<InputCard pbNone v-bind="$attrs" v-on="$listeners" class="px-4 py-2">
		<template slot-scope="{ attrs, listeners }">
			<v-textarea
				:class="`form-new-item form-custom-text-area ${
					hasItems ? 'has-append' : ''
				}`"
				background-color="var(--new-input-bg)"
				outlined
				counter
				auto-grow
				v-bind="attrs"
				v-on="listeners"
			>
				<template v-if="hasItems" slot="append" class="pe-0">
					<v-btn fab small color="primary" class="ma-0" @click="add">
						<v-icon dark size="20"> mdi-plus </v-icon>
					</v-btn>
				</template>
			</v-textarea>
			<CFeildItems
				v-if="hasItems"
				:items="items"
				:icon="attrs['prepend-inner-icon']"
				@removeItem="(index) => $emit('removeItem', index)"
			/>
		</template>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
import CFeildItems from "./CFeildItems.vue";
export default {
	props: {
		hasItems: Boolean,
		items: Array,
	},
	methods: {
		add() {
			this.$emit("add");
		},
	},
	components: {
		InputCard,
		CFeildItems,
	},
};
</script>
<style>
.form-custom-text-area .v-input__prepend-inner {
	margin-top: 12px;
	margin-right: 8px !important;
}
.form-custom-text-area .v-input__slot,
.form-custom-text-area .v-input__slot fieldset {
	border-radius: 12px !important;
}
.form-custom-text-area .v-counter {
	position: absolute;
	top: -20px;
	right: 8px;
	padding: 0;
	color: var(--v-primary-base);
}
.v-application--is-rtl .form-custom-text-area .v-counter {
	right: unset;
	left: 8px;
}
.form-custom-text-area.has-append .v-input__append-inner {
	margin-top: auto !important;
	margin-bottom: auto !important;
}
</style>
