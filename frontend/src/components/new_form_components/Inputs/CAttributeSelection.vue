div
<template>
	<InputCard InputCard v-bind="$attrs" v-on="$listeners">
		<div class="py-3 d-flex flex-wrap" v-if="loading">
			<AttributeItem
				@click="$emit('AddAtrr', item)"
				:titleItem="item.name"
				v-for="(item, index) in titleItems"
				:key="index"
				:selected="selectedAttr.findIndex((el) => el.id == item.id) > -1"
			/>
		</div>
		<div class="d-flex" v-else>
			<v-skeleton-loader
				v-for="i in 5"
				:key="i"
				v-bind="attrs"
				type="list-item-avatar"
				width="150"
			></v-skeleton-loader>
		</div>
		<!-- <v-btn @click="AddAtrr()" outlined color="primary" rounded>{{ $tr("add_attributes") }}</v-btn> -->
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";
import AttributeItem from "../components/AttributeItem.vue";
export default {
	components: { InputCard, AttributeItem },
	props: {
		titleItems: Array,
		selectedAttr: Array,
		loading: {
			type: Boolean,
		},
	},
	data() {
		return {
			attrs: {
				class: "mx-1 mt-3",
				boilerplate: true,
				elevation: 1,
			},
		};
	},
};
</script>
<style scoped></style>
