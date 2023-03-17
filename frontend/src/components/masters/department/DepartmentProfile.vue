<template>
	<master-profile
		:tabitem="items"
		titleProfile="department"
		@click="$emit('update:dialog')"
		:profileData="profileData"
	>
		<template slot="slot_general">
			<ProfileCustomeRow
				headerRow="country"
				:contentRowTooltip="profileData.companies[0].countries"
				tooltipName="countries"
				headerRow2="created_by"
				:contentRow2="
					profileData.created_by.firstname +
					' ' +
					profileData.created_by.lastname
				"
				:contentRow2Tooltip="[]"
			></ProfileCustomeRow>

			<ProfileCustomeRow
				bgColor
				headerRow="company"
				:contentRowTooltip="profileData.companies"
				tooltipName="companies"
				headerRow2="created_at"
				:contentRow2="localeHumanReadableTime(profileData.created_at)"
				:contentRow2Tooltip="[]"
			></ProfileCustomeRow>
			<ProfileCustomeRow
				headerRow="industries"
				:contentRowTooltip="profileData.industries"
				tooltipName="industries"
				headerRow2="updated_at"
				:contentRow2="localeHumanReadableTime(profileData.updated_at)"
				:contentRow2Tooltip="[]"
			></ProfileCustomeRow>
		</template>
		<template slot="slot_remarks">
			<div class="rounded-sm mt-2 mx-3">
				<p class="text-primary ps-1" style="font-size: 1rem">
					{{ profileData.note }}
				</p>
			</div>
		</template>
		<template slot="slot_status_transformation">
			<StatusTransformationTab :tableItems="profileData.status_transformations" />		
		</template>
	</master-profile>
</template>
<script>
	import moment from 'moment-timezone';
	import CloseBtn from '../../design/Dialog/CloseBtn.vue';
	import MasterProfile from '../../masters/MasterProfile.vue';
	import ProfileCustomeRow from '../../masters/ProfileCustomeRow.vue';
	import StatusTransformationTab from '../../common/StatusTransformationTab.vue';

	export default {
		components: {
			CloseBtn,
			MasterProfile,
			ProfileCustomeRow,
			StatusTransformationTab,
		},
		props: {
			dialog: {
				type: Boolean,
				required: true,
			},
			profileData: {
				type: Object,
				required: true,
			},
		},
		methods: {
			localeHumanReadableTime(date) {
				return moment(date)
					.locale(this.$vuetify.lang.current)
					.format('YYYY-MM-DD h:mm: A');
			},
		},

		data() {
			return {
				items: [
					{ tab: 'general' },
					{ tab: 'remarks' },
					{ tab: 'status_transformation' },
				],
			};
		},
	};
</script>

<style></style>
