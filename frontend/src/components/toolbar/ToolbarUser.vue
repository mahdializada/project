<template>
	<div>
		<ProfileUserEdit @changeImage="changeImage" />

		<v-menu offset-y left transition="slide-y-transition">
			<template v-slot:activator="{ on }">
				<v-btn icon class="elevation-2" v-on="on">
					<v-badge color="success" dot bordered offset-x="10" offset-y="10">
						<v-avatar size="40">
							<v-img :src="image1"></v-img>
						</v-avatar>
					</v-badge>
				</v-btn>
			</template>

			<!-- user menu list -->
			<v-list dense nav>
				<v-list-item link @click.prevent="seProfileUser(true)">
					<v-list-item-icon>
						<v-icon small>mdi-account-box-outline</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>{{ $tr("profile") }}</v-list-item-title>
					</v-list-item-content>
				</v-list-item>

				<!-- <v-list-item
          v-for="(item, index) in menu"
          :key="index"
          :to="item.link"
          :exact="item.exact"
          :disabled="item.disabled"
          link
        >
          <v-list-item-icon>
            <v-icon small :class="{ 'grey--text': item.disabled }"
              >{{ item.icon }}
            </v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>{{
              item.key ? $tr(item.key) : item.text
            }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item> -->

				<v-divider class="my-1"></v-divider>

				<v-list-item link @click.prevent="logout">
					<v-list-item-icon>
						<v-icon small>mdi-logout-variant</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>{{ $tr("logout") }}</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
			</v-list>

			<ProgressBar v-if="isLoggingOut" />
		</v-menu>
	</div>
</template>
<script>
import config from "../../configs";
import Alert from "~/helpers/alert";
import ProgressBar from "../common/ProgressBar";
import ProfileUserEdit from "../../components/users/profileEdit.vue";
import { mapActions, mapGetters } from "vuex";
/*
|---------------------------------------------------------------------
| Toolbar User Component
|---------------------------------------------------------------------
|
| Quick menu for user menu shortcuts on the toolbar
|
*/
export default {
	components: { ProgressBar, ProfileUserEdit },
	data() {
		return {
			dialog: false,
			authUser: this.$auth.user,
			menu: config.toolbar.user,
			isLoggingOut: false,
			image1: this.$auth.user.image,
		};
	},

	methods: {
		...mapActions({
			seProfileUser: "users/seProfileUser",
		}),
		changeImage(image) {
			this.image1 = image;
		},

		logout() {
			this.$TalkhAlertNA(
				"Do you want to logout ?",
				"logout",
				async () => {
					try {
						await this.$auth.logout();
						console.log("logout");
					} catch (e) {
						console.log("error", e);
					}
					if (process.client) {
						localStorage.removeItem("company_popup_dialog");
					}
					location.reload();
					this.isLoggingOut = false;
					localStorage.removeItem("global_theme");
				},
				"logout",
			);
			// Alert.logoutDialogAlert(this, async() => {
			//   this.isLoggingOut = true;
			//   try {
			//     await this.$auth.logout();
			//     console.log("logout");
			//   } catch (e) {
			//     console.log("error", e);
			//   }
			//   if (process.client) {
			//     localStorage.removeItem("company_popup_dialog");
			//   }
			//   location.reload();
			//   this.isLoggingOut = false;
			// });
		},
		openDialog() {
			this.dialog = !this.dialog;
		},
	},
};
</script>
