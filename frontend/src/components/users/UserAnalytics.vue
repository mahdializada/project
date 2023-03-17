R
<template>
	<transition name="slide-horizontal">
		<v-row class="px-2" v-if="this.visible">
			<v-col cols="12" md="6" lg="4" class="pa-1">
				<CustomCard class="pa-2 mb-0">
					<h4 class="">{{ $tr("roles") }}</h4>
					<apexchart
						type="donut"
						:options="options"
						:series="series"
						v-if="options.labels.length > 0 && this.series.length > 0"
					>
					</apexchart>
				</CustomCard>
			</v-col>
			<v-col cols="12" md="6" lg="4" class="pa-1">
				<CustomCard class="pa-2 mb-0"></CustomCard>
			</v-col>
			<v-col cols="12" md="6" lg="4" class="pa-1">
				<CustomCard class="pa-2 mb-0"></CustomCard>
			</v-col>
			<v-col cols="12" md="6" lg="4" class="pa-1 py-1">
				<CustomCard class="pa-2 primary white--text mb-0">
					<h2 class="py-2">{{ $tr("active_users_right_now") }}</h2>
					<div class="active-users">
						<p class="active-users-count">{{ this.onlineUsers.length }}</p>
					</div>
				</CustomCard>
			</v-col>
			<v-col cols="12" md="12" lg="8" class="pa-1">
				<CustomCard class="pa-2">
					<h2 class="py-2">{{ $tr("active_users") }}</h2>
					<div style="height: 600px">
						<google-map
							id="map"
							ref="Map"
							:fitBounds="true"
							:zoom="2"
							:center="{
								lat: 49.46800006494457,
								lng: 17.11514008755796,
							}"
						>
							<google-map-marker
								:visible="true"
								:z-index="10000"
								v-for="(user, i) in onlineUsers"
								:key="i"
								:position="{
									lat: user.coords ? user.coords.latitude : '',
									lng: user.coords ? user.coords.longitude : '',
								}"
								centerMap="always"
							/>
						</google-map>
					</div>
				</CustomCard>
			</v-col>
		</v-row>
	</transition>
</template>
<script>
import CustomCard from "../../components/common/CustomCard.vue";
import { mapGetters, mapActions } from "vuex";

export default {
	components: {
		CustomCard,
	},
	props: {
		visible: {
			type: Boolean,
		},
	},
	data: () => ({
		// visible: false,
		interval: null,
		options: {
			labels: [],
			dataLabels: {
				enabled: false,
			},
			plotOptions: {
				pie: {
					customScale: 0.8,
					donut: {
						size: "90%",
						labels: {
							show: true,
							total: {
								show: true,
								showAlways: true,
							},
						},
					},
				},
			},
		},
		series: [],
	}),
	async mounted() {
		await this.fetchRoles({ key: "all", itemsPerPage: -1 });
		this.roles2 = JSON.parse(JSON.stringify(this.roles));
	},
	methods: {
		async userAnalyticsToggleVisible() {
			if (!this.visible) {
				this.options.labels = this.roles2?.reverse().map((role) => role.name);
				this.series = this.roles2.map((role) => 0);
				this.setSeries();
			}
		},
		setSeries() {
			for (let x = 0; x < this.onlineUsers.length; x++) {
				for (let y = 0; y < this.onlineUsers[x].roles.length; y++) {
					for (let z = 0; z < this.roles2?.reverse().length; z++) {
						if (
							this.onlineUsers[x].roles[0].id === this.roles2?.reverse()[z].id
						) {
							this.series[z]++;
						}
					}
				}
			}
		},
		...mapActions({
			// fetchOnlineUsers: "users/fetchOnlineUsers",
			fetchRoles: "roles/fetchItems",
		}),
	},
	computed: {
		...mapGetters({
			onlineUsers: "users/onlineUsers",
			roles: "roles/getItems",
		}),
	},
	watch: {
		onlineUsers: function () {
			this.setSeries();
		},
	},
};
</script>
<style scoped>
.active-users-count {
	font-size: 60px;
	font-weight: 500;
}

.mb-0 {
	margin-bottom: 0 !important;
}

@media only screen and (min-width: 1264px) {
	.active-users {
		height: 600px;
	}
}

.slide-horizontal-enter-active,
.slide-horizontal-leave-active {
	transition: all 0.6s;
}

.slide-horizontal-leave,
.slide-horizontal-enter-to {
	max-height: 600px;
	overflow: hidden;
}

.slide-horizontal-leave {
	opacity: 0;
}

.slide-horizontal-enter,
.slide-horizontal-leave-to {
	overflow: hidden;
	max-height: 0;
}

.slide-horizontal-leave-to {
	opacity: 0;
}
</style>
