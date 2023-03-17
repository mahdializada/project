<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('sourcer'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.product_id"
          :type="`autocomplete`"
          :items="products"
          :label="$tr('product')"
          :item-text="(item) => `${item.name} (${item.code})`"
        />

        <FilterInput
          v-model="form.receiving_country_id"
          :type="`autocomplete`"
          :items="countries"
          :label="$tr('receiving_country')"
          item-text="name"
        />

        <FilterInput
          v-model="form.required_shipping_method"
          :type="`autocomplete`"
          :items="shippingMethods"
          :label="$tr('required_shipping_method')"
        />
        <FilterInput v-model="form.quantity" :label="$tr('quantity')" />
        <FilterInput
          v-model="form.purchase_order_number"
          :label="$tr('purchase_order_number')"
        />
      </template>
      <template v-slot:date_range>
        <FilterInput
          v-model="form.created_date"
          @getDate="getDate"
          :label="$tr('created_at')"
          :type="'date-range'"
        />
        <FilterInput
          :clearInput.sync="clearInput"
          v-model="form.updated_date"
          @getDate="getDate"
          :label="$tr('updated_at')"
          :type="'date-range'"
        />
        <FilterInput
          :clearInput.sync="clearInput"
          v-model="form.arrival_time_date"
          @getDate="getDate"
          :label="$tr('arrival_time')"
          :type="'date-range'"
        />
      </template>
      <template v-slot:id_filtering>
        <FilterInput
          :clearInput.sync="clearInput"
          @isInclude="(isInclude) => (form.include = isInclude)"
          @getIds="(ids) => (form.ids = ids)"
          :label="$tr('id')"
          :type="'id_filtering'"
        />
      </template>
    </CustomFilter>
  </v-dialog>
</template>

<script>
import FilterInput from "~/components/design/components/FilterInput.vue";
import CustomFilter from "~/components/common/CustomFilter.vue";
export default {
  components: {
    FilterInput,
    CustomFilter,
  },
  data() {
    return {
      model: false,
      products: [],
      countries: [],
      form: {
        product_id: "",
        receiving_country_id: "",
        required_shipping_method: "",
        quantity: "",
        purchase_order_number: "",
        created_date: null,
        updated_date: null,
        arrival_time_date: null,
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
      shippingMethods: [
        { id: "sea", name: this.$tr("sea") },
        { id: "air", name: this.$tr("air") },
        { id: "ground", name: this.$tr("ground") },
      ],
    };
  },

  methods: {
    changeModel() {
      this.model = !this.model;
      if (this.model) {
        this.fetchAllProducts();
        this.fetchCountries();
      }
    },

    async fetchCountries() {
      try {
        if (this.countries.length > 0) {
          return;
        }
        const { data } = await this.$axios.get("common/countries");
        this.countries = data.countries;
      } catch (_) {}
    },

    async fetchAllProducts() {
      try {
        if (this.products.length > 0) {
          return;
        }
        const { data } = await this.$axios.get(
          "single-sales/products-ssp/get?filter_product=true"
        );
        this.products = data;
      } catch (_) {}
    },

    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
      else if (selected.toLowerCase().includes("arrival_time"))
        this.form.arrival_time_date = date;
    },

    sortData() {
      this.form = JSON.parse(JSON.stringify(this.form)); // Add this line to prevent reference.
      this.sortedData = {};
      if (this.form.product_id)
        this.sortedData.product_id = [
          "whereHas",
          "product",
          this.form.product_id,
        ];

      if (this.form.required_shipping_method)
        this.sortedData.required_shipping_method =
          "exact@@" + this.form.required_shipping_method;

      if (this.form.quantity)
        this.sortedData.quantity = "like@@" + this.form.quantity;

      if (this.form.purchase_order_number)
        this.sortedData.purchase_order_number =
          "like@@" + this.form.purchase_order_number;

      if (this.form.receiving_country_id)
        this.sortedData.receiving_country_id =
          "like@@" + this.form.receiving_country_id;

      if (this.form.updated_date)
        this.sortedData.updated_at = ["date", "range"].concat(
          this.form.updated_date
        );
      if (this.form.arrival_time_date)
        this.sortedData.arrival_time = ["date", "range"].concat(
          this.form.arrival_time_date
        );

      if (this.form.created_date)
        this.sortedData.created_at = ["date", "range"].concat(
          this.form.created_date
        );

      if (this.form.ids.length > 0) {
        this.sortedData.ids = this.form.ids;
        this.sortedData.include = this.form.include;
      }
    },

    onSubmit() {
      if (!this.isAlreadySubmited()) {
        this.$emit("filterRecords", this.sortedData);
        this.changeModel();
      }
    },
    onClear() {
      this.form = {
        product_id: "",
        required_shipping_method: "",
        quantity: "",
        purchase_order_number: "",
        receiving_country_id: "",
        created_date: null,
        updated_date: null,
        arrival_time_date: null,
        include: 1,
        ids: [],
      };
      this.clearInput = true;
      setTimeout(() => {
        this.clearInput = false;
      }, 2000);

      if (!this.isAlreadySubmited()) {
        this.$emit("filterRecords", this.sortedData);
        this.changeModel();
      }
      this.sortedData = {};
    },

    isAlreadySubmited() {
      const obj1 = this.sortedData;
      this.sortData();
      const obj2 = this.sortedData;
      return JSON.stringify(obj1) === JSON.stringify(obj2);
    },
  },
};
</script>
