<template>
  <v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
    <v-card
      class="pa-0"
      style="background-color: var(--v-surface-base)"
      elevation="0"
    >
      <div style="width: 100%">
        <div class="back">
          <div class="d-flex align-center pa-2">
            <div
              class="title ms-3 font-weight-bold white--text"
              :class="
                this.$vuetify.breakpoint.sm || this.$vuetify.breakpoint.xs
                  ? ''
                  : 'text-h5'
              "
            >
              {{ $tr("profile") }}
            </div>
            <v-spacer></v-spacer>
            <CloseBtn class="white--text" @click="$emit('update:dialog')" />
          </div>

          <v-card class="mt-2 mb-md-2 mx-5 px-2" elevation="0">
            <v-row class="d-flex">
              <v-col
                cols="12"
                md="4"
                class="pa-0 d-flex justify-center flex-column align-center"
                :style="
                  this.$vuetify.breakpoint.md ||
                  this.$vuetify.breakpoint.sm ||
                  this.$vuetify.breakpoint.xs
                    ? 'margin-top: -50px'
                    : 'margin-top: -70px'
                "
              >
                <span>
                  <v-avatar
                    color="white"
                    :size="
                      this.$vuetify.breakpoint.md ||
                      this.$vuetify.breakpoint.sm ||
                      this.$vuetify.breakpoint.xs
                        ? '90'
                        : '140'
                    "
                  >
                    <v-avatar
                      color="primary"
                      :size="
                        this.$vuetify.breakpoint.md ||
                        this.$vuetify.breakpoint.sm ||
                        this.$vuetify.breakpoint.xs
                          ? '80'
                          : '120'
                      "
                    >
                      <v-img
                        v-if="user.image"
                        :src="user.image"
                        alt="User Profile Pictrue"
                      ></v-img>
                      <span v-else class="white--text text-h4">
                        {{
                          user.lastname ? user.lastname[0].toUpperCase() : ""
                        }}
                      </span>
                    </v-avatar>
                  </v-avatar>
                </span>
                <span class="text-h6">{{ fullName }}</span>
                <span>{{ user.current_country.name }} </span>
              </v-col>

              <v-col
                cols="12"
                md="8"
                class="d-flex justify-sm-center justify-md-end py-0"
                style=""
              >
                <div class="d-flex align-center flex-column flex-md-row mx-3">
                  <span
                    v-if="
                      !(
                        this.$vuetify.breakpoint.md ||
                        this.$vuetify.breakpoint.sm ||
                        this.$vuetify.breakpoint.xs
                      )
                    "
                    style="
                      border-radius: 50px;
                      min-height: 60px;
                      margin-top: -140px;
                    "
                    class="px-3 d-flex di align-center elevation-5 surface lighten-1"
                  >
                    <div v-if="user.state" class="me-2">
                      <v-icon color="primary">mdi-map-marker </v-icon>

                      <span class="grey--text">{{
                        $tr("current_country") + " : "
                      }}</span>
                      <span class="text-no-wrap">
                        {{ user.current_country.name }}
                      </span>
                    </div>

                    <div>
                      <v-icon color="primary">mdi-progress-clock</v-icon>
                      <span class="grey--text">{{
                        $tr("last_login") + " : "
                      }}</span>
                      <span> {{ last_login }} </span>
                    </div>
                  </span>
                  <span v-else>
                    <span
                      style="border-radius: 50px; min-height: 45px"
                      class="px-3 d-flex di my-1 align-center elevation-5"
                    >
                      <div v-if="user.state">
                        <v-icon color="primary">mdi-map-marker </v-icon>

                        <span
                          :style="
                            this.$vuetify.breakpoint.sm ||
                            this.$vuetify.breakpoint.xs
                              ? 'font-size:12px'
                              : ''
                          "
                          class="grey--text"
                          >{{ $tr("current_country") + " : " }}</span
                        >
                        <span
                          :style="
                            this.$vuetify.breakpoint.sm ||
                            this.$vuetify.breakpoint.xs
                              ? 'font-size:12px'
                              : ''
                          "
                          class="text-nowrap"
                        >
                          {{ user.current_country.name }}
                        </span>
                      </div>
                    </span>

                    <span
                      style="border-radius: 50px; min-height: 45px"
                      class="px-3 d-flex di align-center elevation-5"
                    >
                      <div>
                        <v-icon color="primary">mdi-progress-clock </v-icon>

                        <span
                          :style="
                            this.$vuetify.breakpoint.sm ||
                            this.$vuetify.breakpoint.xs
                              ? 'font-size:12px'
                              : ''
                          "
                          class="grey--text"
                          >{{ $tr("last_login") + " : " }}</span
                        >
                        <span
                          :style="
                            this.$vuetify.breakpoint.sm ||
                            this.$vuetify.breakpoint.xs
                              ? 'font-size:12px'
                              : ''
                          "
                        >
                          {{ last_login }}
                          <!-- 20 min ago -->
                        </span>
                      </div>
                    </span>
                  </span>
                </div>
              </v-col>
            </v-row>
            <v-container class="pa-0 mt-2 mb-2 pb-2">
              <v-tabs
                v-model="tabs"
                class="custome"
                hide-slider
                grow
                center-active
                show-arrows
              >
                <div class="d-flex w-full align-end">
                  <v-tab
                    v-for="(item, n) in items"
                    :key="n"
                    style="height: 100%"
                  >
                    {{ $tr(item.tab) }}
                  </v-tab>

                  <div
                    class="w-full"
                    style="
                      height: 1px;
                      width: 1px;
                      background-color: var(--v-surface-darken1);
                    "
                  ></div>
                </div>
              </v-tabs>
              <v-tabs-items v-model="tabs">
                <v-tab-item>
                  <v-card
                    outlined
                    style="background-color: inherit; height: 35vh"
                    elevation="0"
                    class="mt-2 overflow-y-auto overflow-x-hidden font-weight-regular"
                  >
                    <ProfileCustomeRow
                      headerRow="email"
                      :linkRow1="`mailto:` + user.email"
                      :linkName1="user.email"
                      :contentRowTooltip="[]"
                      headerRow2="phone"
                      :linkRow2="`tel:` + user.phone"
                      :linkName2="user.phone"
                      :contentRow2Tooltip="[]"
                    ></ProfileCustomeRow>

                    <ProfileCustomeRow
                      bgColor
                      headerRow="whatsapp"
                      :linkRow1="`https://wa.me/` + user.whatsapp"
                      :linkName1="user.whatsapp"
                      :contentRowTooltip="[]"
                      headerRow2="nationality"
                      :contentRow2="user.country.national"
                      :contentRow2Tooltip="[]"
                    >
                    </ProfileCustomeRow>

                    <ProfileCustomeRow
                      headerRow="current_country"
                      :contentRow="user.current_country.name"
                      :contentRowTooltip="[]"
                      headerRow2="city"
                      :contentRow2="user.state.name"
                      :contentRow2Tooltip="[]"
                    ></ProfileCustomeRow>

                    <ProfileCustomeRow
                      bgColor
                      headerRow="company"
                      tooltipName="companies"
                      :contentRowTooltip="user.companies"
                      headerRow2="language"
                      :contentRow2="user.language.name"
                      :contentRow2Tooltip="[]"
                    ></ProfileCustomeRow>

                    <ProfileCustomeRow
                      headerRow="team"
                      tooltipName="teams"
                      :contentRowTooltip="user.teams"
                      headerRow2="status"
                      :contentRow2="user.status"
                      :contentRow2Tooltip="[]"
                    ></ProfileCustomeRow>

                    <ProfileCustomeRow
                      bgColor
                      headerRow="gender"
                      :contentRow="user.gender"
                      :contentRowTooltip="[]"
                      headerRow2="birthday_date"
                      :contentRow2="user.birth_date"
                      :contentRow2Tooltip="[]"
                    ></ProfileCustomeRow>

                    <ProfileCustomeRow
                      headerRow="created_at"
                      :contentRow="localeHumanReadableTime(user.created_at)"
                      :contentRowTooltip="[]"
                      headerRow2="updated_at"
                      :contentRow2="localeHumanReadableTime(user.updated_at)"
                      :contentRow2Tooltip="[]"
                    ></ProfileCustomeRow>

                    <ProfileCustomeRow
                      singleRow
                      bgColor
                      headerRow="address"
                      :contentRow="user.address"
                      :contentRowTooltip="[]"
                    ></ProfileCustomeRow>
                  </v-card>
                </v-tab-item>

                <v-tab-item>
                  <v-card
                    outlined
                    style="background-color: inherit; height: 35vh"
                    elevation="0"
                    class="mt-2 overflow-y-auto overflow-x-hidden"
                  >
                    <v-expansion-panels elevation="0">
                      <v-expansion-panel
                        style="border-radius: 0 !important; box-shadow: none"
                      >
                        <v-expansion-panel-header>
                          {{ $tr("roles") }}
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                          <ul v-for="(item, i) in user.roles" :key="i">
                            <li>
                              {{ item.name }}
                            </li>
                          </ul>
                        </v-expansion-panel-content>
                      </v-expansion-panel>
                    </v-expansion-panels>

                    <v-expansion-panels>
                      <v-expansion-panel
                        style="
                          background-color: var(--v-surface-darken1);
                          border-radius: 0 !important;
                          box-shadow: none;
                        "
                      >
                        <v-expansion-panel-header>
                          {{ $tr("permissions") }}
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                          <v-list
                            v-for="(item, i) in user.permissions"
                            :key="i"
                            class="py-0 cust"
                            style="background-color: inherit"
                          >
                            <v-list-group :value="false">
                              <template v-slot:activator>
                                <v-list-item-title>{{ i }}</v-list-item-title>
                              </template>
                              <v-list-group
                                :value="false"
                                no-action
                                sub-group
                                v-for="(it, j) in item"
                                :key="j"
                              >
                                <template v-slot:activator>
                                  <v-list-item-content>
                                    <v-list-item-title>{{
                                      j
                                    }}</v-list-item-title>
                                  </v-list-item-content>
                                </template>

                                <v-list-group
                                  :value="false"
                                  no-action
                                  sub-group
                                  v-for="(ite, f) in it"
                                  :key="f"
                                  class="nana"
                                >
                                  <template v-slot:activator>
                                    <v-list-item-content>
                                      <v-list-item-title>{{
                                        f
                                      }}</v-list-item-title>
                                    </v-list-item-content>
                                  </template>

                                  <v-list-item
                                    v-for="(items, m) in ite"
                                    :key="m"
                                    link
                                  >
                                    <v-list-item-title
                                      v-text="items"
                                    ></v-list-item-title>
                                  </v-list-item>
                                </v-list-group>
                              </v-list-group>
                            </v-list-group>
                          </v-list>
                        </v-expansion-panel-content>
                      </v-expansion-panel>
                    </v-expansion-panels>
                  </v-card>
                </v-tab-item>

                <v-tab-item>
                  <v-card
                    outlined
                    style="background-color: inherit; height: 35vh"
                    elevation="0"
                    class="mt-2 overflow-y-auto pa-1 overflow-x-hidden"
                  >
                    {{ user.note }}
                  </v-card>
                </v-tab-item>

                <v-tab-item>
                  <v-card
                    outlined
                    style="background-color: inherit; height: 35vh"
                    elevation="0"
                    class="mt-2 overflow-y-auto overflow-x-hidden"
                  >
                    <StatusTransformationTab
                      :tableItems="user.session_activity"
                      :tableHeaders="tableHeaders"
                    />
                  </v-card>
                </v-tab-item>

                <v-tab-item>
                  <v-card
                    outlined
                    style="background-color: inherit; height: 35vh"
                    elevation="0"
                    class="mt-2 overflow-y-auto overflow-x-hidden"
                  >
                    <v-data-table
                      item-key="id"
                      :headers="activityLogs"
                      :items="paginatedItems"
                      :items-per-page="10"
                      class="elevation-0"
                      hide-default-footer
                      :no-data-text="$tr('no_data_available')"
                      style="background-color: inherit"
                    >
                      <template v-slot:[`item.content`]="{ item }">
                        <v-tooltip bottom>
                          <template v-slot:activator="{ on, attrs }">
                            <span
                              class="primary--text"
                              dark
                              v-bind="attrs"
                              style="white-space: nowrap"
                              v-on="on"
                            >
                              {{ $tr("contents") }}
                            </span>
                          </template>
                          <span class="mt-2" v-html="item.content"></span>
                        </v-tooltip>
                      </template>
                      <template v-slot:[`item.date`]="{ item }">
                        {{ localeHumanReadableTime(item.date + item.time) }}
                      </template>
                    </v-data-table>
                    <v-row class="pb-3 align-center ma-0 py-2 tbl-bottom">
                      <v-col cols="0" md="4" class="pa-0"></v-col>
                      <v-col class="py-0" cols="12" md="4">
                        <div
                          class="text-center text-center mx-auto d-flex align-center justify-center"
                        >
                          <p class="ma-0 me-2">
                            {{
                              $tr(
                                "showing_items",
                                started_at,
                                ended_at,
                                paginatedItems.length
                              )
                            }}
                          </p>
                          <div
                            style="
                              width: 90px !important;
                              margin: 0.16rem 0.16rem;
                            "
                          ></div>
                        </div>
                      </v-col>
                      <v-col class="py-1 py-md-0 d-flex" cols="12" md="4">
                        <div class="mx-auto mx-md-0 ms-md-auto">
                          <CustomPagination
                            paginateSmall
                            @paginate="paginateFunc"
                            :pageCount="
                              parseInt(Math.ceil(paginatedItems.length / 10))
                            "
                          />
                        </div>
                      </v-col>
                    </v-row>
                  </v-card>
                </v-tab-item>

                <v-tab-item>
                  <v-card
                    outlined
                    style="background-color: inherit; height: 35vh"
                    elevation="0"
                    class="mt-2 overflow-y-auto overflow-x-hidden"
                  >
                    <StatusTransformationTab
                      :tableItems="user.status_transformations"
                    />
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <ProfileStorage
                    v-if="user.has_storage"
                    :storage="{
                      ...user.storage,
                      ...user.storage.Alldata,
                    }"
                  />
                  <v-card
                    v-else
                    outlined
                    style="background-color: inherit; height: 35vh"
                    elevation="0"
                    class="mt-2 overflow-y-auto overflow-x-hidden"
                  />
                </v-tab-item>
              </v-tabs-items>
            </v-container>
          </v-card>
        </div>
      </div>
    </v-card>
  </v-card>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn";
