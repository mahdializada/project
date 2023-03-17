<template>
	<div
		:class="`w-full input-card background ${px} ${py} position-relative ${
			pbNone ? 'pb-0' : ''
		}`"
		:style="`
			height:${height ? height : ''}% !important; 
			border:${borderStyle}; 
			box-shadow:${boxShadow};
		`"
	>
		<div
			v-if="title != ''"
			class="d-flex flex-column justify-center"
			:style="`min-height:${sub_title ? '66' : '0'}px;`"
		>
			<div class="d-flex align-center">
				<v-btn
					v-if="showBack"
					fab
					small
					text
					color="primary"
					class="ma-0 me-1 bg-custom-gray"
					@click="$emit('back')"
				>
					<v-icon dark size="28">
						mdi-chevron-{{ $vuetify.rtl ? "right" : "left" }}
					</v-icon>
				</v-btn>
				<p :class="`my-1 input-title ${small ? 'small' : ''}`">
					{{ $tr(title) }}
					<CTooltip v-if="tooltip" :text="tooltip" />
				</p>
			</div>
			<p class="input-sub-title mb-1" v-if="sub_title">{{ $tr(sub_title) }}</p>
			<v-breadcrumbs v-if="path" :items="path" class="mt-1 pa-0">
				<template v-slot:item="{ item }">
					<v-breadcrumbs-item
						class="breadcrumb-item breadcrumb-link"
						link
						@click.prevent="$emit('changeTo', 'parent')"
						href="#"
						v-if="typeof item == 'string'"
					>
						{{ $tr(item) }}
					</v-breadcrumbs-item>
					<v-breadcrumbs-item
						class="breadcrumb-item breadcrumb-link"
						link
						@click.prevent="$emit('changeTo', item)"
						href="#"
						v-else
					>
						{{ $tr(item.name) }}
					</v-breadcrumbs-item>
				</template>
			</v-breadcrumbs>
		</div>
		<div
			:class="`search ${sub_title ? 'search-top-30' : ''} ${
				hasFilter ? 'd-flex' : ''
			}`"
			v-if="hasSearch"
		>
			<FormSearch
				@search="(search) => $emit('search', search)"
				@disableSearch="$emit('disableSearch')"
			/>
			<v-menu
				v-if="hasFilter"
				:close-on-content-click="false"
				offset-y
				class="my-custom-menu"
				z-index="10001"
			>
				<template v-slot:activator="{ on, attrs }">
					<v-btn
						fab
						small
						text
						color="primary"
						class="ma-0 ms-1"
						v-bind="attrs"
						v-on="on"
					>
						<v-icon dark size="20"> mdi-tune </v-icon>
					</v-btn>
				</template>
				<div class="filter-item-list">
					<v-list dense flat>
						<v-list-item-group
							v-model="filtersSelected"
							color="primary"
							class="px-1"
							multiple
						>
							<v-list-item
								dense
								class="rounded-lg filter-list-item mb-1 overflow-hidden"
								v-for="(item, index) in filterItems"
								:key="index"
								active-class="filter-list-item-active"
							>
								<template v-slot:default="{ active }">
									<v-list-item-title>{{ item.title }}</v-list-item-title>
									<v-list-item-action>
										<v-checkbox :input-value="active"></v-checkbox>
									</v-list-item-action>
								</template>
							</v-list-item>
						</v-list-item-group>
					</v-list>
				</div>
			</v-menu>
		</div>
		<div
			v-if="hasAllSwitch"
			class="all-switch d-flex align-center justify-center"
		>
			{{ $tr("all") }}
			<v-switch
				class="ms-2"
				dense
				color="primary"
				v-model="switch1"
				@change="(value) => $emit('allSwitchChange', value)"
				inset
			></v-switch>
		</div>
		<FilterItems
			v-if="filtersSelected.length > 0"
			:filtersSelected="filtersSelected"
			:filterItems="filterItems"
			@removeItem="removeItem"
		/>
		<slot :attrs="$attrs" :listeners="$listeners"></slot>
		<Pagination
			v-if="hasPagination && pages > 1"
			:value="page"
			@input="(value) => $emit('updatePage', value)"
			:pages="pages"
		/>
	</div>
</template>
<script>
import FormSearch from "./FormSearch.vue";
import Pagination from "./Pagination.vue";
import CTooltip from "./CTooltip.vue";
import FilterItems from "./FilterItems.vue";
export default {
	props: {
		title: String,
		showBack: Boolean,
		sub_title: String,
		hasSearch: Boolean,
		pbNone: Boolean,
		hasPagination: Boolean,
		hasAllSwitch: Boolean,
		pages: Number,
		page: Number,
		path: Array,
		borderStyle: String,
		boxShadow: String,
		px: {
			type: String,
			default: "px-4",
		},
		py: {
			type: String,
			default: "py-2",
		},
		small: Boolean,
		tooltip: String,
		hasFilter: Boolean,
		filterItems: Array,
		height:String,
	},
	data() {
		return {
			switch1: false,
			filtersSelected: [],
		};
	},
	methods: {
		removeItem(item) {
			this.filtersSelected = this.filtersSelected.filter(
				(item2) => item != item2
			);
			this.$emit("filterDone");
		},
	},
	components: {
		FormSearch,
		Pagination,
		CTooltip,
		FilterItems,
	},
};
</script>
<style scoped>
.input-card {
	border-radius: 16px;
}
.input-title {
	font-size: 18px;
	font-weight: 500;
}
.input-title.small {
	font-size: 16px;
}
.input-title,
.input-sub-title,
.all-switch {
	color: #777;
}
.all-switch {
	position: absolute;
	top: 20px;
	right: 72px;
	z-index: 1;
}
.search {
	position: absolute;
	top: 18px;
	right: 32px;
	z-index: 2;
}
.search-top-30 {
	top: 30px;
}
.v-application--is-rtl .search {
	right: unset;
	left: 32px;
}
.v-application--is-rtl .all-switch {
	right: unset;
	left: 72px;
}
</style>
<style>
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
