<template>
    <v-dialog v-model="model" persistent max-width="1300" width="1300">
      <CustomFilter
        @onClose="changeModel"
        @onSubmit="onSubmit"
        @onClear="onClear"
        :cardTitle="$tr('filter_item', $tr('storage_requests'))"
      >
        <template v-slot:data>
          <div v-if="tabKey == 'country'">
            <FilterInput
              v-model="form.country_id"
              :type="`autocomplete`"
              :items="countries"
              :label="$tr('country')"
            />
          </div>
          <div v-if="tabKey == 'company'">
            <FilterInput
              v-model="form.company_id"
              :type="`autocomplete`"
              :items="companies"
              :label="$tr('company')"
            />
          </div>
          <div v-if="tabKey == 'project'">
            <FilterInput
              v-model="form.project_id"
              :type="`autocomplete`"
              :items="projects"
              :label="$tr('project')"
            />
          </div>
          <div v-if="tabKey == 'sales_type'">
            <FilterInput
              v-model="form.sales_type2"
              :type="`autocomplete`"
              :items="sales_types"
              :label="$tr('sales_type')"
              item-value="id"
              item-text="name"
            />
          </div>
          <div v-if="tabKey == 'item_code'">
            <FilterInput
              v-model="form.item_status"
              :type="`autocomplete`"
              :items="item_status"
              :label="$tr('status')"
              item-value="status"
              item-text="status"
            />
  
            <FilterInput
              v-model="form.product_name"
              :type="`autocomplete`"
              :items="products"
              :label="$tr('name')"
              item-value="product_name"
              item-text="product_name"
            />
            <FilterInput
              v-model="form.product_code"
              :type="`autocomplete`"
              :items="products"
              :label="$tr('item_code')"
              item-value="product_code"
              item-text="product_code"
            />
            <FilterInput
              v-model="form.prod_sale_goal"
              :type="`autocomplete`"
              :items="saleGoal"
              :label="$tr('prod_sale_goal')"
              item-value="id"
              item-text="name"
            />
            <FilterInput
              v-model="form.prod_sales_stability"
              :type="`autocomplete`"
              :items="prod_sales_stability"
              :label="$tr('prod_sales_stability')"
              item-value="id"
              item-text="id"
            />
            <FilterInput
              v-model="form.prod_profitability"
              :type="`autocomplete`"
              :items="prod_profitability"
              :label="$tr('prod_profitability')"
              item-value="id"
              item-text="id"
            />
  
            <FilterInput
              v-model="form.prod_import_source"
              :type="`autocomplete`"
              :items="prod_import_source"
              :label="$tr('prod_import_source')"
              item-value="id"
              item-text="id"
            />
            <FilterInput
              v-model="form.prod_production_type"
              :type="`autocomplete`"
              :items="prod_production_type"
              :label="$tr('prod_production_type')"
              item-value="id"
              item-text="id"
            />
  
            <FilterInput
              v-model="form.prod_source"
              :type="`autocomplete`"
              :items="productSources"
              :label="$tr('product_source')"
              item-value="id"
              item-text="name"
            />
            <FilterInput
              v-model="form.prod_style"
              :type="`autocomplete`"
              :items="productStyles"
              :label="$tr('prod_style')"
              item-value="id"
              item-text="name"
            />
            <FilterInput
              v-model="form.prod_section"
              :type="`autocomplete`"
              :items="productSections"
              :label="$tr('prod_section')"
              item-value="id"
              item-text="name"
            />
            <FilterInput
              v-model="form.prod_new_product_source"
              :type="`autocomplete`"
              :items="productNewSources"
              :label="$tr('productNewSources')"
              item-value="id"
              item-text="name"
            />
            <FilterInput
              v-model="form.prod_work_type"
              :type="`autocomplete`"
              :items="productWorkTyps"
              :label="$tr('prod_work_type')"
              item-value="id"
              item-text="name"
            />
          </div>
        </template>
        <template v-slot:date_range>
          <!-- <FilterInput
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
          /> -->
        </template>
        <template v-slot:id_filtering>
          <FilterInput
            :clearInput.sync="clearInput"
            @isInclude="(isInclude) => (form.include = isInclude)"
            @getIds="(ids) => (form.ids = ids)"
            :label="$tr(tabKey) + ' ' + $tr('id')"
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
        tabKey: "",
        model: false,
        countries: [],
        companies: [],
        projects: [],
        sales_types: [
          { id: "Single Sale", name: "single_sales", active: true },
          { id: "WhatsApp", name: "WhatsApp", active: true },
          { id: "Shopping Cart", name: "shopping_cart", active: false },
        ],
        prod_profitability: [
          { id: "profit" },
          { id: "medium_profit" },
          { id: "less_profit" },
          { id: "loss" },
          { id: "medium_loss" },
          { id: "more_loss" },
        ],
        prod_sales_stability: [{ id: "stable" }, { id: "stable" }],
        prod_import_source: [{ id: "uae" }, { id: "china" }],
        prod_production_type: [{ id: "ready" }, { id: "manufacturing" }],
  
        item_status: [
          {
            status: "ready_to_sale",
          },
          {
            status: "in_creation",
          },
          {
            status: "in_testing",
          },
          {
            status: "in_sales",
          },
          {
            status: "retesting",
          },
          {
            status: "waiting_for_md",
          },
          {
            status: "short_stop",
          },
          {
            status: "final_stop",
          },
          {
            status: "cancel",
          },
          {
            status: "remove",
          },
        ],
        saleGoal: [
          { id: "for_stock_disposal", name: "for_stock_disposal" },
          { id: "for_profit", name: "for_profit" },
        ],
        productSources: [
          {
            id: "warehouse",
            name: "warehouse",
            icon: "mdi-warehouse",
            active: true,
          },
          { id: "market", name: "market", icon: "mdi-cart", active: true },
          { id: "both", name: "both", icon: "mdi-message-alert", active: true },
        ],
        productStyles: [
          { id: "single", name: "single", icon: "mdi-book", active: true },
          {
            id: "collection",
            name: "collection",
            icon: "mdi-bookmark-box-multiple",
            active: true,
          },
          { id: "project", name: "project", icon: "mdi-file", active: true },
        ],
        productSections: [
          { id: "new", name: "new", icon: "mdi-plus", active: true },
          { id: "old", name: "old", icon: "mdi-timer-sand", active: true },
          { id: "stock", name: "stock", icon: "mdi-reload", active: true },
          { id: "renew", name: "renew", icon: "mdi-trending-up", active: true },
        ],
        productNewSources: [
          {
            id: "market_visit",
            name: "market_visit",
            icon: "mdi-share-variant",
          },
          {
            id: "supplier_suggestion",
            name: "supplier_suggestion",
            icon: "mdi-account-search",
          },
          {
            id: "advertiser_products",
            name: "advertiser_products",
            icon: "mdi-chart-line",
          },
          {
            id: "company_source",
            name: "company_source",
            icon: "fa fa-star",
          },
        ],
        productWorkTyps: [
          { id: "creation", name: "creation" },
          { id: "copy", name: "copy" },
        ],
  
        form: {
          prod_sales_stability: "",
          prod_production_type: "",
          prod_profitability: "",
          product_name: "",
          country_id: "",
          company_id: "",
          product_code: "",
          sales_type2: "",
          item_status: "",
          prod_sale_goal: "",
          prod_source: "",
          prod_style: "",
          prod_section: "",
          prod_new_product_source: "",
          prod_work_type: "",
          // created_date: null,
          // updated_date: null,
          include: 1,
          ids: [],
          prod_import_source: "",
        },
        sortedData: {},
        clearInput: false,
        products: [],
      };
    },
  
    methods: {
      changeModel(item) {
        this.model = !this.model;
        this.tabKey = item;
        console.log("item", item);
        if (this.model) {
          if (item == "country") this.fetchCountries();
          if (item == "company") this.fetchCompanies();
          if (item == "project") this.fetchProject();
          if (item == "item_code") this.fetchProducts();
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
  
        if (this.form.country_id)
          this.sortedData.country_id = this.form.country_id;
        if (this.form.company_id)
          this.sortedData.company_id = this.form.company_id;
        if (this.form.project_id)
          this.sortedData.project_id = this.form.project_id;
        if (this.form.product_name)
          this.sortedData.product_name = this.form.product_name;
        if (this.form.product_code)
          this.sortedData.product_code = this.form.product_code;
        if (this.form.sales_type2)
          this.sortedData.sales_type2 = this.form.sales_type2;
        if (this.form.item_status)
          this.sortedData.item_status = this.form.item_status;
        if (this.form.prod_sale_goal)
          this.sortedData.prod_sale_goal = this.form.prod_sale_goal;
        if (this.form.prod_source)
          this.sortedData.prod_source = this.form.prod_source;
        if (this.form.prod_style)
          this.sortedData.prod_style = this.form.prod_style;
        if (this.form.prod_section)
          this.sortedData.prod_section = this.form.prod_section;
        if (this.form.prod_new_product_source)
          this.sortedData.prod_new_product_source =
            this.form.prod_new_product_source;
        if (this.form.prod_work_type)
          this.sortedData.prod_work_type = this.form.prod_work_type;
  
        // if (this.form.updated_date)
        //   this.sortedData.updated_at = ["date", "range"].concat(
        //     this.form.updated_date
        //   );
  
        // if (this.form.created_date)
        //   this.sortedData.created_at = ["date", "range"].concat(
        //     this.form.created_date
        //   );
  
        if (this.form.ids.length > 0) {
          this.sortedData.ids = this.form.ids;
          this.sortedData.include = this.form.include;
        }
  
        if (this.form.prod_profitability)
          this.sortedData.prod_profitability = this.form.prod_profitability;
        if (this.form.prod_import_source)
          this.sortedData.prod_import_source = this.form.prod_import_source;
  
        if (this.form.prod_sales_stability)
          this.sortedData.prod_sales_stability = this.form.prod_sales_stability;
  
        if (this.form.prod_production_type)
          this.sortedData.prod_production_type = this.form.prod_production_type;
      },
  
      onSubmit() {
        if (!this.isAlreadySubmited()) {
          console.log("sort data", this.sortedData);
          this.$emit("filterRecords", this.sortedData);
          this.changeModel();
        }
      },
      onClear() {
        this.form = {
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
      async fetchCountries() {
        try {
          if (this.countries.length > 0) {
            return;
          }
          const { data } = await this.$axios.get("online-sales/filter/countries");
          this.countries = data.items;
        } catch (error) {}
      },
      async fetchCompanies() {
        try {
          if (this.companies.length > 0) {
            return;
          }
  
          const { data } = await this.$axios.get("online-sales/filter/companies");
          this.companies = data.items;
        } catch (error) {}
      },
      async fetchProject() {
        try {
          if (this.projects.length > 0) {
            return;
          }
  
          const { data } = await this.$axios.get("online-sales/filter/projects");
          this.projects = data.items;
        } catch (error) {}
      },
      async fetchProducts() {
        try {
          if (this.products.length > 0) {
            return;
          }
  
          const { data } = await this.$axios.get("online-sales/filter/products");
          this.products = data.items;
        } catch (error) {}
      },
    },
  };
  </script>
  