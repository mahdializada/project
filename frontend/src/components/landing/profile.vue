<template>
  <!-- :class="this.$vuetify.theme.themes  dark ? 'blue--text':''" -->
  <div>
    <div>
      <ReasonDialog
        @closeDialog="closeDialog"
        @onSubmit="onSubmit"
        :title="reasonTitle"
        :isMultiple="true"
        :allReasons="allReasons"
        :showReasonDialog.sync="showReasonDialog"
        :isFetchingReasons.sync="isFetchingReasons"
      />
    </div>
    <div>
      <v-dialog v-model="workAreaEditDialog" persistent width="800">
        <v-card>
          <v-card-title class="pa-1">
            <span class="headline ps-1">{{ $tr("edit_work_area") }}</span>
            <v-spacer />
            <CloseBtn
              class="close-dialog-btn"
              @click="workAreaEditDialog = false"
            />
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="my-2">
            <div class="d-flex flex-column flex-md-row">
              <div class="w-full me-0 me-md-2">
                <CustomInput
                  :items="workAreaTypes"
                  v-model="workAreaEdit.type"
                  @change="fetchTemplates(workAreaEdit.type)"
                  :label="$tr('label_required', $tr('work_area'))"
                  :placeholder="$tr('choose_item', $tr('work_area'))"
                  v-model.trim="workAreaEdit.type"
                  type="autocomplete"
                  item-text="name"
                  item-value="id"
                />
              </div>
              <div class="w-full ms-0 ms-md-2">
                <p class="mb-1 custom-input-title">{{ $tr("template") }} *</p>
                <v-combobox
                  validate-on-blur
                  v-model="workAreaEdit.template_id"
                  class="custom-input auto-complete"
                  ref="templateRefs"
                  dense
                  :placeholder="$tr('template')"
                  :menu-props="{ bottom: true, offsetY: true, class: 'test' }"
                  outlined
                  :loading="isFetchingTemplate"
                  :items="templates"
                  item-value="id"
                  item-text="name"
                >
                  <template v-slot:[`no-data`]>
                    <div class="d-flex pa-1 align-center">
                      <div class="me-1">{{ $tr("no_data_available") }}</div>
                      <div class="ms-5" style="cursor: pointer">
                        <span
                          @click="$refs.templateRefs.blur()"
                          class="font-weight-bold primary--text"
                          >{{ $tr("create") }}
                        </span>
                        {{ $tr("new_template") }}
                      </div>
                    </div>
                  </template>
                </v-combobox>
              </div>
            </div>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <TextButton
              type="secondary"
              class="me-1"
              :text="$tr('cancel')"
              @click="workAreaEditDialog = false"
            >
            </TextButton>
            <TextButton
              type="primary"
              @click="updateWorkArea"
              :text="$tr('update')"
            >
            </TextButton>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
    <SidebarDrawer
      titleText="view_design"
      ref="sidebarDrawerRef"
      :hasDrawer="hasDrawer"
      :isFetchingDetails.sync="isFetchingDetails"
      colorGrey="grey lighten-4"
      :link="`/landing-page/design-request/profile/${profileData.code}`"
      :backLink="`/landing-page/design-request?profile=${profileData.code}`"
    >
      <template slot="header">
        <v-container fluid class="pa-0">
          <v-row class="py-0 my-0">
            <v-col cols="12" md="6" class="">
              <div
                class="
                  d-flex
                  flex-column flex-md-row
                  justify-start
                  align-center
                "
              >
                <div class="pe-2">
                  <v-progress-circular
                    :rotate="-90"
                    :size="125"
                    :width="6"
                    :value="profileData.percentage"
                    color="primary"
                  >
                    <v-avatar color="primary" size="90">
                      <span class="white--text text-h2">
                        {{
                          profileData.product_name
                            ? profileData.product_name[0].toUpperCase()
                            : ""
                        }}</span
                      >
                    </v-avatar>
                    <div class="percentage-value-circle">
                      {{ profileData.percentage }}%
                    </div>
                  </v-progress-circular>
                </div>
                <div class="details flex-grow-1">
                  <h2
                    class="
                      mb-0
                      mt-2
                      text-center text-md-left text-md-start
                      primary--text
                    "
                    style="letter-spacing: 1px"
                  >
                    {{ profileData.product_name }}
                  </h2>
                  <div
                    class="d-flex justify-center justify-md-start align-center"
                  >
                    <span>
                      <v-chip class="" color="primary" outlined>
                        <v-icon left> mdi-clock-outline</v-icon>
                        {{ profileData.status }}
                      </v-chip>
                    </span>
                    <span class="grey--text ps-3">{{ total_time_spent }}</span>
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
                <v-tabs class="tabs_custome" active-class hide-slider disabled>
                  <v-tab
                    :class="profileData.priority == 'low' ? 'activeTwo' : 'no'"
                    disabled
                    style="border-radius: 50px 0 0 50px"
                  >
                    {{ $tr("low") }}
                  </v-tab>
                  <v-tab
                    :class="
                      profileData.priority == 'medium' ? 'activeTwo' : 'no'
                    "
                    disabled
                    >{{ $tr("medium") }}</v-tab
                  >
                  <v-tab
                    :class="profileData.priority == 'high' ? 'activeTwo' : 'no'"
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
      <!-- header -->

      <!-- body -->

      <template slot="body">
        <div>
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
                        class="mx-lg-auto mx-md-0"
                        elevation="0"
                        style="background-color: var(--v-background-darken1)"
                        rounded-sm
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
                              :class="[
                                i == selected_item
                                  ? 'selected'
                                  : 'not-selected',
                                $vuetify.theme.framework.rtl == false
                                  ? 'border_left'
                                  : 'border_right',
                              ]"
                              @click="selectOption(i)"
                            >
                              <v-list-item-icon class="mb-1 mt-1">
                                <v-icon
                                  :color="
                                    i == selected_item
                                      ? 'primary'
                                      : 'not-selected'
                                  "
                                  class="ms-1"
                                  >{{ itemtab.icon }}</v-icon
                                >
                              </v-list-item-icon>

                              <v-list-item-content class="ms-1">
                                <v-list-item-title>{{
                                  $tr(itemtab.text)
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
                          style="height: 50px"
                        >
                          <div
                            class="
                              w-full
                              d-flex
                              align-center
                              justify-space-between
                            "
                          >
                            <div class="text-uppercase white--text">
                              {{ $tr("list_of_time_spent_per_status") }}
                            </div>
                            <div>
                              <v-icon>{{ timerIconMenu }}</v-icon>
                            </div>
                          </div>
                        </v-btn>
                      </template>
                      <v-list class="scrollMenu py-0">
                        <div v-for="item in total_times" :key="item.index">
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
                        </div>
                      </v-list>
                    </v-menu>
                  </div>
                  <v-card
                    v-if="selected_item == 0"
                    outlined
                    elevation="0"
                    class=""
                    style="background-color: inherit"
                  >
                    <v-card-title
                      class="primary pt-1 d-flex"
                      style="height: 50px"
                    >
                      <div class="white--text">
                        {{ $tr("general") }}
                      </div>
                    </v-card-title>
                    <v-card-text class="px-3 pb-0" style="height: 350px">
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
                            {{ profileData.product_code }}
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
                            {{ profileData.product_name }}
                          </div>
                        </v-col>
                      </v-row>

                      <v-row
                        class="pa-2"
                        style="background-color: var(--v-background-darken1)"
                      >
                        <v-col
                          cols="12"
                          md="6"
                          class="d-flex pa-1 align-center ps-1"
                        >
                          <div
                            class="text-capitalize me-md-15 me-sm-2"
                            style="width: 120px"
                          >
                            {{ $tr("work_area") }}
                          </div>
                          <div class="text-capitalize">
                            {{ profileData.type }}
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
                            {{ $tr("template") }}
                          </div>
                          <div
                            class="text-capitalize"
                            v-if="profileData.template"
                          >
                            {{ profileData.template.name }}
                          </div>
                          <v-spacer></v-spacer>
                          <v-btn
                            @click="onEditWorkArea"
                            color="primary"
                            icon
                            text
                            ><v-icon>mdi-pencil</v-icon></v-btn
                          >
                        </v-col>
                      </v-row>

                      <v-row class="pa-2">
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
                          <div
                            class="text-capitalize"
                            v-if="profileData.company"
                          >
                            {{ profileData.company.name }}
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
                          <div class="text-capitalize" v-if="profileData.user">
                            <Userchip
                              :name="
                                profileData.user.firstname +
                                ' ' +
                                profileData.user.lastname
                              "
                              :avatar="
                                profileData.user.firstname
                                  ? profileData.user.firstname[0].toUpperCase()
                                  : ''
                              "
                              class="ma-0"
                            />
                          </div>
                        </v-col>
                      </v-row>

                      <v-row
                        class="pa-2"
                        style="background-color: var(--v-background-darken1)"
                      >
                        <v-col md="6" class="d-flex py-0 align-center ps-1">
                          <div
                            class="text-capitalize me-md-15 me-sm-2"
                            style="width: 120px"
                          >
                            {{ $tr("created_at") }}
                          </div>
                          <div
                            class="text-capitalize"
                            v-if="profileData.created_at"
                          >
                            {{ $formatDateTime(profileData.created_at) }}
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
                            v-if="profileData.updated_at"
                          >
                            {{ $formatDateTime(profileData.updated_at) }}
                          </div>
                        </v-col>
                      </v-row>

                      <v-row class="pa-2 d-flex align-center ps-3">
                        <div
                          class="text-capitalize me-md-15 me-sm-2"
                          style="width: 120px"
                        >
                          {{ $tr("assigned_to") }}
                        </div>
                        <div
                          class="text-capitalize d-flex flex-wrap"
                          v-if="profileData.design_request_order"
                        >
                          <Userchip
                            class="me-1"
                            v-for="(item, index) in assignedUsers"
                            :key="index"
                            :name="
                              item.user.firstname + ' ' + item.user.lastname
                            "
                            :avatar="
                              item.user.firstname
                                ? item.user.firstname[0].toUpperCase()
                                : ''
                            "
                            showCloseBtn
                            selectedClose
                            @close="removeDesign(item.user.id)"
                          />
                        </div>
                      </v-row>
                    </v-card-text>
                  </v-card>
                  <NoteCard
                    v-if="selected_item == 1"
                    :item="profileData"
                    :canEdit="userCandEditSalesNote()"
                    cardItem="in storyboard"
                    titleText="sales_note"
                    :itemId="
                      profileData.design_request_order &&
                      profileData.design_request_order.id
                    "
                    column="sales_note"
                    @onSave="
                      (value) =>
                        (profileData.design_request_order.sales_note = value)
                    "
                    :descriptions="
                      profileData.design_request_order &&
                      profileData.design_request_order.sales_note
                    "
                  />
                  <NoteCard
                    @onReject="onReject"
                    @onApproved="onApproved"
                    :itemId="
                      profileData.design_request_order &&
                      profileData.design_request_order.id
                    "
                    v-if="selected_item == 2"
                    :item="profileData"
                    :canEdit="authUserCanEdit('in storyboard')"
                    cardItem="in storyboard"
                    titleText="storyboard_note"
                    column="storyboard_note"
                    @onSave="
                      (value) =>
                        (profileData.design_request_order.storyboard_note =
                          value)
                    "
                    :descriptions="
                      profileData.design_request_order &&
                      profileData.design_request_order.storyboard_note
                    "
                  />
                  <NoteCard
                    @onReject="onReject"
                    @onApproved="onApproved"
                    v-if="selected_item == 3"
                    :item="profileData"
                    :canEdit="authUserCanEdit('in design')"
                    cardItem="in design"
                    titleText="design_note"
                    :itemId="
                      profileData.design_request_order &&
                      profileData.design_request_order.id
                    "
                    column="design_note"
                    @onSave="
                      (value) =>
                        (profileData.design_request_order.design_note = value)
                    "
                    :descriptions="
                      profileData.design_request_order &&
                      profileData.design_request_order.design_note
                    "
                  />
                  <NoteCard
                    v-if="selected_item == 4"
                    :canEdit="authUserCanEdit('in programming')"
                    :item="profileData"
                    :link="true"
                    titleText="submission_files"
                  />
                </v-col>
              </v-row>
            </v-container>
          </div>
        </div>
      </template>
    </SidebarDrawer>
  </div>
</template>
<script>
import CloseBtn from "../../components/design/Dialog/CloseBtn.vue";
import Userchip from "../../components/design/UserChip.vue";
import ReasonDialog from "../../components/common/ReasonDialog.vue";
import HandleException from "~/helpers/handle-exception";
import Alert from "~/helpers/alert";
import CustomInput from "~/components/design/components/CustomInput";

import TextButton from "../../components/common/buttons/TextButton.vue";
import GlobleRules from "~/helpers/vuelidate";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import SidebarDrawer from "../design/components/SidebarDrawer.vue";
import Editor from "../design/Editor.vue";
import NoteCard from "./NoteCard";
export default {
  components: {
    CloseBtn,
    CustomInput,
    Userchip,
    TextButton,
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
    hasDrawer: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      note: "",
      workAreaEditDialog: false,
      validate: GlobleRules.validate,
      isFetchingTemplate: false,
      templates: [],

      workAreaEdit: {
        type: "",
        template_id: "",
      },

      isEdit: false,
      isFetchingDetails: false,
      isFetchingReasons: false,
      showReasonDialog: false,
      showEditorPanel: false,
      allReasons: [],
      user_id: "",
      assignedUsers: [],
      selected_item: 0,
      reasonTitle: this.$tr("Why you want to un assign this user?"),
      isAnyUserUnAssigned: false,
      isEdit: false,
      isAutoEdit: false,
      editData: {},
      profileTabKey: 0,
      tabItems: [
        { icon: "mdi-information-outline", text: "general", isSelected: true },
        { icon: "mdi-message-bulleted", text: "sales_note", isSelected: false },
        {
          icon: "mdi-message-bulleted",
          text: "storyboard_note",
          isSelected: false,
        },
        {
          icon: "mdi-message-bulleted",
          text: "design_note",
          isSelected: false,
        },
        { icon: "mdi-link", text: "submission_files", isSelected: false },
      ],
      phases: ["storyboard_phase", "design_phase", "programming_phase"],
      total_time_spent: 0,
      total_times: [],
      timerIconMenu: "mdi-chevron-down",
      limitedInterval: "",
    };
  },
  mounted: function () {
    if (process.client) {
      this.limitedInterval = window.setInterval(() => {
        this.setTotalTimeSpent();
        this.totalTimes();
      }, 1000);
    }
  },

  computed: {
    workAreaTypes: function () {
      return [
        { id: "general design", name: this.$tr("general_design") },
        { id: "landing page design", name: this.$tr("landing_page_design") },
        {
          id: "advertisement content",
          name: this.$tr("advertisement_content"),
        },
        { id: "social media design", name: this.$tr("social_media_design") },
        { id: "translation", name: this.$tr("translation") },
        { id: "texts and writings", name: this.$tr("texts_and_writings") },
        { id: "shooting", name: this.$tr("shooting") },
      ];
    },
  },

  watch: {
    $props: {
      handler() {
        this.parseData();
      },
      deep: true,
      immediate: true,
    },
  },
  beforeDestroy() {
    clearInterval(this.limitedInterval);
  },
  async created() {},
  methods: {
    async onEditWorkArea() {
      this.workAreaEditDialog = true;
      this.workAreaEdit.type = this.profileData.type;
      await this.fetchTemplates(this.workAreaEdit.type);
      this.workAreaEdit.template_id =
        this.profileData.template && this.profileData.template.name;
    },

    async fetchTemplates(type) {
      this.workAreaEdit.template_id = null;
      this.templates = [];
      try {
        this.isFetchingTemplate = true;
        const currentComUrl = `design-request-data?template=true&type=${type}`;
        const reponse = await this.$axios.get(currentComUrl);
        this.templates = reponse.data;
      } catch (error) {}
      this.isFetchingTemplate = false;
    },

    async updateWorkArea() {
      const data = {
        template_id: this.workAreaEdit.template_id
          ? this.workAreaEdit.template_id.id
          : null,
        type: this.workAreaEdit.type ? this.workAreaEdit.type : null,
      };

      try {
        const response = await this.$axios.put(
          "design-requests/work-area/" + this.profileData.id,
          data
        );
        if (response?.status === 200 && response?.data?.result) {
          const updated = response?.data?.data;
          this.profileData.type = updated.type;
          this.profileData.template = updated.template;
          this.workAreaEditDialog = false;
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    parseData() {
      let status = "sample";
      if (
        this.profileData &&
        Object.keys(this.profileData).length === 0 &&
        Object.getPrototypeOf(this.profileData) === Object.prototype
      ) {
        this.selected_item = 0;
      } else {
        status = this.profileData?.status;
        if (status.includes("storyboard")) this.selected_item = 2;
        else if (status.includes("design")) this.selected_item = 3;
        else if (status.includes("programming")) this.selected_item = 4;
        else this.selected_item = 0;
      }

      this.assignedUsers =
        this.profileData?.design_request_order?.design_request_order_user;
    },
    checkSpentTime(diff) {
      let total_time_spent = "";
      let total_year = "";
      let total_month = "";
      let total_day = "";
      let total_hour = "";
      let total_minute = "";
      total_year = Math.floor(diff / 31536000);
      if (total_year > 0) {
        diff -= total_year * 31536000;
        total_time_spent = total_time_spent.concat(total_year, "Y");
      }
      total_month = Math.floor(diff / (86400 * 30));
      if (total_month > 0) {
        diff -= total_month * (86400 * 30);
        total_time_spent = total_time_spent.concat(" ", total_month, "MO");
      }
      total_day = Math.floor(diff / 86400);
      if (total_day > 0) {
        diff -= total_day * 86400;
        total_time_spent = total_time_spent.concat(" ", total_day, "D");
      }
      total_hour = Math.floor(diff / 3600);
      if (total_hour > 0) {
        diff -= total_hour * 3600;
        total_time_spent = total_time_spent.concat(" ", total_hour, "H");
      }
      total_minute = Math.floor(diff / 60);
      if (total_minute > 0) {
        diff -= total_minute * 60;
        total_time_spent = total_time_spent.concat(" ", total_minute, "M");
      }
      if (diff > 0) {
        total_time_spent = total_time_spent.concat(" ", Math.round(diff), "S");
      }
      return total_time_spent;
    },
    totalTimes() {
      this.total_times = [];
      if (this.profileData?.waiting_end_date) {
        var waiting = new Date(this.profileData?.waiting_end_date + " UTC");
        waiting.toString();
        let created_date = new Date(this.profileData.created_at);
        created_date.toString();
        let diff = Math.abs(
          waiting.getTime() / 1000 - created_date.getTime() / 1000
        );
        let total_time_spent = this.checkSpentTime(diff);
        this.total_times.push({
          status: "waiting",
          storyboard_stage: 0,
          design_stage: 0,
          diff: diff,
          spent_time: total_time_spent,
        });
      } else {
        var waiting = new Date();
        waiting.toString();
        let created_date = new Date(this.profileData.created_at);
        created_date.toString();
        let diff = Math.abs(
          waiting.getTime() / 1000 - created_date.getTime() / 1000
        );
        let total_time_spent = this.checkSpentTime(diff);
        this.total_times.push({
          status: "waiting",
          storyboard_stage: 0,
          design_stage: 0,
          diff: diff,
          spent_time: total_time_spent,
        });
      }
      if (this.profileData.status_times?.length > 0) {
        this.profileData.status_times.forEach((e) => {
          let statusDate = new Date(e.created_at + " UTC");
          statusDate.toString();
          if (e.end_date) {
            let endDate = new Date(e.end_date + " UTC");
            endDate.toString();
            let diff = Math.abs(
              statusDate.getTime() / 1000 - endDate.getTime() / 1000
            );
            let total_time_spent = this.checkSpentTime(diff);
            this.total_times.push({
              status: e.status,
              storyboard_stage: e.status.includes("storyboard")
                ? e.storyboard_stage
                : 0,
              design_stage: e.status.includes("design") ? e.design_stage : 0,
              diff: diff,
              spent_time: total_time_spent,
            });
          } else {
            var d = new Date();
            d.toString();
            let created = new Date(e?.created_at + " UTC");
            created.toString();
            let diff = Math.abs(d.getTime() / 1000 - created.getTime() / 1000);
            let total_time_spent = this.checkSpentTime(diff);
            this.total_times.push({
              status: e.status,
              storyboard_stage: e.status.includes("storyboard")
                ? e.storyboard_stage
                : 0,
              design_stage: e.status.includes("design") ? e.design_stage : 0,
              diff: diff,
              spent_time: total_time_spent,
            });
          }
        });
      }
      for (let i = 1; i <= this.profileData.storyboard_reject_count + 1; i++) {
        let totalDiffStoryboard = 0;
        this.total_times.forEach((element) => {
          if (
            element.status.includes("storyboard") &&
            element.storyboard_stage == i
          ) {
            totalDiffStoryboard += element.diff;
          }
        });
        if (totalDiffStoryboard > 0) {
          let total_time_spent = this.checkSpentTime(totalDiffStoryboard);
          var totalIndex = null;
          for (let item = 0; item < this.total_times.length; item++) {
            if (
              this.total_times[item].status == "storyboard rejected" &&
              this.total_times[item].storyboard_stage == i
            ) {
              totalIndex = item + 1;
            } else if (
              this.total_times[item].status == "storyboard review" &&
              this.total_times[item].storyboard_stage == i
            ) {
              totalIndex = item + 1;
            }
          }
          if (totalIndex) {
            this.total_times.splice(totalIndex, 0, {
              status: "total_storyboard",
              storyboard_stage: i,
              design_stage: 0,
              diff: totalDiffStoryboard,
              spent_time: total_time_spent,
            });
          }
        }
      }
      for (let i = 1; i <= this.profileData.design_reject_count + 1; i++) {
        let totalDiffDesign = 0;
        this.total_times.forEach((element) => {
          if (element.status.includes("design") && element.design_stage == i) {
            totalDiffDesign += element.diff;
          }
        });
        if (totalDiffDesign > 0) {
          let total_time_spent = this.checkSpentTime(totalDiffDesign);
          var totalIndex = null;
          for (let item = 0; item < this.total_times.length; item++) {
            if (
              this.total_times[item].status == "design rejected" &&
              this.total_times[item].design_stage == i
            ) {
              totalIndex = item + 1;
            } else if (
              this.total_times[item].status == "design review" &&
              this.total_times[item].design_stage == i
            ) {
              totalIndex = item + 1;
            }
          }
          if (totalIndex) {
            this.total_times.splice(totalIndex, 0, {
              status: "total_design",
              storyboard_stage: 0,
              design_stage: i,
              diff: totalDiffDesign,
              spent_time: total_time_spent,
            });
          }
        }
      }
      if (this.profileData?.completed_date) {
        var completed = new Date(this.profileData?.completed_date + " UTC");
        completed.toString();
        let created_date = new Date(this.profileData.created_at);
        created_date.toString();
        let diff = Math.abs(
          completed.getTime() / 1000 - created_date.getTime() / 1000
        );
        let total_time_spent = this.checkSpentTime(diff);
        this.total_times.push({
          status: "completed",
          storyboard_stage: 0,
          design_stage: 0,
          diff: diff,
          spent_time: total_time_spent,
        });
      } else {
        if (this.total_times.length > 1) {
          let totalTime = 0;
          this.total_times.forEach((element) => {
            if (
              !element.status.includes("total") &&
              !element.status.includes("completed")
            ) {
              totalTime += element.diff;
            }
          });
          if (totalTime > 0) {
            let total_time_spent = this.checkSpentTime(totalTime);
            this.total_times.push({
              status: "total_time_spent",
              storyboard_stage: 0,
              design_stage: 0,
              diff: totalTime,
              spent_time: total_time_spent,
            });
          }
        }
      }
    },
    setTotalTimeSpent() {
      var d = new Date();
      d.toString();
      if (this.profileData?.status == "completed") {
        d = new Date(this.profileData?.completed_date + " UTC");
        d.toString();
      } else {
        d = new Date();
        d.toString();
      }
      let cancelledTotalSecond = 0;
      if (this.profileData.status_times?.length > 0) {
        this.profileData.status_times.forEach((e) => {
          if (e.status == "cancelled") {
            let cancelledDate = new Date(e.created_at + " UTC");
            cancelledDate.toString();
            if (e.end_date) {
              let endDate = new Date(e.end_date + " UTC");
              endDate.toString();
              let diff = Math.abs(
                cancelledDate.getTime() / 1000 - endDate.getTime() / 1000
              );
              cancelledTotalSecond += diff;
            } else {
              d = new Date(e?.created_at + " UTC");
              d.toString();
            }
          }
        });
      }
      if (this.profileData.status_times?.length > 0) {
        this.profileData.status_times.forEach((e) => {
          if (e.status == "removed") {
            let removedDate = new Date(e.created_at + " UTC");
            removedDate.toString();
            if (e.end_date) {
              let endDate = new Date(e.end_date + " UTC");
              endDate.toString();
              let diff = Math.abs(
                removedDate.getTime() / 1000 - endDate.getTime() / 1000
              );
              cancelledTotalSecond += diff;
            } else {
              d = new Date(e?.created_at + " UTC");
              d.toString();
            }
          }
        });
      }
      let created_date = new Date(this.profileData.created_at);
      created_date.toString();
      if (this.profileData.status_times?.length > 0) {
        this.profileData.status_times.forEach((e) => {
          if (e.end_date == null) {
            created_date = new Date(e.created_at + " UTC");
            created_date.toString();
          }
        });
      }
      let total_time_spent = "";
      let diff = Math.abs(d.getTime() / 1000 - created_date.getTime() / 1000);
      diff = diff - cancelledTotalSecond;
      total_time_spent = this.checkSpentTime(diff);
      this.total_time_spent = total_time_spent;
    },
    showEdit() {
      this.isEdit = !this.isEdit;
      this.editData = this.profileData;
    },

    userCandEditSalesNote() {
      if (this.$isInScope("drva")) {
        return true;
      }
      if (this.profileData.created_by) {
        if (this.profileData.created_by == this.$auth.user.id) {
          return true;
        }
        return false;
      }
      return false;
    },

    // check user can update the order notes or not
    authUserCanEdit(status) {
      // || this.profileData.status == status
      if (this.$isInScope("drva")) {
        return true;
      }
      let canEdit = false;
      if (this.profileData.design_request_order) {
        let userOrder =
          this.profileData?.design_request_order?.design_request_order_user;
        if (userOrder && userOrder.length) {
          canEdit =
            userOrder[0].user_can_edit && this.profileData.status == status;
        }
        return canEdit;
      }
      canEdit = !this.profileData.created_by.localeCompare(this.$auth.user.id);
      return canEdit;
    },
    selectOption(tab) {
      this.profileTabKey++;
      this.selected_item = tab;
    },
    async removeDesign(id) {
      this.selected_item = 0;
      this.user_id = id;
      this.fetchAllReasons("not assigned");
    },
    onReject() {
      const type =
        this.selected_item === 3 ? "design rejected" : "storyboard rejected";
      this.fetchAllReasons(type);
    },
    onApproved() {
      Alert.removeDialogAlert(this, () => this.onSubmit(), "", "yes_sure");
    },
    async fetchAllReasons(type) {
      this.isFetchingReasons = true;
      try {
        const response = await this.$axios.get(
          `reasons/id?type=${type}&sub_system=Design Request`
        );
        this.allReasons = response?.data?.reasons;
        this.showReasonDialog = true;
        this.isFetchingReasons = false;
      } catch (error) {
        this.showReasonDialog = false;
        if (error?.response?.status === 404 && !error?.response?.data?.result) {
          // this.$toastr.w(this.$tr("sub_system_not_found"));
						this.$toasterNA("orange",this.$tr("sub_system_not_found"));

        }
        this.isFetchingReasons = false;
      }
    },
    async onSubmit(selectedReasons) {
      let selectedStatus =
        this.selected_item === 0
          ? "not_assigned"
          : this.selected_item === 3
          ? selectedReasons == undefined
            ? "in_programming"
            : "design_rejected"
          : selectedReasons == undefined
          ? "in_design"
          : "storyboard_rejected";
      let data = {};
      if (this.selected_item === 0) {
        data = {
          user_id: this.user_id,
          reasons:
            typeof selectedReasons === "string"
              ? [selectedReasons]
              : selectedReasons,
          design_request_id: this.profileData.id,
          design_request_order_id: this.profileData?.design_request_order?.id,
        };
      } else {
        data =
          this.selected_item !== 0 && selectedReasons != undefined
            ? {
                item_ids: [this.profileData?.id],
                status: selectedStatus.replace("_", " "),
                reasons:
                  typeof selectedReasons === "string"
                    ? [selectedReasons]
                    : selectedReasons,
                type: "hasReason",
              }
            : {
                item_ids: [this.profileData?.id],
                status: selectedStatus.replace("_", " "),
                type: "noReason",
              };
      }
      this.showReasonDialog = false;
      try {
        const response =
          this.selected_item === 0
            ? await this.$axios.post(`design-request/not-assign-user`, data)
            : await this.$axios.post(`design-request/change-status`, data);
        if (response?.status === 200 && response?.data?.result) {
          if (this.selected_item === 0) {
            this.isAnyUserUnAssigned = true;
            this.assignedUsers = this.assignedUsers.filter(
              (ele) => ele.user.id != this.user_id
            );
          }
          Alert.successAlert(
            this,
            this.$tr("successfully", this.$tr(selectedStatus))
          );
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
      } catch (error) {
        if (
          [406, 403, 404, 400].includes(error?.response?.status) &&
          !error?.response?.data?.result
        ) {
          switch (error?.response?.status) {
            case 406:
              // this.$toastr.e(this.$tr("not_allowed_to_change_status"));
			this.$toasterNA("red", this.$tr('not_allowed_to_change_status'));

              break;
            case 403:
              this.$toasterNA("red" , this.$tr('employee_task_not_done'))
              break;
            case 400:
              let message = error?.response?.data?.message[0]?.includes(
                "storyboard"
              )
                ? "storyboard"
                : "design";
              message = this.$tr(
                "note_is_either_empty_or_not_enough",
                this.$tr(message)
              );
              this.$toasterNA("red" , message)
              break;
            case 404:
              this.$toasterNA("red" , this.$tr('no_employee_has_been_assigned'))
              break;
            default:
              // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr('something_went_wrong'));

          }
        } else HandleException.handleApiException(this, error);
      }
    },
    closeDialog() {
      this.showReasonDialog = false;
    },
    closeProfileDialog() {
      this.$emit("update:dialog");
      if (this.isAnyUserUnAssigned) this.$emit("fetchNewData");
    },
    toggleDrawer() {
      this.selected_item = "";
      this.$refs.sidebarDrawerRef.toggleDrawer();
    },
    toggleEditor() {
      if (this.showEditorPanel) {
        this.showEditorPanel = false;
      } else {
        this.editorKey++;
        this.showEditorPanel = true;
      }
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
  width: 280px !important;
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
.listInbox .selected {
  background-color: var(--v-surface-base) !important;
  /* border-left: 4px solid var(--v-primary-base); */
  color: var(--v-primary-base) !important;
}
.listInbox .selected.border_left {
  border-left: 4px solid var(--v-primary-base);
}
.listInbox .selected.border_right {
  border-right: 4px solid var(--v-primary-base);
}
.listInbox .not-selected.border_left {
  border-left: 4px solid transparent;
}
.listInbox .not-selected.border_right {
  border-right: 4px solid transparent;
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

.tooltip-top::before {
  border-right: solid 8px transparent;
  border-left: solid 8px transparent;
  transform: translateX(-50%);
  position: absolute;
  z-index: -21;
  content: "";
  top: 100%;
  left: 50%;
  height: 0;
  width: 0;
}
.tooltip-top::before {
  border-top: solid 8px #707070;
}
</style>
