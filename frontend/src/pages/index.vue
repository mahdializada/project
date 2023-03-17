<template>
  <v-container fluid class="pa-0">
    <!-- <TiktokInstantPage /> -->
    <!-- <v-btn @click="openProfile()">show product</v-btn> -->
    <ProductProfileStatistics ref="productProfile" />
    <client-only>
      <system-card></system-card>
    </client-only>
  </v-container>
</template>

<script>
import ProductProfileStatistics from "../components/advertisement/product-statistics-profile/ProductProfileStatistics.vue";
import SystemCard from "../components/dashboard/SystemCard.vue";
import TiktokInstantPage from "../components/tiktok-instant-page/TiktokInstantPage";

export default {
  components: {
    SystemCard,
    TiktokInstantPage,
    ProductProfileStatistics,
  },
  data() {
    return {};
  },
  methods: {
    openProfile() {
      this.$refs.productProfile.openDialog();
    },
  },
};
</script>

<style>
</style>
