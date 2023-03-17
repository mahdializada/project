<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <CustomFilter
      @onClose="changeModel"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('sourcers'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.done_by"
          :type="`autocomplete`"
          :items="users"
          :label="$tr('done_by')"
          :item-text="(item) => `${item.firstname} (${item.lastname})`"
        />


        
        <FilterInput
          v-model="form.created_by"
          :type="`autocomplete`"
          :items="users"
          :label="$tr('created_by')"
          :item-text="(item) => `${item.firstname} (${item.lastname})`"
        />
        <FilterInput
          v-model="form.priority"
          :type="`autocomplete`"
          :items="priorityItems"
          :label="$tr('priority')"
        />
        <FilterInput
          v-model="form.status"
          :type="`autocomplete`"
          :items="status"
          :label="$tr('status')"
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
          v-model="form.done_item_date"
          @getDate="getDate"
          :label="$tr('done_date')"
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
        done_by: "",
        status: "",
        created_by: "",
        priority: "",
        created_date: null,
        updated_date: null,
        done_item_date: null,
        include: 1,
        ids: [],
      },
      sortedData: {},
      clearInput: false,
      priorityItems: [
        { id: "very high", name: this.$tr("very_high") },
        { id: "high", name: this.$tr("high") },
        { id: "normal", name: this.$tr("normal") },
        { id: "low", name: this.$tr("low") },
      ],
      status: [
        this.$tr("inprocess"),
        this.$tr("archived"),
        this.$tr("cancelled"),
        this.$tr("done"),
        this.$tr("failed"),
      ],
    };
  },

  methods: {
    changeModel() {
      this.model = !this.model;
      if (this.model) {
        this.fetchUsers();
      }
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

    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
      else if (selected.toLowerCase().includes("done_date"))
        this.form.done_item_date = date;
    },

    sortData() {
      this.form = JSON.parse(JSON.stringify(this.form)); // Add this line to prevent reference.
      this.sortedData = {};
      if (this.form.done_by)
        this.sortedData.done_by = ["whereHas", "doneBy", this.form.done_by];

      if (this.form.created_by)
        this.sortedData.created_by = "exact@@" + this.form.created_by;

      if (this.form.priority)
        this.sortedData.priority = "exact@@" + this.form.priority;

      if (this.form.status)
        this.sortedData.status = "exact@@" + this.form.status;

      if (this.form.updated_date)
        this.sortedData.updated_at = ["date", "range"].concat(
          this.form.updated_date
        );
      if (this.form.done_item_date)
        this.sortedData.done_date = ["date", "range"].concat(
          this.form.done_item_date
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
        done_by: "",
        created_by: "",
        priority: "",
        created_date: null,
        updated_date: null,
        done_item_date: null,
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
