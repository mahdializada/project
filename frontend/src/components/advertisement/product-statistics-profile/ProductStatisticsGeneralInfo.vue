<template>
  <div class="ps-2">
    <ProductInfoHistory ref="historyRefs" />
    <p class="custom-card-title-1 mb-1">general information</p>
    <v-card elevation="0" height="400" class="white pa-2 rounded-0">
      <div
        class="d-flex flex-column flex-wrap"
        style="height: 370px; overflow-x: auto"
      >
        <div
          v-for="(item, index) in items"
          :key="index"
          class="px-2 my-2"
          style="width: 330px; height: 135px; border-right: 1px solid #cecece"
        >
          <div
            class="d-flex align-center justify-space-between pb-3 pe-1"
            style=""
          >
            <span class="d-flex align-center primary--text">
              <SvgIcon
                style="height: 26.16px; width: 26.16px"
                :icon="item.column"
              ></SvgIcon>
              <p class="mb-0 ps-1" style="color: #777">
                {{ $tr(item.column) }}
              </p>
            </span>
            <v-btn
              plain
              small
              text
              icon
              color="primary"
              class=""
              @click="showHIstory(item.column)"
              ><v-icon>mdi-history</v-icon></v-btn
            >
          </div>
          <div
            class="d-flex flex-wrap justify-space-between pe-1"
            style="overflow-y: auto; max-height: 88px"
          >
            <div
              style="background: #f7f8fc; height: 40px; margin-bottom: 4px"
              :style="` width: ${
                item.enum.length > 4
                  ? '100%'
                  : item.enum.length == 4
                  ? '49%'
                  : item.enum.length == 3
                  ? j == 2
                    ? '100%'
                    : '49%'
                  : '100%'
              }`"
              class="d-flex align-center justify-space-between px-1 rounded-sm cursor-pointer"
              v-for="(enums, j) in item.enum"
              :key="j"
              @click="confirm(index, j)"
            >
              <span style="color: #777">{{ $tr(enums) }} </span>
              <span
                v-if="
                  ['prod_new_product_source', 'prod_import_source'].includes(
                    item.column
                  )
                "
              >
                <v-btn
                  v-if="checkValue(enums, item)"
                  plain
                  small
                  text
                  icon
                  color="primary"
                  :loading="submitting && selected_value == enums"
                  ><v-icon>mdi-checkbox-marked</v-icon></v-btn
                >

                <v-btn
                  plain
                  small
                  text
                  icon
                  color="#777"
                  :loading="submitting && selected_value == enums"
                  v-else
                  ><v-icon>mdi-checkbox-blank-outline</v-icon></v-btn
                >
              </span>

              <span v-else>
                <v-btn
                  v-if="checkValue(enums, item)"
                  plain
                  small
                  text
                  icon
                  color="primary"
                  :loading="submitting && selected_value == enums"
                  ><v-icon>mdi-radiobox-marked</v-icon></v-btn
                >

                <v-btn
                  plain
                  small
                  text
                  icon
                  color="#777"
                  :loading="submitting && selected_value == enums"
                  v-else
                  ><v-icon>mdi-radiobox-blank</v-icon></v-btn
                >
              </span>
            </div>
          </div>
        </div>
      </div>
    </v-card>
  </div>
</template>

<script>
import SvgIcon from "../../design/components/SvgIcon.vue";
import ProductInfoHistory from "./ProductInfoHistory.vue";

export default {
  components: { SvgIcon, ProductInfoHistory },
  props: {
    profile_data: { type: Object, require: true },
    isOnlineSales: Boolean,
  },
  data() {
    return {
      submitting: false,
      selected_value: null,
      items: [
        {
          column: "prod_source",
          value: "",
          enum: ["warehouse", "market", "both"],
        },
        {
          column: "prod_work_type",
          value: "",
          enum: ["creation", "copy"],
        },
        {
          column: "prod_section",
          value: "",
          enum: ["new", "old", "stock", "renew"],
        },
        {
          column: "prod_style",
          value: "",
          enum: ["single", "collection", "project"],
        },
        {
          column: "prod_import_source",
          value: "",
          enum: ["uae", "china"],
        },
        {
          column: "prod_new_product_source",
          value: "",
          enum: [
            "market_visit",
            "supplier_suggestion",
            "advertiser_products",
            "company_source",
          ],
        },
        {
          column: "prod_production_type",
          value: "",
          enum: ["ready", "manufacturing"],
        },
        {
          column: "prod_sale_goal",
          value: "",
          enum: ["for_stock_disposal", "for_profit"],
        },
      ],
    };
  },
  watch: {
    profile_data(value) {
      if (Object.keys(value).length != 0) {
        this.items = this.items.map((row) => {
          row.value = value[row.column];
          return row;
        });
        this.items = [...this.items];
      }
    },
  },
  methods: {
    showHIstory(column) {
      this.$refs.historyRefs.openDialog(this.profile_data, column);
    },
    checkValue(enums, item) {
      let json_column = ["prod_new_product_source", "prod_import_source"];
      if (json_column.includes(item.column)) {
        return item.value.includes(enums);
      } else {
        return enums == item.value;
      }
    },
    async updateColumn(i, j) {
      try {
        this.submitting = true;
        this.selected_value = this.items[i].enum[j];
        const { data } = await this.$axios.put(
          "advertisement/product-profile-info/" + this.profile_data.id,
          { column: this.items[i].column, value: this.selected_value }
        );
        this.items[i].value = this.selected_value;
        this.items = this.items.map((row) => {
          row.value = data[row.column];
          return row;
        });
        this.items = [...this.items];
        this.$toasterNA("primary", this.$tr("successfully_updated"));
      } catch (error) {
        console.error(error);
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
      this.submitting = false;
    },
    confirm(i, j) {
      let json_column = ["prod_new_product_source", "prod_import_source"];
      if (!json_column.includes(this.items[i].column))
        if (this.items[i].value == this.items[i].enum[j]) return;

      this.$TalkhAlertNA(
        "Are you Sure to update value?", //text
        "info", //icon
        async () => {
          this.updateColumn(i, j);
        }, // callback function
        "yes" // btn text
      );
    },
  },
  created() {
    if (!this.isOnlineSales) {
      this.items.unshift({
        column: "prod_sales_stability",
        value: "",
        enum: ["stable", "unstable"],
      });
    }
  },
};
</script>

<style scoped>
.custom-card-title-1 {
  font-size: 17px;
  text-transform: uppercase;
  font-weight: 500;
  color: #777;
}
</style>