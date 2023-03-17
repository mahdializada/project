<template>
  <v-text-field
    hide-details
    rounded
    background-color="bg-custom-gray"
    :class="`pa-0 ma-0 form-search ${expand ? 'expand' : ''} ms-auto`"
    v-model.trim="searchData"
    :placeholder="$tr('search') + '...'"
  >
    <template slot="append" class="pe-0">
      <v-btn fab small text color="primary" class="ma-0" @click="toggleExpand">
        <v-icon dark size="20"> mdi-magnify </v-icon>
      </v-btn>
    </template>
  </v-text-field>
</template>
<script>
export default {
  data() {
    return {
      searchData: "",
      expand: false,
      typingTimer: null,
    };
  },
  methods: {
    toggleExpand() {
      this.expand = !this.expand;
    },
    debounce(callback, wait) {
      clearTimeout(this.typingTimer);
      this.typingTimer = setTimeout(callback, wait);
    },
    onSearchChange() {
      const callback = () => this.$emit("search", this.searchData);
      this.debounce(callback, 1000);
    },
  },
  watch: {
    searchData(value) {
      if (value == "") {
        this.$emit("disableSearch");
        clearTimeout(this.typingTimer);
        this.$emit("search", "");
      } else {
        this.onSearchChange();
      }
    },
  },
};
</script>
<style>
.form-search {
  width: 44px;
  transition: all 0.3s;
  max-width: 360px !important;
  border: 2px solid transparent;
  transition: all 0.4s;
}
.form-search .v-input__slot {
  padding: 0 !important;
  transition: all 0.3s;
}
.form-search.expand {
  width: 100%;
}
.form-search.expand .v-input__slot {
  padding-left: 16px !important;
}
.form-search .v-input__append-inner {
  margin-top: 0 !important;
  padding: 0 !important;
}
.form-search.v-input--is-focused {
  border: 2px solid #2e7be680;
  box-shadow: 0 0 10px #2e7be633;
}
</style>
