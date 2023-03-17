<template>
  <v-text-field
    hide-details
    rounded
    background-color="bg-custom-gray"
    :class="`pa-0 ma-0 form-search ${expand ? 'expand' : ''} ms-auto`"
    v-model.trim="category_name"
    :placeholder="$tr('add_new_category')"
  >
    <template slot="append" class="pe-0">
      <v-btn
        fab
        small
        text
        class="ma-0 primary"
        @click="toggleExpand"
        :loading="loading"
      >
        <v-icon dark size="20"> mdi-plus </v-icon>
      </v-btn>
    </template>
  </v-text-field>
</template>
<script>
export default {
  data() {
    return {
      category_name: "",
      expand: false,
      typingTimer: null,
      loading: false,
    };
  },
  methods: {
    toggleExpand() {
      if (!this.expand) {
        this.expand = true;
      } else {
        if (this.category_name == "") {
          // this.$toastr.e(this.$tr("please type a name "));
			this.$toasterNA("red",this.$tr("please type a name"));

          return;
        }
        this.loading = true;
        this.$emit("submit", this.category_name);
      }
    },
    closeExpand(response = true) {
      if (!response) {
        // this.$toastr.e(this.$tr("some error occured"));
			this.$toasterNA("red",this.$tr("some error occured"));

        this.loading = false;
        return;
      }
      this.loading = false;
      this.expand = false;
      this.category_name = "";
    },
  },
  watch: {},
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
