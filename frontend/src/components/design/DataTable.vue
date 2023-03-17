<template>
  <v-card :elevation="elevation" class="w-full">
    <v-card-title
      class="pa-0 overflow-hidden"
      style="border-radius: 0px !important"
    >
      <slot name="customHeader" />
    </v-card-title>

    <client-only>
      <v-data-table
        :show-select="show_select"
        :item-key="itemkey"
        dense
        :no-data-text="$tr('no_data_available')"
        :loading-text="$tr('loading_text')"
        v-bind="$attrs"
        v-on="$listeners"
        :items-per-page="itemsPerPage"
        virtual-rows
        fixed-header
        :height="height"
        @update:options="onTableChanges"
        :header-props="{ sortIcon: 'fa-sort' }"
        disable-items-per-page
        hide-default-footer
        :page.sync="page"
        :options.sync="options"
        :headers="headers2"
        :class="disbale_data_table_hover"
        :hide-default-header="!$vuetify.breakpoint.xsOnly"
      >
        <template v-slot:[`body`] v-if="$attrs.loading">
          <!-- <Loader /> -->
          <tbody class="data-table-loader" transition="fade-transition">
            <tr v-for="i in 20" :key="i" class="data-table-loader">
              <td
                v-for="j in headers2.length > 0 ? 1 + headers2.length : 10"
                :key="j"
              >
                <v-skeleton-loader type="text"></v-skeleton-loader>
              </td>
            </tr>
          </tbody>
        </template>
        <template v-slot:[`item.gender`]="{ item }">
          <span class="mx-1">
            {{ getGender(item) }}
          </span>
        </template>
        <template
          v-slot:header="{ on, props }"
          v-if="!$vuetify.breakpoint.xsOnly"
        >
          <tr class="custom-header-wrapper">
            <th
              :class="`${
                $vuetify.rtl ? 'text-right' : 'text-left'
              } custom-header`"
              v-for="(head, i) in props.headers"
              :key="i"
            >
              <v-checkbox
                v-if="i == 0"
                v-model="selectAll"
                class="ma-0 px-2"
                style="height: 30px"
                @change="on['toggle-select-all'](selectAll)"
                hide-details
              >
              </v-checkbox>
              <div
                :class="`px-2 item ${sortBy == head.value ? 'sorted' : ''}`"
                v-if="i !== 0"
                @click="head.sortable !== 0 ? on.sort(head.value) : () => {}"
              >
                {{ head.text }}
                <v-tooltip bottom color="primary" max-width="220px">
                  <template v-slot:activator="{ on, attrs }">
                    <v-icon x-small v-bind="attrs" v-on="on">
                      fa-info-circle
                    </v-icon>
                  </template>
                  <span class="" v-html="head.tooltip || 'not set'" />
                </v-tooltip>
                <v-icon x-small v-if="head.sortable !== false">fa-sort</v-icon>
              </div>
            </th>
          </tr>
        </template>
        <template v-slot:[`item.created_by`]="{ item }">
          {{ getCreatedBy(item) }}
        </template>

        <template v-slot:[`item.done_by`]="{ item }">
          {{ getDoneBy(item) }}
        </template>

        <template v-slot:[`item.updated_by`]="{ item }">
          {{ getUpdatedBy(item) }}
        </template>
        <template v-slot:[`item.created_at`]="{ item }">
          {{ localeHumanReadableTime(item.created_at) }}
        </template>

        <template v-slot:[`item.done_date`]="{ item }">
          {{ localeHumanReadableTime(item.done_date) }}
        </template>

        <template v-slot:[`item.updated_at`]="{ item }">
          {{ localeHumanReadableTime(item.updated_at) }}
        </template>

        <template v-slot:[`item.code`]="{ item }">
          <span
            @click="$emit('viewSelectedItem', item)"
            class="mx-1 primary--text font-weight-bold"
          >
            {{ item.code }}
          </span>
        </template>

        <template slot="item.COUNTER" slot-scope="props">
          {{ props.index + 1 }}
        </template>

        <!-- --------    DATATABLE SYSTEMS SECTION        ---------->
        <template v-slot:[`item.systems`]="{ item }">
          <div>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{ $tr("systems") }}
                </span>
              </template>
              <div v-if="2 > 1">
                <div v-if="item.systems.length > 0">
                  <p
                    v-for="system in item.systems"
                    :key="system.index"
                    class="pb-0 mb-0"
                  >
                    {{ system.name }}
                  </p>
                </div>
                <div v-else>
                  <p class="pb-0 mb-0">
                    {{ $tr("no_item", $tr("system")) }}
                  </p>
                </div>
              </div>
            </v-tooltip>
          </div>
        </template>

        <template v-if="useDefaltStatus" v-slot:[`item.status`]="{ item }">
          <div
            :class="`status ${
              item.status == 'active' ||
              item.status == 'live' ||
              item.status == 'approved' ||
              item.status == 'published' ||
              item.status == 'in design'
                ? 'design'
                : item.status == 'in programming'
                ? 'programming'
                : item.status == 'in programming review'
                ? 'programming_review'
                : item.status == 'inactive' || item.status == 'in storyboard'
                ? 'storyboard'
                : item.status == 'blocked' ||
                  item.status == 'archive' ||
                  item.status == 'cancelled'
                ? 'cancelled'
                : item.status == 'storyboard review'
                ? 'storyreview'
                : item.status == 'pending' || item.status == 'waiting'
                ? 'waiting'
                : item.status == 'design review'
                ? 'designreview'
                : item.status == 'warning' ||
                  item.status == 'rejected' ||
                  item.status == 'onhold' ||
                  item.status == 'storyboard rejected'
                ? 'rejected'
                : item.status == 'removed' ||
                  item.status == 'expired' ||
                  item.status == 'deleted'
                ? 'error-2'
                : item.status == 'storyboard rejected'
                ? 'warning-2'
                : item.status == 'design rejected'
                ? 'secondary-2'
                : item.status == 'completed'
                ? 'completed'
                : 'primary'
            }`"
          >
            {{ $tr(item.status) }}
          </div>
        </template>
        <!----------    DATATABLE Countries SECTION        ---------->
        <template v-slot:[`item.countries`]="{ item }" v-if="defaultCountry">
          <div>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{ $tr("countries") }}
                </span>
              </template>
              <div v-if="2 > 1">
                <div v-if="item.countries.length > 0">
                  <p
                    v-for="country in item.countries"
                    :key="country.index"
                    class="pb-0 mb-0"
                  >
                    {{ country.name }}
                  </p>
                </div>
                <div v-else>
                  <p class="pb-0 mb-0">
                    {{ $tr("no_item", $tr("countries")) }}
                  </p>
                </div>
              </div>
            </v-tooltip>
          </div>
        </template>

        <!----------    DATATABLE Department SECTION        ---------->
        <template v-slot:[`item.departments`]="{ item }">
          <div>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{ $tr("departments") }}
                </span>
              </template>
              <div v-if="2 > 1">
                <div v-if="item.departments.length > 0">
                  <p
                    v-for="department in item.departments"
                    :key="department.index"
                    class="pb-0 mb-0"
                  >
                    {{ department.name }}
                  </p>
                </div>
                <div v-else>
                  <p class="pb-0 mb-0">
                    {{ $tr("no_item", $tr("departments")) }}
                  </p>
                </div>
              </div>
            </v-tooltip>
          </div>
        </template>

        <!----------    DATATABLE Company SECTION        ---------->
        <template v-slot:[`item.companies`]="{ item }">
          <div>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{ $tr("companies") }}
                </span>
              </template>
              <div v-if="item.companies.length > 0">
                <p
                  v-for="company in item.companies"
                  :key="company.index"
                  class="pb-0 mb-0"
                >
                  {{ company.name }}
                </p>
              </div>
              <div v-else>
                <p class="pb-0 mb-0">
                  {{ $tr("no_item", $tr("companies")) }}
                </p>
              </div>
            </v-tooltip>
          </div>
        </template>

        <!----------    DATATABLE Department SECTION        ---------->
        <template v-slot:[`item.roles`]="{ item }">
          <div>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{ $tr("roles") }}
                </span>
              </template>
              <div v-if="2 > 1">
                <p
                  v-for="role in item.roles"
                  :key="role.index"
                  class="pb-0 mb-0"
                >
                  {{ role.name }}
                </p>
              </div>
              <div v-else>
                <p class="pb-0 mb-0">
                  {{ $tr("no_item", $tr("roles")) }}
                </p>
              </div>
            </v-tooltip>
          </div>
        </template>

        <template v-slot:[`item.target_sale_country`]="{ item }">
          <div>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{
                    item.target_sale_country[0]
                      ? $tr("target_sale_country")
                      : ""
                  }}
                </span>
              </template>
              <div v-if="2 > 1">
                <p
                  v-for="target_sale in item.target_sale_country"
                  :key="target_sale.index"
                  class="pb-0 mb-0"
                >
                  {{ target_sale.name }}
                </p>
              </div>
              <div v-else>
                <p class="pb-0 mb-0">
                  {{ $tr("no_item", $tr("target_sale_country")) }}
                </p>
              </div>
            </v-tooltip>
          </div>
        </template>

        <!----------    DATATABLE Department SECTION        ---------->
        <template v-slot:[`item.teams`]="{ item }">
          <div>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  {{ $tr("teams") }}
                </span>
              </template>
              <div v-if="2 > 1">
                <p
                  v-for="team in item.teams"
                  :key="team.index"
                  class="pb-0 mb-0"
                >
                  {{ team.name }}
                </p>
              </div>
              <div v-else>
                <p class="pb-0 mb-0">
                  {{ $tr("no_item", $tr("teams")) }}
                </p>
              </div>
            </v-tooltip>
          </div>
        </template>

        <!----------    DATATABLE Address SECTION        ---------->
        <template v-slot:[`item.address`]="{ item }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <span v-bind="attrs" v-on="on" style="white-space: nowrap">
                {{ getSubString(item.address) }}
              </span>
            </template>
            <span>{{ item.address }}</span>
          </v-tooltip>
        </template>

        <!----------    DATATABLE Note SECTION        ---------->
        <template v-slot:[`item.note`]="{ item }" v-if="defaultNote">
          <v-tooltip bottom max-width="400px">
            <template v-slot:activator="{ on, attrs }">
              <span v-bind="attrs" v-on="on" style="white-space: nowrap">
                <span v-html="getSubString(item.note, 40)"></span>
              </span>
            </template>
            <span v-html="item.note"></span>
          </v-tooltip>
        </template>

        <!----------    DATATABLE Image SECTION        ---------->
        <template v-slot:[`item.image`]="{ item }">
          <v-menu
            offset-y
            open-on-hover
            :close-on-content-click="false"
            open-delay="500"
          >
            <template v-slot:activator="{ on, attrs }">
              <span
                style="white-space: nowrap"
                v-bind="attrs"
                v-on="on"
                class="mb-1"
              >
                <v-img
                  class="rounded-circle"
                  width="30"
                  height="30"
                  lazy-src="https://picsum.photos/id/11/10/6"
                  :src="item.image"
                ></v-img>
              </span>
            </template>
            <span>
              <v-img
                width="400"
                lazy-src="https://picsum.photos/id/11/10/6"
                :src="item.image"
              ></v-img>
            </span>
          </v-menu>
        </template>
        <template v-slot:[`item.product_image`]="{ item }">
          <v-menu
            offset-y
            open-on-hover
            :close-on-content-click="false"
            open-delay="500"
          >
            <template v-slot:activator="{ on, attrs }">
              <span
                style="white-space: nowrap"
                v-bind="attrs"
                v-on="on"
                class="mb-1"
              >
                <v-img
                  class="rounded-circle"
                  width="30"
                  height="30"
                  lazy-src="https://picsum.photos/id/11/10/6"
                  :src="item?.attachment[0]?.path"
                ></v-img>
              </span>
            </template>
            <span>
              <v-img
                width="400"
                lazy-src="https://picsum.photos/id/11/10/6"
                :src="item?.attachment[0]?.path"
              ></v-img>
            </span>
          </v-menu>
        </template>

        <!----------    DATATABLE Image SECTION        ---------->
        <!----------    DATATABLE icon SECTION        ---------->
        <template v-slot:[`item.icon`]="{ item }">
          <template>
            <span style="white-space: nowrap" class="mb-1">
              <v-img
                class="rounded-circle"
                width="30"
                height="30"
                lazy-src="https://picsum.photos/id/11/10/6"
                :src="item.icon"
              ></v-img>
            </span>
          </template>
        </template>

        <!----------    DATATABLE icon SECTION        ---------->
        <!----------    DATATABLE banner SECTION        ---------->
        <template v-slot:[`item.banner`]="{ item }">
          <template>
            <span style="white-space: nowrap" class="mb-1">
              <v-img
                class="rounded-circle"
                width="30"
                height="30"
                lazy-src="https://picsum.photos/id/11/10/6"
                :src="item.banner"
              ></v-img>
            </span>
          </template>
        </template>

        <!----------    DATATABLE banner SECTION        ---------->
        <!----------    DATATABLE Logo SECTION        ---------->
        <template v-slot:[`item.logoAvatar`]="{ item }">
          <v-avatar :size="30" color="blue-grey darken-3">
            <span class="white--text text-h6 pa-1">
              {{ item.name ? item.name[0].toUpperCase() : "" }}
            </span>
          </v-avatar>
        </template>

        <template v-slot:[`item.logo`]="{ item }" v-if="defaultLogo">
          <!-- <v-menu v-if="item.logo" offset-y open-on-hover :close-on-content-click="false">
            <span>
              <v-img v-if="isPreview" width="400" lazy-src="https://picsum.photos/id/11/10/6" :src="item.logo"></v-img>
            </span>
          </v-menu> -->
          <span style="white-space: nowrap" class="mb-1">
            <v-img
              class="rounded-circle"
              width="30"
              height="30"
              lazy-src="https://picsum.photos/id/11/10/6"
              :src="item.logo"
            ></v-img>
          </span>
        </template>

        <template v-slot:[`item.platform.platform_name`]="{ item }">
          <span
            v-if="item.platform && item.platform.platform_name"
            class="text-capitalize"
          >
            {{ item.platform.platform_name }}
          </span>
        </template>

        <template v-slot:[`item.platform.logo`]="{ item }">
          <span style="white-space: nowrap" class="mb-1">
            <v-img
              class="rounded-circle"
              width="30"
              height="30"
              lazy-src="https://picsum.photos/id/11/10/6"
              :src="item.platform.logo"
            ></v-img>
          </span>
        </template>
        <!----------    DATATABLE Logo SECTION        ---------->

        <!-- Pass on all named slots -->
        <slot v-for="slot in Object.keys($slots)" :name="slot" :slot="slot" />

        <!-- Pass on all scoped slots -->
        <template
          v-for="slot in Object.keys($scopedSlots)"
          :slot="slot"
          slot-scope="scope"
        >
          <slot :name="slot" v-bind="scope" />
        </template>
      </v-data-table>
    </client-only>
    <v-row class="pb-3 align-center ma-0 py-2 tbl-bottom" v-if="hasPagination">
      <v-col cols="0" :md="paginateSmall ? '0' : '4'" class="pa-0"></v-col>
      <v-col class="py-0" cols="12" :md="paginateSmall ? '5' : '4'">
        <div
          class="text-center text-center mx-auto d-flex align-center justify-center"
        >
          <p class="ma-0 me-2">
            {{
              $tr(
                "showing_items",
                this.options.itemsPerPage * (this.options.page - 1),
                this.options.itemsPerPage == -1
                  ? this.ItemsLength
                  : this.options.itemsPerPage * this.options.page > ItemsLength
                  ? ItemsLength
                  : this.options.itemsPerPage * this.options.page,
                ItemsLength
              )
            }}
          </p>
          <CustomButton
            icon="fa-sync-alt fa-3x"
            text=""
            type="primary"
            class="customDisabled"
            v-if="showRefreshBtn"
          />

          <div style="width: 90px !important; margin: 0.16rem 0.16rem">
            <v-select
              v-model="itemsPerPage2"
              :items="perpageDropdown"
              label=""
              item-text="text"
              item-value="value"
              dense
              outlined
              :menu-props="{ bottom: true, offsetY: true }"
              hide-details="auto"
              @input="perPageSelect"
            >
            </v-select>
          </div>
        </div>
      </v-col>
      <v-col
        class="py-1 py-md-0 d-flex"
        cols="12"
        :md="paginateSmall ? '7' : '4'"
      >
        <div class="mx-auto mx-md-0 ms-md-auto">
          <CustomPagination
            paginateSmall
            @paginate="paginateFunc"
            :pageCount="parseInt(Math.ceil(ItemsLength / itemsPerPage2))"
          />
        </div>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
import Alert from "../../helpers/alert";
import CustomPagination from "./components/CustomPagination.vue";
import CustomButton from "./components/CustomButton.vue";
import { mapActions, mapGetters } from "vuex";
import moment from "moment-timezone";
import Loader from "./Loader.vue";
export default {
  name: "DataTable",
  components: { CustomButton, CustomPagination, Loader },
  props: {
    itemkey: {
      type: String,
      default: "id",
    },
    hasPagination: {
      type: Boolean,
      default: true,
    },
    paginateSmall: {
      type: Boolean,
      default: false,
    },
    showRefreshBtn: {
      type: Boolean,
      default: true,
    },
    disbale_data_table_hover: {
      type: String,
      default: "",
    },
    perpageDropdown: {
      type: Array,
      default: function () {
        return [
          //  10, 20, 50, 100 , 200, 500 , 100 , all
          {
            text: "2",
            value: 2,
          },
          {
            text: "10",
            value: 10,
          },
          {
            text: "20",
            value: 20,
          },
          {
            text: "50",
            value: 50,
          },
          {
            text: "100",
            value: 100,
          },
          {
            text: "200",
            value: 200,
          },
          {
            text: "500",
            value: 500,
          },
          {
            text: "1000",
            value: 1000,
          },
          {
            text: this.$tr("all"),
            value: -1,
          },
        ];
      },
    },
    height: {
      type: String,
      default: "400px",
    },
    elevation: {
      type: Number,
      default: 24,
    },
    show_select: {
      type: Boolean,
      default: true,
    },
    ItemsLength: {
      type: Number,
    },
    // Table Actions Props
    showRestore: {
      type: Boolean,
      default: false,
    },

    showDetails: {
      type: Boolean,
      default: false,
    },

    showUpdate: {
      type: Boolean,
      default: true,
    },
    showDelete: {
      type: Boolean,
      default: true,
    },
    defaultCountry: {
      type: Boolean,
      default: true,
    },
    itemsPerPage: {
      type: Number,
      default: 10,
    },
    defaultNote: {
      type: Boolean,
      default: true,
    },
    defaultLogo: {
      type: Boolean,
      default: true,
    },
    headers: {
      type: Array,
    },
    useDefaltStatus: {
      type: Boolean,
      default: true,
    },
  },
  async created() {
    await this.translateHeaders();
  },

  watch: {
    headers: function () {
      this.headers2 = this.headers.map((header) => {
        return {
          ...header,
          text: this.$tr(header.text),
        };
      });
    },
    getTranslatedLanguage: async function () {
      await this.translateHeaders();
    },
  },

  data() {
    return {
      show_tooltip: false,
      secondTime: false,
      isSecondTime: false,
      perPageFunc: true,
      selectAll: false,
      headers2: [],
      page: 0,
      canPaginate: false,
      itemsPerPage2: 10,
      options: {
        groupBy: [],
        groupDesc: [],
        itemsPerPage: this.itemsPerPage,
        multiSort: false,
        mustSort: false,
        page: 1,
        sortBy: [],
        sortDesc: [false],
      },

      // Logo Overly
      isPreview: false,
      timer: null,
      overlay: false,
      overlayLogo: "",
      sortBy: "",
      force_reset: false,
    };
  },

  async mounted() {
    this.itemsPerPage2 = await JSON.parse(JSON.stringify(this.itemsPerPage));
    this.canPaginate = true;
  },

  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      getTranslatedLanguage: "translations/getTranslatedLanguage",
    }),
  },

  methods: {
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
    }),

    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },

    async translateHeaders() {
      if (this.getTranslations.length == 0) {
        await this.fetchTranslations({
          language_id: this.$auth.user.translated_language_id,
        });
      }
      this.headers2 = this.headers.map((header) => {
        return {
          ...header,
          text: this.$tr(header.text),
        };
      });
    },

    getUpdatedBy(item) {
      const firstname = item?.updated_by?.firstname || "";
      const lastname = item?.updated_by?.lastname || "";

      return firstname + " " + lastname;
    },

    getCreatedBy(item) {
      const firstname = item?.created_by?.firstname || "";
      const lastname = item?.created_by?.lastname || "";
      return firstname + " " + lastname;
    },
    getDoneBy(item) {
      const firstname = item?.done_by?.firstname || "";
      const lastname = item?.done_by?.lastname || "";
      return firstname + " " + lastname;
    },

    onTableChanges(options) {
      if (this.force_reset) {
        this.force_reset = false;
        return;
      }
      if (this.secondTime) {
        this.sortBy = options.sortBy[0];
        if (this.canPaginate) {
          this.$emit("onTablePaginate", options);
        }
      } else {
        this.secondTime = true;
      }
    },

    paginateFunc(page) {
      this.page = page;
      this.options = {
        ...this.options,
        itemsPerPage: this.itemsPerPage2,
        page,
      };
      this.onTableChanges(this.options);
    },

    perPageSelect() {
      this.options = {
        ...this.options,
        itemsPerPage: this.itemsPerPage2,
      };
      this.$root.$emit("resetPageNumber");
    },

    // Table Footer Actions Methods
    onMultiUpdate() {
      this.$emit("onMultiUpdate");
    },
    onMultiDelete() {
      this.$emit("onMultiDelete");
    },
    onMultiRestore() {
      this.$emit("onMultiRestore");
    },

    // Table Action Methods
    onUpdate(item) {
      this.$emit("onUpdate", item);
    },

    async onDelete(item) {
      await Alert.removeDialogAlert(this, () => {
        this.$emit("onDelete", item);
      });
    },

    getGender(item) {
      let gender = "";
      if (item.gender === "male") {
        gender = this.$tr("male");
      } else if (item.gender === "female") {
        gender = this.$tr("female");
      }
      return gender;
    },

    onDetails(item) {
      this.$emit("onDetails", item);
    },

    getSubString(value, length = 20) {
      let subText = value?.substring(0, length);
      if (value?.length > length) {
        subText += "...";
      }
      return subText;
    },

    // line break on note
    lineBreak(data) {
      let string = data + "";
      return string.replace(/(\S+\s*){1,7}/g, "$&\n");
    },

    forceReset() {
      this.force_reset = true;
      this.options.sortBy = [];
      this.options.sortDesc = [];
      this.options.page = 1;
      this.sortBy = "";
      this.secondTime = true;
    },
  },
};
</script>
<style scoped>
.custom-header {
  font-size: 12px;
  cursor: pointer;
}

.custom-header-wrapper {
  background: white;
  position: sticky;
  top: 0px;
  z-index: 5;
  /* box-shadow: #00b41d; */
  box-shadow: 0 6px 6px -6px rgba(0, 0, 0, 0.3);
}

.theme--dark.v-data-table .custom-header-wrapper {
  background: #05090c;
  position: sticky;
  top: 0px;
  z-index: 5;
  box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.3);
}

.status {
  max-width: 190px;
  max-height: 32px;
  border-radius: 20px;
  color: white;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2px 6px !important;
}

.cancelled {
  background: #ff00001a !important;
}

.error-2 {
  background: var(--v-error-darken3) !important;
}

.warning-2 {
  background: var(--v-warning-darken3) !important;
}

.info-2 {
  background: var(--v-info-darken2) !important;
}

.secondary-2 {
  background: var(--v-secondary-darken2) !important;
}

.success-2 {
  background: var(--v-success-darken2) !important;
}

.completed {
  background: #00b41d !important;
}

.waiting {
  background: #ddb099 !important;
}

.storyboard {
  background: #e6af2e !important;
}

.storyreview {
  background: #ff9700 !important;
}

.design {
  background: #2ebce6 !important;
}

.designreview {
  background: #0a90d3 !important;
}

.programming {
  background: #4791f7 !important;
}

.programming_review {
  background: #1e6ad4 !important;
}

.rejected {
  background: #ff0000 !important;
}
</style>
<style>
/* .v-icon.v-data-table-header__icon.fa.fa-sort {
  font-size: 14px !important;
  margin: 0 14px;
  opacity: 1 !important;
  transform: unset !important;
} */

.v-data-table .v-input--selection-controls__ripple {
  margin-left: 0 !important;
  margin-right: 0 !important;
  left: -5px !important;
}

.v-icon.notranslate.mdi.mdi-checkbox-blank-outline.theme--light {
  opacity: 0.6;
}

.tbl-bottom {
  border-top: 1px solid #ccc;
}

tbody tr:nth-of-type(odd) {
  /* background-color: rgba(0, 0, 0, 0.02) !important; */
}

.v-data-table--dense > .v-data-table__wrapper > table > tbody > tr > td,
.v-data-table--dense > .v-data-table__wrapper > table > thead > tr > td,
.v-data-table--dense > .v-data-table__wrapper > table > tfoot > tr > td {
  font-size: 0.9rem !important;
}

.custom-header .item .v-icon {
  margin-left: 4px !important;
}

.v-data-table .custom-header .item .v-icon {
  font-size: 16px !important;
}

.v-data-table .custom-header .item,
.v-data-table .custom-header .item .v-icon {
  color: rgba(0, 0, 0, 0.55) !important;
}

.theme--dark.v-data-table .custom-header .item,
.theme--dark.v-data-table .custom-header .item .v-icon {
  color: rgba(255, 255, 255, 0.6) !important;
}

.v-data-table .custom-header .item.sorted,
.v-data-table .custom-header .item.sorted .v-icon {
  color: rgba(0, 0, 0, 1) !important;
}

.theme--dark.v-data-table .custom-header .item.sorted,
.theme--dark.v-data-table .custom-header .item.sorted .v-icon {
  color: rgba(255, 255, 255, 1) !important;
}

.data-table-loader .v-skeleton-loader__text {
  margin: 0 !important;
}
</style>

<style scoped>
.v-tooltip__content::after {
  content: " ";
  position: absolute;
  bottom: 100%;
  /* At the top of the tooltip */
  right: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: var(--v-primary-base);
}

.v-tooltip__content {
  opacity: 1 !important;
}
</style
