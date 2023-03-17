<template>
  <div class="h-full mt-10">
    <CTitle :text="$tr('select_item_code')" />
    <div class="w-full d-flex">
      <InputCard
        :title="this.$tr('item_code')"
        :hasSearch="true"
        :hasPagination="true"
        @search="(v) => (searchItem = v)"
      >
        <NoCheckboxItemContainer
          v-model="form.item_code.$model"
          :items="filterItemCode"
          :loading="isFetchingItemCode"
          :item_text="'pcode'"
          :item_value="'pcode'"
          @selectedItem="selectedItem"
          :no_data="filterItemCode.length < 1 && !isFetchingItemCode"
          :rules="
            validateRule(
              form.item_code, // validate objec
              $root, // context
              $tr('country') // lable for feedback
            )
          "
          :invalid="form.item_code.$invalid"
          height="300px"
          :customIcon="'product'"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div>
  </div>
</template>

<script>
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      searchItem: "",
      isFetchingItemCode: false,
      validateRule: GlobalRules.validate,
      items: [],
    };
  },
  computed: {
    filterItemCode: function () {
      if (this.searchItem) {
        const filter = (item) =>
          item?.pcode?.toLowerCase()?.includes(this.searchItem?.toLowerCase());
        return this.items.filter(filter);
      }
      return this.items;
    },
  },
  methods: {
    async validate() {
      this.form.item_code.$touch();
      return !this.form.item_code.$invalid;
    },
    selectedItem(item) {
      this.form.$model.item_name = item.name;
    },
    async loaded() {
      if (this.form.company_id.$model) {
        this.items = [];
        this.fetchItems();
      }
    },
    async fetchItems() {
      try {
        let page_type = 0;
        switch (this.form.sales_type.$model) {
          case "WhatsApp Sales":
            page_type = 1;
            break;
          case "Quick Card Sales":
            page_type = 2;
            break;
        }
        this.isFetchingItemCode = true;
        const url = `advertisement/connection/generate_link/products/${
          this.form.$model.company_id +
          "," +
          this.form.$model.country_id +
          "," +
          page_type
        }`;
        const { data } = await this.$axios.get(url);
        this.items = data.items;
        this.isFetchingItemCode = false;
      } catch (error) {
        this.isFetchingItemCode = false;
      }
    },
  },
  components: { InputCard, NoCheckboxItemContainer, CTitle },
};
</script>

<style>
</style>