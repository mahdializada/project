<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('storage_requests'))"
    >
      <template v-slot:data> 
        <FilterInput v-model="form.name" :label="$tr('name')" />
        <FilterInput v-model="form.code" :label="$tr('code')" />
        <FilterInput v-model="form.values" :label="$tr('values')" /> 
        <FilterInput
          v-model="form.created_by"
          :type="`autocomplete`"
          :items="users"
          :label="$tr('created_by')"
          :item-text="(item) => `${item.firstname} (${item.lastname})`"
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
      users: [],
      form: { 
        code : "",
        name: "",
        created_by: "",
        values: "",
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
        this.fetchUsers();
      }
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

      if (this.form.code )
        this.sortedData.code  = "like@@" + this.form.code ;

      if (this.form.name) this.sortedData.name = "like@@" + this.form.name;
      if (this.form.values) this.sortedData.values = "like@@" + this.form.values;

      if (this.form.updated_date)
        this.sortedData.updated_at = ["date", "range"].concat(
          this.form.updated_date
        );

      if (this.form.created_date)
        this.sortedData.created_at = ["date", "range"].concat(
          this.form.created_date
        ); 
      if (this.form.created_by)
        this.sortedData.created_by  = 'exact@@'+ this.form.created_by;

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
        description : "",
        name: "",
        code: "",
        created_by: "",
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
    async fetchUsers() {
      try {
        if (this.users.length > 0) {
          return;
        }
        const { data } = await this.$axios.get("common/users");
        this.users = data.users;
      } catch (_) {}
    },
  },
};
</script>
