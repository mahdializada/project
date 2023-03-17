<template>
  <div>
    <div
      class="lib-top-bar d-flex justify-space-between align-center ps-2 pe-1 file-border-bottom position-relative overflow-hidden"
    >
      <div
        :class="`d-flex justify-space-between align-center first-one ${
          showSearch ? 'show-search' : ''
        }`"
      >
        <h2 class="file-top-bar-title ma-0">
          {{ $tr("companies") }}
        </h2>
        <div>
          <v-btn fab x-small text class="" @click="showSearchMethod">
            <v-icon dark size="20"> mdi-magnify </v-icon>
          </v-btn>
        </div>
      </div>
      <v-text-field
        ref="companySearchInput"
        hide-details
        rounded
        background-color="surface"
        :class="`pa-0 ma-0 custom-search second-one ${
          showSearch ? 'show-search' : ''
        }`"
        v-model="searchData"
        @input="$emit('search', searchData)"
        @blur="showSearch = !showSearch"
        color="white"
        :placeholder="$tr('search') + '...'"
      >
        <template slot="append" class="pe-0">
          <v-btn
            fab
            x-small
            text
            class="ma-0 me-1"
            @click="$emit('search', searchData)"
          >
            <v-icon dark size="20"> mdi-magnify </v-icon>
          </v-btn>
        </template>
      </v-text-field>
    </div>
    <div>
      <v-list dense>
        <div v-if="isFetchingCompanies">
          <v-skeleton-loader
            v-for="item in 6"
            :key="item"
            width="100%"
            class="mb-1"
            type="list-item-avatar-two-line"
          ></v-skeleton-loader>
        </div>

        <v-virtual-scroll
          v-else
          :items="filterCompanies"
          :item-height="50"
          :height="this.$vuetify.breakpoint.height - 190"
        >
          <template v-slot:default="{ item }">
            <v-list-item
              :key="item.id"
              class="company-item"
              :class="{ 'company-selected': selectedCompany == item.id }"
              @click="onCompanyClicked(item)"
            >
              <v-list-item-avatar size="30">
                <v-img
                  style="border: 2px solid var(--v-primary-base)"
                  :alt="`${item.name} avatar`"
                  :src="item.logo"
                ></v-img>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title v-text="item.name"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </template>
        </v-virtual-scroll>
      </v-list>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      selectedCompany: null,
      searchData: "",
      showSearch: false,
      isFetchingCompanies: false,
      companies: [],
    };
  },

  mounted() {
    if (process.client) this.fetchCompanies();
  },

  computed: {
    filterCompanies: function () {
      if (this.searchData) {
        const value = this.searchData.toLowerCase();
        const filteredItems = this.companies.filter(({ name }) =>
          name.toLowerCase().includes(value)
        );
        return filteredItems;
      }
      return this.companies;
    },
  },

  methods: {
    async fetchCompanies() {
      if (this.isFetchingCompanies) {
        return;
      }
      try {
        this.isFetchingCompanies = true;
        const response = await this.$axios.get("paginate-companies");
        this.companies = response.data;
      } catch (error) {}
      this.isFetchingCompanies = false;
    },

    onCompanyClicked({ id }) {
      this.selectedCompany = id;
      this.$emit("toggleCompanies");
      this.$emit("companyChanged", id);
    },

    showSearchMethod() {
      var app = this;
      this.showSearch = !this.showSearch;
      setTimeout(() => {
        app.$refs.companySearchInput.focus();
      }, 300);
    },
  },
};
</script>
<style>
.company-selected {
  background-color: var(--v-surface-base) !important;
  color: var(--v-primary-base) !important;
}
.company-item.v-list-item::after {
  min-height: 1px;
  display: block !important;
  content: "" !important;
  width: 90% !important;
  height: 1px !important;
  background: #ddd !important;
  position: absolute;
  bottom: 0px;
  left: 50%;
  transform: translateX(-50%);
}
.first-one,
.second-one {
  position: absolute;
  transition: all 0.3s;
}
.first-one {
  left: 16px;
  right: 8px;
}
.v-application--is-rtl .first-one {
  left: 8px;
  right: 16px;
}
.second-one {
  left: 100%;
  right: -100%;
}
.first-one.show-search {
  left: -100%;
  right: 100%;
}
.second-one.show-search {
  left: 16px;
  right: 16px;
}
</style>
