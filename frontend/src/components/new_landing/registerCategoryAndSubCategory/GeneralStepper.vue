<template>
  <div>
    <div
      class="d-flex flex-column flex-md-row mt-3 mb-0 pb-0"
      v-if="category_type == 'sub_category'"
    >
      <v-form lazy-validation ref="category" class="w-full">
        <div class="w-full">
          <CustomInput
            :items="categories"
            v-model="$v.categoriesData.category_id.$model"
            :rules="validate($v.categoriesData.category_id, $root, 'category')"
            item-value="id"
            item-text="name"
            :label="$tr('label_required', $tr('category'))"
            :placeholder="$tr('choose_item', $tr('category'))"
            type="autocomplete"
            class="mb-0"
            :loading="is_loading_api"
            :error-messages="categoryErrorMessage"
          />
        </div>
      </v-form>
    </div>

    <v-expansion-panels class="mb-1">
      <v-expansion-panel
        readonly
        ref="categoryPanelRefs"
        v-for="(category, index) in categoriesData.category"
        :key="index"
      >
        <v-expansion-panel-header
          color=""
          :hide-actions="selected_index == index && expansion_status"
        >
          <div class="d-flex align-center">
            <v-avatar :size="30" color="blue-grey darken-3">
              <img :src="generateIconPath(category.icon)" alt="" />
            </v-avatar>
            <h4 class="primary--text ms-1">{{ category.name }}</h4>
          </div>

          <v-spacer />

          <v-btn
            class="ms-2"
            style="height: 32px; max-width: 32px"
            text
            icon
            color="error"
            @click="toggleExpansion(index)"
            v-if="selected_index == index && expansion_status"
          >
            <v-icon size="30">mdi-close-circle-outline</v-icon>
          </v-btn>
          <v-btn
            class="ms-2"
            style="height: 32px; max-width: 32px"
            text
            icon
            color="primary"
            v-if="selected_index == index && expansion_status"
            @click="saveChanges(index)"
          >
            <v-icon size="30">mdi-checkbox-marked-circle</v-icon>
          </v-btn>

          <v-btn
            class="ms-2"
            style="height: 32px; max-width: 32px"
            text
            icon
            color="error"
            v-else
            @click="deleteCategory(index)"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
          <template v-slot:actions>
            <v-icon
              color="primary"
              @click="toggleExpansion(index)"
              class="ms-2"
            >
              mdi-chevron-down-circle-outline
            </v-icon>
          </template>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-form lazy-validation ref="categoryForm2" class="w-full">
            <v-card elevation="0" class="w-full">
              <div class="d-flex flex-column flex-md-row">
                <div class="w-full me-0 me-md-2">
                  <CustomInput
                    v-model.trim="$v.category.name.$model"
                    :rules="validate($v.category.name, $root, 'name')"
                    :label="$tr('label_required', $tr('name'))"
                    :placeholder="$tr('name')"
                    type="textfield"
                  />
                </div>
                <div class="w-full">
                  <p class="mb-1 custom-input-title">
                    {{ $tr("label_required", $tr("icon")) }}
                  </p>

                  <v-file-input
                    v-model.trim="$v.category.icon.$model"
                    :rules="validate($v.category.icon, $root, 'icon')"
                    placeholder="Icon"
                    dense
                    outlined
                    prepend-icon=""
                    accept="image/jpeg,image/jpg,image/png"
                    class="product-custom-file-input"
                    @change="validateCategoryIcon"
                  >
                    <template
                      slot="prepend-inner"
                      class="d-flex justify-start align-center"
                    >
                      <v-avatar color="primary" size="30" v-if="category.icon">
                        <img
                          :src="generateIconPath(category.icon)"
                          style="width: 100%; height: 100%"
                        />
                      </v-avatar>
                      <v-icon v-else>mdi-camera</v-icon>
                    </template>
                  </v-file-input>
                </div>
              </div>
              <div class="d-flex flex-column flex-md-row">
                <div class="w-full">
                  <CustomInput
                    v-model.trim="$v.category.description.$model"
                    :rules="
                      validate($v.category.description, $root, 'description')
                    "
                    :label="$tr('label_required', $tr('description'))"
                    :placeholder="$tr('description')"
                    type="textarea"
                  />
                </div>
              </div>
            </v-card>
          </v-form>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>

    <v-expansion-panels class="mb-1">
      <v-expansion-panel readonly ref="addCategoryPanelRefs" key="1">
        <v-expansion-panel-header
          hide-actions
          class="product-custom-expansion-panel-header"
        >
          <div
            class="d-flex align-center"
            @click="toggleAddCategoryExpansion()"
          >
            <v-btn icon>
              <v-icon color="primary" size="30"
                >mdi-plus-circle-outline</v-icon
              ></v-btn
            >
            <h4
              class="primary--text ms-1"
              v-html="
                category_type == 'sub_category'
                  ? $tr('add_subcategory')
                  : $tr('add_category')
              "
            ></h4>
          </div>
          <v-spacer />
        </v-expansion-panel-header>

        <v-expansion-panel-content>
          <v-form lazy-validation ref="categoryForm" class="w-full">
            <v-card elevation="0" class="w-full">
              <div class="d-flex flex-column flex-md-row">
                <div class="w-full me-0 me-md-2">
                  <CustomInput
                    v-model.trim="$v.category.name.$model"
                    :label="$tr('label_required', $tr('name'))"
                    :placeholder="$tr('name')"
                    type="textfield"
                    :rules="validate($v.category.name, $root, 'name')"
                  />
                </div>
                <div class="w-full">
                  <p class="mb-1 custom-input-title">
                    {{ $tr("label_required", $tr("icon")) }}
                  </p>
                  <v-file-input
                    ref="file2"
                    placeholder="Icon"
                    dense
                    outlined
                    prepend-icon=""
                    accept="image/jpeg,image/jpg,image/png"
                    class="product-custom-file-input"
                    :rules="validate($v.category.icon, $root, 'icon')"
                    @change="validateCategoryIcon"
                    :key="file_key"
                  >
                    <template
                      slot="prepend-inner"
                      class="d-flex justify-start align-center"
                    >
                      <v-avatar color="primary" size="30" v-if="category.icon">
                        <img
                          :src="generateIconPath($v.category.icon.$model)"
                          style="width: 100%; height: 100%"
                        />
                      </v-avatar>
                      <v-icon v-else>mdi-camera</v-icon>
                    </template>
                  </v-file-input>
                </div>
              </div>
              <div class="d-flex flex-column flex-md-row">
                <div class="w-full">
                  <CustomInput
                    :label="$tr('label_required', $tr('description'))"
                    v-model.trim="$v.category.description.$model"
                    :placeholder="$tr('description')"
                    :rules="
                      validate($v.category.description, $root, 'description')
                    "
                    type="textarea"
                  />
                </div>
              </div>
              <div class="d-flex flex-column flex-md-row justify-end">
                <div class="d-flex justify-end">
                  <TextButton
                    type="secondary"
                    :text="$tr('cancel')"
                    @click="cancelSaveCategory()"
                  />
                  <TextButton :text="$tr('save')" @click="saveCategory()" />
                </div>
              </div>
            </v-card>
          </v-form>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>
  </div>
