<template>
  <master-profile
    @click="$emit('update:dialog')"
    titleProfile="Company"
    :profileData="profileData"
    :tabitem="items"
    :imageProfile="profileData.logo"
  >
    <template slot="slot_general">
      <ProfileCustomeRow
        headerRow="code_id"
        :contentRow="profileData.code"
        :contentRowTooltip="[]"
        headerRow2="created_by"
        :contentRow2="
          profileData.created_by.firstname +
          ' ' +
          profileData.created_by.lastname
        "
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        bgColor
        headerRow="investment_type"
        :contentRow="profileData.investment_type"
        :contentRowTooltip="[]"
        headerRow2="created_at"
        :contentRow2="localeHumanReadableTime(profileData.created_at)"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        headerRow="department"
        :contentRowTooltip="profileData.departments"
        tooltipName="departments"
        headerRow2="updated_by"
        :contentRow2="
          profileData.updated_by.firstname +
          ' ' +
          profileData.updated_by.lastname
        "
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        bgColor
        headerRow="company_country"
        :contentRowTooltip="profileData.countries"
        tooltipName="countries"
        headerRow2="updated_at"
        :contentRow2="localeHumanReadableTime(profileData.updated_at)"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>
    </template>
    <template slot="slot_remarks">
      <div class="rounded-sm mt-2 mx-3">
        <p class="text-primary ps-1" style="font-size: 1rem">
          {{ profileData.note }}
        </p>
      </div>
    </template>
    <template slot="slot_status_transformation">
      <StatusTransformationTab :tableItems="profileData.status_transformations" />		
    </template>
  </master-profile>
</template>

<script>
	import StatusTransformationTab from '../../components/common/StatusTransformationTab.vue';

import CloseBtn from "../design/Dialog/CloseBtn";
import CustomInput from "../design/components/CustomInput";
import FlagIcon from "../common/FlagIcon";
import MasterProfile from "./MasterProfile.vue";
import ProfileCustomeRow from "./ProfileCustomeRow.vue";
import moment from "moment-timezone";

export default {
  name: "ProjectProfile",
  components: {
    StatusTransformationTab,
    FlagIcon,
    CustomInput,
    CloseBtn,
    MasterProfile,
    ProfileCustomeRow,
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
  data() {
    return {
      items: [
        { tab: "general" },
        { tab: "remarks" },
        { tab: "status_transformation" },
      ],
    };
  },
  methods: {
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },
  },
};
</script>
<style></style>
