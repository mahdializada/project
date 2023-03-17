<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('product_study'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.product_id"
          :type="`autocomplete`"
          :items="products"
          :label="$tr('product')"
          :item-text="(item) => `${item.name} (${item.code})`"
          hasAvatar
        />

        <FilterInput
          v-model="form.study_language_id"
          :type="`autocomplete`"
          :items="languages"
          :label="$tr('language')"
          :item-text="(item) => `${item.name} (${item.native})`"
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
      languages: [],
      form: {
        product_id: "",
        study_language_id: "",
        created_date: null,
        updated_date: null,
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
    };
  },

  methods: {
    changeModel() {
      this.model = !this.model;
      if (this.model) {
        this.fetchAllProducts();
        this.fetchLanguages();
      }
    },

    async fetchLanguages() {
      try {
        if (this.languages.length > 0) {
          return;
        }
        const url = `single-sales/product-study/fetch?languages=true${
          this.$attrs.order ? "&order=true" : ""
        }`;
        const { data } = await this.$axios.get(url);
        this.languages = data;
      } catch (_) {}
    },

    async fetchAllProducts() {
      try {
        if (this.products.length > 0) {
          return;
        }
        const url = `single-sales/product-study/fetch?products=true${
          this.$attrs.order ? "&order=true" : ""
        }`;
        const { data } = await this.$axios.get(url);
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

      if (this.form.study_language_id)
        this.sortedData.study_language_id =
          "like@@" + this.form.study_language_id;

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
        product_id: "",
        study_language_id: "",
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
