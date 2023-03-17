<template>
  <v-dialog v-model="showDialog" width="1300" persistent>
    <v-card class="pb-3" elevation="1" width="100%">
      <v-card-title class="px-2 pt-2 pb-0">
        <span class="dialog-title ps-1"> {{ $tr("assign_item",$tr('supplier')) }}</span>
        <v-spacer />
        <svg
          @click="toggleDialog()"
          width="40px"
          height="40px"
          viewBox="0 0 700 560"
          fill="currentColor"
          style="transform: scaleY(-1); cursor: pointer"
        >
          <g>
            <path
              d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
            />
            <path
              d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
            />
          </g>
        </svg>
      </v-card-title>
      <v-container fluid class="pt-0 pb-0">
        <v-row class="px-2">
          <!-- for Category -->
          <v-col cols="12" md="5" class="px-1">
            <v-card
              elevation="5"
              height="450px"
              class="py-1 ps-3 position-relative"
            >
              <p class="mb-0 custom-card-title-1 pt-1">
                {{ $tr("selected_item",$tr('product')) }}
              </p>

              <div class="search" :class="$vuetify.rtl ? 'rtl' : ''">
                <SearchBtn
                  @searchHeader="searchProduct"
                  :placeholder="'search'"
                />
              </div>

              <div class="me-1 mt-2 list-card" id="custom-scroll">
                <v-list-item
                  dense
                  class="me-2 selected_product_list px-2 align-center"
                  color="primary"
                  v-for="(product, index) in products"
                  :key="index"
                >
                  <div class="d-flex align-center">
                    <v-list-item-icon>
                      <v-icon
                        v-text="'mdi-eye'"
                        color="#777"
                        class="me-1"
                      ></v-icon>
                    </v-list-item-icon>
                  </div>

                  <v-list-item-content>
                    <v-list-item-title
                      class="custom-card-title-3"
                      style="line-height: 25px; z-index: 33333"
                      v-html="product.product_name"
                    />
                  </v-list-item-content>

                  <v-list-item-action>
                    <div class="d-flex align-center">
                      <v-icon
                        class="ms-1 icon-color"
                        @click="remove(product.id)"
                        >mdi-close-circle-outline</v-icon
                      >
                    </div>
                  </v-list-item-action>
                </v-list-item>
              </div>
            </v-card>
          </v-col>
          <v-col cols="12" md="7" class="px-1">
            <v-card
              elevation="5"
              height="450px"
              class="py-1 px-3 position-relative"
            >
              <p class="mb-0 custom-card-title-1 pt-1">
                {{ $tr("choose_item",$tr('supplier')) }}
              </p>

              <product-auto-complete
                class="product-autocomplete me-5"
                :class="$vuetify.rtl ? 'rtl' : ''"
                :itemText="'name'"
              />

              <div class="search" :class="$vuetify.rtl ? 'rtl' : ''">
                <SearchBtn
                  @searchHeader="searchSupplier"
                  :placeholder="'search'"
                />
              </div>

              <NoCheckboxItemContainer
                item_text="supplier_name"
                v-model="selected_supplier"
                :items="suppliers"
                :loading="supplier_loading"
                :no_data="suppliers.length < 0 && !supplier_loading"
                height="360px"
                :multi="true"
                supplierIcon="supplier"
                @input="openPriceDialog"
                :isSupplier="true"
              >
              </NoCheckboxItemContainer>
            </v-card>
          </v-col>
        </v-row>
        <div class="d-flex">
          <v-spacer></v-spacer>
          <v-btn
            class="me-1 custom-btn"
            color="primary"
            @click="toggleDialog"
            outlined
          >
            {{ $tr("cancel") }}
          </v-btn>
          <v-btn
            class="primary custom-btn"
            :loading="loading"
            style=""
            @click="saveChanges()"
          >
            {{ $tr("save") }}
          </v-btn>
        </div>
      </v-container>
    </v-card>
   <SingleInputDialog ref="openDialogRef" :type="'number'" @close="dialog = false" @addItem="addItem"/>
  </v-dialog>
</template>
<script>
import SearchBtn from "../../customizeColumn/SearchBtn.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import ProductAutoComplete from "./ProductAutoComplete.vue";
import SingleInputDialog from "../../design/Dialog/SingleInputDialog.vue";

