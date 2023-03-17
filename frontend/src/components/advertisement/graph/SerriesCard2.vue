<template>
  <div class="pa-2 pb-1 pe-1">
    <v-card height="460" elevation="0" class="pa-1 py-2 pb-3">
      <div :class="`series-border`" style="">
        <v-text-field
          hide-details
          v-model="search"
          rounded
          dense
          background-color="bg-custom-gray"
          style="border-radius: 50px"
          :class="`pa-0 ps-1 ma-0 column-search-serries 
				`"
          :placeholder="$tr('search')"
        >
          <template slot="append" class="pe-0">
            <v-btn fab x-small text class="ma-0 primary--text">
              <v-icon dark size="20"> mdi-magnify </v-icon>
            </v-btn>
          </template>
        </v-text-field>
        <div
          id="serries-scroll"
          style="height: 330px; overflow-y: auto"
          class="mt-1"
        >
          <v-list rounded dense flat class="pb-2 ps-0" v-show="!loading">
            <v-list-item-group multiple active-class="">
              <v-list-item
                v-for="(item, i) in searchHeader"
                :key="i"
                @click="toggleColumn(item)"
                :value="item.id"
                :class="`px-1 ${checkSelection(item) ? 'primary--text' : ''}`"
              >
                <v-list-item-icon>
                  <v-icon
                    :class="`${checkSelection(item) ? 'primary--text' : ''}`"
                    v-text="
                      checkSelection(item)
                        ? 'mdi-check-circle'
                        : 'mdi-checkbox-blank-circle-outline'
                    "
                  >
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>{{
                    item.name || item.ad_name
                  }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
          <div v-show="loading">
            <v-skeleton-loader
              v-for="i in 6"
              type="list-item"
              :key="i"
              class="rounded-0 w-full"
            />
          </div>
        </div>
      </div>
    </v-card>
  </div>
</template>

<script>
import Alert from "~/helpers/alert";

export default {
  props: {
    serries: Array,
    selected_tab: Object,
    loading: Boolean,
  },
  data() {
    return {
      save_column_menu: false,
      scope_type: false,
      selected_series: [],
      search: "",
      setting_name: "",
    };
  },
  watch: {},
  computed: {
    searchHeader() {
      try {
        if (this.search == "") {
          return this.serries;
        }
        return this.serries.filter((row) => {
          return row[row.name]
            .toLowerCase()
            .includes(this.search.toLowerCase());
        });
      } catch (error) {
        console.error("error", error);
        return [];
      }
    },
  },

  created() {},
  methods: {
    clickListGroup() {
      console.log("clickListGrou");
    },
    checkSelection(item) {
      return this.selected_series.filter((row) => row.id == item.id).length > 0;
    },
    toggleColumn(item) {
      try {
        if (this.checkSelection(item)) {
          this.selected_series = this.selected_series.filter(
            (row) => row.id != item.id
          );
          this.$emit("toggleChartLine", { type: "pop", item });
        } else {
          if (
            this.selected_series.filter((row) => this.selected_tab.id in row)
              .length > 1
          ) {
            console.log("cant add more than tow");
            return;
          }
          this.$emit("toggleChartLine", {
            type: "push",
            item,
          });
          this.selected_series.push(item);
        }
      } catch (error) {
        console.error("error", error);
      }
    },
  },
};
</script>

<style>
.series-border {
  border: 1px solid #e3e3e3;
  padding: 8px;
  border-radius: 16px !important;
}
.column-search-serries {
  width: 200px;
  margin-left: auto;
  margin-right: auto;
  border: 2px solid #2e7be680;
}

.column-search-serries .v-input__slot {
  padding: 0 5px !important;
  /* padding-left: 3px; */
}

.column-search-serries .v-input__append-inner {
  margin-top: 0 !important;
  padding-left: 5px !important;
}

.column-search-serries.v-input--is-focused {
  border: 2px solid #2e7be680;
  box-shadow: 0 0 10px #2e7be633;
}

.custom-select.v-input--is-focused {
  border: 2px solid #2e7be680;
  box-shadow: 0 0 10px #2e7be633;
}

.custom-select .v-input__slot {
  padding: 0 5px !important;
}

.custom-select {
  border: 2px solid #2e7be680;
  color: #2e7be680 !important;
}
</style>
<style>
#serries-scroll::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}

#serries-scroll::-webkit-scrollbar {
  width: 8px !important;
  background-color: var(--v-surface-base) !important;
}

#serries-scroll::-webkit-scrollbar:hover {
  cursor: alias !important;
}

#serries-scroll::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

#serries-scroll::-webkit-scrollbar-thumb {
  border-radius: 8px !important;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-background-darken2) !important;
}
</style>