import ProfileCustomeRow from "../masters/ProfileCustomeRow.vue";
import moment from "moment-timezone";
import CustomPagination from "../design/components/CustomPagination.vue";
import StatusTransformationTab from "../../components/common/StatusTransformationTab.vue";
import ProfileStorage from "./ProfileStorage.vue";

export default {
  components: {
    StatusTransformationTab,
    CloseBtn,
    ProfileCustomeRow,
    CustomPagination,
    ProfileStorage,
  },
  props: {
    dialog: {
      type: Boolean,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      tableHeaders: [
        { text: this.$tr("browser"), value: "browser", sortable: false },
        { text: this.$tr("location"), value: "location", sortable: false },
        { text: this.$tr("login_time"), value: "login_time", sortable: false },
      ],
      activityLogs: [
        { text: this.$tr("system"), value: "system", sortable: false },
        { text: this.$tr("sub_system"), value: "page", sortable: false },
        { text: this.$tr("event"), value: "event", sortable: false },
        { text: this.$tr("content"), value: "content", sortable: false },
        { text: this.$tr("date"), value: "date", sortable: false },
      ],
      paginatedItems: this.user.log_activity.slice(0, 10),
      started_at: this.user.log_activity.length ? 1 : 0,
      ended_at:
        this.user.log_activity.length >= 10
          ? 10
          : this.user.log_activity.length,

      items: [
        { tab: "general_information" },
        { tab: this.$tr("roles") + " & " + this.$tr("permissions") },
        { tab: "remarks" },
        { tab: "activity_sessions" },
        { tab: "activity_logs" },
        { tab: "status_transformation" },
      ],
      admins: [
        ["Management", "mdi-account-multiple-outline"],
        ["Settings", "mdi-cog-outline"],
      ],
      items2: [],
      tabs: null,
      tabs2: null,
      sessions: [],
      paginated_session: [],
      started_at: 0,
      ended_at: 0,
      last_login: "no_login_yet",
      dateTime: "",
    };
  },

  computed: {
    fullName() {
      const firstname = this.user?.firstname || "";
      const lastname = this.user?.lastname || "";
      return firstname + " " + lastname;
    },
  },
  methods: {
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },

    paginateFunc(page) {
      let start_index = (page - 1) * 10;
      this.started_at = start_index + 1;
      this.paginatedItems = [];
      for (let index = start_index; index < start_index + 10; index++) {
        if (this.user.log_activity[index] !== undefined) {
          this.ended_at = index + 1;
          this.paginatedItems.push(this.user.log_activity[index]);
        }
      }
    },
  },
  created() {
    this.sessions = this.user.session_activity.reverse();
    if (this.sessions.length > 0) {
      this.last_login = this.sessions[0].login_time;
      this.last_login = this.localeHumanReadableTime(this.last_login);
    }
    this.paginateFunc(1);
    if (this.user && this.user.has_storage) {
      this.items.push({ tab: "storage" });
    }
  },
};
</script>

