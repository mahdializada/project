<template>
  <div>
    <CTitle :text="`supplier`" />
    <div class="w-full d-flex">
      <InputCard
        :title="this.$tr('supplier')"
        :hasSearch="true"
        :hasPagination="true"
        @search="(v) => (searchItem = v)"
      >
        <NoCheckboxItemContainer
          v-model="form.supplier_id.$model"
          :items="filterSupplier"
          :loading="isFetchingSupplier"
          :item_text="'supplier_name'"
          :item_value="'id'"
          @selectedItem="selectedItem"
          :no_data="filterSupplier.length < 1 && !isFetchingSupplier"
          :rules="
            validateRule(
              form.supplier_id, // validate objec
              $root, // context
              $tr('country') // lable for feedback
            )
          "
          :invalid="form.supplier_id.$invalid"
          height="250px"
          :supplierIcon="'supplier_icon'"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div>
    <!-- <div class="w-full d-flex mt-2">
      <InputCard
        :title="this.$tr('brand')"
        :hasSearch="true"
        :hasPagination="true"
        @search="(v) => (searchBrands = v)"
      >
        <NoCheckboxItemContainer
          v-model="form.brand_id.$model"
          :items="filterBrand"
          :loading="isFetchingBrand"
          @selectedItem="selectedBrand"
          :no_data="filterBrand.length < 1 && !isFetchingBrand"
          :rules="
            validateRule(
              form.brand_id, // validate objec
              $root, // context
              $tr('brand') // lable for feedback
            )
          "
          :invalid="form.brand_id.$invalid"
          height="250px"
          :customIcon="'brand'"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div> -->
    <!-- <div class="w-full me-lg-1 mb-2 mb-lg-0">
         <CSelect
              @blur="form.brand_id.$touch()"
              v-model="form.brand_id.$model"
              :rules="validateRule(form.brand_id, $root, $tr('brand'))"
              :title="$tr('label_required', $tr('brand'))"
              :placeholder="$tr('brand')"
              prepend-inner-icon="mdi-tag"
              :items="brands"
              item-text="name"
              item-value="id"
            />
    </div> -->
  </div>
</template>

<script>
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import GlobalRules from "~/helpers/vuelidate";
import CSelect from "../../new_form_components/Inputs/CSelect";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      searchItem: "",
      searchBrands: "",
      isFetchingSupplier: false,
      isFetchingBrand: false,
      validateRule: GlobalRules.validate,
      suppliers: [],
      brands: [],
    };
  },
  components: { InputCard, NoCheckboxItemContainer, CSelect, CTitle },
  computed: {
    filterSupplier: function () {
      if (this.searchItem) {
        const filter = (item) =>
          item?.name?.toLowerCase()?.includes(this.searchItem?.toLowerCase());
        return this.suppliers.filter(filter);
      }
      return this.suppliers;
    },
    // filterBrand: function () {
    //   if (this.searchBrands) {
    //     const filter = (item) =>
    //       item?.name?.toLowerCase()?.includes(this.searchBrands?.toLowerCase());
    //     return this.brands.filter(filter);
    //   }
    //   return this.brands;
    // },
  },
  methods: {
    async validate() {
      this.form.supplier_id.$touch();
      // this.form.brand_id.$touch();
      console.log(this.form);
      return !this.form.supplier_id.$invalid ;
      // !this.form.brand_id.$invalid;
    },
    selectedItem(item) {
      console.log(item);
      this.form.supplier_id.$model = item.id;
      this.form.supplier_name.$model=item;
      console.log(this.form);
    },
    // selectedBrand(item) {
    //   this.form.brand_id.$model = item.id;
    // },
    async loaded() {
      this.fetchSuppliers();
      // this.fetchBrands();
    },
    async fetchSuppliers() {
      this.isFetchingSupplier = true;
      try {
        const supplier = await this.$axios.get("supplier");
        this.supplier_list = supplier.data;
        this.suppliers = this.supplier_list;
        console.log("name:",this.form);
      } catch (error) {
        console.log("error", error);
      }
      this.isFetchingSupplier = false;
    },
    // async fetchBrands() {
    //   this.isFetchingBrand = true;
    //   try {
    //     const brand = await this.$axios.get(
    //       "single-sales/brand-ssp/get?filter_brand=true"
    //     );
    //     this.brand_list = brand.data;
    //     this.brands = this.brand_list;
    //   } catch (error) {}
    //   this.isFetchingBrand = false;
    // },
  },
};
</script>

<style></style>
