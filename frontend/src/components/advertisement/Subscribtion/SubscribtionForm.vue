<template>
  <v-dialog v-model="showDialog" width="800" persistent>
    <v-card v-if="this.showDialog">
      <v-card-title class="pa-2 pb-1" style="color: #777">
        <v-avatar size="40" class="me-1" color="black">
          <v-img :src="application.platform.logo"></v-img>
        </v-avatar>
        <span> {{ application.name }}</span>
        <v-spacer />
        <svg
          @click="closeDialog()"
          width="48px"
          height="48px"
          viewBox="0 0 700 560"
          fill="currentColor"
          style="transform: scaleY(-1); cursor: pointer"
        >
          <g>
            <path
              d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
            />
            <path
              d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
            />
          </g>
        </svg>
      </v-card-title>
      <v-card-text>
        <div class="pt-2">
          <p class="mb-1 custom-card-title-1">{{ $tr("ad_accounts") }}</p>
          <p class="custom-card-title-2">
            {{ $tr("subscribe accounts to recieve orders from tiktok") }}
          </p>
          <v-divider></v-divider>
        </div>
        <div
          v-show="loading_api"
          style="height: 400px; overflow: auto"
          class="pt-3"
        >
          <v-skeleton-loader
            v-for="item in 5"
            :key="item"
            class="mb-3"
            type="list-item-avatar, divider"
          ></v-skeleton-loader>
        </div>

        <v-list
          v-show="!loading_api"
          two-line
          style="height: 400px; overflow: auto"
        >
          <template v-for="(account, index) in ad_accounts">
            <div :key="index">
              <v-divider :inset="true" v-if="index > 0"></v-divider>
              <v-list-item>
                <v-avatar color="grey lighten-3" size="50" class="my-2 me-2">
                  <v-avatar color="primary" size="35">
                    <v-icon color="white">mdi-account</v-icon>
                  </v-avatar>
                </v-avatar>

                <v-list-item-content>
                  <v-list-item-title v-html="account.name"></v-list-item-title>
                </v-list-item-content>
                <v-list-item-action>
                  <v-btn
                    v-if="account.subcribtion"
                    outlined
                    color="primary"
                    class="btn-text px-2"
                    :loading="subscribtion_loading"
                    @click="toggleSubcribtion(account.subcribtion, index)"
                    ><v-icon left>mdi-bell-off</v-icon> unsubscribe
                  </v-btn>
                  <v-btn
                    v-else
                    :loading="subscribtion_loading"
                    class="primary btn-text px-2"
                    @click="toggleSubcribtion(account.subcribtion, index)"
                    ><v-icon left>mdi-bell</v-icon> subscribe
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </div>
          </template>
        </v-list>
      </v-card-text>
      <v-card-actions class="pa-3">
        <v-spacer></v-spacer>
        <v-btn
          outlined
          color="primary"
          class="btn-text px-1"
          @click="closeDialog()"
          >{{ $tr("cancel") }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data() {
    return {
      showDialog: false,
      loading_api: false,
      application: null,
      subscribtion_loading: false,
      ad_accounts: [],
      selected_account: [],
    };
  },
  methods: {
    openDialog(item) {
      if (item.platform.platform_name != "tiktok") {
        this.$toasterNA("error", this.$tr("no_subscribtion_for_this_account"));
        return;
      }
      this.showDialog = true;
      this.application = item;
      console.log("applicatoin", this.application);
      this.getAdAccounts();
    },
    closeDialog() {
      this.showDialog = false;
    },
    async getAdAccounts() {
      try {
        this.loading_api = true;
        const response = await this.$axios.get(
          `advertisement/application-ad-accounts/${this.application.id}`
        );

        this.ad_accounts = response.data;
        console.log("ad accounts", response.data);
      } catch (error) {}
      this.loading_api = false;
    },
    async toggleSubcribtion(subscrition, index) {
      try {
        this.subscribtion_loading = true;

        const response = await this.$axios.post(
          `advertisement/application-subcribtion/${this.ad_accounts[index].id}`
        );
        if (response.status == 200) {
          this.$toasterNA("primary", this.$tr("successfully_subscribed"));

          if (subscrition) {
            this.ad_accounts[index].subcribtion = null;
          } else {
            this.ad_accounts[index].subcribtion = true;
          }
        } else {
          this.$toasterNA("error", this.$tr("something_wenth_wrong"));
        }
      } catch (error) {
        this.$toasterNA("error", this.$tr("something_wenth_wrong"));

        console.log("error", error);
      }
      this.subscribtion_loading = false;
    },
  },
};
</script>

<style scoped>
.custom-card-title-1 {
  font-size: 17px;
  font-weight: 500;
  color: #777;
}

.custom-card-title-2 {
  font-size: 16px;
  font-weight: 400;
  color: #777;
}
.btn-text {
  font-size: 16px;
  font-weight: 400;
}
</style>