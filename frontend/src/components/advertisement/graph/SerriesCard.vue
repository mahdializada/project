<template>
  <div class="pa-2 pb-1 pe-1">
    <v-card height="450" elevation="0" class="pa-1 py-2 pb-3">
      <div :class="`${has_border ? 'series-border' : ''}`" style="">
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
          <v-list rounded dense color="" class="pb-2 ps-0">
            <v-list-item-group
              color="primary"
              multiple
              v-model="selected_serries"
            >
              <v-list-item
                v-for="(item, i) in searchHeader"
                :key="i"
                @click="toggleColumn(item)"
                :value="item.label"
                class="px-1"
              >
                <v-list-item-icon>
                  <v-icon
                    v-text="
                      selected_serries.includes(item.label)
                        ? 'mdi-check-circle'
                        : 'mdi-checkbox-blank-circle-outline'
                    "
                  >
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>{{ $tr(item.label) }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </div>

        <v-menu
          v-if="series_type == 'advertisement'"
          v-model="save_column_menu"
          offset-y
          :close-on-content-click="false"
          class="elevation-10"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              class="mt-2"
              color="primary"
              rounded
              outlined
              block
              dark
              v-bind="attrs"
              v-on="on"
            >
              save view
            </v-btn>
          </template>
          <div class="pa-2 white">
            <p
              class="mb-1"
              style="font-size: 16px; font-weight: 500; color: #777"
            >
              {{ $tr("view_name") }}
            </p>
            <v-text-field
              v-model="setting_name"
              outlined
              dense
              single-line
              filled
              :placeholder="$tr('type a name')"
              hide-details
            ></v-text-field>
            <v-checkbox
              v-model="scope_type"
              style="font-size: 16px; font-weight: 500; color: #777"
              off-icon="mdi-circle-outline"
              on-icon="mdi-checkbox-marked-circle"
              class="mt-1"
              :label="$tr('keep_it_private')"
              color="primary"
              hide-details
            ></v-checkbox>
            <div class="d-flex justify-space-between mt-3">
              <v-btn
                @click="save_column_menu = false"
                class="px-2 py-1"
                rounded
                text
                small
                color="primary"
                style="font-size: 14px; font-weight: 400"
                >{{ $tr("cancle") }}</v-btn
              >
              <v-btn
                @click="finalSubmit()"
                :loading="loading"
                class="px-2 py-1"
                rounded
                small
                color="primary"
                style="font-size: 14px; font-weight: 400"
                >{{ $tr("save") }}</v-btn
              >
            </div>
          </div>
        </v-menu>
      </div>
    </v-card>
  </div>
</template>

<script>
import Alert from "~/helpers/alert";

export default {
  props: {
    series_type: String,
    has_border: Boolean,
  },
  data() {
    return {
      save_column_menu: false,
      scope_type: false,
      loading: false,
      serries: [
        { label: "spend", color: "rgba(0 ,128, 0,1)" },

        { label: "cpo", color: "rgba(255 ,111, 0,1)" },
        {
          label: "view_completion",
          color: "rgba(156, 39, 176, 1)",
        },
        {
          label: "result",
          color: "rgba(192 ,202 ,51,1)",
        },
        {
          label: "impressions",
          color: "rgba(77, 40, 145, 1)",
        },

        {
          label: "viewable_impressions",
          color: "rgba(38, 166, 154,1)",
        },
        {
          label: "two_second_video_views",
          color: "rgba(117, 117, 117,1)",
        },
        {
          label: "six_second_video_views",
          color: "rgba(183, 28, 28,1)",
        },
        {
          label: "video_views",
          color: "rgba(57, 2, 255, 1)",
        },
        {
          label: "average_screen_time",
          color: "rgba(136, 14 ,79,1)",
        },
        {
          label: "clicks",
          color: "rgba(87, 170, 113, 1)",
        },
        {
          label: "total_page_views",
          color: "rgba(29, 143, 142, 1)",
        },
        {
          label: "story_opens",
          color: "rgba(0 ,191 ,165,1)",
        },
        {
          label: "reach",
          color: "rgba(15, 236, 114, 1)",
        },
        {
          label: "cost_per_fifteen_sec_video_view",
          color: "rgba(236, 142, 15, 1)",
        },
        {
          label: "frequency",
          color: "rgba(15, 236, 233, 1)",
        },
        {
          label: "crm_total_orders",
          color: "rgba(15, 82, 236, 1)",
        },
        {
          label: "crm_confirm",
          color: "rgba(155, 15, 236, 1)",
        },
        {
          label: "crm_cancelled",
          color: "rgba(240, 10, 113, 1)",
        },
        {
          label: "crm_repeated",
          color: "rgba(240, 10, 10, 1)",
        },
        {
          label: "crm_pickedup",
          color: "rgba(2 ,119, 189,1)",
        },
        {
          label: "crm_total_pickedup",
          color: "rgba(0, 245, 0, 1)",
        },
        {
          label: "crm_logis_deliverd",
          color: "rgba(0, 90, 0, 1)",
        },
        {
          label: "crm_logis_cancelled",
          color: "rgba(0, 90, 137, 1)",
        },
        {
          label: "crm_total_sale",
          color: "rgba(227, 0, 240, 1)",
        },
        // {
        // 	label: "crm_total_sale",
        // 	color: "rgba(227, 0, 67, 1)",
        // },
        {
          label: "crm_product_cost",
          color: "rgba(157, 37, 73, 1)",
        },
        {
          label: "crm_delivery_cost",
          color: "rgba(157, 198, 73, 1)",
        },
        {
          label: "ga_total_users",
          color: "rgba(15, 247, 240, 1)",
        },
        {
          label: "ga_new_users",
          color: "rgba(247, 15, 235, 1)",
        },
        {
          label: "ga_user_engagement",
          color: "rgba(91, 186, 198, 1)",
        },
        {
          label: "ga_sessions",
          color: "rgba(74, 172, 185, 1)",
        },
        {
          label: "ga_engaged_sessions",
          color: "rgba(178, 166, 186, 1)",
        },
        {
          label: "ga_page_views",
          color: "rgba(83, 83, 176, 1)",
        },
        {
          label: "sale_price",
          color: "rgba(178, 178, 44, 1)",
        },
        {
          label: "exact_price",
          color: "rgba(38 ,198, 218,1)",
        },
        // {
        //   label: "cpo",
        //   color: "rgba(45, 60, 179, 1)",
        // },
        {
          label: "cpm",
          color: "rgba(255 ,64, 129,1)",
        },
        {
          label: "ctr",
          color: "rgba(130 ,177, 255,1) ",
        },
        {
          label: "cpc",
          color: "rgba(129, 118, 22,1)",
        },
        {
          label: "cpp",
          color: "rgba(84 ,110, 122,1)",
        },
        {
          label: "story_open_rate",
          color: "rgba(171, 71 ,188,1)",
        },
        {
          label: "cost_per_story_open",
          color: "rgba(102, 187, 106,1)",
        },

        {
          label: "crm_confirmed_percentage",
          color: "rgba(255, 112, 67,1)",
        },
        {
          label: "crm_profit_lose",
          color: "rgba(13 ,71, 161,1)",
        },
        {
          label: "crm_cancelled_percentage",
          color: "rgba(141 ,110, 99,1)",
        },
        {
          label: "crm_send_back_percentage",
          color: "rgba(251, 140 ,0,1)",
        },
        {
          label: "crm_difference",
          color: "rgba(41 ,182, 246,1)",
        },
        {
          label: "crm_delivered_percentage",
          color: "rgba(255, 128, 171,1)",
        },
        {
          label: "crm_logis_cancelled_percentage",
          color: "rgba(29, 233, 182,1)",
        },
        {
          label: "crm_final_percentage",
          color: "rgba(234, 128, 252,1)",
        },
        {
          label: "crm_total_expense",
          color: "rgba(212, 225, 87,1)",
        },
        {
          label: "crm_profit_percentage",
          color: "rgba(77, 182 ,172,1)",
        },
        {
          label: "crm_product_cost_percentage",
          color: "rgba(63 ,81 ,181,1)",
        },
        {
          label: "crm_delivery_cost_percentage",
          color: "rgba(255, 23, 68,1)",
        },
      ],
      selected_serries: ["spend"],
      search: "",
      selected: ["spend"],
      setting_name: "",
    };
  },
  computed: {
    searchHeader() {
      if (this.search == "") {
        // this.selected_serries = this.selected;
        return this.serries;
      }
      return this.serries.filter((row) => {
        return row.label.toLowerCase().includes(this.search.toLowerCase());
      });
    },
  },

  created() {
    if (this.series_type == "percentage") {
      this.serries = [
        { label: "view_quality_percantage", color: "rgba(3 ,155, 229,1)" },

        { label: "ad_attraction_percentage", color: "rgba(255 ,111, 0,1)" },
        {
          label: "buyer_percentage",
          color: "rgba(156, 39, 176, 1)",
        },
      ];
      this.selected_serries = ["view_quality_percantage"];
      this.selected = ["view_quality_percantage"];
    } else if (this.series_type == "media") {
      this.serries = [];
    }
  },
  methods: {
    toggleColumn(item) {
      this.selected = this._.clone(this.selected_serries);
      if (this.selected_serries.includes(item.label)) {
        this.selected = this.selected.filter((row) => {
          row != item.label;
        });
        this.$emit("toggleColumn", { label: item, action: "pop" });
      } else {
        this.selected = this.selected.push(item.label);
        this.$emit("toggleColumn", { label: item, action: "push" });
      }
    },
    unselectSerries(label) {
      if (label == "all") {
        if (this.series_type == "percentage") {
          this.selected_serries = ["view_quality_percantage"];
        } else {
          this.selected_serries = ["spend"];
        }
        return;
      }
      this.selected_serries = this.selected_serries.filter(
        (row) => this.$tr(row) != label
      );
    },
    getSelectedLabels() {
      return this.selected_serries;
    },
    assignSerries(data) {
      this.selected_serries = data;
      return this.serries.filter((row) => data.includes(row.label));
    },
    async finalSubmit() {
      if (this.setting_name == "") {
        this.$toasterNA("red", this.$tr("name_required"));
        return;
      }
      this.loading = true;
      try {
        const data = {
          name: this.setting_name,
          page_name: "graph_profile",
          columns: this.selected_serries,
          scope_type: this.scope_type,
        };
        const response = await this.$axios.post("view_name", data);
        if (response.status == 200) {
          this.setting_name = "";
          this.save_column_menu = false;
          this.scope_type = false;
          this.$toasterNA("green", this.$tr("saved_successfully"));
          this.$emit("onSubmit", response.data);
        }
      } catch (error) {}
      this.loading = false;
    },

    async deleteView(id) {
      try {
        const response = await this.$axios.delete("view_name/" + id);
        if (response.status == 200) {
          // this.all_views = this.all_views.filter((row) => row.id != id);
          // this.$toastr.s(this.$tr("delete_successfully", this.$tr("view")));
          this.$toasterNA("green", this.$tr("delete_successfully"));
        }
      } catch (error) {}
    },

    applyChanges(data) {
      this.selected_serries = data;
      const label = this.serries.filter((row) => {
        if (this.selected_serries.includes(row.label)) return row;
      });

      this.$emit("applySavedFilter", label);
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