export default {
  props: {
    selectedItems: Array,
  },
  data() {
    return {
      showDialog: false,
      suppliers: [],
      supplier_list: [],
      products: [],
      supplier_loading: false,
      loading: false,
      selected_supplier:[],
      cost: "",
      dialog: false,
      item:'',
    };
  },
  methods: {
     openPriceDialog() {
      try {
        this.$root.$on("msItem", (data) => {
          this.item = data;
          if(this.selected_supplier?.find((item)=>item == data.id)){
            this.$refs?.openDialogRef?.openModel(data?.cost);
          }
          // else{
          //   this.suppliers = this.suppliers.map((i) =>{
          //     if(i.id == this.item.id)
          //       i.cost = '';
          //     return i;
          //   })
          // }
      })
    } catch (error) {
        this.$toasterNA("orange", this.$tr("someting_went_wrong"));
      }
    },
    addItem(item){
      this.suppliers = this.suppliers.map((i) =>{
        if(i.id == this.item.id)
          i.cost = item;
        return i;
      })
    },
    async saveChanges() {
      try {
        console.log(this.selected_supplier);
      if (this.selected_supplier == 0) {
        this.$toasterNA("red", this.$tr("please_select_at_least_one_item",this.$tr('supplier')));
        return;
      }
      console.log(this.products);
      if (this.products.length == 0) {
        this.$toasterNA("red", this.$tr("please_select_at_least_one_item",this.$tr('product')));
        return;
      }
        let flag = false;
        const data = {
          product_ids: this.products.map((i) => {
            return i.id;
          }),
          suppliers:this.suppliers.filter((i)=>{
              if(this.selected_supplier.includes(i.id)){
                if(!i.cost)
                  flag = true;
                return i;
              }
            })
          };
          if(flag){
            this.$toasterNA("red", this.$tr("item_is_required",this.$tr('product_cost')));
          return;
        }
        this.loading = true;
        const result = await this.$axios.post("change_product_supplier", data);
        if (result.status == 200) {
          this.showDialog = !this.showDialog;
          this.$emit("fetchData");
          this.selected_supplier = "";
          this.$toasterNA("green", this.$tr("supplier_successfully_added_to_selected_product"));
        }
        this.loading = false;
      } catch (error) {
        this.$toasterNA("orange", this.$tr("something_went_wrong"));
        this.loading = false;
      }
    },
    remove(id) {
      this.products = this.products.filter((item) => item.id != id);
    },
    searchProduct(e) {
      this.products = this.selectedItems.filter((item) =>
        item.product_name.toLowerCase().includes(e)
      );
    },
    searchSupplier(e) {
      this.suppliers = this.supplier_list.filter((item) =>
        item.supplier_name.toLowerCase().includes(e)
      );
    },
    toggleDialog() {
      this.selected_supplier = [];
      if (!this.showDialog) {
        this.fetchSuppliers();
        this.products = this.selectedItems;
        this.selected_supplier = this.selectedItems[0].suppliers.map((item)=>{
          return item.id;
        })
      }
      this.showDialog = !this.showDialog;
    },
    async fetchSuppliers() {
      this.supplier_loading = true;
      try {
        const supplier = await this.$axios.get("supplier");
        this.supplier_list = supplier.data;
        for (let i = 0; i < this.products.length; i++) {
          let sup = this.products[i].suppliers;
          for (let j = 0; j < sup.length; j++) {
            this.supplier_list = this.supplier_list.map((item)=>{
              if(item.id == sup[j].pivot.supplier_id)
                item.cost = sup[j].pivot.product_cost;
              return item;
            })
          }
        }
        this.suppliers = this.supplier_list;
      } catch (error) {
        console.log("error", error);
      }
      this.supplier_loading = false;
    },
    selectAll() {},
  },
  components: {
    SearchBtn,
    ProductAutoComplete,
    NoCheckboxItemContainer,
    SingleInputDialog
},
};
</script>

<style scoped>
.dialog-title {
  font-size: 20px;
  font-weight: 600;
  color: #777;
}

.custom-card-title-1 {
  font-size: 17px;
  font-weight: 500;
  color: #777;
}

.custom-card-title-2 {
  font-size: 16px;
  font-weight: 400;
  color: #777;
}

.custom-card-title-3 {
  font-size: 15px;
  font-weight: 400;
  color: #777;
}
.search {
  position: absolute;
  top: 10px;
  right: 15px;
  z-index: 2;
}
.product-autocomplete {
  position: absolute;
  top: 10px;
  right: 20px;
  z-index: 1;
}

.search.rtl {
  right: unset !important;
  left: 15px;
}

.selected_product_list {
  border: 1px solid #d8d5d5;
  color: #777;
  border-radius: 5px;
  margin-bottom: 8px;
  cursor: pointer;
}

.selected_product_list.selected {
  border: 1px solid var(--v-primary-base);
  color: var(--v-primary-base);
  box-shadow: 0 0 10px #2e7be633;
}

.selected_product_list.selected * {
  color: var(--v-primary-base);
}

.selected_product_list:hover {
  color: var(--v-primary-base);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
}

.selected_product_list::before {
  background: unset !important;
}
.select-all-btn {
  display: inline-block;
  text-decoration: underline;
  cursor: pointer;
  color: var(--v-primary-base);
  position: absolute;
  top: 17px;
  right: 30px;
  z-index: 1;
  font-weight: 400;
}
.custom-column-list2 .custom-card-title-3:hover {
  color: var(--v-primary-base) !important;
}

.select-all-btn.rtl {
  right: unset !important;
  left: 30px !important;
}
.list-card {
  height: 380px;
  overflow-y: auto;
}
</style>

<style>
#custom-scroll::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}

#custom-scroll::-webkit-scrollbar {
  width: 10px;
  background-color: var(--v-surface-base);
}

#custom-scroll::-webkit-scrollbar:hover {
  cursor: alias !important;
}

#custom-scroll::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

#custom-scroll::-webkit-scrollbar-thumb {
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-background-darken2);
}
</style>
