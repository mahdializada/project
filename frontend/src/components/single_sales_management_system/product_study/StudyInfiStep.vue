<template>
  <div>
    <div class="h-full d-flex align-center-justify-center">
      <div class="w-full">
        <InputCard
          :title="'Languages'"
          :hasSearch="true"
          :hasPagination="true"
          :page="history[searchActive ? 'search' : 'current'].page"
          :pages="history[searchActive ? 'search' : 'current'].pages"
          @search="search"
          @disableSearch="disableSearch"
          @updatePage="
            (newPage) => {
              history[searchActive ? 'search' : 'current'].page = newPage;
            }
          "
        >
          <ItemsContainer
            v-model="form.study_language_id.$model"
            :items="
              history[searchActive ? 'search' : 'current'].items.slice(
                history[searchActive ? 'search' : 'current'].page * 14 - 14,
                history[searchActive ? 'search' : 'current'].page * 14
              )
            "
            :loading="loading"
            :no_data="
              history[searchActive ? 'search' : 'current'].no_data
                ? true
                : false
            "
            :height="'260px'"
          ></ItemsContainer>
        </InputCard>
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextField
          v-model="form.name.$model"
          :min="0"
          title="Product Study Name"
          placeholder="Name"
          prepend-inner-icon="mdi-card-text-outline"
          :rules="
            validateRule(
              form.name, // validate objec
              $root, // context
              $tr('name') // lable for feedback
            )
          "
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextArea
          v-model.trim="form.study_reason.$model"
          :title="$tr('label_required', $tr('study_reasons'))"
          :rules="
            validateRule(
              form.study_reason, // validate objec
              $root, // context
              $tr('study_reason') // lable for feedback
            )
          "
          :placeholder="$tr('study_reason')"
          :invalid="form.study_reason.$invalid"
          prepend-inner-icon="mdi-text"
        />
      </div>
    </div>
  </div>
</template>

<script>
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import GlobalRules from "~/helpers/vuelidate";
import CTextArea from "../../new_form_components/Inputs/CTextArea.vue";
import CTextField from '../../new_form_components/Inputs/CTextField.vue';
export default {
  props: {
    form: Object,
  },
  components: { InputCard, ItemsContainer, CTextArea, CTextField },
  data() {
    return {
      searchActive: false,
      loading: false,
      validateRule: GlobalRules.validate,
      history: {
        current: {
          items: [],
          pages: 0,
          page: 1,
          parent: null,
        },
        search: {
          items: [],
          pages: 0,
          page: 1,
          parent: null,
        },
      },
    };
  },
  methods: {
    async loaded() {
      this.fetchLanguages();
    },
    async validate() {
      this.form.name.$touch();
      this.form.study_language_id.$touch();
      this.form.study_reason.$touch();
      return (
        !this.form.name.$invalid &&
        !this.form.study_language_id.$invalid &&
        !this.form.study_reason.$invalid
      );
    },
    disableSearch() {
      this.searchActive = false;
    },
    async fetchLanguages() {
      this.loading = true;
      try {
        let res = await this.$axios.get("languages", {
          params: {
            itemsPerPage: -1,
            withCountries: true,
          },
        });
        let data = [];
        for (let i = 0; i < res?.data?.data?.length; i++) {
          const el = res?.data?.data[i];
          data.push({
            id: el.country_language_id,
            name: el.language_native,
            english_name: el.language_name,
            country_name: el.country_name,
            iso2: el.iso2.toLowerCase(),
            type: "language",
          });
        }
        let obj = {};
        if (data?.length > 0) {
          obj = {
            items: data.sort(this.sortByName),
            parent: null,
            pages: Math.ceil(data.length / 21),
            page: 1,
          };
        } else {
          obj = {
            no_data: true,
            items: [],
            parent: null,
            pages: 0,
            page: 1,
          };
        }
        this.history.current = obj;
        this.history.parent = obj;
      } catch (error) {}

      this.loading = false;
    },
    async search(search) {
      this.loading = true;
      let data = [];
      if (search) {
        this.searchActive = true;
        for (let i = 0; i < this?.history?.current?.items?.length; i++) {
          const el = this?.history?.current?.items[i];
          if (el) {
            if (
              el.name.toLowerCase().indexOf(search.toLowerCase()) > -1 ||
              el.english_name.toLowerCase().indexOf(search.toLowerCase()) >
                -1 ||
              el.country_name.toLowerCase().indexOf(search.toLowerCase()) > -1
            ) {
              data.push(el);
            }
          }
        }
        var obj = {};
        if (data.length > 0) {
          obj = {
            items: data,
            parent: null,
            pages: Math.ceil(data.length / 21),
            page: 1,
          };
        } else {
          obj = {
            no_data: true,
            items: [],
            parent: null,
            pages: 0,
            page: 1,
          };
        }
      }
      this.history.search = obj;
      this.loading = false;
    },
    sortByName(itemA, itemB) {
      const nameA = itemA.name.toLowerCase();
      const nameB = itemB.name.toLowerCase();
      return nameA.localeCompare(nameB);
    },
  },
};
</script>

<style></style>
