<template>
	<div class="table-container px-2 py-1">
		<!-- <v-data-table :headers="headers" :items="items"> </v-data-table> -->
		<DataTable
			ref="projectTableRef"
			:headers="headers"
			:items="items"
			:ItemsLength="items.length"
			v-model="selectedItems"
			:loading="apiCalling"
			@onTablePaginate="onTableChanges"
			@click:row="onRowClicked"
			class="px-0 mx-0"
			:itemkey="'post_id'"
			:hasPagination="false"
			:nameLogo="true"
		>
		</DataTable>
	</div>
</template>

<script>
import DataTable from "~/components/design/DataTable.vue";
export default {
	props: {
		items: {
			type: Array,
		},
		headers: Array,
		apiCalling: Boolean,
	},
	components: {
		DataTable,
	},
	data() {
		return {
			selectedItems: [],
		};
	},
	methods: {
		onTableChanges() {},
		onRowClicked(item) {
			this.selectedItems = [item];

			this.$root.$emit("onAddressSelect", this.selectedItems);
		},
	},
};
</script>

<style scoped>
.table-container {
	border: 1px solid lightgray;
	border-radius: 10px;
	min-width: 100%;
	max-height: 200px;
	overflow-y: scroll;
}
</style>