</template>

<script>
import TextButton from "../../common/buttons/TextButton.vue";
import CustomInput from "../../design/components/CustomInput.vue";
import { minLength, required, requiredIf } from "vuelidate/lib/validators";
import GlobleRules from "~/helpers/vuelidate";
import { mapActions, mapGetters } from "vuex";

export default {
  components: { CustomInput, TextButton },
  props: {
    category_type: {
      type: String,
      required: true,
    },
    store_url: {
      type: String,
      required: true,
    },
    api_url: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      is_loading_api: false,
      validate: GlobleRules.validate,
      categoryErrorMessage: "",
      category_id: "",
      file_key: 0,
      category: {
        name: "",
        description: "",
        icon: null,
      },

      categoriesData: {
        category_id: "",
        category: [],
      },
      expansion_status: false,
      selected_index: null,
    };
  },
  validations: {
    category: {
      name: { required, minLength: minLength(2) },
      description: { required, minLength: minLength(5) },
      icon: { required },
    },

    categoriesData: {
      category_id: {
        required: requiredIf(function () {
          return this.category_type == "sub_category";
        }),
      },
    },
  },
  computed: {
    ...mapGetters({
      categories: "new_landing/allCategories",
    }),
  },
  watch: {
    category_type: function (value) {
      if (value == "sub_category") {
        this.getCategory();
      }
      if (!this.$refs.addCategoryPanelRefs.isActive) {
        this.$refs.addCategoryPanelRefs.toggle();
        this.$v.$reset();
        this.category.name = "";
        this.category.description = "";
        this.category.icon = null;
        this.file_key++;
      }
    },
  },
  methods: {
    ...mapActions({
      fetchAllCategories: "new_landing/fetchAllCategories",
    }),
    toggleExpansion(index) {
      this.$refs.categoryPanelRefs[index].toggle();
      this.selected_index = index;
      if (!this.expansion_status) {
        this.category = this.categoriesData.category[index];
      }
      this.expansion_status = !this.$refs.categoryPanelRefs[index].isActive;

      if (this.$refs.addCategoryPanelRefs.isActive) {
        this.$refs.addCategoryPanelRefs.toggle();
      }
    },

    deleteCategory(index) {
      this.categoriesData.category.splice(index, 1);
    },

    toggleAddCategoryExpansion() {
      this.$refs.addCategoryPanelRefs.toggle();
      if (!this.$refs.addCategoryPanelRefs.isActive) {
        this.$v.$reset();
        this.category.name = "";
        this.category.description = "";
        this.category.icon = null;
        this.file_key++;
      }
      if (this.expansion_status) {
        this.toggleExpansion(this.selected_index);
      }
    },

    // check & validate category icon
    validateCategoryIcon(file) {
      if (file) {
        const fileExtension = file.type;
        const allowedExtensions = ["image/jpeg", "image/jpg", "image/png"];
        if (!allowedExtensions.includes(fileExtension)) {
          // this.$toastr.w(this.$tr("inappropriate_uploaded_file"));
						this.$toasterNA("orange",this.$tr('inappropriate_uploaded_file'));

          return;
        }
        this.category.icon = file;
      } else {
        this.category.icon = null;
      }
    },

    saveCategory() {
      this.$refs.categoryForm.validate();
      this.$v.category.$touch();
      if (this.$v.category.$invalid) {
        // this.$toastr.e("inccorect data");
				this.$toasterNA("red", this.$tr('inccorect_data'));

      } else {
        this.categoriesData.category.unshift(this._.clone(this.category));
        this.toggleAddCategoryExpansion();
      }
    },

    cancelSaveCategory() {
      this.toggleAddCategoryExpansion();
    },

    generateIconPath(file) {
      if (file != null) {
        return URL.createObjectURL(file);
      } else {
        return "";
      }
    },

    saveChanges(index) {
      this.$v.category.$touch();
      if (this.$v.category.$invalid) {
        // this.$toastr.e("inccorect data");
				this.$toasterNA("red", this.$tr('inccorect_data'));

        return;
      }
      this.categoriesData.category[index].name = this.category.name;
      this.categoriesData.category[index].description =
        this.category.description;
      this.categoriesData.category[index].icon = this.category.icon;
      this.toggleExpansion(index);
    },

    async submit() {
      if (this.category_type == "sub_category") {
        this.$refs.category.validate();
        this.$v.categoriesData.$touch();
        if (this.$v.categoriesData.$invalid) {
          // this.$toastr.e("inccorect data");
				this.$toasterNA("red", this.$tr('inccorect_data'));

          return false;
        }
      }
      if (this.categoriesData.category.length < 1) {
        // this.$toastr.e("no record to save");
				this.$toasterNA("red", this.$tr('no_record_to_save'));

        return false;
      }
      try {
        // "landing/product-categories"
        const response = await this.$axios.post(
          this.store_url,
          this.categoriesData
        );
        if (response.status == 201) {
        }
        return true;
      } catch (error) {
        return false;
      }
    },

    startOver() {
      this.categoriesData.category = [];
      this.categoriesData.category_id = "";
      (this.category = {
        name: "",
        description: "",
        icon: null,
      }),
        this.file_key++;
      this.$refs.categoryForm.reset();
      if (this.category_type == "sub_category") {
        this.$refs.category.reset();
      }
    },

    async getCategory() {
      this.is_loading_api = true;
      await this.fetchAllCategories({
        url: this.api_url,
        data: { status: "active" },
      });
      this.is_loading_api = false;
    },
  },

  created() {
    if (this.category_type == "sub_category") {
      this.getCategory();
    }
  },
};
</script>

<style>
.product-custom-file-input .v-input__prepend-inner {
  margin-top: auto !important;
  margin-bottom: auto !important;
}

.product-custom-file-input .v-input__slot {
  padding-left: 8px !important;
}

.product-custom-expansion-panel-header
  .v-expansion-panel-header
  .v-expansion-panel-header--active {
  border-bottom: 1px solid red !important;
}
</style>