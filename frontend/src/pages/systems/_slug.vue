<template>
  <v-container fluid class="pa-0">
    <v-card elevation="24" class="rounded-0">
      <v-card-title
        class="white--text primary text-h5 py-1 font-weight-bold justify-center"
      >
        {{ $tr("sub_systems") }}
      </v-card-title>
      <v-card-text class="pt-3">
        <v-row>
          <template v-for="(system, key) in systems">
            <v-col
              cols="12"
              md="2"
              sm="4"
              xs="6"
              :key="key"
              v-if="system.text && $isInScope(system.scope)"
              class="py-2 d-flex justify-center"
            >
              <nuxt-link
                v-if="$isInScope(system.scope)"
                :to="{
                  path: system.items
                    ? system.key === 'settings' ||
                      system.key === 'language_setting'
                      ? `/systems/${system.subLink}`
                      : `/systems/${system.text}`
                    : system.link,
                }"
                style="text-decoration: none; color: inherit"
              >
                <div
                  class="d-flex flex-column"
                  style="max-width: 160px; cursor: pointer"
                >
                  <div
                    class="rounded-circle d-flex justify-center mx-auto"
                    style="border: 3px solid #eeef; padding: 2px"
                  >
                    <v-icon large v-if="system.icon">{{ system.icon }}</v-icon>
                  </div>
                  <p class="text-center mt-1 mb-0 font-weight-medium">
                    {{ $tr(system.key) }}
                  </p>
                </div>
              </nuxt-link>
            </v-col>
          </template>
        </v-row>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import SystemCard from "~/components/dashboard/SystemCard.vue";
import NavRoutes from "../../configs/navigation";

export default {
  components: {
    SystemCard,
  },

  data() {
    return {
      systemName: "",
    };
  },
  async asyncData({ params }) {
    let systems = [];
    const systemName = params.slug;
    for (const routes of NavRoutes.menu) {
      for (const levelOne of routes.items) {
        if (levelOne.link !== "/" && levelOne.text === systemName) {
          systems = levelOne.items;
          for (const levelTwo of levelOne.items) {
            if (levelTwo.link !== "/" && levelTwo.text === systemName) {
              systems = levelTwo.items;
            }
          }
        } else if (levelOne.link !== "/") {
          for (const levelTwo of levelOne.items) {
            if (levelTwo.link !== "/" && levelTwo.subLink === systemName) {
              systems = levelTwo.items;
            }
          }
        }
      }
    }

    return {
      systems,
    };
  },

  methods: {
    goesToPageRoute(route) {
      this.$router.push(route);
    },
  },
};
</script>

<style></style>
