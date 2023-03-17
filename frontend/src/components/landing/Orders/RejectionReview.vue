<template>
  <v-card height="80vh">
    <v-card-title class="py-1 primary">
      <div class="d-flex align-center mt-2">
        <img
          v-if="requestDetails.status == 'cancelled'"
          alt=""
          src="/icons/LANDING_REJECT_ICONS/storyboard.svg"
          width="40px"
        />
        <img
          v-else-if="requestDetails.status == 'design rejected'"
          alt=""
          src="/icons/LANDING_REJECT_ICONS/design.svg"
          width="40px"
        />
        <img
          v-else
          alt=""
          src="/icons/LANDING_REJECT_ICONS/storyboard.svg"
          width="40px"
        />
        <span class="text-subtitle-1 ps-1 text-uppercase white--text">
          {{ requestDetails.status }}</span
        >
      </div>
      <v-spacer />
      <CloseBtn @click="$emit('closeDialog')" class="white--text" />
    </v-card-title>

    <v-card-text class="py-2" v-if="requestDetails.reasons.length > 0">
      <v-expansion-panels
        accordion
        v-for="(item, i) in requestDetails.reasons"
        :key="i"
      >
        <v-expansion-panel>
          <v-expansion-panel-header>
            <div class="d-flex align-center">
              <v-icon color="error">mdi-alert-circle-outline</v-icon>
              <p class="mb-0 ps-1" style="font-size: 18px; font-weight: 400">
                {{ item.status + ` ` + ++i }}
              </p>
              <v-spacer></v-spacer>
              <span class="mr-2">{{
                new Date(item.created_at).toLocaleDateString()
              }}</span>
            </div>
            <template v-slot:actions>
              <v-icon color="primary"> mdi-chevron-down-circle-outline </v-icon>
            </template>
          </v-expansion-panel-header>
          <v-expansion-panel-content class="elevation-0">
            <ul v-if="item.design_request_reject_reason.length > 0">
              <li
                v-for="(item2, i) in item.design_request_reject_reason"
                :key="i"
              >
                <span> {{ item2.reasons.title }}</span>
              </li>
            </ul>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-card-text>
    <v-card-text v-else>
      <h3 class="text-center grey--text mt-5">
        {{ $tr("no_reject_reasons") }}
      </h3>
    </v-card-text>
  </v-card>
</template>

<script>
import CloseBtn from "../../design/Dialog/CloseBtn.vue";
export default {
  components: { CloseBtn },

  props: {
    requestDetails: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      note: "",
      title: "sales_note",
    };
  },

  methods: {},

  created() {},
};
</script>

<style></style>
