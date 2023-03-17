<template lang="">
  <v-card class="rounded-lg mx-1" outlined width="250" height="350">
    <v-card-title
      class="blue lighten-5 rounded-lg ma-1 h-100 d-flex justify-center position-relative"
    >
      <v-icon
        color="primary"
        class="position-absolute customCss2"
        @click="$emit('closeShowCard', itemsShow)"
        >mdi-close</v-icon
      >
      <v-avatar
        color="white"
        size="100"
        class="d-flex justify-center align-center customCss"
      >
        <v-icon color="primary" size="98">
          {{ itemsShow.selectedPlatform.icon }}
        </v-icon>
      </v-avatar>
    </v-card-title>
    <v-card-text
      class="d-flex justify-center flex-column align-center"
      style="margin-top: 60px"
    >
      <p class="mb-0 text-subtitle-1">{{ $tr(itemsShow.selectedPlatform.title) }}</p>
      <p class="text-capitalize text-body-1 mb-0 text-center">
        <span v-for="(item, index) in itemsShow.selectedPlacement">{{
          $tr(item) + ", "
        }}</span>
      </p>
      <div class="w-90 text-body-1 mt-3">
        <div class="d-flex justify-space-between align-center mx-1 my-1">
          <span class="d-flex">
            <v-icon color="grey">mdi-sack-percent</v-icon>
            <p class="mb-0 ms-1 grey--text">{{ $tr("budget") }}</p>
          </span>
          <p class="mb-0 primary--text">{{ itemsShow.budget + "$" }}</p>
        </div>
        <v-divider></v-divider>
        <div class="d-flex justify-space-between align-center mx-1 my-1">
          <span class="d-flex">
            <v-icon color="grey">mdi-bag-personal-tag</v-icon>
            <p class="mb-0 ms-1 grey--text">{{ $tr("target_CPO") }}</p>
          </span>
          <p class="mb-0 primary--text">{{ itemsShow.target_CPO + "$" }}</p>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>
<script>
export default {
  props: {
    itemsShow: Object,
  },
  methods: {},
  created() {},
};
</script>
<style scoped>
.borderGrey {
  height: 40px;
  width: 40px;
  border: 1px solid var(--v-surface-darken2);
  border-radius: 5px !important;
}
.h-100 {
  height: 100px;
}
.w-90 {
  width: 90%;
}
.customCss {
  position: absolute;
  top: 45%;
}
.customCss2 {
  top: 5px;
  right: 5px;
}
</style>
