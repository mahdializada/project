<template>
  <div class="countryFilter">
    <CustomFilter
      @onClose="$emit('close')"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle='$tr("filter")'
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.subsystem"
          :label="$tr('sub_system')"
          :items="subSystems"
          :type="'autocomplete'"
           
        />
        <FilterInput v-model="form.title" :label="$tr('title')" />
      </template>
      <template v-slot:date_range>
        <FilterInput
          v-model="form.created_date"
          @getDate="getDate"
          :label="$tr('created_at')"
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
    subSystems: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      form: {
        subsystem: "",
        title: "",
        created_date: "",
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
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
      
      if (this.form.subsystem) this.sortedData.subsystem = "exact@@" + this.form.subsystem;

      if (this.form.title)
        this.sortedData.title = "like@@" + this.form.title;
 

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
        subsystem: "",
        title: "",
        created_date: "",
        include: 1,
        ids: [],
      },
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
