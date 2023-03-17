<template>
  <div class="countryFilter">
    <CustomFilter
      @onClose="$emit('close')"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('roles'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.department_id"
          :label="$tr('departments')"
          :type="'autocomplete'"
          :items="departments"
          :hasAvatar="true"
        />
        <FilterInput
          v-model="form.name"
          :label="$tr('name')"
          :placeholder="$tr('name')"
        />
      </template>
      <template v-slot:date_range>
        <FilterInput
          v-model="form.created_at"
          @getDate="getDate"
          :label="$tr('created_date')"
          :type="'date-range'"
        />
        <FilterInput
          :clearInput.sync="clearInput"
          v-model="form.updated_at"
          @getDate="getDate"
          :label="$tr('last_edited')"
          :placeholder="$tr('select_last_updated_date')"
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
import FilterInput from "../../design/components/FilterInput.vue";
import CustomFilter from "../../common/CustomFilter.vue";
export default {
  components: {
    FilterInput,
    CustomFilter,
  },
  props: {
    departments: {
      require: true,
    },
  },
  data() {
    return {
      form: {
        name: "",
        department_id: "",
        created_at: null,
        updated_at: null,
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
        this.form.created_at = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_at = date;
    },

    sortData() {
      this.sortedData = {};
      if (this.form.name) this.sortedData.name = "like@@" + this.form.name;

      if (this.form.department_id)
        this.sortedData.department_id = [
          "whereHas",
          "departments",
          this.form.department_id,
        ];

      if (this.form.updated_at)
        this.sortedData.updated_at = ["date", "range"].concat(
          this.form.updated_at
        );

      if (this.form.created_at)
        this.sortedData.created_at = ["date", "range"].concat(
          this.form.created_at
        );

      if (this.form.ids.length > 0) {
        this.sortedData.ids = this.form.ids;
        this.sortedData.include = this.form.include;
      }
    },

    onSubmit() {
      if (!this.isAlreadySubmited()) {
        this.$emit("filterRecords", this.sortedData);
        this.$emit("close");
      }
    },
    onClear() {
      this.form = {
        name: "",
        department_id: "",
        created_at: null,
        updated_at: null,
        include: 1,
        ids: [],
      };
      this.clearInput = true;
      setTimeout(() => {
        this.clearInput = false;
      }, 2000);

      if (!this.isAlreadySubmited())
        this.$emit("filterRecords", this.sortedData);
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
