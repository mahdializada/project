<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('storage_request'))"
    >
      <template v-slot:data>
        <FilterInput v-model="form.description " :label="$tr('description ')" />
        <FilterInput v-model="form.name" :label="$tr('name')" />
          <FilterInput
          v-model="form.attribute_id"
          :type="`autocomplete`"
          :items="attributes"
          :label="$tr('attribute')"
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
      attributes: [],
      form: {
        description : "",
        name: "",
        created_date: null,
        updated_date: null,
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
    };
  },
 
 async created(){
    await this.getAttributes();
 },

  methods: {
    changeModel() {
      this.model = !this.model;
    },

    // fetch all users
    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
    },
  async getAttributes() {
      try {
        const url = "single-sales/categories-ssp?key=getAttributes";
        const { data } = await this.$axios.get(url);
        this.attributes = data;
      } catch (error) {}
    },
    sortData() {
      this.form = JSON.parse(JSON.stringify(this.form)); // Add this line to prevent reference.
      this.sortedData = {};

      if (this.form.description )
        this.sortedData.description  = "like@@" + this.form.description ;

      if (this.form.name) this.sortedData.name = "like@@" + this.form.name;

      if (this.form.attribute_id) this.sortedData.attribute_id = ["whereHas",'attributes' , this.form.attribute_id];

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
        description : "",
        name: "",
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
