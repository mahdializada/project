<template>
	<span>
		<v-row
			v-if="singleRow"
			:style="
				bgColor
					? 'background-color: var(--v-surface-darken1)'
					: 'background-color: inherit'
			"
		>
			<v-col cols="12" md="12" class="py-1 ps-2 ps-md-6 d-flex">
				<div style="width: 24%" class="font-weight-black">
					{{ $tr(headerRow) }}
				</div>

				<div style="width: 76%" v-if="contentRow">{{ contentRow }}</div>

				<div style="width: 76%" v-if="listItem">
					<span v-for="(item, i) in listItem" :key="i">
						{{ item.name }} ,
					</span>
				</div>

				<v-tooltip bottom>
					<template v-slot:activator="{ on, attrs }">
						<span
							v-bind="attrs"
							v-on="on"
							style="white-space: nowrap"
							class="primary--text"
						>
							{{ $tr(tooltipName) }}
						</span>
					</template>

					<div style="width: 75%" v-if="contentRowTooltip.length > 0">
						<p
							v-for="item in contentRowTooltip"
							:key="item.index"
							class="pb-0 mb-0"
						>
							{{ item.name }}
						</p>
					</div>
					<div v-else>
						<p class="pb-0 mb-0">
							{{ $tr("no_item", $tr(tooltipName)) }}
						</p>
					</div>
				</v-tooltip>
			</v-col>
		</v-row>

		<v-row
			v-else
			:style="
				bgColor
					? 'background-color: var(--v-surface-darken1)'
					: 'background-color: inherit'
			"
		>
			<v-col cols="12" md="6" class="py-1 ps-6 d-flex">
				<div style="width: 50%" class="font-weight-black">
					{{ $tr(headerRow) }}
				</div>
				<div style="width: 50%" v-if="contentRow">
					{{ contentRow }}
				</div>
				
				<div style="width: 50%" v-if="linkRow1">
					<a :href="linkRow1" target="_blank">{{ linkName1 }}</a>
				</div>

				<v-tooltip bottom>
					<template v-slot:activator="{ on, attrs }">
						<span
							v-bind="attrs"
							v-on="on"
							style="white-space: nowrap"
							class="primary--text"
						>
							{{ $tr(tooltipName) }}
						</span>
					</template>

					<div v-if="contentRowTooltip.length > 0">
						<p
							v-for="item in contentRowTooltip"
							:key="item.index"
							class="pb-0 mb-0"
						>
							{{ item.name }}
						</p>
					</div>
					<div v-else>
						<p class="pb-0 mb-0">
							{{ $tr("no_item", $tr(tooltipName)) }}
						</p>
					</div>
				</v-tooltip>
			</v-col>
			<v-divider vertical v-if="dividerRow"> </v-divider>
			<v-col cols="12" md="6" class="py-1 d-flex">
				<div style="width: 50%" class="font-weight-black">
					{{ $tr(headerRow2) }}
				</div>
				<div style="width: 50%" v-if="contentRow2">{{ contentRow2 }}</div>
				<div style="width: 50%" v-if="linkRow2">
					<a :href="linkRow2" target="_blank">{{ linkName2 }}</a>
				</div>

				<v-tooltip bottom>
					<template v-slot:activator="{ on, attrs }">
						<span
							v-bind="attrs"
							v-on="on"
							style="white-space: nowrap"
							class="primary--text"
						>
							{{ $tr(tooltipName2) }}
						</span>
					</template>

					<div v-if="contentRow2Tooltip.length > 0">
						<p
							v-for="item in contentRow2Tooltip"
							:key="item.index"
							class="pb-0 mb-0"
						>
							{{ item.name }}
						</p>
					</div>
					<div v-else>
						<p class="pb-0 mb-0">
							{{ $tr("no_item", $tr(tooltipName2)) }}
						</p>
					</div>
				</v-tooltip>
			</v-col>
		</v-row>
	</span>
</template>
<script>
export default {
	props: {
		headerRow: {
			type: String,
		},
		contentRow: {
			type: String,
		},
		headerRow2: {
			type: String,
		},
		contentRow2: {
			type: String,
		},
		dividerRow: {
			type: Boolean,
			default: true,
		},
		bgColor: {
			type: Boolean,
			default: false,
		},
		contentRowTooltip: {
			type: Array,
			default:[],
		},
		tooltipName: {
			type: String,
		},
		contentRow2Tooltip: {
			type: Array,
		},
		tooltipName2: {
			type: String,
		},
		singleRow: {
			type: Boolean,
			default: false,
		},
		linkRow1: {
			type: String,
		},
		linkRow2: {
			type: String,
		},
		linkName2: {
			type: String,
		},
		linkName1: {
			type: String,
		},
		listItem: {
			type: Array,
		},
		editable: {
			type: Boolean,
			default: false,
		},
	},
};
</script>