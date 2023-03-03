<template>
  <v-card :elevation="elevation" class="w-full">
    <v-card-title
      class="pa-0 overflow-hidden"
      style="border-radius: 0px !important"
    >
      <slot name="customHeader" />
    </v-card-title>
    <v-data-table
    :show-select="show_select"
    item-key="id"
    dense
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
    :headers="headers"
    class="disbale-data-table-hover"
    >
    <template v-slot:[`item.gender`]="{ item }">
        <span class="mx-1">
        {{ getGender(item) }}
        </span>
    </template>
    <template v-slot:[`item.created_by`]="{ item }">
        {{ getCreatedBy(item) }}
    </template>

    <template v-slot:[`item.updated_by`]="{ item }">
        {{ getUpdatedBy(item) }}
    </template>

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

    <template slot="item.COUNTER" slot-scope="props">
        {{ props.index + 1 }}
    </template>

    <!----------    DATATABLE SYSTEMS SECTION        ---------->
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
            <div v-if="item.systems">
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
                <p class="pb-0 mb-0">{{ $tr("no_item", $tr("system")) }}</p>
            </div>
            </div>
        </v-tooltip>
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
            <div v-if="item.countries">
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
            <div v-if="2 > 1">
            <!-- <div v-if="item.companies.length > 1"> -->
            <p
                v-for="company in item.companies"
                :key="company.index"
                class="pb-0 mb-0"
            >
                {{ company.name }}
            </p>
            </div>
            <div v-else>
            <p class="pb-0 mb-0">{{ $tr("no_item", $tr("companies")) }}</p>
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
            <p class="pb-0 mb-0">{{ $tr("no_item", $tr("roles")) }}</p>
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
            <p class="pb-0 mb-0">{{ $tr("no_item", $tr("teams")) }}</p>
            </div>
        </v-tooltip>
        </div>
    </template>

    <!----------    DATATABLE Note SECTION        ---------->
    <template v-slot:[`item.note`]="{ item }" v-if="defaultNote">
        <v-tooltip bottom max-width="400px">
        <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on" style="white-space: nowrap">
            {{ getSubString(item.note, 40) }}
            </span>
        </template>
        {{ item.note }}
        </v-tooltip>
    </template>

    <!----------    DATATABLE Logo SECTION        ---------->
    <!----------    DATATABLE Image SECTION        ---------->
    <template v-slot:[`item.logoAvatar`]="{ item }">
        <v-avatar :size="30" color="blue-grey darken-3">
        <span class="white--text text-h6 pa-1">
            {{ item.name ? item.name[0].toUpperCase() : "" }}
        </span>
        </v-avatar>
    </template>
    </v-data-table>
    <v-row class="pb-3 align-center ma-0 py-2 tbl-bottom">
      <v-col cols="0" md="4" class="pa-0"></v-col>
      <v-col class="py-0" cols="12" md="4">
        <div
          class="text-center text-center mx-auto d-flex align-center justify-center"
        >
          <CustomButton
            icon="fa-sync-alt fa-3x"
            text=""
            type="primary"
            class="customDisabled"
            v-if="showRefreshBtn"
          />

          <div style="width: 90px !important; margin: 0.16rem 0.16rem">
            <v-select
              v-model="itemsPerPage"
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
      <v-col class="py-1 py-md-0 d-flex" cols="12" md="4">
        <div class="mx-auto mx-md-0 ms-md-auto">
          <CustomPagination
            @paginate="paginateFunc"
            :pageCount="parseInt(Math.ceil(ItemsLength / itemsPerPage))"
          />
        </div>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
import Alert from "../../../helpers/alert";
import CustomPagination from "../components/CustomPagination.vue";
import CustomButton from "../components/CustomButton.vue";
import { mapActions, mapGetters } from "vuex";

export default {
  name: "DataTable",
  components: { CustomButton, CustomPagination },
  props: {
    showRefreshBtn: {
      type: Boolean,
      default: true,
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
      default: "250px",
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
  },
  data() {
    return {
      page: 0,
      canPaginate: false,
      options: {},
      // Logo Overly
      isPreview: false,
      timer: null,
      overlay: false,
      overlayLogo: "",
    };
  },

  mounted() {
    this.canPaginate = true;
  },
  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
    }),
  },
  methods: {
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
    }),
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

    onTableChanges(options) {
      if (this.canPaginate) {
        this.$emit("onTablePaginate", options);
      }
    },

    paginateFunc(page) {
      this.page = page;
      this.options.page = page;
      this.onTableChanges(this.options);
    },
    perPageSelect() {
      this.options.itemsPerPage = this.itemsPerPage2;
      this.onTableChanges(this.options);
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
    // leaveMove on logo
    async leaveMouse() {
      if (this.timer) {
        await clearTimeout(this.timer);
        this.isPreview = false;
        this.timer = null;
      }
    },
    // preview logo on mouse hover
    async previewLogo() {
      let vm = this;
      vm.isPreview = false;
      if (vm.timer) {
        await clearTimeout(vm.timer);
        vm.timer = null;
      }
      vm.timer = await setTimeout(function () {
        vm.isPreview = true;
      }, 800);
    },
  },
};
</script>
<style>
.disbale-data-table-hover > .v-data-table__wrapper > table > tbody > tr:hover {
  background-color: #fffff7 !important;
  color: black;
}
.v-icon.v-data-table-header__icon.fa.fa-sort {
  font-size: 14px !important;
  margin: 0 14px;
  opacity: 1 !important;
  transform: unset !important;
}

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
  background-color: rgba(0, 0, 0, 0.02) !important;
}

.v-data-table--dense > .v-data-table__wrapper > table > tbody > tr > td,
.v-data-table--dense > .v-data-table__wrapper > table > thead > tr > td,
.v-data-table--dense > .v-data-table__wrapper > table > tfoot > tr > td {
  font-size: 0.925rem !important;
}
</style>
