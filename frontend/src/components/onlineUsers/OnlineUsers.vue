<template>
  <div>
    <v-btn
      v-if="!right"
      ref="button"
      class="drawer-button"
      color="primary"
      dark
      @click="getData"
    >
      <v-icon left>mdi-account-group-outline</v-icon>
      {{ $tr("online_users") }}
    </v-btn>

    <v-navigation-drawer
      v-model="right"
      fixed
      :right="!$vuetify.rtl"
      hide-overlay
      width="310"
      style="top: unset; height: 55%; bottom: 0"
    >
      <v-card max-width="310" class="mx-auto">
        <v-card-title
          class="py-0 d-flex align-center"
          style="position: sticky; top: 0; z-index: 10000000"
          :style="`background: ${$vuetify.theme.dark ? '#000000' : '#FFFFFF'}`"
        >
          <v-app-bar-title>
            <span>{{ $tr("online_users") }} </span>
          </v-app-bar-title>
          <v-spacer />
          <CloseBtn @click="() => (right = !right)" />
        </v-card-title>

        <v-list three-line>
          <v-card
            v-for="(user, index) in onlineUsers"
            :color="$vuetify.theme.dark ? 'black' : '#f9fafd'"
            class="rounded-sm"
            :key="index + 1"
            elevation="0"
          >
            <v-list-item :key="user.title" class="px-1">
              <template>
                <v-list-item-avatar color="ms-1">
                  <v-img
                    :src="user.image"
                    :alt="`${user.title} avatar`"
                  ></v-img>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title>
                    {{ user.firstname + " " + user.lastname }}
                    <v-badge
                      style="padding-top: 3px !important"
                      class="ma-0 ms-1"
                      color="green"
                      dot
                    >
                    </v-badge>
                  </v-list-item-title>
                  <v-list-item-subtitle
                    v-text="user.location.countryName"
                  ></v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                  <FlagIcon
                    class="me-1"
                    :flag="
                      user.location.countryCode
                        ? user.location.countryCode.toLowerCase()
                        : ''
                    "
                    round
                  />
                </v-list-item-action>
              </template>
            </v-list-item>
            <v-divider
              v-if="index < onlineUsers.length - 1"
              :key="index"
            ></v-divider>
          </v-card>
        </v-list>
      </v-card>
    </v-navigation-drawer>
  </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from "vuex";
import CloseBtn from "../../components/design/Dialog/CloseBtn.vue";
import moment from "moment-timezone";
import FlagIcon from "../common/FlagIcon.vue";

export default {
  data() {
    return {
      right: false,
      interval: null,
      background: this.$vuetify.theme.dark ? "#FFC107" : "#E91E63",
      gettingLocation: false,
      location: null,
      errorStr: null,
      timeout: null,
      fetch: true,
    };
  },
  components: {
    FlagIcon,
    CloseBtn,
  },
  async mounted() {
    // this.timeout = setInterval(async () => {
    // 	await this.getGeolocation();
    // }, 60000 * 30);  // half an hour
    this.$echo
      .join(`online`)
      .here((users) => {
        this.setOnlineUsers(users);
      })
      .joining((user) => {
        this.addOnlineUser(user);
      })
      .leaving((user) => {
        this.removeOnlineUser(user);
      })
      .error((error) => {});
  },
  beforeDestroy() {
    clearInterval(this.timeout);
  },
  methods: {
    getData() {
      this.right = !this.right;
    },
    ...mapMutations({
      setOnlineUsers: "users/SET_ONLINE_USERS_DATA",
      addOnlineUser: "users/ADD_ONLINE_USER",
      removeOnlineUser: "users/REMOVE_ONLINE_USER",
    }),
    getTime(time) {
      return moment(time).locale(this.$vuetify.lang.current).fromNow(true);
    },
    async getGeolocation() {
      if (!("geolocation" in navigator)) {
        this.errorStr = "Geolocation is not available.";
      } else {
        this.gettingLocation = true;
        await navigator.geolocation.getCurrentPosition(
          async (pos) => {
            this.gettingLocation = false;
            this.location = pos;
            if (this.fetch) {
              await this.$axios.post("users/update-location", {
                latitude: pos.coords.latitude,
                longitude: pos.coords.longitude,
              });
              this.fetch = false;
            }
          },
          async (err) => {
            this.gettingLocation = false;
            this.errorStr = err.message;
            return this.$nuxt.error({
              statusCode: 403,
            });
          }
        );
      }
    },
  },
  computed: {
    ...mapGetters({
      onlineUsers: "users/onlineUsers",
    }),
  },
};
</script>

<style lang="scss" scoped>
.drawer-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 6;
  box-shadow: 1px 1px 8px var(--v-primary-base) !important;
  .v-icon {
    font-size: 1.3rem;
  }
}
.v-application--is-rtl {
  .drawer-button {
    left: 20px;
    right: unset;
  }
}
</style>
