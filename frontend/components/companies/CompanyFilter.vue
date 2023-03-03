<template>
  <div class="countryFilter">
    <CustomFilter
      @onClose="$emit('close')"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('companies'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.system_id"
          :type="`autocomplete`"
          :items="systems"
          :label="$tr('system')"
        />
        <FilterInput
          v-model="form.country_id"
          :type="`autocomplete`"
          :items="countries"
          :label="$tr('country')"
        />
        <FilterInput
          v-model="form.investment_type"
          :type="`autocomplete`"
          :items="investment_types"
          :label="$tr('investment_type')"
        />

        <FilterInput
          v-model="form.name"
          :label="$tr('item_name', $tr('company'))"
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
  </div>
</template>

<script>
import FilterInput from "../design/components/FilterInput.vue";
import CustomFilter from "../common/CustomFilter.vue";
export default {
  components: {
    FilterInput,
    CustomFilter,
  },
  props: {
    countries: {
      require: true,
    },
    systems: {
      require: true,
    },
  },
  data() {
    return {
      form: {
        name: "",
        include: 1,
        system_id: "",
        investment_type: "",
        country_id: "",
        created_date: null,
        updated_date: null,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
      investment_types: [
        { id: "Main Company", name: this.$tr("main_company") },
        { id: "Other", name: this.$tr("other") },
      ],
    };
  },

  methods: {
    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
    },

    sortData() {
      this.sortedData = {};
      if (this.form.name) this.sortedData.name = "like@@" + this.form.name;
      if (this.form.system_id)
      this.sortedData.system_id = ['whereHas', 'systems', this.form.system_id]

      if (this.form.investment_type)
        this.sortedData.investment_type = "exact@@" + this.form.investment_type;

      if (this.form.country_id)
      this.sortedData.country_id = ['whereHas', 'countries', this.form.country_id]

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
        this.$emit("getRecord", this.sortedData);
        this.$emit("close");
      }
    },
    onClear() {
      this.form = {
        name: "",
        system_id: "",
        investment_type: "",
        country_id: "",
        created_date: null,
        updated_date: null,
        include: 1,
        ids: [],
      };
      this.clearInput = true;
      setTimeout(() => {
        this.clearInput = false;
      }, 2000);

      if (!this.isAlreadySubmited()) this.$emit("getRecord", this.sortedData);
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
