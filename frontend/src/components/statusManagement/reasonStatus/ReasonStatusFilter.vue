<template>
  <div class="countryFilter">
    <CustomFilter
      @onClose="$emit('close')"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('status_event_list'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.subsystem"
          :type="`autocomplete`"
          :items="subSystems"
          :itemValue="`name`"
          :label="$tr('sub_system')"
          @change="addSubSystem"
        />
        <FilterInput
          v-model="form.type"
          :label="$tr('types')"
          :type="'autocomplete'"
          :items="types"
          :loading="loadingAutocompleteData"
        />
        <FilterInput
          v-model="form.reason"
          :label="$tr('reasons')"
          :itemText="'title'"
          :type="'autocomplete'"
          :items="reasons"
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
import { mapGetters, mapActions } from "vuex";
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
    slug: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      form: {
        subsystem: "",
        type: "",
        reason: "",
        created_at: null,
        updated_at: null,
        include: 1,
        ids: [],
      },
      types: [],
      sortedData: {},
      loadingAutocompleteData: false,
      clearInput: false,
    };
  },
  computed: {
    ...mapGetters({
      reasons: "reasons/get_reasons",
    }),
  },
  async created() {
    await this.fetchReasons({ slug: this.slug });
  },
  methods: {
    ...mapActions({
      fetchReasons: "reasons/fetchReasons",
    }),
    async addSubSystem() {
      this.loadingAutocompleteData = true;
      this.form.type = "";
      this.types = [];

      const subsystem = this.form.subsystem;
      const response = await this.$axios.get("subsystems", {
        params: { subsystem: subsystem },
      });

      this.loadingAutocompleteData = false;
      this.types = response.data;

      // this.subSystem = "";
    },
    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_at = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_at = date;
    },

    sortData() {
      this.sortedData = {};
      if (this.form.subsystem)
        this.sortedData.name = ['whereHas', 'subsystem', this.form.subsystem];

      if (this.form.type) this.sortedData.type = ['whereHas', 'reasonTypes', this.form.type];

      if (this.form.reason)
        this.sortedData.id = ['whereHas', 'reason', this.form.reason];

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
        this.$emit("getRecord", this.sortedData);
        this.$emit("close");
      }
    },
    onClear() {
      this.form = {
        subsystem: "",
        type: "",
        created_at: null,
        updated_at: null,
        reason:'',
        include: 1,
        ids: [],
      };
      this.clearInput = true;
      setTimeout(() => {
        this.clearInput = false;
      }, 2000);

      if (!this.isAlreadySubmited())
        this.$emit("getRecord", this.sortedData);
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
