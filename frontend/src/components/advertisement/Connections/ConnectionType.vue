<template>
	<div class="connection__container">
		<CTitle :text="`choose_connection_type`" />
		<div class="mb-3 d-flex">
			<ItemCard
				v-for="(item, index) in items"
				:key="index"
				:item="item"
				:selected="item.type == form.connection_type.$model"
				class="custom__item__card"
				@click="handleClick(item)"
			/>
		</div>
	</div>
</template>
<script>
import ItemCard from "~/components/new_form_components/components/ItemCard.vue";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";

export default {
	props: {
		form: Object,
	},
	data() {
		return {
			items: [
				{
					type: "new",
					name: this.$tr("create_new"),
				},
				{
					type: "template",
					name: this.$tr("choose_a_template"),
				},
			],
		};
	},
	methods: {
		handleClick(item) {
			this.form.connection_type.$model = item.type;
		},
		validate() {
			this.form.connection_type.$touch();
			return !this.form.connection_type.$invalid;
		},
	},
	components: {
		CTitle,
		ItemCard,
	},
};
</script>

<style scoped lang="scss">
.connection__container {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 100%;
}
.custom__item__card {
	width: 220px;
	height: 160px;
}
.custom__item__card .item-name {
	font-size: 15px !important;
	font-weight: 600 !important;
}
</style>
