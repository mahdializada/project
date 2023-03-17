<template>
	<div>
		<v-data-table
			item-key="id"
			:headers="tableHeaders.length === 0 ? defaultHeaders : tableHeaders"
			:items="paginatedItems"
			:items-per-page="10"
			class="elevation-0"
			hide-default-footer
			:no-data-text="$tr('no_data_available')"
			style="background-color: inherit"
		>
		</v-data-table>
		<v-row class="pb-3 align-center ma-0 py-2 tbl-bottom">
			<v-col cols="0" md="4" class="pa-0"></v-col>
			<v-col class="py-0" cols="12" md="4">
				<div
					class="
						text-center text-center
						mx-auto
						d-flex
						align-center
						justify-center
					"
				>
					<p class="ma-0 me-2">
						{{ $tr("showing_items", started_at, ended_at, tableItems.length) }}
					</p>
					<div style="width: 90px !important; margin: 0.16rem 0.16rem"></div>
				</div>
			</v-col>
			<v-col class="py-1 py-md-0 d-flex" cols="12" md="4">
				<div class="mx-auto mx-md-0 ms-md-auto">
					<CustomPagination
						paginateSmall
						@paginate="paginateFunc"
						:pageCount="parseInt(Math.ceil(tableItems.length / 7))"
					/>
				</div>
			</v-col>
		</v-row>
	</div>
</template>


<script>
import CustomPagination from "../design/components/CustomPagination.vue";

export default {
	name: "StatusTransformationTab",
	components: { CustomPagination },
	props: {
		tableItems: {
			type: Array,
			required: true,
		},
		tableHeaders: {
			type: Array,
			default:() => [
				
			],
		},
	},

	data() {
		return {
			defaultHeaders: [
				{ text:this.$tr("status"), value: "status", sortable: false },
				{ text:this.$tr("applied_by"), value: "applied_by", sortable: false },
				{ text:this.$tr("reason"), value: "reason", sortable: false },
				{ text:this.$tr("date"), value: "date", sortable: false },
			],
			paginatedItems: this.tableItems.slice(0, 7),
			started_at: this.tableItems.length ? 1 : 0,
			ended_at: this.tableItems.length >= 7 ? 7 : this.tableItems.length,
		};
	},

	methods: {
		paginateFunc(page) {
			let start_index = (page - 1) * 7;
			this.started_at = start_index + 1;
			this.paginatedItems = [];
			for (let index = start_index; index < start_index + 7; index++) {
				if (this.tableItems[index] !== undefined) {
					this.ended_at = index + 1;
					this.paginatedItems.push(this.tableItems[index]);
				}
			}
		},
	},
};
</script>