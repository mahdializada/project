<template>
  <v-card height="90vh">
    <v-card-title class="py-1 primary">
      <div class="d-flex align-center mt-2">
        <img
          v-if="title == 'sales_note'"
          alt=""
          src="/icons/LANDING_NOTE_ICONS/sales-icon-light.svg"
          width="40px"
        />
        <img
          v-else-if="title == 'storyboard_note'"
          alt=""
          src="/icons/LANDING_NOTE_ICONS/storyboard-icon-light.svg"
          width="40px"
        />
        <img
          v-else
          alt=""
          src="/icons/LANDING_NOTE_ICONS/design-icon-light.svg"
          width="40px"
        />
        <span class="text-subtitle-1 ps-1 text-uppercase white--text">{{
          $tr(title)
        }}</span>
      </div>
      <v-spacer />
      <CloseBtn @click="$emit('closeDialog')" class="white--text" />
    </v-card-title>
    <v-card-text class="px-4 py-3">
      <!-- <div class="d-flex align-center">
        <v-avatar size="40" color="primary">A</v-avatar>
        <div class="ms-1">
          <p class="mb-0 text-subtitle-2">Nasim Alizai</p>
          <p class="mb-0">Created on: 2021-2-20 (04: 34: 08 pm)</p>
        </div>
      </div> -->
      <div style="overflow-y: auto; max-height: 65vh">
        <div v-html="note"></div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import CloseBtn from "../../design/Dialog/CloseBtn.vue";
export default {
  components: { CloseBtn },
  props: {
    title: {
      type: String,
      require: true,
    },
    note: {
      type: String,
      require: true,
    },
  },
};
</script>

<style>
</style>