<template>
  <SidebarDrawer
    titleText="view_design"
    ref="sidebarDrawerRef"
    colorGrey="grey lighten-4"
  >
    <!-- header -->
    <template slot="header">
      <v-container fluid class="pa-0" v-if="isDrawerOpened">
        <v-row class="py-0 my-0">
          <v-col cols="12" md="6" class="">
            <div
              class="d-flex flex-column flex-md-row justify-start align-center"
            >
              <div class="pe-2">
                <v-progress-circular
                  :rotate="-90"
                  :size="125"
                  :width="6"
                  :value="historyItem.task_prograss"
                  color="primary"
                >
                  <v-avatar color="primary" size="90">
                    <span class="white--text text-h2">
                      {{
                        historyItem.product_name
                          ? historyItem.product_name[0].toUpperCase()
                          : ""
                      }}</span
                    >
                  </v-avatar>
                  <div class="percentage-value-circle">
                    {{ historyItem.task_prograss }}%
                  </div>
                </v-progress-circular>
              </div>
              <div class="details flex-grow-1">
                <h2
                  class="mb-0 text-center text-md-left text-md-start primary--text"
                  style="letter-spacing: 1px"
                >
                  {{ historyItem.product_name }}
                </h2>
                <div
                  class="d-flex justify-center justify-md-start align-center"
                >
                  <span>
                    <v-chip class="" color="primary" outlined>
                      <v-icon left> mdi-clock-outline</v-icon>
                      {{ $tr(historyItem.status) }}
                    </v-chip>
                  </span>
                </div>
              </div>
            </div>
          </v-col>
          <v-col
            cols="12"
            md="6"
            class="d-flex justify-md-end justify-sm-center align-center"
          >
            <div
              class="mx-3 mt-2"
              :style="
                !$vuetify.rtl
                  ? 'border-right: 2px solid #d4d4d4'
                  : 'border-left:2px solid #d4d4d4'
              "
              v-if="profileData.type"
            >
              <v-tooltip top content-class="tooltip-top">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    v-bind="attrs"
                    v-on="on"
                    icon
                    color="primary"
                    outlined
                    class="me-3"
                    x-large
                  >
                    <img
                      width="25px"
                      :src="`/design-request/${profileData.type.replaceAll(
                        ' ',
                        '_'
                      )}.svg`"
                      alt=""
                    />
                  </v-btn>
                </template>
                <span>{{ $tr(profileData.type) }}</span>
              </v-tooltip>
            </div>
            <span class="mt-2">
              <v-tabs
                class="tabs_custome"
                active-class
                hide-slider
                fixed-tabs
                disabled
              >
                <v-tab
                  :class="historyItem.priority == 'low' ? 'activeTwo' : 'no'"
                  disabled
                  style="border-radius: 50px 0 0 50px"
                >
                  {{ $tr("low") }}
                </v-tab>
                <v-tab
                  :class="historyItem.priority == 'medium' ? 'activeTwo' : 'no'"
                  disabled
                  >{{ $tr("medium") }}</v-tab
                >
                <v-tab
                  :class="historyItem.priority == 'high' ? 'activeTwo' : 'no'"
                  disabled
                  style="border-radius: 0 50px 50px 0"
                >
                  {{ $tr("High") }}
                </v-tab>
              </v-tabs>
            </span>
          </v-col>
        </v-row>
      </v-container>
    </template>

    <!-- body -->
    <template slot="body">
      <div v-if="isDrawerOpened">
        <div
          class=""
          style="min-height: 100px: background-color:var(--v-surface-base)"
        >
          <div
            class="d-flex flex-row align-center mx-5"
            style="margin-top: -25px"
          >
            <span class="text-h5">{{ $tr("details") }}</span>
            <v-divider inset class="mx-2 grey darken-1"></v-divider>
          </div>
          <v-container fluid class="py-0">
            <v-row>
              <v-col cols="12" md="3" sm="4" class="pe-3 pe-md-0">
                <div class="">
                  <div class="">
                    <v-card
                      class="mx-lg-auto mx-md-0 cardInbox"
                      elevation="0"
                      style="background-color: var(--v-background-darken2)"
                    >
                      <div>
                        <v-list
                          class="listInbox mt-1 w-full"
                          dense
                          style="background-color: inherit"
                        >
                          <div class="pt-1"></div>
                          <v-list-item
                            v-for="(itemtab, i) in tabItems"
                            :key="i"
                            link
                            :class="
                              i == selected_item ? 'selected' : 'not-selected'
                            "
                            @click="selectOption(i)"
                          >
                            <v-list-item-icon class="mb-1 mt-1">
                              <v-icon
                                :color="
                                  i == selected_item
                                    ? 'primary'
                                    : 'not-selected'
                                "
                                >{{ itemtab.icon }}</v-icon
                              >
                            </v-list-item-icon>

                            <v-list-item-content class="ms-1">
                              <v-list-item-title>{{
                                itemtab.text
                              }}</v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <div class="pb-1"></div>
                        </v-list>
                      </div>
                    </v-card>
                  </div>
                </div>
              </v-col>
              <v-col :key="profileTabKey" cols="12" md="9" sm="8" class="">
                <div>
                  <v-menu offset-y>
                    <template v-slot:activator="{ attrs, on }">
                      <v-btn
                        color="primary"
                        class="w-full px-3 my-1"
                        v-bind="attrs"
                        v-on="on"
                        large
                      >
                        <div
                          class="w-full d-flex align-center justify-space-between"
                        >
                          <div class="text-uppercase white--text">
                            {{ $tr("list_of_time_spent_per_status") }}
                          </div>
                          <div>
                            <v-icon>mdi-chevron-down</v-icon>
                          </div>
                        </div>
                      </v-btn>
                    </template>
                    <v-list class="scrollMenu py-0">
                      <!-- <div v-for="item in total_times" :key="item.index">
                        <v-list-item class="">
                          <v-list-item-title>
                            <div class="px-1 d-flex justify-space-between">
                              <div
                                :class="
                                  item.status.includes('total')
                                    ? 'text-uppercase font-weight-bold'
                                    : 'text-capitalize'
                                "
                              >
                                <span>{{ $tr(item.status) }}</span>
                                <span
                                  v-if="
                                    item.status.includes('storyboard') &&
                                    item.storyboard_stage > 0
                                  "
                                  >{{ item.storyboard_stage }}</span
                                >
                                <span
                                  v-if="
                                    item.status.includes('design') &&
                                    item.design_stage > 0
                                  "
                                  >{{ item.design_stage }}</span
                                >
                              </div>
                              <div class="grey--text">
                                {{ item.spent_time }}
                              </div>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                        <v-divider></v-divider>
                      </div> -->
                    </v-list>
                  </v-menu>
                </div>
                <v-card
                  v-if="selected_item == 0"
                  outlined
                  elevation="0"
                  class=""
                  style="background-color: inherit; border-radius: 20px"
                >
                  <v-card-title
                    class="primary pt-1 d-flex"
                    style="height: 50px"
                  >
                    <div class="white--text">
                      {{ $tr("general") }}
                    </div>
                  </v-card-title>
                  <v-card-text
                    class="px-3 pb-0 Scroll"
                    style="overflow-y: scroll; height: 300px"
                  >
                    <v-row class="pa-2">
                      <v-col
                        cols="12"
                        md="6"
                        class="d-flex pa-1 align-center ps-1"
                      >
                        <div
                          class="text-capitalize me-md-15 me-sm-2"
                          style="width: 120px"
                        >
                          {{ $tr("product_code") }}
                        </div>
                        <div class="text-capitalize">
                          {{ historyItem.product_code }}
                        </div>
                      </v-col>
                      <v-col
                        cols="12"
                        md="6"
                        class="d-flex pa-1 align-center ps-2"
                      >
                        <div
                          class="text-capitalize me-md-15 me-sm-2"
                          style="width: 120px"
                        >
                          {{ $tr("product_name") }}
                        </div>
                        <div class="text-capitalize">
                          {{ historyItem.product_name }}
                        </div>
                      </v-col>
                    </v-row>

                    <v-row
                      class="pa-1"
                      style="background-color: var(--v-background-darken2)"
                    >
                      <v-col
                        cols="12"
                        md="6"
                        class="d-flex py-0 align-center ps-2"
                      >
                        <div
                          class="text-capitalize me-md-15 me-sm-2"
                          style="width: 120px"
                        >
                          {{ $tr("company") }}
                        </div>
                        <div class="text-capitalize" v-if="historyItem.company">
                          {{ historyItem.company.name }}
                        </div>
                      </v-col>
                      <v-col
                        cols="12"
                        md="6"
                        class="d-flex pa-1 align-center ps-2"
                      >
                        <div
                          class="text-capitalize me-md-15 me-sm-2"
                          style="width: 120px"
                        >
                          {{ $tr("created_by") }}
                        </div>
                        <div class="text-capitalize" v-if="historyItem.user">
                          <Userchip
                            :name="
                              historyItem.created_by.firstname +
                              ' ' +
                              historyItem.created_by.lastname
                            "
                            :avatar="
                              historyItem.created_by.firstname
                                ? historyItem.created_by.firstname[0].toUpperCase()
                                : ''
                            "
                            class="ma-0"
                          />
                        </div>
                      </v-col>
                    </v-row>

                    <v-row class="pa-2">
                      <v-col md="6" class="d-flex py-0 align-center ps-1">
                        <div
                          class="text-capitalize me-md-15 me-sm-2"
                          style="width: 120px"
                        >
                          {{ $tr("created_at") }}
                        </div>
                        <div
                          class="text-capitalize"
                          v-if="historyItem.created_at"
                        >
                          {{ $formatDateTime(historyItem.created_at) }}
                        </div>
                      </v-col>
                      <v-col
                        cols="12"
                        md="6"
                        class="d-flex pa-1 align-center ps-2"
                      >
                        <div
                          class="text-capitalize me-md-15 me-sm-2"
                          style="width: 120px"
                        >
                          {{ $tr("updated_at") }}
                        </div>
                        <div
                          class="text-capitalize"
                          v-if="historyItem.updated_at"
                        >
                          {{ $formatDateTime(historyItem.updated_at) }}
                        </div>
                      </v-col>
                    </v-row>

                    <v-row
                      class="pa-3"
                      style="background-color: var(--v-background-darken2)"
                    >
                      <div
                        class="text-capitalize me-md-15 me-sm-2"
                        style="width: 120px"
                      >
                        {{ $tr("assigned_to") }}
                      </div>
                      <div class="text-capitalize d-flex">
                        <Userchip
                          class="me-1"
                          :name="
                            historyItem.user.firstname +
                            ' ' +
                            historyItem.user.lastname
                          "
                          :avatar="
                            historyItem.user.firstname
                              ? historyItem.user.firstname[0].toUpperCase()
                              : ''
                          "
                          selectedClose
                        />
                      </div>
                    </v-row>
                  </v-card-text>
                </v-card>
                <NoteCard
                  :item="historyItem"
                  v-if="selected_item == 1"
                  :canEdit="false"
                  :profileData="profileData"
                  titleText="sales_note"
                  :descriptions="historyItem.sales_note"
                />
                <NoteCard
                  :item="historyItem"
                  v-if="selected_item == 2"
                  :canEdit="false"
                  :profileData="profileData"
                  titleText="storyboard_note"
                  :descriptions="historyItem.storyboard_note"
                />
                <NoteCard
                  v-if="selected_item == 3"
                  :canEdit="false"
                  :item="historyItem"
                  :profileData="profileData"
                  titleText="design_note"
                  :descriptions="historyItem.design_note"
                />

                <v-card
                  v-if="selected_item == 4"
                  outlined
                  elevation="0"
                  class=""
                  style="background-color: inherit; border-radius: 20px"
                >
                  <v-card-title class="primary pt-1" style="height: 50px">
                    <span class="white--text">{{
                      $tr("submission_link")
                    }}</span>
                  </v-card-title>
                  <v-card-text
                    class="px-3 pb-0 overflow-auto Scroll"
                    style="height: 300px"
                  >
                    <v-row class="pa-1">
                      <v-col cols="12" md="6" class="pa-1 ps-2">
                        <div class="d-flex align-center justify-space-between">
                          <div
                            class="text-capitalize black--text me-md-15 me-sm-2"
                          >
                            {{ $tr("landing_page_link") }}
                          </div>
                          <div
                            class="text-capitalize grey--text d-flex align-center"
                          >
                            <v-tooltip bottom>
                              <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                  height="30"
                                  width="70"
                                  rounded
                                  target="_blank"
                                  color="primary"
                                  dark
                                  v-bind="attrs"
                                  v-on="on"
                                  :href="historyItem.landing_page_link"
                                >
                                  {{ $tr("link") }}
                                  <v-icon class="hover" color="white"
                                    >mdi-link</v-icon
                                  >
                                </v-btn>
                              </template>
                              <span>{{ historyItem.landing_page_link }}</span>
                            </v-tooltip>
                          </div>
                        </div>
                      </v-col>
                      <v-col cols="12" md="6" class="pa-1 ps-2">
                        <div class="d-flex align-center justify-space-between">
                          <div
                            class="text-capitalize black--text me-md-15 me-sm-2"
                          >
                            {{ $tr("design_link") }}
                          </div>
                          <div
                            class="text-capitalize grey--text d-flex justify-space-center align-center"
                          >
                            <v-tooltip bottom>
                              <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                  height="30"
                                  width="70"
                                  rounded
                                  target="_blank"
                                  color="primary"
                                  dark
                                  v-bind="attrs"
                                  v-on="on"
                                  :href="historyItem.design_link"
                                >
                                  {{ $tr("link") }}
                                  <v-icon class="hover" color="white"
                                    >mdi-link</v-icon
                                  >
                                </v-btn>
                              </template>
                              <span>{{ historyItem.design_link }}</span>
                            </v-tooltip>
                          </div>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </div>
      </div>
    </template>
  </SidebarDrawer>
</template>
<script>
import CloseBtn from "../../components/design/Dialog/CloseBtn.vue";
import Userchip from "../../components/design/UserChip.vue";
import ReasonDialog from "../../components/common/ReasonDialog.vue";
import HandleException from "~/helpers/handle-exception";
import Alert from "~/helpers/alert";

import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";

import SidebarDrawer from "../design/components/SidebarDrawer.vue";
import Editor from "../design/Editor.vue";
import NoteCard from "./NoteCard";
export default {
  components: {
    CloseBtn,
    Userchip,
    ReasonDialog,
    DesignRequestEdit,
    SidebarDrawer,
    Editor,
    NoteCard,
  },
  props: {
    profileData: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      profileTabKey: 0,
      selected_item: 0,
      isDrawerOpened: false,
      tabItems: [
        { icon: "mdi-information-outline", text: "General", isSelected: true },
        { icon: "mdi-message-bulleted", text: "Sales Note", isSelected: false },
        {
          icon: "mdi-message-bulleted",
          text: "Storyboard Note",
          isSelected: false,
        },
        {
          icon: "mdi-message-bulleted",
          text: "Design Note",
          isSelected: false,
        },
        { icon: "mdi-link", text: "Submission Link", isSelected: false },
      ],
      phases: ["storyboard_phase", "design_phase", "programming_phase"],
      historyItem: {},
    };
  },

  methods: {
    selectOption(tab) {
      this.profileTabKey++;
      this.selected_item = tab;
    },

    toggleDrawer(item) {
      this.historyItem = item;
      this.isDrawerOpened = true;
      this.$refs.sidebarDrawerRef.toggleDrawer();
    },
  },
};
</script>

<style>
.percentage-value-circle {
  position: absolute;
  bottom: -15px;
  background: var(--bg-white);
  border-color: var(-v--primary-base);
  border: 3px solid;
  border-radius: 80px;
  width: 60px;
  text-align: center;
  font-weight: bold;
}

.scrollMenu {
  max-height: 400px;
  overflow-y: scroll !important;
}
.tabs_custome .v-tab .v-tab--active::before {
  content: none !important;
}
.tabs_custome {
  border-radius: 50px;
  background-color: inherit !important;
  border: 1px solid grey;
  width: 300px !important;
}
.tabs_custome .v-tab--disabled {
  opacity: 1;
}

.activeTwo {
  background-color: var(--v-primary-base);
  border: 1px solid var(--v-primary-base);
  color: var(--v-secondary-lighten5) !important;
}
.tabs_custome .v-tabs-bar {
  background-color: inherit !important;
}
a {
  text-decoration: none;
}
a:hover,
.hover:hover {
  cursor: pointer !important;
}
.cardInbox {
  border-radius: 15px !important;
}
.listInbox .selected {
  background-color: var(--v-surface-base) !important;
  border-left: 4px solid var(--v-primary-base);
  color: var(--v-primary-base) !important;
}
.listInbox .not-selected {
  border-left: 4px solid transparent;
}
.avatarCustom {
  border: 1px solid gray;
  position: relative;
  width: 40px;
  border-radius: 50px;
  padding: 2px;
}
.avatarCustom .customAvatar {
  font-size: 25px;
  font-weight: bolder;
  color: var(--v-secondary-lighten5);
}
.Scroll::-webkit-scrollbar {
  width: 10px;
  background-color: var(--v-surface-base);
}
.Scroll::-webkit-scrollbar:hover {
  cursor: alias !important;
}
.Scroll::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

.Scroll::-webkit-scrollbar-thumb {
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-primary-base);
}
</style>
