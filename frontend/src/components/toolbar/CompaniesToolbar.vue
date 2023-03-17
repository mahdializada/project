<template>
  <div>
    <v-menu
      offset-y
      left
      transition="slide-y-transition"
      :close-on-content-click="false"
    >
      <template v-slot:activator="{ on }">
        <v-btn
          rounded
          class="elevation-2 ps-2"
          v-on="on"
          v-if="selectedCompanies.length"
        >
          <template v-for="(company, key) in selectedCompanies">
            <span :key="key" style="width: 13px" v-if="key < 3">
              <v-avatar
                color="white"
                style="border: 2px solid white"
                size="28"
                :class="{ 'ml-n1': selectedCompanies.length > 1 }"
                outlined
              >
                <img :src="company.logo" :alt="company.name" />
              </v-avatar>
            </span>
          </template>
          <span class="ms-1" v-if="selectedCompanies.length > 3">
            +{{ selectedCompanies.length - 3 }}
          </span>
          <span :class="`${selectedCompanies.length == 1 ? 'ms-3' : 'ms-2'}`">
            {{ $tr("list_of_companies") }}
          </span>
          <span class="primary--text">
            <v-icon right dark small> fa-chevron-circle-down </v-icon>
          </span>
        </v-btn>

        <v-btn rounded v-else v-on="on" class="elevation-2 ps-1">
          <span class="primary--text">
            <v-avatar
              color="#e2f3f3"
              size="30"
              :class="{ 'ml-n1': selectedCompanies.length > 1 }"
              outlined
            >
              <v-icon dark small> fa-building </v-icon>
            </v-avatar>
          </span>
          <span class="ms-1">{{ $tr("list_of_companies") }}</span>
        </v-btn>
      </template>

      <v-list dense>
        <v-list-item-group>
          <v-list-item
            v-if="selectedCompanyIds.length !== user.companies.length"
          >
            <v-list-item-content>
              <v-list-item-title>{{ $tr("select_all") }} </v-list-item-title>
            </v-list-item-content>
            <v-list-item-icon>
              <v-switch
                inset
                dense
                :disabled="user.companies.length <= 0"
                color="white"
                :input-value="false"
                @change="onSelectAll"
                hide-details
                :class="`mt-0 pt-0 custom-switch `"
              ></v-switch>
            </v-list-item-icon>
          </v-list-item>

          <v-list-item v-else>
            <v-list-item-content>
              <v-list-item-title>{{ $tr("unselect_all") }} </v-list-item-title>
            </v-list-item-content>
            <v-list-item-icon
              :class="`${user.companies.length > 7 ? 'me-3' : ''}`"
            >
              <v-switch
                inset
                dense
                :disabled="user.companies.length <= 1"
                color="white"
                :input-value="
                  selectedCompanyIds.length === user.companies.length
                "
                @change="onSelectAll"
                hide-details
                :class="`mt-0 pt-0 custom-switch selected`"
              ></v-switch>
            </v-list-item-icon>
          </v-list-item>
          <div
            :style="`${
              user.companies.length > 7 ? 'height: 300px;' : ''
            } overflow-y: auto`"
          >
            <v-list-item
              v-for="(company, index) in user.companies"
              :key="index"
              :disabled="company.status !== 'active'"
            >
              <v-list-item-icon>
                <v-avatar size="26">
                  <v-img :src="company.logo"></v-img>
                </v-avatar>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ company.name }} </v-list-item-title>
              </v-list-item-content>
              <v-list-item-icon>
                <v-switch
                  v-model="selectedCompanyIds"
                  :disabled="
                    selectedCompanyIds.length === 1 &&
                    selectedCompanyIds.includes(company.id)
                  "
                  inset
                  dense
                  color="white"
                  :value="company.id"
                  @change="onCompanySelected"
                  :loading="isLoading ? 'primary' : false"
                  hide-details
                  :class="`mt-0 pt-0 custom-switch ${
                    selectedCompanyIds.includes(company.id) ? 'selected' : ''
                  }`"
                ></v-switch>
              </v-list-item-icon>
            </v-list-item>
          </div>
        </v-list-item-group>
      </v-list>
    </v-menu>
  </div>
</template>
<script>
import { mapState } from "vuex";

export default {
  data() {
    return {
      selectedCompanyIds: [],
      selectedCompanies: [],
      isLoading: false,
    };
  },

  watch: {
    user(userItem) {
      this.selectedCompanyIds = userItem.selectedCompanies.map(
        (item) => item.id
      );
      this.selectedCompanies = JSON.parse(
        JSON.stringify(userItem.selectedCompanies)
      );
    },
  },

  computed: {
    ...mapState("auth", ["user"]),
  },

  created() {
    this.selectedCompanyIds = JSON.parse(
      JSON.stringify(this.user.selectedCompanies.map((item) => item.id))
    );
    this.selectedCompanies = JSON.parse(
      JSON.stringify(this.user.selectedCompanies)
    );
  },

  methods: {
    async onCompanySelected() {
      this.isLoading = true;
      const companies = this.user.companies.filter((item) =>
        this.selectedCompanyIds.includes(item.id)
      );
      const data = { companies: companies.map((item) => item.id) };
      try {
        await this.$axios.post("change-auth-user-companies", data);
        await this.$auth.fetchUser();
        this.isLoading = false;
        // this.$toastr.i(this.$tr("company_changed"));
			  this.$toasterNA("primary", this.$tr("company_changed"));

      } catch (error) {
        this.isLoading = false;
        // this.$toastr.w(this.$tr("selected_company_is_not_save"));
					this.$toasterNA("orange",this.$tr("selected_company_is_not_save"));

      }
      this.isLoading = false;
    },
    onSelectAll() {
      if (this.selectedCompanyIds.length !== this.user.companies.length) {
        this.selectedCompanyIds = JSON.parse(
          JSON.stringify(this.user.companies.map((item) => item.id))
        );
        this.onCompanySelected();
      } else {
        this.selectedCompanyIds = this.user.companies
          ? [this.user.companies[0].id]
          : [];

        this.onCompanySelected();
      }
    },
  },
};
</script>
<style>
.custom-switch .v-input--selection-controls__input {
  width: 30px !important;
}
.custom-switch.selected.v-input--switch .v-input--switch__track {
  opacity: 1 !important;
  background: var(--v-primary-base) !important;
}
.custom-switch .primary--text {
  color: unset !important;
}
</style>
