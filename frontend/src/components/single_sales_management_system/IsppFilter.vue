<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('ISPP'))"
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
          v-model="form.company_id"
          :type="`autocomplete`"
          :items="companies"
          :loading="isFetchingCompany"
          :label="$tr('company')"
        />

        <FilterInput
          v-model="form.creation_type"
          :type="`autocomplete`"
          :items="creation_types"
          :label="$tr('creation_type')"
        />

        <FilterInput
          v-model="form.sales_modal"
          :type="`autocomplete`"
          :items="sales_modals"
          :label="$tr('sales_modal')"
        />

        <FilterInput v-model="form.head_selling_price" :label="$tr('head_selling_price')" />
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
      products: [],
      companies: [],
      model: false,
      form: {
        product: "",
        product_id: "",
        sales_modal: "",
        creation_type: "",
        head_selling_price: "",
        created_date: null,
        updated_date: null,
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
      isFetchingCompany: false,
      creation_types: [
        { id: "online", name: this.$tr("online") },
        { id: "offline", name: this.$tr("offline") },
      ],
      sales_modals: [
        { id: "model1", name: this.$tr("model1") },
        { id: "model2", name: this.$tr("model2") },
      ],
    };
  },

  created() {
    // if (this.companies?.length == 0) {
    //   this.getCompanies({ key: "all" });
    //   console.log("data", data);
    // }
  },

  methods: {
    changeModel() {
      this.model = !this.model;
      if (this.model) {
        this.fetchAllProducts();
      }
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

    // fetch all users
    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
    },

    sortData() {
      this.form = JSON.parse(JSON.stringify(this.form)); // Add this line to prevent reference.
      this.sortedData = {};

      if (this.form.product_id)
        this.sortedData.product_id = [
          "whereHasOne",
          "product",
          this.form.product_id,
        ]; 

      if (this.form.company_id)
        this.sortedData.company_id = [
          "whereHas",
          "companies",
          this.form.company_id,
        ];

      if (this.form.creation_type)
        this.sortedData.creation_type = "exact@@" + this.form.creation_type;

      if (this.form.sales_modal)
        this.sortedData.sales_modal = "exact@@" + this.form.sales_modal;

      if (this.form.head_selling_price)
        this.sortedData.head_selling_price = "like@@" + this.form.head_selling_price;

      if (this.form.updated_date)
        this.sortedData.updated_at = ["date", "range"].concat(
          this.form.updated_date
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
        product: "",
        product_id: "",
        creation_type: "",
        sales_modal: "",
        head_selling_price: "",
        created_date: null,
        updated_date: null,
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
