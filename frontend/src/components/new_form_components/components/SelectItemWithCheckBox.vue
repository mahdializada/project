<template>
	<div style="height: 230px" class="d-flex my-3 overflow-auto flex-wrap">
		<div
			style="height: 100%; width: 100%"
			class="d-flex align-center justify-center"
			v-if="form.$model.sub_systems.length == 0"
		>
			{{ $tr("no_data") }}
		</div>

		<div class="d-flex" v-if="loading">
			<v-skeleton-loader
				v-for="i in 5"
				:key="i"
				width="200"
				height="50"
				class="mx-2 d-flex align-center"
				type="text,avatar"
			>
			</v-skeleton-loader>
		</div>
		<div
			v-else
			v-for="(item, index) in items"
			:key="index"
			@click="$emit('click', item)"
			class="type-div mx-1 d-flex align-center justify-space-between"
			:style="item.selected ? `border:1px solid var(--v-primary-base)` : ``"
		>
			<span class="mx-1">{{ item.translated }}</span>
			<v-icon v-if="item.selected" color="primary " class="mx-1"
				>mdi-check-circle</v-icon
			>
			<v-icon v-else class="mx-1" color="grey"
				>mdi-checkbox-blank-circle-outline</v-icon
			>
		</div>
	</div>
</template>
<script>
import InputCard from "./InputCard";
export default {
	components: {
		InputCard,
	},
	props: {
		selected: Boolean,
		items: Array,
		loading: Boolean,
	},
};
</script>

<style>
.item__card__loader .v-skeleton-loader__table-cell {
	height: 35px !important;
}
</style>

<style lang="scss" scoped>
.item__card {
	width: 170px;
	display: flex;
	align-items: center;
	justify-content: flex-start;
	margin: 6px !important;
	border-radius: 8px;
	transition: all 0.1s;
	border: 1px solid #2e7be62b !important;
}
.item__card.selected {
	border: 2px solid #2e7be680 !important;
	box-shadow: 0 0 10px #2e7be633;
}
</style>
