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
          class="elevation-2 ps-2 me-2"
          v-on="on"
          v-if="user.currency_id && authCurrency"
        >
          <FlagIcon
            small
            :flag="authCurrency.country.iso2.toLowerCase()"
            round
          />
          <span class="ms-1">
            {{ authCurrency.name }}
          </span>
          <span class="primary--text">
            <v-icon right dark small> fa-chevron-circle-down </v-icon>
          </span>
        </v-btn>

        <v-btn rounded v-else v-on="on" class="elevation-2 ps-1 me-2">
          <span class="ms-1">
            {{ $tr(isFetching ? "loading" : "currency_list") }}
          </span>
        </v-btn>
      </template>

      <v-list dense>
        <v-list-item-group>
          <div
            :style="`${
              currencies.length > 7 ? 'height: 300px;' : ''
            } overflow-y: auto`"
          >
            <v-list-item v-for="(currency, index) in currencies" :key="index">
              <v-list-item-icon>
                <FlagIcon
                  small
                  :flag="currency.country.iso2.toLowerCase()"
                  round
                />
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="ms-1 currency__text">
                  {{ currency.name }}
                </v-list-item-title>
              </v-list-item-content>
              <v-list-item-icon>
                <v-switch
                  v-model="selectedCurrencyId"
                  inset
                  dense
                  color="white"
                  @change="onCurrencyChanged(currency)"
                  :value="currency.id"
                  :loading="
                    isLoading && selectedCurrencyId == currency.id
                      ? 'primary'
                      : false
                  "
                  :disabled="user.currency_id == currency.id"
                  hide-details
                  :class="`mt-0 pt-0 custom-switch ${
                    selectedCurrencyId == currency.id ? 'selected' : ''
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
import FlagIcon from "../common/FlagIcon.vue";

export default {
  data() {
    return {
      isLoading: false,
      selectedCurrencyId: null,
      authCurrency: null,
      isFetching: false,
      currencies: [],
    };
  },

  computed: {
    ...mapState("auth", ["user"]),
  },
  created() {
    this.fetchCurrenies();
    this.selectedCurrencyId = this.user.currency_id;
  },
  methods: {
    async onCurrencyChanged(selectedCurrency) {
      if (selectedCurrency && this.selectedCurrencyId) {
        this.isLoading = true;
        try {
          const body = { currency_id: selectedCurrency.id };
          const { data } = await this.$axios.put("auth/update/currency", body);
          if (data.status == "success") {
            this.authCurrency = selectedCurrency;
            await this.$auth.fetchUser();
            // this.$toastr.i(this.$tr("currency_changed"));
							this.$toasterNA("primary",this.$tr('currency_changed'));

          } else {
            // this.$toastr.w(this.$tr("selected_currency_is_not_saved"));
					this.$toasterNA("orange",this.$tr("selected_currency_is_not_saved"));

          }
        } catch (error) {
          this.selectedCurrencyId = this.user.currency_id;
          // this.$toastr.w(this.$tr("selected_currency_is_not_saved"));
					this.$toasterNA("orange",this.$tr("selected_currency_is_not_saved"));

        }
        this.isLoading = false;
      } else {
        this.selectedCurrencyId = this.user.currency_id;
      }
    },

    async fetchCurrenies() {
      try {
        this.isFetching = true;
        const { data } = await this.$axios.get("currency/list");
        this.currencies = data;
        if (this.user.currency_id) {
          const item = data.find((item) => item.id == this.user.currency_id);
          this.authCurrency = item;
        }
      } catch (error) {}
      this.isFetching = false;
    },
  },
  components: { FlagIcon },
};
</script>
<style>
.currency__text {
  white-space: nowrap;
  text-overflow: ellipsis;
  width: 75px;
}
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
