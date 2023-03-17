<template>
  <master-profile
    :tabitem="items"
    titleProfile="business_location"
    @click="$emit('update:dialog')"
    :profileData="profileData"
  >
    <template slot="slot_general">
      <ProfileCustomeRow
        headerRow="name"
        :contentRow="profileData.name"
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
        headerRow="location_type"
        :contentRow="profileData.location_type"
        :contentRowTooltip="[]"
        headerRow2="created_at"
        :contentRow2="localeHumanReadableTime(profileData.created_at)"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        headerRow="status"
        :contentRow="profileData.status"
        :contentRowTooltip="[]"
        headerRow2="updated_at"
        :contentRow2="localeHumanReadableTime(profileData.updated_at)"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        bgColor
        headerRow="email"
        :contentRow="profileData.email"
        :contentRowTooltip="[]"
        headerRow2="updated_by"
        :contentRow2="
          profileData.updated_by.firstname +
          ' ' +
          profileData.updated_by.lastname
        "
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
    <template slot="slot_business_location">
      <ProfileCustomeRow
        headerRow="country"
        :contentRow="profileData.country.name"
        :contentRowTooltip="[]"
        headerRow2="state"
        :contentRow2="profileData.state.name"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>

      <ProfileCustomeRow
        bgColor
        headerRow="company"
        :contentRow="profileData.company.name"
        :contentRowTooltip="[]"
        :headerRow2="$tr('company') + ' ' + $tr('email')"
        :contentRow2="profileData.company.email"
        :contentRow2Tooltip="[]"
      ></ProfileCustomeRow>
      <ProfileCustomeRow
        singleRow
        headerRow="address"
        :contentRow="profileData.address"
        :contentRowTooltip="[]"
      ></ProfileCustomeRow>
    </template>
    <template slot="slot_status_transformation">
      <StatusTransformationTab :tableItems="profileData.status_transformations" />		
    </template>
  </master-profile>
</template>
<script>
	import StatusTransformationTab from '../../../components/common/StatusTransformationTab.vue';

import moment from "moment-timezone";
import CloseBtn from "../../design/Dialog/CloseBtn.vue";
import MasterProfile from "../../masters/MasterProfile.vue";
import ProfileCustomeRow from "../../masters/ProfileCustomeRow.vue";

export default {
  components: { StatusTransformationTab, CloseBtn, MasterProfile, ProfileCustomeRow },
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
  methods: {
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },
  },
  data() {
    return {
      items: [
        { tab: "general" },
        { tab: "remarks" },
        { tab: "business_location" },
        { tab: "status_transformation" },
      ],
    };
  },
};
</script>

<style></style>
