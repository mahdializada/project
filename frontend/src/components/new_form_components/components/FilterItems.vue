<template>
	<div class="mt-1 d-flex align-center bg-custom-gray py-1 px-2 rounded">
		<div class="me-1">{{ $tr("filters") }} :</div>
		<div class="d-flex" style="min-height: 32px">
			<v-menu
				offset-y
				v-for="(item, i) in filtersSelected"
				:key="i"
				:close-on-content-click="false"
				v-model="filterItems[item].menu"
			>
				<template v-slot:activator="{ on, attrs }">
					<div
						v-on="on"
						v-bind="attrs"
						class="filter-item rounded me-1 d-flex align-center"
					>
						<v-icon color="primary" class="me-1" size="16">{{
							filterItems[item].icon
						}}</v-icon>
						{{ filterItems[item].title }}
						<div
							class="filter-item-close rounded-circle primary d-flex align-center justify-center"
							@click="
								() => {
									if (filterItems[item].id == 'date') {
										filterItems[item].selected.start_date = '';
										filterItems[item].selected.end_date = '';
									} else {
										filterItems[item].selected = null;
									}
									$emit('removeItem', item);
								}
							"
						>
							<v-icon dark size="14">mdi-close</v-icon>
						</div>
					</div>
				</template>
				<div class="background pa-2" style="max-width: 300px">
					<v-select
						v-if="filterItems[item].id != 'date'"
						:placeholder="filterItems[item].title"
						v-model="filterItems[item].selected"
						hide-details
						:items="filterItems[item].items"
						class="form-new-item form-custom-select"
						background-color="var(--new-input-bg)"
						outlined
						dense
						:rounded="true"
						:menu-props="{
							bottom: true,
							offsetY: true,
						}"
						:item-text="filterItems[item].itemText"
						:item-value="filterItems[item].itemValue"
					>
					</v-select>
					<div v-else style="min-width: 268px">
						<v-list dense flat>
							<v-list-item-group v-model="date_list">
								<v-list-item
									v-for="(item, i) in dateList"
									:key="i"
									class="rounded-lg filter-list-item mb-1 overflow-hidden"
									dense
									active-class="filter-list-item-active"
									:value="item"
								>
									<template v-slot:default="{ active }">
										<v-list-item-title>
											{{ $tr(item) }}
										</v-list-item-title>
										<v-list-item-action>
											<v-checkbox
												:input-value="active"
												hide-details
											></v-checkbox>
										</v-list-item-action>
									</template>
								</v-list-item>
							</v-list-item-group>
						</v-list>
						<div class="d-flex" v-if="showDate">
							<v-menu
								ref="menu"
								v-model="startDateMenu"
								:close-on-content-click="false"
								transition="scale-transition"
								offset-y
								min-width="auto"
							>
								<template v-slot:activator="{ on, attrs }">
									<v-text-field
										v-model="filterItems[item].selected.start_date"
										v-bind="attrs"
										v-on="on"
										:placeholder="$tr('start_date')"
										readonly
										background-color="var(--new-input-bg)"
										outlined
										rounded
										:class="`form-new-item start-date`"
										dense
										hide-details
									></v-text-field>
								</template>
								<v-date-picker
									v-model="filterItems[item].selected.start_date"
									no-title
									@input="startDateMenu = false"
									:max="filterItems[item].selected.end_date"
								>
								</v-date-picker>
							</v-menu>
							<v-menu
								ref="menu"
								v-model="endDateMenu"
								:close-on-content-click="false"
								transition="scale-transition"
								offset-y
								min-width="auto"
							>
								<template v-slot:activator="{ on, attrs }">
									<v-text-field
										v-model="filterItems[item].selected.end_date"
										v-bind="attrs"
										v-on="on"
										:placeholder="$tr('end_date')"
										readonly
										background-color="var(--new-input-bg)"
										outlined
										rounded
										:class="`form-new-item end-date`"
										dense
										hide-details
									></v-text-field>
								</template>
								<v-date-picker
									v-model="filterItems[item].selected.end_date"
									no-title
									@input="endDateMenu = false"
									:min="filterItems[item].selected.start_date"
								>
								</v-date-picker>
							</v-menu>
						</div>
					</div>
					<v-btn
						outlined
						class="mt-2"
						block
						color="primary"
						rounded
						@click="
							() => {
								filterItems[item].menu = false;
								filterItems[item].filterDone();
							}
						"
					>
						{{ $tr("done") }}
					</v-btn>
				</div>
			</v-menu>
		</div>
	</div>
