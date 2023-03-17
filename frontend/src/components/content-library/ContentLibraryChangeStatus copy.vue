<template>
    <div class="text-center" :key="refersh">
      <v-dialog v-model="dialog" width="800" persistent>
        <v-card>
          <v-card-title class="">
            {{ $tr("status") }}
            <v-spacer />
            <DialogCloseBtn class="close-dialog-btn" @click="closeDialog" />
          </v-card-title>
          <v-card-text style="max-height: 500px; overflow-y: scroll">
            {{ $tr("change_status") }}
            <br />
            Lorem ipsum dolor sit amet, consectetur adipisicing elit.
            <div v-for="(content, i) in seletedItem" :key="i">
              <v-divider class="my-2" />
              <div class="h-full d-flex">
                <div class="w-full position-relative">
                  <v-avatar size="32" class="avatar">
                    <v-icon color="white" size="20">mdi-video</v-icon>
                  </v-avatar>
                  <span style="position: absolute" class="ms-1">{{
                    content.content_library.item_code
                  }}</span>
                  <span style="position: absolute" class="mt-3 ms-1">{{
                    content.content_library.item_name
                  }}</span>
                </div>
                <div class="w-ful">
                  <v-select
                    :items="status"
                    style="width: 200px"
                    :placeholder="$tr('select_status')"
                    dense
                    outlined
                    append-icon="mdi-chevron-down"
                    :menu-props="{ bottom: true, offsetY: true }"
                    hide-details="auto"
                  >
                    <template v-slot:[`selection`]="{ item }">
                      <div>
                        {{ $tr(item.value) }}
                      </div>
                    </template>
                    <template v-slot:[`item`]="{ item }">
                      <v-list-item-content>
                        <div>
                          <v-list-item-title
                            @click="change(item.id, content.id)"
                          >
                            <v-icon>{{ item.icon }}</v-icon> {{ $tr(item.value) }}
                          </v-list-item-title>
                        </div>
                      </v-list-item-content>
                    </template>
                  </v-select>
                </div>
              </div>
              <div
                class="h-full d-flex mt-2"
                v-if="changeStatus[i]?.status == 'rejected'"
              >
                <v-expansion-panels flat>
                  <v-expansion-panel>
                    <v-expansion-panel-header hide-actions @click="openReason">
                      <template v-slot:default="{ open }">
                        <p style="z-index: 2">
                          {{ $tr("choose_item", $tr("reasons")) }}
                        </p>
                        <v-icon class="chevron">{{
                          open
                            ? "mdi-chevron-up-circle-outline"
                            : "mdi-chevron-down-circle-outline"
                        }}</v-icon>
                      </template>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                      <span :class="`${isOpen ? 'borderStyle1' : 'borderStyle'}`">
                      </span>
                      <div class="pa-1" style="height: 120px; overflow-y: scroll">
                        <span v-for="item in allReasons" :key="item.id">
                          <v-checkbox
                            style="border: 1px solid lightgray"
                            class="pa-1 mb-1 rounded-lg"
                            multiple
                            dense
                            @change="selectR(item, i)"
                            hide-details
                            :value="item.id"
                            :label="item.title"
                          ></v-checkbox>
                        </span>
                      </div>
                    </v-expansion-panel-content>
                  </v-expansion-panel>
                </v-expansion-panels>
              </div>
              <div class="h-full mt-2 position-relative d-block" v-if="1 > 2">
                <span class="ml-4" v-if="!isOpen">{{
                  $tr("pleas_choose_reason")
                }}</span>
                <v-icon
                  style="position: absolute; left: 185px; top: -2px"
                  @click="openReason"
                  >{{
                    isOpen
                      ? "mdi-chevron-up-circle-outline"
                      : "mdi-chevron-down-circle-outline"
                  }}</v-icon
                >
                <fieldset
                  :class="`rounded-lg ${isOpen ? 'fieldset' : 'fieldset1'}`"
                  v-if="isOpen"
                >
                  <legend class="ml-3 pe-4">
                    &nbsp;{{ $tr("pleas_choose_reason") }}
                  </legend>
                  <div class="pa-1" style="height: 150px; overflow-y: scroll">
                    <span v-for="reason in allReasons" :key="reason.id">
                      <v-checkbox
                        style="border: 1px solid lightgray"
                        class="pa-1 mb-1 rounded-lg"
                        multiple
                        dense
                        v-model="selectReason"
                        hide-details
                        :value="reason.reason_id"
                        :label="reason.reason_id"
                      ></v-checkbox>
                    </span>
                  </div>
                </fieldset>
              </div>
            </div>
          </v-card-text>
  
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" outlined @click="dialog = false">{{
              $tr("close")
            }}</v-btn>
            <v-btn color="primary" @click="onSave">{{ $tr("save") }}</v-btn>
          </v-card-actions>
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
        refersh: 0,
        panel: 1,
        changeStatus: [],
        status: [
           { id: "not publish",value: "not_publish", icon: "mdi-folder-open" },
            { id: "rejected",value: "rejected", icon: "mdi-folder-remove" },
        ],
        allReasons: [],
        selectReason: [],
        isOpen: false,
      };
    },
    methods: {
      async fetchAllReasons(statusTo = null) {
      try {
        const response = await this.$axios.get(
          `reasons/id?type=rejected&sub_system=Content library`
        );
        console.log(response);
        this.allReasons = response?.data?.reasons;
      } catch (error) {
        if (error?.response?.status === 404 && !error?.response?.data?.result)
          // this.$toastr.w(this.$tr("sub_system_not_found"));
          this.$toasterNA("orange", this.$tr("sub_system_not_found"));
      }
    },
      selectR(item, i) {
        if (this.changeStatus[i].reasons.find((s) => s.id == item.id))
          this.changeStatus[i].reasons.filter((s) => s.id != item.id);
        else this.changeStatus[i].reasons.push(item.id);
      },
      onSave() {
        console.log(this.changeStatus);
      },
      fillStatus(tabKey) {
        if (tabKey == "pending") {
          this.status = [
            { id: "not publish",value: "not_publish", icon: "mdi-folder-open" },
            { id: "rejected",value: "rejected", icon: "mdi-folder-remove" },
          ];
        } else if (tabKey == "not_publish") {
          this.status = [{id: "rejected", value: "rejected", icon: "mdi-folder-remove" }];
        } else if (tabKey == "rejected") {
          this.status = [{id: "not publish", value: "not_publish", icon: "mdi-folder-open" }];
        }
      },
      openDialog(item, tabKey) {
        this.fillStatus(tabKey);
        this.refersh++;
        this.seletedItem = item;
        this.changeStatus = item.map((i)=>{
          return {
            id:i.id,
            status:i.status,
            reasons:[],
          };
        })
        this.dialog = true;
      },
      reset() {
        this.refersh++;
        this.changeStatus = [];
      },
      closeDialog() {
        this.reset();
        this.dialog = false;
      },
      change(status, id) {
          this.changeStatus = this.changeStatus.map((item) => {
            if (item.id == id) {
              item.status = status;
              if (status != "rejected") item.reasons = [];
              else
              this.fetchAllReasons();
            }
            return item;
          });
        this.changeStatus = [...this.changeStatus];
      },
      openReason() {
        this.isOpen = !this.isOpen;
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
    left: 20px;
    z-index: 1;
    padding-left: 124px;
    background: white;
    top: 10px;
  }
  .fieldset {
    height: auto;
    transition: all 0.5s ease;
  }
  .fieldset1 {
    height: 0px;
    transition: all 0.5s ease;
  }
  .borderStyle {
    position: absolute;
    width: 100%;
    top: 24px;
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
    top: 24px;
    left: 0px;
    border-radius: 10px;
    opacity: 1;
    height: 200px;
    border: 1px solid lightgray;
    transition: all 0.5s ease;
  }
  </style>