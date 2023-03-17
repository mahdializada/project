<template>
  <div>
    <CustomFilter
      @onClose="$emit('close')"
      @onSubmit="onSubmit"
      @onClear="onClear"
      :cardTitle="$tr('filter_item', $tr('design_request'))"
    >
      <template v-slot:data>
        <FilterInput
          v-model="form.company"
          :hasAvatar="true"
          :label="$tr('company')"
          :items="companies"
          type="autocomplete"
        />
        <FilterInput
          @input="fetchTemplates"
          @click:clear="form.template = ''"
          v-model="form.type"
          :label="$tr('type')"
          :items="types"
          :type="'autocomplete'"
        />
        <FilterInput   
          :loading="isFetchingProducts"      
          v-model="form.product"
          :label="$tr('product_name')"
          :items="products"
          :type="'autocomplete'"
        />
        <FilterInput
          :loading="isFetchingTemplates"
          v-model="form.template"
          :label="$tr('template')"
          :items="templates"
          :type="'autocomplete'"
        />
        <FilterInput
          v-model="form.priority"
          :label="$tr('priority')"
          :items="priorities"
          :type="'autocomplete'"
        />
        <FilterInput
          v-model="form.order_type"
          :label="$tr('order_type')"
          :items="order_types"
          :type="'autocomplete'"
        />
        <FilterInput
          v-model="form.is_assigned"
          :label="$tr('assigned')"
          :items="assigned"
          :type="'autocomplete'"
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
          v-model="form.updated_date"
          @getDate="getDate"
          :label="$tr('created_at')"
          :type="'date-range'"
        />
        <FilterInput
          :clearInput.sync="clearInput"
          v-model="form.completed_date"
          @getDate="getDate"
          :label="$tr('completed_at')"
          :type="'date-range'"
        />
        <FilterInput
          v-model="form.storyboard_reject_count"
          :label="$tr('storyboard_reject_count')"
          :type="'number'"
        />
        <FilterInput
          v-model="form.design_reject_count"
          :label="$tr('design_reject_count')"
          :type="'number'"
        />
        <FilterInput
          @getTime="
            (start_spent_time) => {
              form.start_spent_time = start_spent_time;
            }
          "
          :label="$tr('start_spent_time')"
          :type="'time'"
        />
        <FilterInput
          @getTime="
            (end_spent_time) => {
              form.end_spent_time = end_spent_time;
            }
          "
          :label="$tr('end_spent_time')"
          :type="'time'"
        />
        <FilterInput
          v-model="form.task_prograssive"
          :label="$tr('task_prograssive')"
          :type="'number'"
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
        <FilterInput
          class="pt-1"
          v-model="form.product_code"
          :label="$tr('product_code')"
          :items="products"
          :loading="isFetchingProducts"
          :type="'autocomplete'"
          :itemText="'pcode'"
          :itemValue="'pcode'"
        />
      </template>
    </CustomFilter>
  </div>
