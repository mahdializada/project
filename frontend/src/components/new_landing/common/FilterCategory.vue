<template>
  <div>
    <div class="categories">
      <div class="d-flex flex-column flex-md-row">
        <div class="w-full">
          <CustomInput
            :items="allCategories"
            v-model="selectedCategory"
            return-object
            item-text="name"
            :label="$tr('label_required', $tr('category'))"
            :placeholder="$tr('choose_item', $tr('category'))"
            type="autocomplete"
            class="me-md-4 mb-md-2 mb-0"
            :error-messages="categoryErrorMessage"
            :loading="categoryFlag"
            :hasIcon="true"
            @change="onChangeHandler"
            @blur="validateCategory"
          />
        </div>
      </div>
			<div class="selected d-flex flex-wrap">
				<SelectedItem
          v-if="selectedCategory"
					@close="removeSelectedCategory"
					:title="selectedCategory.name"
          :logoUrl="selectedCategory.icon"
          logoName
				/>
			</div>
    </div>
    <div class="sub-categories">
      <div class="d-flex flex-column flex-md-row">
        <div class="w-full">
          <CustomInput
            :items="subCategories"
            v-model="selectedSubCategory"
            :label="$tr('label_required', $tr('sub_category'))"
            :placeholder="$tr('choose_item', $tr('sub_category'))"
            item-text="name"
            logoName
            has-logo
            return-object
            type="autocomplete"
            class="me-md-4 mb-md-2"
            :loading="subCategoryFlag"
            :hasIcon="true"
            :error-messages="subCategoryErrorMessage"
            @change="validateSubCategory"
            @blur="validateSubCategory"
          />
        </div>
      </div>
			<div class="selected d-flex flex-wrap">
				<SelectedItem
          v-if="selectedSubCategory"
					@close="removeSelectedSubCategory"
					:title="selectedSubCategory.name"
          :logoUrl="selectedSubCategory.icon"
          logoName
				/>
			</div>
    </div>
  </div>
</template>

<script>
import FormBtn from "../../design/components/FormBtn.vue";
import SelectedItem from "../../design/components/SelectedItem";
import CustomInput from "../../design/components/CustomInput";
import { mapActions, mapGetters, mapMutations } from "vuex";

export default {
  name: "FilterCountry",
  components: { CustomInput, SelectedItem, FormBtn },
  props: {
    url: {
      type: String,
      require: true,
    },
    sub_url: {
      type: String,
      require: true,
    },
  },
  data() {
    return {
      subCategoryErrorMessage: "",
      categoryErrorMessage: "",
      subCategoryFlag: false,
      categoryFlag: false,
      categories: [],
      selectedCategory: null,
      selectedSubCategory: null,
    };
  },

  created() {
    if (this.categories.length == 0) {
      this.fetchAllCategories();
    }
  },

  computed: {
    ...mapGetters({
      subCategories: "new_landing/subCategories",
      allCategories: "new_landing/allCategories",
    }),
  },

  methods: {
    ...mapMutations({
      setSubCategories: "new_landing/SET_SUB_CATEGORIES",
    }),
    ...mapActions({
      fetchSubCategories: "new_landing/fetchSubCategories",
      fetchCategories: "new_landing/fetchAllCategories",
    }),
    removeSelectedCategory(){
      this.selectedCategory = null;
      this.validateCategory();
      this.removeSelectedSubCategory();
      this.setSubCategories([]);
    },
    removeSelectedSubCategory(){
      this.selectedSubCategory = null;
      this.validateSubCategory();
    },
    async fetchAllCategories() {
      this.categoryFlag = true;
      const data = {
        status: "active",
      };
      await this.fetchCategories({ url: this.url, data });
      this.categoryFlag = false;
    },
    validateCategory() {
      const CategoryRequiredText = this.$tr(
        "item_is_required",
        this.$tr("category")
      );
      this.categoryErrorMessage = this.selectedCategory
        ? ""
        : CategoryRequiredText;
    },
    validateSubCategory() {
      const subCategoryRequiredText = this.$tr(
        "item_is_required",
        this.$tr("company")
      );
      this.subCategoryErrorMessage = this.selectedSubCategory
    ? ""
        : subCategoryRequiredText;
    },
    // check validations
    checkValidation() {
      // validate category
      this.validateCategory();
      // validate sub-category
      this.validateSubCategory();
      // check  validations
      let isCategoryInvalid = this.selectedCategory ? true : false;
      let isSubCategoryInvalid = this.selectedSubCategory ? true : false;

      return isCategoryInvalid && isSubCategoryInvalid;
    },
    clearForm() {
      this.selectedCategory = null;
      this.selectedSubCategory= null;
    },
    async onChangeHandler() {
      this.validateCategory();
      const data = {
        category_id: this.selectedCategory.id,
        key: "all",
      };
      this.subCategoryFlag = true;
      await this.fetchSubCategories({ url: this.sub_url, data });
      this.subCategoryFlag = false;
    },
  },
};
</script>

<style scoped></style>
