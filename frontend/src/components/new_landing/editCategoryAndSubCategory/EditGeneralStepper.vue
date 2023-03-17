<template>
  <div>
    <v-form lazy-validation ref="categoryForm2" class="w-full">
      <v-card class="w-full">
        <div class="w-full">
          <CustomInput
            :items="categories"
            v-model="$v.category_id.$model"
            :rules="validate($v.category_id, $root, 'category')"
            item-value="id"
            item-text="name"
            :label="$tr('label_required', $tr('category'))"
            :placeholder="$tr('choose_item', $tr('category'))"
            type="autocomplete"
            class="mb-0"
            :loading="is_loading_api"
          />
        </div>
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
              :rules="validate($v.category.description, $root, 'description')"
              :label="$tr('label_required', $tr('description'))"
              :placeholder="$tr('description')"
              type="textarea"
            />
          </div>
        </div>
      </v-card>
    </v-form>
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
      file_key: 0,
      category: {
        category_id: "",
        name: "",
        description: "",
        icon: null,
      },
      expansion_status: false,
      selected_index: null,
    };
  },
  validations: {
    category: {
      name: { required, minLength: minLength(2) },
      description: { required, minLength: minLength(3) },
      icon: { required },
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
  watch: {},
  methods: {
    ...mapActions({
      fetchAllCategories: "new_landing/fetchAllCategories",
    }),

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

    saveCategory() {},

    generateIconPath(file) {
      if (file != null) {
        return URL.createObjectURL(file);
      } else {
        return "";
      }
    },

    async submit() {
      try {
        const response = await this.$axios.post(this.store_url, this.category);
        if (response.status == 201) {
        }
        return true;
      } catch (error) {
        return false;
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