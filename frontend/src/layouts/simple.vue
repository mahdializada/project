<template>
  <v-app>
    <div
      class="pa-2 pa-md-4 flex-grow-1 align-center justify-center d-flex flex-column"
    >
      <nuxt />
    </div>
    <LoadingComponent :loadingProps.sync="loading" />
  </v-app>
</template>
<script>
import { mapActions } from "vuex";
import LoadingComponent from "../components/LoadingComponent.vue";

export default {
  components: {
    LoadingComponent,
  },
  data() {
    return {
      loading: true,
    };
  },
  async created() {
    if (process.client) {
      if (this.$auth.loggedIn) {
        await this.fetchTranslations({
          language_id: this.$auth.user.translated_language_id,
        });
      } else {
        await this.fetchTranslations({
          baseLanguage: true,
        });
      }
      this.loading = false;
    }
  },
  methods: {
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
    }),
  },
};
</script>
