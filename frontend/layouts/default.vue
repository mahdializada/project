<template>
  <v-app dark>
    <v-navigation-drawer v-model="drawer" :mini-variant="miniVariant" fixed app>
      <left_sidebar />
    </v-navigation-drawer>

    <v-app-bar app :clipped-left="clipped" fixed>
      <v-container fluid class="pa-0">
        <Header @drawing="miniVariant = !miniVariant" />
      </v-container>
    </v-app-bar>
    <v-main>
      <!-- Provides the application the proper gutter -->
      <v-container fluid>
        <Nuxt />
        <!-- If using vue-router -->
     
      </v-container>
    </v-main>

  </v-app>
</template>
<script>
import Header from "~/components/Common/Header.vue";
import Left_sidebar from "~/components/Common/left_sidebar.vue";
export default {
  auth: true,
  components: { Header, Left_sidebar },
  data() {
    return {
      clipped: false,
      drawer: true,
      fixed: false,
      miniVariant: false,
    };
  },
};
</script>

<style></style>
