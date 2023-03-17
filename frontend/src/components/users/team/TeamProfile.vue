<template>
	<master-profile
		:tabitem="items"
		titleProfile="team_list"
		@click="$emit('update:dialog')"
		:profileData="profileData"
	>
		<template slot="slot_general">
			<ProfileCustomeRow
				headerRow="team"
				:contentRow="profileData.name"
				:contentRowTooltip="[]"
				headerRow2="created_by"
				contentRow2=""
				:contentRow2Tooltip="[]"
			></ProfileCustomeRow>

			<ProfileCustomeRow
				bgColor
				headerRow="code_id"
				:contentRow="profileData.code"
				:contentRowTooltip="[]"
				headerRow2="updated_by"
				contentRow2=""
				:contentRow2Tooltip="[]"
			></ProfileCustomeRow>

			<ProfileCustomeRow
				headerRow="company"
				:contentRowTooltip="profileData.department_company_country[0].companies"
				tooltipName="companies"
				headerRow2="created_at"
				:contentRow2="localeHumanReadableTime(profileData.created_at)"
				:contentRow2Tooltip="[]"
			></ProfileCustomeRow>

			<ProfileCustomeRow
				bgColor
				headerRow="department"
				:contentRow="profileData.department_company_country[0].name"
				:contentRowTooltip="[]"
				headerRow2="updated_at"
				:contentRow2="localeHumanReadableTime(profileData.updated_at)"
				:contentRow2Tooltip="[]"
			></ProfileCustomeRow>
			<ProfileCustomeRow
				headerRow="status"
				:contentRow="profileData.status"
				:contentRowTooltip="[]"
				headerRow2=""
				contentRow2=""
				:contentRow2Tooltip="[]"
			></ProfileCustomeRow>
		</template>
		<template slot="slot_team_role">
			<ProfileCustomeRow
				bgColor
				headerRow="company"
				:contentRowTooltip="profileData.department_company_country[0].companies"
				tooltipName="companies"
				headerRow2="permissions"
				tooltipName2="permissions"
				:contentRow2Tooltip="profileData.permissions"
			></ProfileCustomeRow>
		</template>
		<template slot="slot_members">
			<ProfileCustomeRow
				singleRow
				headerRow="members"
				:contentRow="profileData.number_of_people + '  ' + $tr('person')"
				:contentRowTooltip="[]"
			></ProfileCustomeRow>
			<v-divider></v-divider>
			<v-row>
				<v-col
					v-for="(item, i) in profileData.members"
					:key="i"
					cols="12"
					md="6"
					class="py-1"
				>
					<v-card class="mx-1" elevation="0" style="background-color:var(--v-surface-darken1)">
						<v-card-text class="py-1 d-flex justify-space-between align-center">
							<span class="d-flex align-center">
								<v-avatar size="70">
									<img v-if="item.image" :src="item.image" alt="John" />
									<span v-else class="white--text text-h4">
										{{ item.lastname ? item.lastname[0].toUpperCase() : "" }}
									</span>
								</v-avatar>
								<p class="ma-0 ms-2 font-weight-bold">
									{{ item.firstname + " " + item.lastname }}
								</p>
							</span>
							<span>
								<v-tooltip bottom>
									<template v-slot:activator="{ on, attrs }">
										<v-btn
											icon
											:href="`mailto:` + item.email"
											target="_blank"
											v-bind="attrs"
											v-on="on"
										>
											<v-avatar color="grey darken-3" size="30">
												<v-icon color="white" size="20">mdi-email</v-icon>
											</v-avatar>
										</v-btn>
									</template>
									<span>{{ item.email }}</span>
								</v-tooltip>

								<v-tooltip bottom>
									<template v-slot:activator="{ on, attrs }">
										<v-btn
											v-bind="attrs"
											v-on="on"
											icon
											:href="`tel:` + item.phone"
											class="mx-1"
										>
											<v-avatar color="grey darken-3" size="30">
												<v-icon color="white" size="20">mdi-cellphone</v-icon>
											</v-avatar>
										</v-btn>
									</template>
									<span>{{ item.phone }}</span>
								</v-tooltip>

								<v-tooltip bottom>
									<template v-slot:activator="{ on, attrs }">
										<v-btn
											icon
											:href="`https://wa.me/` + item.whatsapp"
											target="_blank"
											v-bind="attrs"
											v-on="on"
										>
											<v-avatar color="grey darken-3" size="30">
												<v-icon color="white" size="20">mdi-whatsapp</v-icon>
											</v-avatar>
										</v-btn>
									</template>
									<span>{{ item.whatsapp }}</span>
								</v-tooltip>
							</span>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
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
import moment from "moment-timezone";
import CloseBtn from "../../design/Dialog/CloseBtn.vue";
import MasterProfile from "../../masters/MasterProfile.vue";
import ProfileCustomeRow from "../../masters/ProfileCustomeRow.vue";
import StatusTransformationTab from '../../../components/common/StatusTransformationTab.vue'
export default {
	components: { StatusTransformationTab, CloseBtn, MasterProfile, ProfileCustomeRow },
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
				.format("YYYY-MM-DD h:mm: A");
		},
	},

	data() {
		return {
			items: [
				{ tab: "general" },
				{ tab: "team_role" },
				{ tab: "members" },
				{ tab: "remarks" },
				{ tab: 'status_transformation' },

			],
		};
	},
	
};
</script>

<style>
</style>