</template>
<script>
import moment from "moment-timezone";

export default {
	props: {
		filterItems: Array,
		filtersSelected: Array,
	},
	data() {
		return {
			startDateMenu: false,
			endDateMenu: false,
			showDate: false,
			date_list: [],
			custom_date: undefined,
			dateList: [
				"today",
				"yesterday",
				"3_days_ago",
				"1_week_ago",
				"1_month_ago",
				"custom_date",
			],
		};
	},
	watch: {
		date_list(value) {
			if (value) {
				if (value == "custom_date") {
					this.showDate = true;
				} else {
					this.showDate = false;
				}
			} else {
				this.showDate = false;
			}
			if (value) {
				if (value == "today") {
					this.filterItems[this.filterItems.length - 1].selected.start_date =
						moment().format("YYYY-MM-DD");
					this.filterItems[this.filterItems.length - 1].selected.end_date = "";
				} else if (value == "yesterday") {
					this.filterItems[this.filterItems.length - 1].selected.start_date =
						moment().subtract(1, "day").format("YYYY-MM-DD");
					this.filterItems[this.filterItems.length - 1].selected.end_date = "";
				} else if (value == "3_days_ago") {
					this.filterItems[this.filterItems.length - 1].selected.start_date =
						moment().subtract(3, "day").format("YYYY-MM-DD");
					this.filterItems[this.filterItems.length - 1].selected.end_date = "";
				} else if (value == "1_week_ago") {
					this.filterItems[this.filterItems.length - 1].selected.start_date =
						moment().subtract(7, "day").format("YYYY-MM-DD");
					this.filterItems[this.filterItems.length - 1].selected.end_date = "";
				} else if (value == "1_month_ago") {
					this.filterItems[this.filterItems.length - 1].selected.start_date =
						moment().subtract(30, "day").format("YYYY-MM-DD");
					this.filterItems[this.filterItems.length - 1].selected.end_date = "";
				}
			}
		},
	},
};
</script>
<style scoped>
.filter-item {
	background: var(--v-background-base);
	border: 1px solid transparent;
	padding: 4px 8px;
	font-size: 12px;
	position: relative;
}
.filter-item-close {
	height: 16px;
	width: 16px;
	position: absolute;
	top: 0;
	right: 0;
	transform: translate(50%, -50%);
	cursor: pointer;
}
</style>
<style>
.form-new-item.start-date {
	border-top-right-radius: 0 !important;
	border-bottom-right-radius: 0 !important;
}
.form-new-item.end-date {
	border-top-left-radius: 0 !important;
	border-bottom-left-radius: 0 !important;
}
.form-new-item.start-date::after {
	content: "";
	display: block;
	height: 20px;
	width: 4px;
	background: #dddcdc;
	border-radius: 4px;
	position: absolute;
	right: -2px;
	top: 12px;
}
.v-application--is-rtl .form-new-item.start-date {
	border-top-right-radius: 28px !important;
	border-bottom-right-radius: 28px !important;
	border-top-left-radius: 0 !important;
	border-bottom-left-radius: 0 !important;
}
.v-application--is-rtl .form-new-item.end-date {
	border-top-left-radius: 28px !important;
	border-bottom-left-radius: 28px !important;
	border-top-right-radius: 0 !important;
	border-bottom-right-radius: 0 !important;
}
.v-application--is-rtl .form-new-item.start-date::after {
	left: -2px;
	right: unset;
}
.filter-list-item {
	border: 1px solid transparent;
	max-height: 40px;
}
.filter-list-item .v-list-item__title {
	font-weight: 400 !important;
}
.filter-list-item-active {
	border: 1px solid var(--v-primary-base);
}
</style>
