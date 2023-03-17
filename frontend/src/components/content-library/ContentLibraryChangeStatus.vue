<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="800" persistent>
      <v-card>
        <v-card-title class="">
          {{ $tr("status") }}
          <v-spacer />
          <DialogCloseBtn class="close-dialog-btn" @click="closeDialog" />
        </v-card-title>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-text style="max-height: 800px; overflow-y: scroll">
            {{ $tr("change_status") }}
            <br />
            Lorem ipsum dolor sit amet, consectetur adipisicing elit.
            <div>
              <v-divider class="my-2" />
              <div class="h-full d-flex">
                <div class="w-full position-relative">
                  <v-avatar size="32" class="avatar">
                    <v-icon color="white" size="20">mdi-video</v-icon>
                  </v-avatar>
                  <span style="position: absolute" class="ms-1">{{
                    seletedItem?.content_library?.item_code
                  }}</span>
                  <span style="position: absolute" class="mt-3 ms-1">{{
                    seletedItem?.content_library?.item_name
                  }}</span>
                </div>
                <div class="w-ful">
                  <v-select
                    :items="status"
                    v-model="selectedStatus"
                    @change="changed"
                    item-value="id"
                    item-text="value"
                    style="width: 200px"
                    :placeholder="$tr('select_status')"
                    dense
                    outlined
                    :rules="[
                      (v) => !!v || $tr('item_is_required', $tr('status')),
                    ]"
                    append-icon="mdi-chevron-down"
                    :menu-props="{ bottom: true, offsetY: true }"
                    hide-details="auto"
                  >
                    <template v-slot:[`selection`]="{ item }">
                      <div>
                        {{ $tr(item?.value) }}
                      </div>
                    </template>
                    <template v-slot:[`item`]="{ item }">
                      <v-list-item-content>
                        <div>
                          <v-list-item-title>
                            <v-icon>{{ item?.icon }}</v-icon>
                            {{ $tr(item?.value) }}
                          </v-list-item-title>
                        </div>
                      </v-list-item-content>
                    </template>
                  </v-select>
                </div>
              </div>

              <div class="h-full d-flex mt-2" :key="refresh">
                <v-expansion-panels flat>
                  <v-expansion-panel>
                    <v-expansion-panel-header hide-actions>
                      <template v-slot:default="{ open }">
                        <p style="z-index: 2">
                          {{ $tr("choose_item", $tr("reasons")) }}
                        </p>
                        <v-icon
                          :class="`${$vuetify.rtl ? 'chevronRtl' : 'chevron'}`"
                          >{{
                            open
                              ? "mdi-chevron-up-circle-outline"
                              : "mdi-chevron-down-circle-outline"
                          }}</v-icon
                        >
                        <span
                          :class="`${open ? 'borderStyle1' : 'borderStyle'}`"
                        >
                        </span>
                      </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                      <div
                        class="pa-1"
                        :style="`height: 140px; overflow-y: ${
                          allReasons.length > 0 ? 'scroll' : 'hidden'
                        }`"
                      >
                        <div v-if="skeletone">
                          <v-skeleton-loader
                            height="30px"
                            v-for="i in 3"
                            :key="i"
                            type="image"
                            class="mb-1"
                          ></v-skeleton-loader>
                        </div>
                        <div
                          v-else-if="allReasons.length == 0"
                          class="text-center"
                        >
                          {{ $tr("no_reasons_yet") }}
                        </div>
                        <div v-else>
                          <span v-for="item in allReasons" :key="item.id">
                            <v-checkbox
                              style="border: 1px solid lightgray"
                              class="pa-1 mb-1 rounded-lg"
                              multiple
                              dense
                              v-model="reason_id"
                              hide-details
                              :value="item.reason_id"
                              :label="item.title"
                            ></v-checkbox>
                          </span>
                        </div>
                      </div>
                    </v-expansion-panel-content>
                  </v-expansion-panel>
                </v-expansion-panels>
              </div>
            </div>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" outlined @click="closeDialog">{{
              $tr("close")
            }}</v-btn>
            <v-btn color="primary" @click="onSave" :loading="loading">{{
              $tr("save")
            }}</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>
    <script>
import DialogCloseBtn from "../design/Dialog/CloseBtn.vue";
export default {
  components: {
    DialogCloseBtn,
  },
  data() {
    return {
      dialog: false,
      seletedItem: null,
      panel: 1,
      status: [],
      allReasons: [],
      reason_id: [],
      selectedStatus: "",
      tab: "",
      valid: true,
      loading: false,
      skeletone: false,
      refresh: 0,
    };
  },
  methods: {
    async fetchAllReasons(type = "rejected") {
      try {
        this.skeletone = true;
        const response = await this.$axios.get(
          `reasons/id?type=${type}&sub_system=Content library`
        );
        this.skeletone = false;
        this.allReasons = response?.data?.reasons;
      } catch (error) {
        if (error?.response?.status === 404 && !error?.response?.data?.result)
          this.$toasterNA("orange", this.$tr("sub_system_not_found"));
      }
    },
    changed(item) {
      this.allReasons = [];
      this.fetchAllReasons(item);
    },
    async onSave() {
      if (!this.$refs.form.validate()) return;
      if (!this.reason_id.length > 0) {
        this.$toasterNA(
          "red",
          this.$tr(
            "please_select_reasons",
            this.$tr(
              this.selectedStatus == "not publish"
                ? "not_publish"
                : this.selectedStatus
            )
          )
        );
        return;
      }
      try {
        const data = {
          item_id: this.seletedItem.id,
          reason_id: this.reason_id,
          tab: this.tab,
          status: this.selectedStatus,
        };
        this.loading = true;
        const response = await this.$axios.post("library/change-status", data);
        if (response.status == 200) {
          this.$emit("changeStatus", this.seletedItem.id);
          this.closeDialog();
          this.loading = false;
        }
      } catch (error) {
        console.log(error);
        this.loading = false;
      }
    },
    fillStatus() {
      if (this.tab == "publish" || this.tab == "not publish") {
        this.status = [
          { id: "rejected", value: "rejected", icon: "mdi-folder-remove" },
        ];
      }else if (this.tab == "rejected") {
        if (this.seletedItem.used_in_advertisement == 1)
          this.status = [
            { id: "publish", value: "publish", icon: "mdi-folder-check" },
          ];
        else
          this.status = [
            {
              id: "not publish",
              value: "not_publish",
              icon: "mdi-folder-open",
            },
          ];
      }
    },
    openDialog(item, tabKey) {
      this.tab = tabKey;
      this.seletedItem = item[0];
      this.fillStatus();
      this.dialog = true;
    },
    reset() {
      this.reason_id = [];
      this.$refs.form.reset();
      this.selectedStatus = "";
      this.refresh++;
      this.allReasons = [];
    },
    closeDialog() {
      this.reset();
      this.dialog = false;
    },
  },
};
</script>
  <style scoped>
.avatar {
  background: linear-gradient(to right, #2e7be6, #2ebae6);
}
.chevron {
  position: absolute;
  left: 19px;
  z-index: 1;
  padding-left: 125px;
  padding-right: 3px;
  background: white;
  top: 10px;
}
.chevronRtl {
  position: absolute;
  right: 19px;
  z-index: 1;
  padding-right: 125px;
  padding-left: 3px;
  background: white;
  top: 10px;
}
.borderStyle {
  position: absolute;
  width: 100%;
  top: 22px;
  opacity: 0;
  left: 0px;
  border-radius: 10px;
  height: 0px;
  border: 1px solid lightgray;
  transition: all 0.5s ease;
}
.borderStyle1 {
  position: absolute;
  width: 100%;
  top: 22px;
  left: 0px;
  border-radius: 10px;
  opacity: 1;
  height: 210px;
  border: 1px solid lightgray;
  transition: all 0.5s ease;
}
</style>
