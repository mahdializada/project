<template>
  <v-text-field
    hide-details
    rounded
    dense
    v-model="search"
    background-color="bg-custom-gray"
    :class="`pa-0 ma-0 column-search ${expand ? 'expand' : ''} ${
      $vuetify.rtl ? 'rtl' : ''
    } ms-auto`"
    :placeholder="$tr('search')"
    @keypress.enter="searchHeader()"
    @keyup="searchHeader()"
  >
    <template slot="append" class="pe-0">
      <v-btn fab x-small text class="ma-0" @click="toggleExpand()">
        <v-icon dark size="20"> mdi-magnify </v-icon>
      </v-btn>
    </template>
  </v-text-field>
</template>

<script>
export default {
  data() {
    return {
      expand: false,
      search: "",
    };
  },
  methods: {
    toggleExpand() {
      if (this.expand && this.search != "") {
        return;
      }
      this.expand = !this.expand;
    },
    searchHeader() {
      this.$emit("searchHeader", this.search);
    },
  },
};
</script>

<style>
.column-search {
  width: 36px;
  transition: all 0.3s;
  max-width: 360px !important;
  border: 2px solid transparent;
  transition: all 0.4s;
}
.column-search .v-input__slot {
  padding: 0 !important;
  transition: all 0.3s;
}
.column-search.expand {
  width: 100%;
}
.column-search.expand .v-input__slot {
  padding-left: 16px !important;
}
.column-search.expand.rtl .v-input__slot {
  padding-left: unset !important;
  padding-right: 16px !important;
}

.column-search .v-input__append-inner {
  margin-top: 0 !important;
  padding: 0 !important;
}
.column-search.v-input--is-focused {
  border: 2px solid #2e7be680;
  box-shadow: 0 0 10px #2e7be633;
}
</style>