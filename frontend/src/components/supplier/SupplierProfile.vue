<template>
  <master-profile
    :tabitem="items"
    titleProfile="supplier"
    @click="$emit('update:dialog')"
    :profileData="profileData"
    name_selector="supplier_name"
    >
    <!-- :isEditBtn="true"
    @onEdit="onEdit"
    :isOpenEdit="isOpenEdit"
    @cancel="cancel"
    @onSave="onSave"
    :isLoading="isLoading" -->
    <template slot="slot_general">
      <ProfileCustomeRow
        
        headerRow="company"
        :contentRowTooltip="profileData.companies"
        tooltipName="companies"
        headerRow2="supply_type"
        :contentRow2="profileData.supply_type"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        bgColor
        
        headerRow="legal_type"
        :contentRow="profileData.legal_type"
        :contentRowTooltip="[]"
        headerRow2="country_type"
        :contentRow2="profileData.country_type"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>
      <ProfileCustomeRow
        headerRow="supplier_source"
        :contentRow="profileData.sourcer"
        :contentRowTooltip="[]"
        headerRow2="commercial_type"
        :contentRow2="profileData.commercial_type"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        bgColor
        
        headerRow="created_by"
        :contentRow="
          profileData.created_by.firstname +
          '  ' +
          profileData.created_by.lastname
        "
        :contentRowTooltip="[]"
        headerRow2="updated_by"
        :contentRow2="
          profileData.updated_by.firstname +
          '  ' +
          profileData.updated_by.lastname
        "
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>
      <ProfileCustomeRow
        
        headerRow="created_at"
        :contentRow="localeHumanReadableTime(profileData.created_at)"
        :contentRowTooltip="[]"
        headerRow2="updated_at"
        :contentRow2="localeHumanReadableTime(profileData.updated_at)"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>
    </template>
    <template slot="slot_remarks">
      <div class="rounded-sm mt-2 mx-3" >
        <p class="text-primary ps-1" style="font-size: 1rem">
          {{ profileData.note }}
        </p>
      </div>
    </template>
    <template slot="slot_supplier_information">
      <ProfileCustomeRow
        
        headerRow="supplier_name"
        :contentRow="profileData.supplier_name"
        :contentRowTooltip="[]"
        headerRow2="supplier_trading_name"
        :contentRow2="profileData.supplier_trading_name"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        bgColor
        
        headerRow="main_phone"
        :contentRow="profileData.main_phone"
        :contentRowTooltip="[]"
        headerRow2="email"
        :contentRow2="profileData.email"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        
        headerRow="purchase_order_phone"
        :contentRow="profileData.purchase_order_phone"
        :contentRowTooltip="[]"
        headerRow2="prepration_period"
        :contentRow2="profileData.prepration_period"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        
        headerRow="status"
        :contentRow="profileData.status"
        :contentRowTooltip="[]"
        headerRow2="code_id"
        :contentRow2="profileData.code"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        
        bgColor
        singleRow
        headerRow="website"
        :contentRow="profileData.website"
        :contentRowTooltip="[]"
      ></ProfileCustomeRow>
    </template>

    <!-- <template slot="slot_general">
      <HCustomProfileEdit
        v-show="isEdit"
        headerRow="company"
        :contentRowTooltip="profileData.companies"
        :contentRowTooltipItems="companies"
        headerRow2="supply_type"
        :contentRowSelect2="profileData.supply_type"
        :items2="supplyTypes"
      ></HCustomProfileEdit>

      <HCustomProfileEdit
        bgColor
        v-show="isEdit"
        headerRow="legal_type"
        :contentselect="profileData.legal_type"
        :contentselectItems="legalTypes"
        headerRow2="country_type"
        :contentRowSelect2="profileData.country_type"
        :items2="countryTypes"
      ></HCustomProfileEdit>

      <HCustomProfileEdit
        v-show="isEdit"
        headerRow="supplier_source"
        :contentRowTooltip="profileData.sourcers"
        :contentRowTooltipItems="sourcers"
        headerRow2="commercial_type"
        :contentRowSelect2="profileData.commercial_type"
        :items2="commercialTypes"
      ></HCustomProfileEdit>
    </template>
    <template slot="slot_remarks">
      <div class="rounded-sm mt-2 mx-3" v-show="isEdit">
        <CustomInput v-model="note" type="textfield" />
      </div>
    </template>
    <template slot="slot_supplier_information">
      <HCustomProfileEdit
        v-show="isEdit"
        headerRow="supplier_name"
        :contentRow="profileData.supplier_name"
        headerRow2="supplier_trading_name"
        :contentRow2="profileData.supplier_trading_name"
      ></HCustomProfileEdit>

      <HCustomProfileEdit
        bgColor
        v-show="isEdit"
        headerRow="main_phone"
        :contentRow="profileData.main_phone"
        headerRow2="email"
        :contentRow2="profileData.email"
      ></HCustomProfileEdit>

      <HCustomProfileEdit
        v-show="isEdit"
        headerRow="purchase_order_phone"
        :contentRow="profileData.purchase_order_phone"
        headerRow2="prepration_period"
        :contentRow2="profileData.prepration_period"
      ></HCustomProfileEdit>


      <HCustomProfileEdit
        v-show="isEdit"
        bgColor
        singleRow
        headerRow="website"
        v-model="website"
      ></HCustomProfileEdit>
    </template> -->

    <template slot="slot_status_transformation">
      <v-data-table
        :headers="$tr(headers)"
        :items="dataTable"
        :items-per-page="3"
        class="elevation-0"
        hide-default-footer
        style="background-color: inherit"
      >
      </v-data-table>
    </template>
  </master-profile>
