<template>
  <div class="white ps-4 d-flex align-center justify-space-between">
    <span v-if="selected_ads.ad_name">
      <v-avatar size="40" class="grey lighten-3 me-1">
        <v-avatar size="35" color="white">
          <v-avatar size="30" color="white">
            <v-avatar size="30" class="text-h6 primary white--text">
              {{ selected_ads.ad_name[0] }}
            </v-avatar>
          </v-avatar>
        </v-avatar>
      </v-avatar>
      <span class="dialog-title"> {{ selected_ads.ad_name }}</span>
    </span>
    <div class="d-flex align-center">
      <slot name="date_slot"></slot>
      <ad-profile-svg class="ms-2" />
    </div>
  </div>
</template>

<script>
import AdProfileSvg from "./AdProfileSvg.vue";
export default {
  props: { selected_ads: Object },
  components: { AdProfileSvg },
};
</script>

<style>
</style>