<style scoped>
a {
  text-decoration: none;
  color: black !important;
}

.theme--dark a {
  color: white !important;
}

.border-radius-1 {
  border-radius: 2px !important;
}
.back {
  background: linear-gradient(
    to bottom,
    var(--v-anchor-base) 0%,
    var(--v-anchor-base) 50%,
    var(--v-surface-base) 50%,
    var(--v-surface-base) 100%
  );
}
</style>
<style>
.custome .v-tab--active {
  border: 1px solid var(--v-surface-darken1);
  border-bottom: 1px solid transparent !important;
  border-radius: 3px 3px 0 0;
}
.custome .v-tab {
  border-bottom: 1px solid var(--v-surface-darken1);
}
.di .theme--light.v-divider,
.di .theme--dark.v-divider {
  border: 1px solid rgba(0, 0, 0, 0.09) !important;
}
</style>
<style>
.cust .v-list-item__icon.v-list-group__header__prepend-icon {
  margin-top: 5px;
  margin-bottom: 4px;
  margin-left: 30px;
}
.cust .nana .v-list-item__icon.v-list-group__header__prepend-icon {
  margin-bottom: 0;

  margin-left: 50px;
}
.cust .v-list-group__header__append-icon .v-icon {
  font-size: 2rem !important;
}
.cust .v-list-item__title {
  font-size: 13px !important;
}
.cust .v-list-item {
  min-height: 37px !important;
}
</style>
