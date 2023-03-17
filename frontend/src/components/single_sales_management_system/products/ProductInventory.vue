<template>
  <div :key="i">
  <v-dialog v-model="InventoryModel" width="1300">
    <Dialog persistent max-width="1300" @closeDialog="closeInventory">
      <v-card>
        <v-form
          @submit.prevent="submit"
          v-model="valid"
          lazy-validation
          ref="form"
          :loading="loading"
          >
          <CustomStepper
            :headers="headers"
            :canNext="false"
            @validate="() => {}"
            :is-submitting="false"
            ref="addedInventory"
            @submit="submit"
            :isSubmitting="isSubmitting"
          >
            <template v-slot:step1>
              <v-card-text
                  style="max-height: 650px; overflow-y: scroll; margin-top: -90px"
                >
                <MultiInputItem
                    :items="form"
                    @addMore="addOneMore"
                    @removeItem="removeItem"
                    :title="'inventory'"
                >
                  <template v-slot:content="{ item,index }">
                      <div
                        class="h-full d-flex"
                        style="height: fit-content !important"
                      >
                        <div class="w-full">
                          <CTextField 
                            :title="$tr('label_required', $tr('sku'))"
                            :placeholder="$tr('label_required', $tr('sku'))"
                            type="textfield"
                            class="me-md-2"
                            rounded
                            v-model="item.sku"
                            :rules="skuRules"
                          />
                        </div>
                      </div>
                      <div
                        class="h-full d-flex"
                        style="height: fit-content !important"
                      >
                        <div class="w-full">
                          <CTextField 
                            :title="$tr('label_required', $tr('quantity'))"
                            placeholder="Supplier Trading Name"
                            type="number"
                            class="me-md-2"
                            rounded
                            v-model="item.quantity"
                            :rules="quantityRules"
                          />
                        </div>
                        <div class="w-full">
                          <CTextField 
                            :title="$tr('label_required', $tr('price_per_unit'))"
                            placeholder="Supplier Trading Name"
                            type="number"
                            class="me-md-2"
                            rounded
                            v-model="item.price_per_unit"
                            :rules="PriceRules"
                          />
                        </div>
                      </div>
                      <div class="d-flex">
                        <InputCard>
                          <div v-for="(attr, i) in item.attributes" :key="i">
                            <div class="d-flex flex-column flex-md-row">
                              <div style="width: 45%">
                                  <CSelect
                                    v-model="attr.attribute_id"
                                    :title="$tr('label_required', $tr('name'))"
                                    :placeholder="$tr('name')"
                                    prepend-inner-icon="mdi-chart-bar-stacked"
                                    :items="attrNames"
                                    :loading="attributeNameLoading"
                                    item-text="name"
                                    item-value="id"
                                    @change="selectData($event,index)"
                                    :rules="nameRules"

                                  />
                              </div>
                              <div style="width: 45%">
                                  <CSelectMulti
                                  title="Choose attributes"
                                  placeholder="Attribute"
                                  :items="attrValues[index]"
                                  v-model="attr.value"
                                  item-text="value"
                                  item-value="value"
                                  multi
                                  prepend-inner-icon="mdi-bookmark"
                                  :rules="attrRules"
                                    
                                  />
                              </div>
                              <div style="width: 10%">
                                <v-icon
                                  :class="`${
                                    i == item.attributes.length - 1
                                      ? 'primary'
                                      : 'red--text'
                                  } rounded-circle mt-4`"
                                  color="white"
                                  style="left: 30px !important;top: 30px !important;width: 37px;height: 40px;
                                  "
                                  @click="
                                    clicked(i, index, item.attributes.length, attr)
                                  "
                                  >{{
                                    i == item.attributes.length - 1
                                      ? "mdi-plus"
                                      : "mdi-close"
                                  }}
                                </v-icon>
                              </div>
                            </div>
                          </div>
                        </InputCard>
                      </div>
                  </template>
                </MultiInputItem>
              </v-card-text>
            </template>
            <template v-slot:step2>
                <DoneMessage
                  :title="$tr('item_all_set', $tr('inventory'))"
                  :subTitle="$tr('you_can_access_your_item', $tr('inventory'))"
                />
            </template>
          </CustomStepper>
        </v-form>
      </v-card>
    </Dialog>
  </v-dialog>
    
  </div>
</template>

<script>
import DoneMessage from '../../design/components/DoneMessage.vue';
import Dialog from '../../design/Dialog/Dialog.vue';
import CustomStepper from '../../design/FormStepper/CustomStepper.vue';
import MultiInputItem from '../../new_form_components/cat_product_selection/MultiInputItem.vue';
import InputCard from '../../new_form_components/components/InputCard.vue';
import CSelect from '../../new_form_components/Inputs/CSelect.vue';
import CSelectMulti from '../../new_form_components/Inputs/CSelectMulti.vue';
import CTextField from '../../new_form_components/Inputs/CTextField.vue';

export default {
    components: { CustomStepper, MultiInputItem, CTextField, InputCard, CSelect, CSelectMulti, DoneMessage, Dialog },
    props:{
      id:{
        // type:String,
        required:true
      },
      reset:Boolean,
    },
    data(){
      return{
        InventoryModel:false,
        valid: true,
        dialog: false,
        loading: false,

        addedInventory: "",
        inventoryData: [],
        isSubmitting:false,
        product_id: "",
        i:0,
        attrNames: [],
        attrValues: [],
        selectValueModel: [],
        selectNameModel: [],
        selectedItems: [],
        attributeNameLoading: false,
        form: [
          {
              sku: "",
              quantity: "",
              price_per_unit: "",
              attributes: [
                {
                  attribute_id: "",
                  value: [],
                },
              ],
          },
			  ],
        skuRules: [
				(v) => !!v || "sku is required",
				(v) => (v && v.length <= 30) || "sku must be less than 30 characters",
        ],
        quantityRules: [
          (v) => v.length > 0 || "Quantity  is required",
          (v) =>
            Number.isInteger(Number(v)) || "The value must be an integer number",
          (v) => v > 0 || "The value must be greater than zero",
        ],
        PriceRules: [
          (v) => v.length > 0 || "price per uint   is required",
          (v) =>
            Number.isInteger(Number(v)) || "The value must be an integer number",
          (v) => v > 0 || "The value must be greater than zero",
        ],
        attrRules:
        [
          (v) => !!v || "attribute  is required",
        ],
        nameRules: [
          (v) => !!v || " Name is required",
        ],
        
        headers: [
          {
            icon: "fa-globe",
            title: "inventory",
            slotName: "step1",
          },
          {
            icon: "fa-thumbs-up",
            title: "done",
            slotName: "step2",
          },
        ],

      }
    },
    watch:{
      reset: function(){
        this.resetForm();
      },
    },
    methods:{
      openModelInventory(){
        this.InventoryModel=true;
        this.fetchAttributes();
      },
       closeInventory() {
        this.InventoryModel = false;
        this.resetForm();
      },
      async addOneMore() {
        if (this.$refs.form.validate()) {
          this.form.push({
            sku: "",
            quantity: "",
            price_per_unit: "",
            attributes: [
              {
                attribute_id: "",
                value: [],
              },
            ],
            
          });
        }
      },
      async fetchAttributes() {
      this.selectNameModel = [];
      this.selectValueModel = [];
      this.attributeNameLoading = true;
      try {
        const { data } = await this.$axios.get(
          "single-sales/attribute-ssp?key=all"
        );
        console.log(data.data);
        this.attrNames = data.data;
      } catch (error) {}
      this.attributeNameLoading = false;
    },
      async submit() {
        console.log(this.form);
        try {
          this.isSubmitting = true;
          if (this.$refs.form.validate()) {
            let inventoryData = {
              inventory: this.form,
              product_id: this.id,
            };
            console.log("inventory",inventoryData);
            this.loading = true;
            const result = await this.$axios.post(
              "single-sales/product-inventory-ssp",
              inventoryData
            );
            if (result.status == 201) {
              this.$refs.addedInventory.forceNext();
              this.resetForm();
              // this.dialog = false;
              this.loading = false;
            }
          }
          this.isSubmitting = false;
        } catch (error) {
          console.log(error);
        }
      },
      resetForm() {
        this.form = [
          {
            sku: "",
            quantity: "",
            price_per_unit: "",
            attributes: [
              {
                attribute_id: "",
                value: [],
              },
            ],
          },
        ];
        this.$refs.form.resetValidation()
      },
      async removeItem(item) {
        if (this.form.length > 1) this.form.splice(item, 1);
      },
      selectData(id,index) {
        const val = this.attrNames.find((i)=>i.id == id);
        if (val) {
          const values = JSON.parse(val.values);
          this.attrValues[index] = values.map((i) => {
            return {
              value: i,
            };
          });
        }
      },
      clicked(i, index, length, item) {
        if (i == length - 1) {
          this.addOneMore1(index);
        } else {
          this.removeItem1(item, index);
        }
      },
      addOneMore1(index) {
      this.form[index].attributes.push({
        attribute_id: "",
        value: [],
      });
    },
      removeItem1(item, index) {
      this.form[index].attributes.splice(item, 1);
    },
    }
}
</script>

<style>

</style>