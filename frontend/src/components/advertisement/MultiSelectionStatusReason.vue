<template>
  <v-dialog v-model="model" persistent width="1200" scrollable>
    <v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
      <v-card-title
        class=""
        style="border-bottom: 1px solid var(--v-surface-darken2)"
      >
        <v-app-bar-title>
          <span class="custom-dialog-title">{{ $tr("reasons") }}</span>
        </v-app-bar-title>
        <v-spacer />
        <CloseBtn @click="toggleDialog" />
      </v-card-title>

      <v-card-text class="pa-0">
        <div
          style="height: 500px"
          v-if="submiting"
          class="d-flex align-center justify-center"
        >
          <div
            class="connection__container pa-5 d-flex align-center flex-column justify-center"
            v-if="!successMessage"
          >
            <lottie-player
              src="/assets/man-on-rocket.json"
              background="transparent"
              speed="3"
              style="width: 300px; height: 300px"
              loop
              autoplay
            >
            </lottie-player>
            <h2 class="pa-2 primary--text">
              {{
                // $tr("pulish_ads", published, form.$model.ads.length) +
                "Processing ..."
              }}
            </h2>
          </div>

          <div v-show="successMessage">
            <div class="d-flex align-center justify-center flex-column">
              <lottie-player
                src="/assets/done_animation.json"
                background="transparent"
                speed="1"
                loop
                autoplay
                style="width: 300px; height: 300px"
              >
              </lottie-player>

              <div
                class="friqiBase--text"
                style="font-size: 30px; font-weight: 600"
              >
                {{ $tr("successfully_inserted") }}
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex" style="height: 500px" v-if="!submiting">
          <div class="white" style="width: 25%; height: 100%">
            <div
              class="pa-2 primary--text d-flex align-center"
              style="border-bottom: 1px solid var(--v-surface-darken2)"
            >
              <v-icon color="primary" @click="toggleSelectAll()">{{
                select_all
                  ? "mdi-checkbox-marked"
                  : "mdi-checkbox-blank-outline"
              }}</v-icon>
              <p class="mb-0 ps-1">Select All</p>
            </div>
            <div style="height: 440px; overflow-y: auto" class="">
              <v-list dense class="">
                <v-list-item-group
                  v-model="selected_lists"
                  color="primary"
                  :multiple="select_all"
                  :aria-disabled="select_all"
                  mandatory
                >
                  <v-list-item v-for="(item, index) in items" :key="index">
                    <v-list-item-icon>
                      <v-icon>mdi-checkbox-marked</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title>
                        {{ getItemName(item).name }}</v-list-item-title
                      >
                    </v-list-item-content>
                    <v-list-item-action
                      ><v-icon>mdi-chevron-right</v-icon>
                    </v-list-item-action>
                  </v-list-item>
                </v-list-item-group>
              </v-list>
            </div>
          </div>
          <div class="" style="width: 75%; height: 100%; background: #f2f2f2">
            <div class="pa-3 pb-0 pe-1">
              <p style="font-size: 24px; font-weight: 600">select reason</p>
              <v-skeleton-loader
                v-if="isFetchingReasons"
                v-bind="attrs"
                type="list-item-two-line"
              ></v-skeleton-loader>
              <div style="height: 430px; overflow-y: auto" class="pe-2">
                <div
                  v-for="(item, index) in allReasons"
                  :key="index"
                  style="min-height: 40px"
                  class="w-full white rounded d-flex align-center mb-2 cursor-pointer"
                  @click="addReason(index)"
                >
                  <div
                    style="min-height: 40px; width: 5%"
                    class="d-flex align-center justify-center"
                  >
                    <v-icon>{{
                      checkAssign(item.reason_id)
                        ? "mdi-checkbox-marked"
                        : "mdi-checkbox-blank-outline"
                    }}</v-icon>
                  </div>
                  <div
                    class="pa-2"
                    style="
                      width: 95%;
                      font-size: 16px;
                      font-weight: 500;
                      border-left: 1px solid var(--v-surface-darken2);
                    "
                  >
                    {{ item.title }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </v-card-text>
      <v-card-actions
        class="d-flex justify-end"
        style="border-top: 1px solid var(--v-surface-darken2)"
      >
        <v-btn
          outlined
          color="primary"
          v-if="isSubmiting"
          @click="toggleDialog"
        >
          {{ $tr("cancel") }}
        </v-btn>
        <v-btn class="primary" v-if="isSubmiting" @click="submitReasons()">{{
          $tr("submit")
        }}</v-btn>
        <v-btn class="primary" v-if="done" @click="doneStatus">{{
          $tr("done")
        }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import EditReasonNewVue from "../statusManagement/reasons/EditReasonNew.vue";
import CloseBtn from "./../../components/design/Dialog/CloseBtn.vue";
import ToggleItemStatus from "./toggle-status/ToggleItemStatus.vue";

export default {
  components: { CloseBtn, ToggleItemStatus },
  props: {
    currentTab: Object,
  },
  data() {
    return {
      done: false,
      isSubmiting: true,
      successMessage: false,
      model: false,
      select_all: false,
      items: [],
      selected_lists: [0],
      allReasons: [],
      submiting: false,
      isFetchingReasons: true,
      attrs: {
        class: "mb-1",
        boilerplate: true,
        elevation: 1,
      },
    };
  },
  computed: {},
  methods: {
    toggleDialog(items, new_status) {
      this.new_status = new_status;
      if (this.model == false) {
        for (let index = 0; index < items.length; index++) {
          items[index].reasons = [];
        }
        console.log(items);
        this.items = items;
        this.fetchAllReasons(new_status);
        this.model = true;
      } else {
        this.model = false;
        this.selected_lists = 0;
        this.items = [];
        this.select_all = false;
        this.allReasons = [];
        this.isFetchingReasons = true;
        this.successMessage = false;
      }
    },
    doneStatus() {
      this.submiting = false;
      this.model = false;
      this.successMessage = false;
      this.isSubmiting = true;
      this.done = false;
    },
    async fetchAllReasons(status) {
      try {
        const response = await this.$axios.get(
          `reasons/id?type=${status}&sub_system=advertisement&tab_name=${this.tabName}`
        );
        this.allReasons = response?.data?.reasons;
        this.isFetchingReasons = false;
      } catch (error) {
        if (error?.response?.status === 404 && !error?.response?.data?.result)
          this.$toasterNA("orange", this.$tr("sub_system_not_found"));
      }
    },
    getItemName(item) {
      switch (this.currentTab.name) {
        case "country":
          return {
            name: item.name,
            avatar: item.iso2.toLowerCase(),
          };
        case "company":
          return {
            name: item.name,
            avatar: item.logo,
          };
        case "platform":
          return {
            name: item.platform_name,
            avatar: item.logo,
          };
        case "item_code":
          return {
            name: item.pname,
            avatar: item.pname.charAt(0),
          };

        case "ispp_code":
          return item.ispp_code;

        case "ad":
          return {
            name: item.ad_name,
            avatar: item.ad_name.charAt(0),
          };
        default:
          return {
            name: item?.name,
            avatar: item?.name?.charAt(0),
          };
      }
    },

    async submitReasons() {
      if (this.submiting) return;
      this.submiting = true;
      const currentTab = this.currentTab.key;
      const statusTabs = ["campaign", "ad_set", "ad"];
      if (statusTabs.includes(currentTab)) {
        this.changeAdvertisementStatus();

        return;
      }
      const data = {
        model: this.currentTab.key,
        id: "",
        status: this.new_status,
        reason_ids: [],
        multipleItems: this.items,
      };
      try {
        const response = await this.$axios.post("assign-reason", data);
        if (response.status == 200) {
          this.successAnimation();

          this.items = [];
          this.$emit("MultiStatusChange");
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
        this.submiting = false;
      }
    },

    async changeAdvertisementStatus() {
      try {
        const body = {
          section: this.currentTab.key,
          status: this.newStatus == "active" ? "ACTIVE" : "INACTIVE",
          multipleItems: this.items,
        };
        const url = `/advertisement/change-status`;
        const { data } = await this.$axios.put(url, body);
        if (data.status == "success") {
          const message = this.$tr(
            "record_item_status_changed",
            this.$tr(data.type)
          );
          this.successAnimation();
        } else {
          this.$toasterNA("red", this.$tr(data.status));
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
        this.submiting = false;
      }
    },

    addReason(index) {
      try {
        if (!this.select_all) {
          if (!this.checkAssign(this.allReasons[index].reason_id)) {
            this.items[this.selected_lists].reasons.push(
              this.allReasons[index].reason_id
            );
            this.items = [...this.items];
          } else {
            this.items[this.selected_lists].reasons = this.items[
              this.selected_lists
            ].reasons.filter((row) => row != this.allReasons[index].reason_id);
            console.log("selected_lists", this.items);
            this.items = [...this.items];
          }
        } else {
          console.log("items", this.items);
          for (let indx = 0; indx < this.items.length; indx++) {
            if (this.checkAssign(this.allReasons[index].reason_id)) {
              this.items[indx].reasons = this.items[indx].reasons.filter(
                (row) => row != this.allReasons[index].reason_id
              );
            } else {
              this.items[indx].reasons.push(this.allReasons[index].reason_id);
            }
          }
          this.items = [...this.items];
        }
      } catch (error) {
        console.log("error", error);
      }
    },

    toggleSelectAll() {
      console.log("selected item", this.selected_lists);
      this.selected_lists = 0;
      this.select_all = !this.select_all;
      if (this.select_all) {
        this.selected_lists = [];
        for (let index = 0; index < this.items.length; index++) {
          this.selected_lists.push(index);
        }
        this.selected_lists = [...this.selected_lists];
      }
      for (let index = 0; index < this.items.length; index++) {
        this.items[index].reasons = [];
      }
    },

    checkAssign(id) {
      if (!this.model) return false;
      let result = false;
      try {
        if (!this.select_all) {
          if (this.items[this.selected_lists].reasons.includes(id))
            result = true;
        } else {
          if (this.items[0].reasons.includes(id)) result = true;
        }
      } catch (error) {
        console.log("error", error);
      }
      return result;
    },

    successAnimation() {
      this.successMessage = true;
      this.isSubmiting = false;
      this.done = true;
      setTimeout(() => {
        this.submiting = false;
        this.model = false;
        this.successMessage = false;
        this.isSubmiting = true;
        this.done = false;
      }, 2000);
    },
  },
};
</script>
<style>
.reason:hover {
  cursor: pointer;
}
.borderDe {
  border: 1px solid var(--v-surface-darken2);
}
.reason.blue.lighten-5 {
  border-color: var(--v-primary-base) !important;
}
.active-list {
  border: 1px solid var(--v-primary-base) !important;
}
</style>