</template>
<script>
import FilterInput from "../design/components/FilterInput.vue";
import CustomFilter from "../common/CustomFilter.vue";
export default {
  name: "CompanyFilter",
  components: {
    CustomFilter,
    FilterInput,
  },
  props: {},
  data() {
    return {
      sortedData: {},
      clearInput: false,
      priorities: [
        { id: "Low", name: this.$tr("low") },
        { id: "Medium", name: this.$tr("medium") },
        { id: "High", name: this.$tr("high") },
      ],
      order_types: [
        { id: "copy", name: this.$tr("copy") },
        { id: "scratch", name: this.$tr("scratch") },
        { id: "mix", name: this.$tr("mix") },
      ],
      types: [
        { id: "landing page design", name: "Landing page design" },
        { id: "advertisement content", name: "Advertisement content" },
        { id: "social media design", name: "Social media design" },
        { id: "translation", name: "Translation" },
        { id: "texts and writings", name: "Texts and writings" },
        { id: "general design", name: "General design" },
        { id: "shooting", name: "Shooting" },
      ],
      status: [
        "waiting",
        "in storyboard",
        "storyboard review",
        "storyboard rejected",
        "in design",
        "design review",
        "design rejected",
        "in programming",
        "completed",
        "cancelled",
        "removed",
      ],
      form: {
        product: "",
        priority: "",
        is_assigned: "",
        status: "",
        created_date: null,
        updated_date: null,
        completed_date: null,
        storyboard_reject_count: "",
        product_code: "",
        order_type: "",
        company: "",
        template: "",
        include: 1,
        ids: [],
        task_prograssive: "",
        start_spent_time: "",
        end_spent_time: "",
      },
      companies: [],
      products: [],
      templates: [],
      assigned: [
        { id: 1, name: this.$tr('assigned') },
        { id: 2, name: this.$tr('not_assigned') },
      ],
      isFetchingProducts: false,
      isFetchingTemplates: false,
    };
  },

  methods: {
    async fetchCompanies() {
      const response = await this.$axios.get(
        "companies?key=all&sortBy[]=name&sortDesc[]=false&itemsPerPage=-1"
      );
      
      if (response?.status == 200) this.companies = response?.data?.data;
    },

    async fetchProducts() {
      if(this.products.length > 1) return 0;
      try {
        this.isFetchingProducts = true;
        const currentComUrl = 'design-request-data?product-code=true';
        const reponse = await this.$axios.get(currentComUrl);
        this.products = reponse.data;
      } catch (error) {}
      this.isFetchingProducts = false;
    },

    async fetchTemplates() {
      if(this.templates.length > 1 || !this.form.type){
        this.form.template = '';
        this.templates = [];
        return 0;
      }    
      try {
        let type = this.form.type;
        this.isFetchingTemplates = true;
        const currentComUrl = `design-request-data?template=true&type=${type}`;
        const reponse = await this.$axios.get(currentComUrl);
        this.templates = reponse.data;
      } catch (error) {}
      this.isFetchingTemplates = false;
    },

    getDate(date, selected) {
      if (selected.toLowerCase().includes("created"))
        this.form.created_date = date;
      else if (selected.toLowerCase().includes("updated"))
        this.form.updated_date = date;
      else if (selected.toLowerCase().includes("completed"))
        this.form.completed_date = date;
    },

    sortData() {
      this.sortedData = {};
      if (this.form.company) this.sortedData.company_id = "exact@@" + this.form.company;

      if (this.form.type)
        this.sortedData.type = "exact@@" + this.form.type;

      if (this.form.product)
        this.sortedData.product_name = "exact@@" + this.form.product;

      if (this.form.template && this.form.type)
        this.sortedData.template_id = "exact@@" + this.form.template;

      if (this.form.priority)
        this.sortedData.priority = "exact@@" + this.form.priority;

      if (this.form.is_assigned)
        this.sortedData.is_assigned = "exact@@" + this.form.is_assigned; // Wait

      if (this.form.order_type)
        this.sortedData.order_type = ['whereHas', 'designRequestOrder', this.form.order_type];
  
      if (this.form.design_reject_count)
        this.sortedData.design_reject_count = "exact@@" + this.form.design_reject_count;

      if (this.form.storyboard_reject_count)
        this.sortedData.storyboard_reject_count = "exact@@" + this.form.storyboard_reject_count;

      if (this.form.start_spent_time)
        this.sortedData.start_spent_time = ['time_range', this.form.start_spent_time.days, this.form.start_spent_time.hours, this.form.start_spent_time.minutes,'>=']

      if (this.form.end_spent_time)
        this.sortedData.end_spent_time = ['time_range', this.form.end_spent_time.days, this.form.end_spent_time.hours, this.form.end_spent_time.minutes,'<=']

      if (this.form.task_prograssive)
        this.sortedData.task_prograss = this.form.task_prograssive

      if (this.form.product_code)
        this.sortedData.product_code = "exact@@" + this.form.product_code

      if (this.form.updated_date)
        this.sortedData.updated_at = ["date", "range"].concat(
          this.form.updated_date
        );

      if (this.form.created_date)
        this.sortedData.created_at = ["date", "range"].concat(
          this.form.created_date
        );

        if (this.form.completed_date)
        this.sortedData.completed_date = ["date", "range"].concat(
          this.form.completed_date
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
        product: "",
        priority: "",
        is_assigned: "",
        status: "",
        created_date: null,
        updated_date: null,
        completed_date: null,
        storyboard_reject_count: '',
        design_reject_count: "",
        product_code: "",
        company: "",
        order_type: "",
        template: "",
        include: 1,
        ids: [],
        task_prograssive: null,
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
  async fetch() {
    await this.fetchCompanies();
    await this.fetchProducts();
  },
};
</script>
