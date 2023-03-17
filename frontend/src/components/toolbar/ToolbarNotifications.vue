<template>
	<v-menu
		offset-y
		left
		:close-on-content-click="false"
		transition="slide-y-transition"
	>
		<template v-slot:activator="{ on }">
			<v-badge
				bordered
				:content="notSeenCount"
				:value="notSeenCount"
				offset-x="22"
				offset-y="22"
			>
				<v-btn icon v-on="on">
					<v-icon>mdi-bell-outline</v-icon>
				</v-btn>
			</v-badge>
		</template>

		<!-- dropdown card -->
		<v-card>
			<div class="d-flex align-center justify-space-between">
				<h5 class="pa-2 font-weight-bold">{{ $tr("notifications") }}</h5>
				<v-btn
					v-if="notSeenCount > 0"
					text
					small
					@click="markAllRead"
					class="me-2"
				>
					{{ $tr("mark_all_read") }}
				</v-btn>
			</div>
			<v-list
				three-line
				dense
				max-width="400"
				min-width="300"
				style="max-height: 364px; overflow-y: auto"
			>
				<div v-for="(item, index) in notifications" :key="index">
					<v-divider
						v-if="index > 0 && index < notifications.length"
						inset
					></v-divider>
					<v-list-item
						nuxt
						:to="getLink(item)"
						@click="changeSeen(item)"
						:class="`${item.seen == 0 ? 'primary lighten-5' : ''}`"
						active-class="custom-active"
					>
						<v-list-item-avatar
							size="32"
							:color="
								item.notification.type == 'notify'
									? 'primary'
									: item.notification.type == 'success'
									? 'teal lighten-1'
									: item.notification.type == 'error'
									? 'error'
									: item.notification.type == 'warning'
									? 'warning darken-1'
									: ''
							"
						>
							<v-icon dark small>{{ item.notification.icon }}</v-icon>
						</v-list-item-avatar>

						<v-list-item-content>
							<v-list-item-title
								v-text="$tr(item.notification.title, ...item.title_args)"
							>
							</v-list-item-title>
							<v-list-item-subtitle
								class="caption"
								v-text="
									$tr(item.notification.description, ...item.content_args)
								"
							>
							</v-list-item-subtitle>
						</v-list-item-content>
						<v-list-item-action class="align-self-center">
							<v-list-item-action-text v-text="getTime(item.created_at)">
							</v-list-item-action-text>
						</v-list-item-action>
					</v-list-item>
				</div>
				<div v-if="loading">
					<v-skeleton-loader
						v-for="i in 10"
						:key="i"
						v-bind="{
							class: '',
							boilerplate: true,
							elevation: 2,
						}"
						type="list-item-avatar-three-line"
					></v-skeleton-loader>
				</div>
			</v-list>

			<div class="text-center py-2">
				<v-btn
					small
					@click="loadMore"
					v-if="allCount > notifications.length && !loading"
					>{{ $tr("see_more") }}</v-btn
				>
				<v-btn small v-if="allCount <= notifications.length && allCount == 0">
					{{ $tr("no_items_found") }}
				</v-btn>
				<v-btn small v-if="loading">{{ $tr("loading") }}</v-btn>
			</div>
		</v-card>
	</v-menu>
</template>

<script>
/*
|---------------------------------------------------------------------
| Toolbar Notifications Component
|---------------------------------------------------------------------
|
| Quickmenu to check out notifications
|
*/
import moment from "moment-timezone";

export default {
	async created() {
		await this.fetchData();
	},
	mounted() {
		this.$echo
			.private(`notifications.${this.$auth.user.id}`)
			.listen("SendNotification", async (e) => {
				this.notifications.unshift(e.notification);
				this.notSeenCount++;
				this.allCount++;
				this.playSound();
			});
	},
	data() {
		return {
			notifications: [],
			page: 1,
			loading: false,
			notSeenCount: 0,
			allCount: 0,
		};
	},
	methods: {
		async fetchData() {
			this.loading = true;
			try {
				const response = await this.$axios.get(`notifications`, {
					params: { page: this.page },
				});
				if (response.status == 200) {
					this.notifications = [...this.notifications, ...response?.data?.data];
					this.notSeenCount = response?.data?.notSeenCount;
					this.allCount = response?.data?.total;
				}
			} catch (e) {}
			this.loading = false;
		},

	async changeSeen(item) {
			if (item.seen == 0) {
				item.seen = 1;
				try {
					const response = await this.$axios.post(`notifications/seen`, {
						items: [item.id],
					});
					if (response.status == 200) {
						if (this.notSeenCount != 0) {
							this.notSeenCount--;
						}
					}
				} catch (e) {
					item.seen = 0;
				}
			}
		},

		async markAllRead() {
			try {
				const response = await this.$axios.post(`notifications/seen`, {
					all: true,
				});
				if (response.status == 200) {
					this.notSeenCount = 0;
					this.notifications.map((item) => {
						if (item.seen == 0) {
							item.seen = 1;
						}
						return item;
					});
				}
			} catch (e) {}
		},

		playSound() {
			const audio = new Audio("/sounds/notification.mp3");
			audio.play();
		},

		loadMore() {
			this.page++;
			this.fetchData();
		},
		getLink(item) {
			if (item.meta?.system == "My Order") {
				return "/landing-page/orders/" + item.meta?.order_id;
			} else if (item.meta?.system == "Design Request") {
				return "/landing-page/design-request/" + item.meta?.design_request_id;
			} else if (item.meta?.system == "Sharing") {
				return `/files/personal/shared/`;
			} else if (item.meta?.system == "personal-drive") {
				return `/files/personal/drive/${
					item.meta?.item_parent ? item.meta?.item_parent : ""
				}?preview=true&preview_id=${item.meta?.item}`;
			} else if (item.meta?.system == "personal-shared") {
				return `/files/personal/shared/${
					item.meta?.item_parent ? item.meta?.item_parent : ""
				}?preview=true&preview_id=${item.meta?.item}`;
			}
			return "";
		},

		getTime(time) {
			return moment(time).locale(this.$vuetify.lang.current).fromNow(true);
		},
	},
};
</script>
<style>
.custom-active::before {
	opacity: 0 !important;
}
.theme--light.custom-active:hover::before {
	opacity: 0.04 !important;
}
.custom-active .v-icon {
	color: #fff !important;
}
</style>
