<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('ipa'))"
    >
      <template v-slot:data> 
        <FilterInput
          v-model="form.product_id"
          :type="`autocomplete`"
          :items="products"
          :label="$tr('product')"
          :item-text="(item) => `${item.name}`"
        />
        <FilterInput
          v-model="form.target_age_min"
          :type="'number'"
          :label="$tr('min_age')"
          item-text="name"
        />
        <FilterInput
          v-model="form.target_age_max"
          :type="`number`"
          :label="$tr('max_age')"
          item-text="name"
        />
        <FilterInput
          v-model="form.target_gender"
          :type="`autocomplete`"
          :items="genders"
          :label="$tr('target_gender')"
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
      form: {
        target_age_min:'',
        target_gender:'',
        product_id:'',
        target_age_max:'',
        created_date: null,
        updated_date: null,
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
      genders:[
        {name:this.$tr('male'),id:"male"},
        {name:this.$tr('female'),id:"female"},
        {name:this.$tr('both'),id:"both"}
      ],
    };
  }, 
  methods: {
    changeModel() {
      this.model = !this.model;
      if(this.model){
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
          "ispp",
          this.form.product_id,
        ]; 
      if (this.form.target_age_max)
        this.sortedData.target_age_max =
          "exact@@" + this.form.target_age_max;

      if (this.form.target_age_min)
        this.sortedData.target_age_min =
            "exact@@" + this.form.target_age_min;
      if (this.form.target_gender)
        this.sortedData.target_gender =
          "exact@@" + this.form.target_gender;

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
        sourcing_type: "",
        importing_country_id: "",
        receiving_country_id: "",
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
