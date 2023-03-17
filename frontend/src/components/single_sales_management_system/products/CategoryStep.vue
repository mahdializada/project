<template>
  <div class="w-full h-full d-flex align-center justify-center">
    <div class="w-full">
      
      <CTitle :text="`choose_a_category`" />
      <div class="mb-3">
        <InputCard
          :title="$tr('choose_a_category')"
          :hasSearch="true"
          :hasPagination="true"
          :showBack="path.length > 1"
          :page="history[searchActive ? 'search' : 'current'].page"
          :pages="history[searchActive ? 'search' : 'current'].pages"
          :path="path"
          @back="back"
          @search="search"
          @disableSearch="disableSearch"
          @updatePage="
            (newpage) => {
              history[searchActive ? 'search' : 'current'].page = newpage;
            }
          "
          @changeTo="changeTo"
          :rules="
            validateRule(
              form.category_id, // validate objec
              $root, // context
              $tr('name') // lable for feedback
            )
          "
        >
          <ItemsContainer
            v-model="form.category_id.$model"
            :items="
              history[searchActive ? 'search' : 'current'].items.slice(
                history[searchActive ? 'search' : 'current'].page * 21 - 21,
                history[searchActive ? 'search' : 'current'].page * 21
              )
            "
            :loading="loading"
            @category_clicked="category_clicked"
            @category_doubleClicked="category_doubleClicked"
            :no_data="
              history[searchActive ? 'search' : 'current'].no_data
                ? true
                : false
            "
            :customIcon="'category'"
          ></ItemsContainer>
        </InputCard>
      </div>
      
      
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CSelect from "../../new_form_components/Inputs/CSelect.vue";
import CSelectMulti from "../../new_form_components/Inputs/CSelectMulti.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      // product selection
      selectedItems: null,
      errorMessage: "",
      attributeLoading: false,
      attributeNameLoading: false,
      attrNames: [],
      attrValues: [],
      selectValueModel: [],
      selectNameModel: [],
      selectModel: [],
      selectedItems: [],
      attributeLoading: false,
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
      path: ["categories"], //stack
      loading: false,
      searchActive: false,
      clicks: 0,
      timer: null,
    };
  },
  methods: {
  //  async selectAttribute(item){
  //     console.log(item);
     
  //     this.selectNameModel = [];
  //     this.selectValueModel = [];
  //     this.attributeNameLoading = true;
  //     try {
  //       const url = "product-management/pr-attributes?fields=id,name&key=all";
  //       const { data } = await this.$axios.get(url ,{
  //       params: {
  //         item
  //       }});

  //       // const { data } = await this.$axios.get(
  //       //   "single-sales/attribute-ssp?key=all"
  //       // );
  //       console.log(data);
  //       this.attrNames = data;
  //     } catch (error) {}
  //     this.attributeNameLoading = false;
  //   },
  //   clicked(i, length) {
  //     if (i == length - 1) {
  //       this.addOneMore();
  //     } else {
  //       this.removeItem1();
  //     }
  //   },
  //   addOneMore() {
  //     this.form.$model.attributes.push({
  //       attribute_id: "",
  //       value: [],
  //     });
  //   },
  //   removeItem1() {
  //     this.form.$model.attributes.splice(1);
  //   },
    // selectData(id) {
    //   const val = this.attrNames.find((i)=>i.id == id);
    //   if (val) {
    //     const values = JSON.parse(val.values);
    //     this.attrValues = values.map((i) => {
    //       return {
    //         value: i,
    //       };
    //     });
    //   }
    // },
    async loaded() {
      this.searchActive=false;
      this.fetchRootItems();
    },
    async validate() {
      console.log(this.form);
      // this.form.attributes.$touch();

      return !this.form.category_id.$invalid   ;
    },
    // addItems() {
    //   for (let i = 0; i < this.selectModel.length; i++) {
    //     const el = this.selectModel[i];
    //     console.log(this.form.attribute.$model);
    //     let index = this.form.attribute.$model.findIndex(
    //       (item) => item.id == el.id
    //     );
    //     if (index == -1) {
    //       this.form.attribute.$model.push(el);
    //     }
    //   }
    //   this.selectModel = [];
    // },
    // removeItem(item) {
    //   this.form.attribute.$model = this.form.attribute.$model.filter(
    //     (el) => el.id != item.id
    //   );
    // },
    // product selection
    async category_clicked(item) {
      this.clicks++;
      console.log("fjsldfjlds");
      if (this.clicks === 1) {
        this.timer = setTimeout(() => {
          if (this.form.category_id.$model == item.id) {
            this.form.category_id.$model = "";
            console.log(this.form.category_id.$model);

          } else {
            this.form.category_id.$model = item.id;
            console.log(this.form.category_id.$model);
          }
          this.clicks = 0;
        }, 100);
       
      } else {
        clearTimeout(this.timer);
        this.clicks = 0;
      }
    },
    async category_doubleClicked(item) {
      await this.fetchChildItems(item);
      if (this.path[this.path.length - 1].id !== item.id) {
        this.path.push(item);
      }
    },
    async fetchRootItems() {
      this.loading = true;
      try {
        let res = await this.$axios.get("single-sales/categories-ssp", {
          params: {
            fetch_for_form: true,
          },
        });
        console.log("result:",res);
        let obj = {};
        if (res.data.length > 0) {
          obj = {
            items: res.data.sort(this.sortByName),
            parent: null,
            pages: Math.ceil(res.data.length / 21),
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
    async fetchChildItems(category) {
      this.loading = true;
      if (this.history[category.id]) {
        this.history.current = this.history[category.id];
        console.log("category is ssjl");
      } else {
        let res1 = await this.$axios.get("/product-management/pr-categories", {
          params: {
            fetch_for_form: true,
            category_id: category.id,
          },
        });
        let data = res1.data;
        var obj = {};
        if (data.length > 0) {
          obj = {
            items: data.sort(this.sortByName),
            parent: category,
            pages: Math.ceil(data.length / 21),
            page: 1,
          };
        } else {
          obj = {
            no_data: true,
            items: [],
            parent: category,
            pages: 0,
            page: 1,
          };
        }
        this.history[category.id] = obj;
        this.history.current = obj;
      }
      this.loading = false;
    },
    back() {
      this.loading = true;
      if (this.path.length > 1) {
        this.path.pop();
        if (this.path.length == 1) {
          if (this.history.parent) {
            this.history.current = this.history.parent;
          } else {
            this.fetchRootItems();
          }
        } else {
          if (this.history[this.path[this.path.length - 1].id]) {
            this.history.current =
              this.history[this.path[this.path.length - 1].id];
          } else {
            this.fetchChildItems(this.path[this.path.length - 1].id);
          }
        }
      }
      this.loading = false;
    },
    changeTo(item) {
      if (item == "parent") {
        this.path = this.path.slice(0, 1);
        if (this.history.parent) {
          this.history.current = this.history.parent;
        } else {
          this.fetchRootItems();
        }
      } else {
        let index = this.path.findIndex((item2) => item2?.id == item.id);
        this.path = this.path.slice(0, index + 1);
        if (this.history[this.path[this.path.length - 1].id]) {
          this.history.current =
            this.history[this.path[this.path.length - 1].id];
        } else {
          this.fetchChildItems(this.path[this.path.length - 1].id);
        }
      }
    },
    async search(search) {
      this.loading = true;
      var data = [];
      var obj = {
        no_data: true,
        items: [],
        parent: null,
        pages: 0,
        page: 1,
      };
      try {
        if (search !== "") {
          this.searchActive = true;
          let res = await this.$axios.get("/product-management/pr-categories", {
            params: {
              search_for_from: true,
              only_category: true,
              search,
              category_id: this.history.current.parent
                ? this.history.current.parent.id
                : null,
            },
          });
          data = res.data.categories;
        } else {
          if (this.history.current.parent) {
            this.fetchChildItems(this.history.current.parent);
          } else {
            this.fetchRootItems();
          }
        }
        if (data.length > 0) {
          obj = {
            items: data.sort(this.sortByName),
            parent: null,
            pages: Math.ceil(data.length / 21),
            page: 1,
          };
        }
        this.history.search = obj;
      } catch (err) {}
      this.loading = false;
    },
    sortByName(itemA, itemB) {
      const nameA = itemA.name.toLowerCase();
      const nameB = itemB.name.toLowerCase();
      return nameA.localeCompare(nameB);
    },
    disableSearch() {
      this.searchActive = false;
    },
  },
  components: {
    ItemsContainer,
    InputCard,
    CTitle,
    CSelectMulti,
    CSelect
},
};
</script>
