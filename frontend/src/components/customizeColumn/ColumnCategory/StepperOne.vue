<template>
  <div class="mt-5">
    <div>
      <CTitle :text="`select_categories`" />
      <div class="custom-card white w-full position-relative">
        <div class="">
          <p class="pt-2 ps-3 input-title mb-0">
            {{ $tr("select_categories") }}
          </p>
          <p
            class="ps-3 mb-0 input-title-small"
            v-html="$tr('you_can_select_multiple_categories_or_add_new_one')"
          ></p>
        </div>
        <div class="search search-top-30">
          <AddNewCategory
            @submit="addNewCategory($event)"
            ref="NewCategoryRefs"
          />
        </div>

        <!-- categories -->
        <div class="ps-3 mt-5" style="height: 500px; overflow-y: auto">
          <div class="d-flex flex-wrap">
            <div
              class="
                category-card
                d-flex
                align-center
                justify-space-between
                me-1
                mb-1
              "
              :class="checkSelection(category.id) ? 'selected_category' : ''"
              @click="selectCategory(category.id)"
              v-for="(category, index) in categories"
              :key="index"
            >
              <div class="d-flex">
                <svg
                  class="mx-1"
                  xmlns="http://www.w3.org/2000/svg"
                  width="26.532"
                  height="27.483"
                  viewBox="0 0 26.532 27.483"
                >
                  <g
                    id="category-icon"
                    transform="translate(-4882.234 5772.245)"
                  >
                    <path
                      id="Path_151"
                      data-name="Path 151"
                      d="M6.084,12.633H2.074A1.931,1.931,0,0,1,.009,10.486Q0,6.8.009,3.114A13.135,13.135,0,0,1,.048,1.68,1.823,1.823,0,0,1,1.826.013Q6.1-.011,10.37.012A1.9,1.9,0,0,1,12.2,2.043c.007,2.383,0,4.766,0,7.149,0,.479.006.957,0,1.436a1.916,1.916,0,0,1-1.955,2q-2.082,0-4.164,0"
                      transform="translate(4882.229 -5772.246)"
                      fill="#2e7be6"
                    />
                    <path
                      id="Path_152"
                      data-name="Path 152"
                      d="M44.344,6.32q0-2.09,0-4.181A1.917,1.917,0,0,1,46.393.016h8.081a1.91,1.91,0,0,1,2.046,2.1q0,4.228,0,8.457a1.9,1.9,0,0,1-2,2.074H46.344a1.9,1.9,0,0,1-2-2.046c-.006-1.425,0-2.851,0-4.276"
                      transform="translate(4852.243 -5772.255)"
                      fill="#2e7be6"
                    />
                    <path
                      id="Path_153"
                      data-name="Path 153"
                      d="M6.127,44.38c1.357,0,2.714,0,4.071,0a1.911,1.911,0,0,1,2,2.08q0,4.228,0,8.457a1.909,1.909,0,0,1-2,2.077q-4.087,0-8.173,0A1.907,1.907,0,0,1,.006,54.929q-.012-4.244,0-8.489a1.905,1.905,0,0,1,2.02-2.06q2.051,0,4.1,0"
                      transform="translate(4882.234 -5801.761)"
                      fill="#2e7be6"
                    />
                    <path
                      id="Path_154"
                      data-name="Path 154"
                      d="M50.442,44.38c1.367,0,2.735,0,4.1,0a1.887,1.887,0,0,1,1.974,2.034q.007,4.276,0,8.552a1.882,1.882,0,0,1-1.95,2.028q-4.132.009-8.265,0a1.879,1.879,0,0,1-1.957-2.019q-.01-4.292,0-8.584a1.877,1.877,0,0,1,1.964-2.01c1.377,0,2.755,0,4.133,0"
                      transform="translate(4852.245 -5801.76)"
                      fill="#2e7be6"
                    />
                  </g>
                </svg>

                <p
                  :class="checkSelection(category.id) ? 'primary--text' : ''"
                  class="mb-0 input-title-small"
                  v-html="category.category_name"
                  style="
                    text-overflow: ellipsis;
                    width: 200px;
                    white-space: nowrap;
                    overflow: hidden;
                  "
                ></p>
              </div>
              <v-icon
                class="me-1"
                color="primary"
                v-if="checkSelection(category.id)"
                >mdi-checkbox-marked-circle</v-icon
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import CTooltip from "../../new_form_components/components/CTooltip.vue";
import AddNewCategory from "./AddNewCategory.vue";
import GlobalRules from "~/helpers/vuelidate";

export default {
  components: { InputCard, CTitle, CTooltip, AddNewCategory },
  props: {
    form: Object,
  },
  data: () => ({
    validateRule: GlobalRules.validate,

    categories: [],
    category_ids: [],
  }),
  methods: {
    async loaded() {
      await this.fetchCategories();
    },
    async validate() {
      return !this.form.category_ids.$invalid;
    },
    async fetchCategories() {
      try {
        const response = await this.$axios.get("all-column-category");

        this.categories = response?.data;
      } catch (e) {
      }
    },
    checkSelection(id) {
      let data_set = new Set(this.form.$model.category_ids);
      return data_set.has(id);
    },
    selectCategory(id) {
      let data_set = new Set(this.form.$model.category_ids);
      if (data_set.has(id)) data_set.delete(id);
      else data_set.add(id);
      this.form.$model.category_ids = Array.from(data_set);
    },
    async addNewCategory(data) {
      try {
        const response = await this.$axios.post("add-column-category", {
          category_name: data,
        });

        if (response.status == 200) {
          // this.$toastr.s(this.$tr("category_added"));
				this.$toasterNA("green", this.$tr('category_added'));


          this.categories.unshift(response.data);
          this.$refs.NewCategoryRefs.closeExpand();
        } else {
          this.$refs.NewCategoryRefs.closeExpand(false);
        }
      } catch (e) {
      }
    },
  },
};
</script>

<style scoped>
.custom-card {
  border-radius: 16px;
  height: 650px;
  overflow-y: auto;
}
.input-title {
  font-size: 18px;
  font-weight: 500;
  color: #777;
}
.input-title-small {
  font-weight: 500;
  font-size: 16px;
  color: #777;
}
</style>
<style>
.custom-active-class {
  border: 1px solid var(--v-primary-base);
  border-radius: 10px;
}

.my-custom-list .v-list-group__header.v-list-item.v-list-item--link:hover {
  background-color: #e3f2fd !important;
  border-radius: 10px;
}
.my-custom-list .v-list-group__header.v-list-item.v-list-item--link {
  height: 30px;
}

.my-custom-list .v-list-item--link:before {
  background-color: unset !important;
}
.custom-child-list {
  cursor: pointer !important;
  height: 30px;
  /* margin-top: 2px; */
}
.custom-child-list:hover {
  background: #e3f2fd !important;
  border-radius: 10px !important;
}
.my-custom-list .v-list-group__header__append-icon .v-icon {
  font-size: 1.5rem !important;
}
.search {
  position: absolute;
  top: 18px;
  right: 32px;
  z-index: 2;
}
.search-top-30 {
  top: 30px;
}
.v-application--is-rtl .search {
  right: unset;
  left: 32px;
}
.v-application--is-rtl .all-switch {
  right: unset;
  left: 72px;
}
.category-card {
  border: 0.2px solid #d8d5d5;
  border-radius: 10px;
  height: 60px;
  width: 250px;
  padding: 4px;
  cursor: pointer;
}
.category-card:hover {
  border-color: var(--v-primary-base);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
}
.selected_category {
  border: 1px solid var(--v-primary-base);
  box-shadow: 0 0 10px #2e7be633;
}
</style>