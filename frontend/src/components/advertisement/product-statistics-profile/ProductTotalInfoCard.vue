<template>
  <div class="d-flex">
    <ProductInfoHistoryGraph ref="historyRefs" />

    <v-card
      height="90"
      style="width: 243px"
      elevation="0"
      class="white ms-2 d-flex align-center justify-space-between pa-1 ps-0"
      v-for="(item, index) in items"
      :key="index"
    >
      <div class="d-flex align-center ps-1">
        <v-avatar :style="`background: ${item.background}`" size="60">
          <SvgIcon
            style="height: 26.16px; width: 26.16px"
            :icon="item.column"
          ></SvgIcon>
        </v-avatar>
        <div class="align-self-start ps-2">
          <div
            class="d-flex align-start justify-spacebetween"
            style="min-height: 40px"
          >
            <p
              class="mb-0"
              :style="`opacity: 0.7; min-width: 120px;color:${item.color}`"
            >
              {{ $tr(item.column) }}
            </p>
            <v-btn
              plain
              small
              text
              icon
              color="#777"
              @click="showHIstory(item.column)"
              ><v-icon>mdi-history</v-icon></v-btn
            >
          </div>
          <div
            class="d-flex align-start justify-spacebetween"
            v-if="selected_column.column != item.column"
          >
            <h4
              class="align-self-end"
              :style="`min-width: 120px;color: ${item.color}`"
            >
              {{ item.value }}
            </h4>
            <v-btn
              v-if="item.column != 'prod_profit'"
              plain
              small
              text
              icon
              color="primary"
              @click="editProperty(item)"
              ><v-icon>mdi-pencil</v-icon></v-btn
            >

            <v-menu
              v-else
              offset-y
              v-model="profit_menu"
              :close-on-content-click="false"
              class="elevation-10"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  @click="editProperty(item)"
                  plain
                  small
                  text
                  icon
                  color="primary"
                  ><v-icon>mdi-pencil</v-icon></v-btn
                >
              </template>
              <div class="pa-2 white">
                <p
                  class="mb-1"
                  style="font-size: 16px; font-weight: 500; color: #777"
                >
                  {{ $tr("prod_profit") }}
                </p>
                <v-select
                  :items="profit_list"
                  outlined
                  :item-text="
                    (item) => {
                      $tr(item);
                    }
                  "
                  v-model="selected_column.type"
                  dense
                  filled
                  hide-details
                  :placeholder="$tr('prod_profitability')"
                  class="mb-2"
                ></v-select>
                <v-text-field
                  outlined
                  v-model="selected_column.value"
                  dense
                  single-line
                  filled
                  :placeholder="$tr('prod_profit')"
                  hide-details
                ></v-text-field>

                <div class="d-flex justify-space-between mt-3">
                  <v-btn
                    class="px-2 py-1"
                    rounded
                    text
                    small
                    @click="profit_menu = false"
                    color="primary"
                    style="font-size: 14px; font-weight: 400"
                    >{{ $tr("cancle") }}</v-btn
                  >
                  <v-btn
                    class="px-2 py-1"
                    @click="updateColumn(index)"
                    :loading="submitting"
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
          <div class="d-flex align-start justify-spacebetween" v-else>
            <v-text-field
              dense
              type="number"
              v-model="selected_column.value"
              rounded
              hide-details
              outlined
              color="primary"
              class="me-1 my-custom-input4"
            ></v-text-field>
            <v-btn
              plain
              small
              text
              icon
              color="primary"
              :loading="submitting"
              @click="updateColumn(index)"
              ><v-icon>mdi-check-bold</v-icon></v-btn
            >
          </div>
        </div>
      </div>
    </v-card>
  </div>
</template>

<script>
import SvgIcon from "../../design/components/SvgIcon.vue";
import ProductInfoHistoryGraph from "./ProductInfoHistoryGraph.vue";

export default {
  components: { SvgIcon, ProductInfoHistoryGraph },
  props: {
    profile_data: {
      type: Object,
      require: true,
    },
    loading: Boolean,
    isOnlineSales: Boolean,
  },
  data() {
    return {
      selected_column: { column: null, value: null },
      submitting: false,
      profit_menu: false,
      profit_list: [
        "profit",
        "medium_profit",
        "less_profit",
        "loss",
        "medium_loss",
        "more_loss",
      ],
      items: [
        {
          column: "prod_cost",
          value: 423,
          color: "#36a2eb",
          background: "#e9f6fe",
        },
        {
          column: "prod_available_quantity",
          value: 423,
          color: "#ffcc55",
          background: "#fefaee",
        },
        {
          column: "prod_max_adver_cost",
          value: 423,
          color: "#fc6382",
          background: "#feeff2",
        },
      ],
    };
  },
  watch: {
    profile_data(value) {
      if (Object.keys(value).length != 0) {
        try {
          this.items = this.items.map((row) => {
            row.value = value[row.column];
            return row;
          });
          this.items = [...this.items];
        } catch (error) {
          console.log("error", error);
        }
      }
    },
  },
  methods: {
    showHIstory(column) {
      this.$refs.historyRefs.openDialog(this.profile_data, column);
    },
    editProperty(item) {
      if (item.column == "prod_profit") {
        this.selected_column = {};
        this.selected_column.column = item.column + "s";
        this.selected_column.value = item.value;
        this.selected_column.type = "less_profit";
        this.profit_menu = true;
        return;
      }
      console.log("item", item);
      this.selected_column = JSON.parse(JSON.stringify(item));
    },
    async updateColumn(index) {
      try {
        if (this.selected_column.value == this.items[index].value) {
          this.selected_column = { column: null, value: null };
          this.profit_menu = false;
          return;
        }
        this.submitting = true;
        const response = await this.$axios.put(
          "advertisement/product-profile-info/" + this.profile_data.id,
          this.selected_column
        );
        console.log("response:", response);
        this.items[index].value = JSON.parse(
          JSON.stringify(this.selected_column.value)
        );
        this.selected_column = { column: null, value: null };
        this.$toasterNA("primary", this.$tr("successfully_updated"));
        this.profit_menu = false;
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));

        console.error("error", error);
      }
      this.submitting = false;
    },
  },
  created() {
    if (!this.isOnlineSales) {
      this.items.push({
        column: "prod_profit",
        value: 423,
        color: "#49c0c0",
        background: "#dbefef",
      });
    }
  },
};
</script>

<style >
.my-custom-input4.v-text-field--filled.v-input--dense
  > .v-input__control
  > .v-input__slot,
.my-custom-input4.v-text-field--outlined.v-input--dense
  > .v-input__control
  > .v-input__slot {
  min-height: 24px !important;
  padding: auto 5px;
}

.my-custom-input4.v-text-field--rounded > .v-input__control > .v-input__slot {
  padding: auto 5px !important;
}

.my-custom-input4.v-text-field input {
  padding: unset !important;
  padding: 4px 0 4px !important;
}
.my-custom-input4.v-text-field--rounded > .v-input__control > .v-input__slot {
  padding: unset !important;
  padding: 0px 10px !important;
}
.my-custom-input4.v-input input {
  color: unset !important;
  color: #777 !important;
}
</style>