</template>
<script>
import moment from "moment-timezone";
import CloseBtn from "~/components/design/Dialog/CloseBtn.vue";
import MasterProfile from "~/components/masters/MasterProfile.vue";
import ProfileCustomeRow from "~/components/masters/ProfileCustomeRow.vue";
import CustomInput from "../design/components/CustomInput.vue";
// import HCustomProfileEdit from "../new_form_components/components/HCustomProfileEdit.vue";
import { mapActions, mapGetters } from "vuex";

export default {
  components: {
    CloseBtn,
    MasterProfile,
    ProfileCustomeRow,
    // HCustomProfileEdit,
    CustomInput,
  },
  props: {
    dialog: {
      type: Boolean,
      required: true,
    },
    profileData: {
      type: Object,
      required: true,
    },
  },
  // computed: {
  //   ...mapGetters({
  //     sourcers: "sourcers/getSourcers",
  //   }),
  // },
  methods: {
    // ...mapActions({
    //   fetchSourcers: "sourcers/fetchSourcers",
    // }),
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },
    // onEdit() {
    //   (this.isView = false), (this.isEdit = true);
    //   this.isOpenEdit = true;
    //   this.fetchCompanies();
    //   if (this.sourcers.length == 0) {
    //     this.fetchSourcers();
    //   }
    // },
    // confirmClose(){
    //   if (this.isEdit == true) {
    //     this.$TalkhAlertNA(
    //       "Do you want to save the Changes?",
    //       "saveChanges",
    //       async () => this.onClose(true)
    //     );
    //   }else{
    //     this.onClose(true)
    //   }
    // },
    // onClose(save = false) {
    // if (save) {
    //   this.onSave()
    // }
    //   this.$emit("update:dialog");
    //   this.isView = true;
    //   this.isEdit = false;
    //   this.isOpenEdit = false;
    //   this.isLoading = false;
    // },
    // cancel() {
    //   this.isView = true;
    //   this.isEdit = false;
    //   this.isOpenEdit = false;
    //   this.isLoading = false;
    // },
    // async fetchCompanies() {
    //   try {
    //     const url = "advertisement/platforms/fetch?companies=true";
    //     const { data } = await this.$axios.get(url);
    //     this.companies = data;
    //   } catch (error) {}
    // },
    // onSave() {
    //   this.isLoading = true;
    //   console.log("hashmat", this.website,this.note);
    // },
  },
  data() {
    return {
      // website:this.profileData.website,
      // note:this.profileData.note,
      // isOpenEdit: false,
      // isLoading: false,
      // companies: [],
      // commercialTypes: [
      //   { id: "1", type: "Distributer" },
      //   { id: "2", type: "Brand" },
      //   { id: "3", type: "Factory" },
      // ],
      // countryTypes: [
      //   { id: "1", type: "Local" },
      //   { id: "2", type: "International" },
      // ],
      // supplyTypes: [
      //   { id: "1", type: "Row Material" },
      //   { id: "2", type: "Ready Made" },
      // ],
      // legalTypes: [
      //   { id: "1", type: "Company" },
      //   { id: "2", type: "Indivitual" },
      // ],
      items: [
        { tab: "general" },
        { tab: "supplier_information" },
        { tab: "remarks" },
        { tab: "status_transformation" },
      ],
      // isView: true,
      // isEdit: false,
      headers: [
        { text: "status", sortable: false },
        { text: this.$tr("applied_by"), sortable: false },
        { text: "reasons", sortable: false },
        { text: "date", sortable: false },
      ],
      dataTable: [],
      tabs: null,
    };
  },
};
</script>

<style></style>
