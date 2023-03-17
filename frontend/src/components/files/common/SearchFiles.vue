<template>
  <div>
    <v-menu
      transition="slide-y-transition"
      :close-on-content-click="false"
      offset-y
      v-model="showMenu"
      max-width="500px"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          fab
          x-small
          text
          class="d-lg-none d-xl-none d-md-none d-sm-block d-block"
          v-bind="attrs"
          v-on="on"
          @click="(showMenu = !showMenu), $emit('search', searchData)"
        >
          <v-icon dark size="20"> mdi-magnify </v-icon>
        </v-btn>
      </template>
      <v-card width="100%">
        <v-card-text class="d-flex align-center">
          <v-form @submit.prevent="onClick" class="w-full">
            <v-text-field
              v-model.trim="searchData"
              hide-details
              rounded
              background-color="surface "
              class="pa-0 ma-0 custom-search2 me-1"
              :placeholder="$tr('search') + '...'"
            >
              <template slot="append" class="pe-0">
                <v-btn
                  fab
                  x-small
                  color="surface "
                  class="ma-0 me-1"
                  @click="onClick"
                  elevation="0"
                >
                  <v-icon color="primary" size="20"> mdi-magnify </v-icon>
                </v-btn>
              </template>
            </v-text-field>
          </v-form>
          <v-btn
            fab
            x-small
            color="surface "
            class="ms-0"
            @click="showMenu = false"
            elevation="0"
          >
            <v-icon color="primary" size="20"> mdi-close </v-icon>
          </v-btn>
        </v-card-text>
      </v-card>
    </v-menu>

    <v-form @submit.prevent="onClick">
      <v-text-field
        hide-details
        rounded
        background-color="surface "
        class="pa-0 ma-0 custom-search d-sm-none d-none d-lg-block d-xl-block d-md-block white"
        v-model.trim="searchData"
        color="white"
        :placeholder="$tr('search') + '...'"
      >
        <template slot="append" class="pe-0">
          <v-btn
            fab
            x-small
            text
            :loading="loading"
            color="primary"
            class="ma-0 me-1"
            @click="onClick"
          >
            <v-icon dark size="20"> mdi-magnify </v-icon>
          </v-btn>
        </template>
      </v-text-field>
    </v-form>
  </div>
</template>
<script>
export default {
  props: {
    loading: false,
  },
  data() {
    return {
      transitionVar: true,
      showMenu: false,
      searchData: "",
      typingTimer: null,
    };
  },

  watch: {
    searchData: function (value) {
      if (value) {
        this.onSearchChange();
      } else {
        this.onClick();
      }
    },
  },

  methods: {
    debounce(callback, wait) {
      clearTimeout(this.typingTimer);
      this.typingTimer = setTimeout(callback, wait);
    },

    onClick() {
      this.$emit("search", this.searchData);
    },

    onSearchChange() {
      if (this.loading) return;
      const callback = () => this.$emit("search", this.searchData);
      this.debounce(callback, 1000);
    },
  },
};
</script>
<style>
.custom-search .v-input__slot.surface,
.custom-search2 .v-input__slot.surface {
  padding: 4px 0 4px 20px !important;
}
.v-application--is-rtl .custom-search .v-input__slot.surface,
.v-application--is-rtl .custom-search2 .v-input__slot.surface {
  padding: 4px 20px 4px 0 !important;
}
.custom-search2 .v-input__slot.surface {
  padding-right: 0 !important;
}
.v-application--is-rtl .custom-search2 .v-input__slot.surface {
  padding-right: 16px;
  padding-left: 0 !important;
}
.custom-search2 .v-input__append-inner,
.custom-search .v-input__append-inner {
  padding: 0;
  margin: 0;
}
.custom-search input {
  caret-color: var(--input-title-color);
}
</style>
