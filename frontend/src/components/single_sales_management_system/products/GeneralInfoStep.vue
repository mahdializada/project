<template>
  <div :key="i" >
      <CTitle :text="`general_info_caps`" />
      <MultiInputItem
        :items="form.products.$each.$iter"
        @addMore="addOneMore"
        @removeItem="removeItem"
        :title="'product'"
      >
        <template v-slot:content="{ item }">
          
          <div class=" d-flex flex-column flex-lg-row">
            <div class="w-full me-lg-1 mb-lg-0">
              <CTextField
                @blur="item.arabic_name.$touch()"
                v-model="item.arabic_name.$model"
                :rules="validateRule(item.arabic_name, $root, $tr('arabic_name'))"
                :title="$tr('label_required', $tr('arabic_name'))"
                :placeholder="$tr('arabic_name')"
                prepend-inner-icon="mdi-archive"
              />
             
            </div>
            <div class="w-full ms-lg-1">
              <CTextField
                @blur="item.name.$touch()"
                v-model="item.name.$model"
                :rules="validateRule(item.name, $root, $tr('english_name'))"
                :title="$tr('label_required', $tr('english_name'))"
                :placeholder="$tr('english_name')"
                prepend-inner-icon="mdi-archive"
              />
            </div>
          </div>
          <div class="d-flex flex-column flex-md-row position-relative">
              <CTextField
                @blur="item.pcode.$touch()"
                v-model="item.pcode.$model"
                :disabled="!item.isDefaultProductCode.$model"
                :rules="validateRule(item.pcode, $root, $tr('pcode'))"
                :title="$tr('label_required', $tr('pcode'))"
                :placeholder="$tr('choose_item', $tr('pcode'))"
                prepend-inner-icon="mdi-pound"
                :loading="isFetchingProductCode"
               
              />
              <v-checkbox
                v-model="item.isDefaultProductCode.$model"
                inset
                class="position-absolute"
                style="margin-left:140px; margin-top: 20px"
                :label="`Custom Product Code`"
              ></v-checkbox>
              <CTextField
                 
                @blur="item.parent_sku.$touch()"
                v-model="item.parent_sku.$model"
                :disabled="!item.isDefaultSKU.$model"
                :rules="validateRule(item.parent_sku, $root, $tr('parent_sku'))"
                :title="$tr('label_required', $tr('parent_sku'))"
                :placeholder="$tr('choose_item', $tr('parent_sku'))"
                prepend-inner-icon="mdi-pound"
                :error-messages="skuError"
                :loading="isFetchingSku"
                type="number"
                :maxlength="6"
              />
              <v-checkbox
                v-model="item.isDefaultSKU.$model"
                inset
                class="position-absolute"
                style="margin-left:550px; margin-top: 20px"
                :label="`Custom Parent SKU`"
              ></v-checkbox>
          </div>
          <div class=" d-flex flex-column flex-lg-row">
            <div class="w-full me-lg-1  mb-lg-0">
              <CTextArea
                @blur="item.arabic_description.$touch()"
                v-model="item.arabic_description.$model"
                :disabled="item.arabic_name.$model?false:true"
                :rules="validateRule(item.arabic_description, $root, $tr('arabic_description'))"
                :title="$tr('label_required', $tr('arabic_description'))"
                :placeholder="$tr('arabic_description')"
                prepend-inner-icon="mdi-initemation"
              />
              
            </div>
            <div class="w-full ms-lg-1">
              <CTextArea
                @blur="item.description.$touch()"
                v-model="item.description.$model"
                :disabled="!item.arabic_name.$model?false:true && item.name.$model?false:true  "
                :rules="validateRule(item.description, $root, $tr('english_description'))"
                :title="$tr('label_required', $tr('english_description'))"
                :placeholder="$tr('english_description')"
                prepend-inner-icon="mdi-initemation"
              />
            </div>
          </div>
          <div class=" d-flex flex-column flex-lg-row">
            <div class="w-full d-flex ">
              <CAutoComplete
                v-model="item.brand_id.$model"
                @blur="item.brand_id.$touch()"
                :rules="
                  validateRule(
                    item.brand_id,
                    $root, 
                    $tr('sourcer'), 
                  )
                "
                :items="brands"
                item-value="id"
                item-text="name"
                rounded
                :placeholder="$tr('choose_item', $tr('brand'))"
                :invalid="item.brand_id.$invalid"
                prepend-inner-icon="mdi-source-branch"
                :isCheckNeeded="true"
                title="brand"
            />
              <!-- <InputCard
                :title="$tr('brand')"
                :hasSearch="true"
                :hasPagination="true"
                @search="(v) => (searchBrands = v)"
              >
                <NoCheckboxItemContainer
                  v-model="item.brand_id.$model"
                  :items="filterBrand"
                  :loading="isFetchingBrand"
                  @selectedItem="selectedBrand"
                  :no_data="filterBrand.length < 1 && !isFetchingBrand"
                  :rules="
                    validateRule(
                      item.brand_id, // validate objec
                      $root, // context
                      $tr('brand') // lable for feedback
                    )
                  "
                  :invalid="item.brand_id.$invalid"
                  height="250px"
                  :customIcon="'brand'"
                ></NoCheckboxItemContainer>
              </InputCard> -->
            </div>
          </div>
          <div class="d-flex flex-column flex-md-row position-relative">
            <CTextField 
                @blur="item.quantity.$touch()"
                :title="$tr('label_required', $tr('quantity'))"
                :placeholder="$tr('quantity')"
                type="number"
                class="me-md-2"
                rounded
                v-model="item.quantity.$model"
                :rules="validateRule(item.quantity, $root, $tr('quantity'))"
              />
             
              <CTextField 
                @blur="item.cost_per_unit.$touch()"
                :title="$tr('label_required', $tr('cost_of_supplier'))"
                :placeholder="$tr('cost_of_supplier')"
                type="number"
                class="me-md-2"
                rounded
                v-model="item.cost_per_unit.$model"
                :rules="validateRule(item.cost_per_unit, $root, $tr('cost_of_supplier'))"
              />
              <v-chip
                class=" position-absolute"
                rounded
                outlined
                color="success"
                style="margin-left:580px; margin-top: 20px"
              >
              <v-icon style="padding-right:5px">

                mdi-account-circle
              </v-icon>
                 {{ form.$model.supplier_name.supplier_name}}
              </v-chip>
          </div>
           <span style="padding-left:30px; font-size:18px;">
            {{ $tr('attributes')+'*' }}
            
            </span> <br/><br>
          <div class=" d-flex flex-column flex-lg-row pl-5 mb-2" v-for="(attr, i) in attrNames" :key="i"  >
            <div style="width:35%;" class="d-flex align-center
            ">
              <h3>{{ attr.name }} </h3>
            </div>
            <div style="width:65%">
              <span v-if="attr.type == 'value_select'">
                <v-chip
                  class="me-1 "
                  v-for="j in attr.values"
                  :key="j"
                  outlined
                  >
                  <v-icon color="blue" class="me-1">mdi-checkbox-marked</v-icon>
                  {{ j }}
                </v-chip>
              </span>
              <span v-else-if="attr.type == 'value_input' && attr.values=='text'" >
              
                  <CTextField
                    v-model="item.text_value.value.$model"
                    :placeholder="$tr('text_value')"
                    :dense="true"
                    style="padding-top:0px !important"
                  />
                  <!-- <v-text-field 
                  v-model="item.text_value.$model"
                  :placeholder="$tr('text_value')"
                  outlined
                  class="form-new-item"
                  rounded
                  small
                  background-color="var(--new-input-bg)"
                  >

                  </v-text-field> -->
              </span>
              <span v-else-if="attr.type == 'value_input' && attr.values=='number'" >
              
                <CTextField 
                class="me-md-2"
                v-model="item.number_value.value.$model"
                    :placeholder="$tr('input_value_here...')"
                     type="number"
                     :dense="true"
                    
                  />
              </span>
              <span v-else-if="attr.type == 'value_input' && attr.values=='link'" >
              
                <CTextField 
                    class="me-md-2"
                    v-model="item.link_value.value.$model"
                    :placeholder="$tr('input_value_here...')"
                    :dense="true"

                  />
              </span>
              <span v-else-if="attr.type == 'value_input' && attr.values=='file'">
                <CFileInputSingle
                    v-model="item.file_value.value.$model"
                    :placeholder="$tr('upload_file_here...')"
                    prepend-inner-icon="mdi-cloud-upload"
                    accept="*"
                    style="padding-top:-10px"
                    :dense="true"
                  />
              </span>
            </div>
            
            <!-- <InputCard :px="'0'" :py="'0'">
              <div v-for="(attr, i) in item.attributes.$each.$iter" :key="i">
                <div class="d-flex flex-column flex-md-row">
                  <div style="width: 45%">
                    <CSelect
                        v-model="attr.attribute_id.$model"
                        @blur="attr.attribute_id.$touch()"
                        :rules="
                          validateRule(
                            attr.attribute_id, // validate objec
                            $root, // context
                            $tr('attribute') // lable for feedback
                          )
                        "
                        :invalid="attr.attribute_id.$invalid"
                        :title="$tr('label_required', $tr('attribute'))"
                        :placeholder="$tr('attribute')"
                        prepend-inner-icon="mdi-chart-bar-stacked"
                        :items="attrNames"
                        :loading="attributeNameLoading"
                        item-text="name"
                        item-value="id"
                        @change="selectData($event,i,index)"
                    />
                  </div>
                  <div style="width: 45%">
                      <CSelectMulti
                        :title="$tr('attribute_values')"
                        placeholder="Attribute"
                        :items="attrValues[index]"
                        v-model="attr.value.$model"
                        item-text="value"
                        item-value="value"
                        multi
                        prepend-inner-icon="mdi-bookmark"
                        @blur="attr.value.$touch()"
                        :rules="
                          validateRule(
                            attr.value, // validate objec
                            $root, // context
                            $tr('value') // lable for feedback
                          )
                        "
                        :invalid="attr.value.$invalid"
                      />
                    </div>
                    <div style="width: 10%">
                      <v-icon
                        :class="`${
                           i == item.$model.attributes.length - 1
                            ? 'primary'
                            : 'red--text'
                        } rounded-circle mt-4`"
                        color="white"
                        style="   
                        left: 10px !important;top:  30px !important;width: 37px;height: 40px;
                        "
                        @click="
                          clicked(i,index, item.$model.attributes.length, attr)
                        "
                        >{{
                          i == item.$model.attributes.length - 1
                            ? "mdi-plus"
                            : "mdi-close"
                        }}
                      </v-icon>
                    </div>
                </div> 
              </div>
            </InputCard> -->
          </div>
         
          
          <div class="d-flex flex-column flex-lg-row">
            <div class="w-full d-flex ">
              <CFileUploadCloud2
              system_name="single-sales"
              v-model="item.images.$model"
              :form="form"
              dense
              ref="uploadRefs"
              >
              </CFileUploadCloud2>
            </div>
          </div>
        
        </template>
      </MultiInputItem>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CTextField from "../../new_form_components/Inputs/CTextField";
import CTextArea from "../../new_form_components/Inputs/CTextArea";
import CSelect from "../../new_form_components/Inputs/CSelect";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import MultiInputItem from "../../new_form_components/cat_product_selection/MultiInputItem.vue";
import CFileUploadCloud2 from "../../new_form_components/Inputs/CFileUploadCloud2.vue";
import CFileItemsImage from "../../new_form_components/Inputs/CFileItemsImage.vue";
import CSelectMulti from "../../new_form_components/Inputs/CSelectMulti.vue";
import CAutoComplete from '../../new_form_components/Inputs/CAutoComplete.vue';
import CFileInputSingle from '../../new_form_components/Inputs/CFileInputSingle.vue';

export default {
  name: "GeneralInfoStep",
  components: { CTextArea, CTextField, CSelectMulti, CSelect, CTitle, NoCheckboxItemContainer, InputCard, MultiInputItem, CFileUploadCloud2, CFileItemsImage, CAutoComplete, CFileInputSingle },
  props: {
    form: Object,
  },

  data() {
    return {
      ids:{},
      searchBrands: "",
      brands: [],
      isFetchingBrand: false,
      i:0,
      max:6,
      loading: false,
      model: "",
      item_code: "",
      sku_generate:"",
      validateRule: GlobalRules.validate,
      skuError: "",
      isFetchingProductCode:false,
      isFetchingSku:false,
      attrNames: [],
      attrValues: [],
      selectValueModel: [],
      selectNameModel: [],
      selectedItems: [],


      attributeNameLoading: false,
    };
  },
  computed:{
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
    async loaded() {
      this.FetchProductCode();
      this.FetchSKU();
      this.fetchBrands();
      this.selectAttribute();

    },
    async selectAttribute(){
     
      this.selectNameModel = [];
      this.selectValueModel = [];
      this.attributeNameLoading = true;
      try {
        let item= this.form.category_id.$model;
        const url = "product-management/pr-attributes?fields=id,name&key=all";
        const { data } = await this.$axios.get(url ,{
        params: {
          item
        }});

        console.log(data);
        this.attrNames = data;
        data.map((i)=>{
          if(i.type == "value_select"){
          this.form.$model.products[0].attribute = i.id
          this.ids['attribute']=i.id;
          }else{
            if(i?.values == "text")
          this.form.$model.products[0].text_value.id = i.id;
          this.ids['text']=i.id;
          if(i?.values == "link")
          this.form.$model.products[0].link_value.id = i.id;
          this.ids['link']=i.id;
          if(i?.values == "number")
          this.form.$model.products[0].number_value.id = i.id;
          this.ids['number']=i.id;
          if(i?.values == "file")
          this.form.$model.products[0].file_value.id = i.id;
          this.ids['file']=i.id;
          }
            });
      } catch (error) {}
      this.attributeNameLoading = false;
    },
    selectData(id) {
      const val = this.attrNames.find((i)=>i.id == id);
      if (val) {
        this.attrValues = val.values.map((i) => {
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
        this.removeItem1(item,index);
      }
    },
    addOneMore1(index) {
      console.log("products",this.form.$model);
      console.log("attrributes",this.form.$model.products[0].attributes);
      this.form.$model.products[index].attributes.push({
        attribute_id: "",
        value: [],
      });
    },
    removeItem1(item, index) {
      this.form.$model.products[index].attributes.splice(item,1);
    },
    async addOneMore() {
        this.form.products.$model.push(
          {
            name: null,
            arabic_name: null,
            description: null,
            arabic_description: null,
            brand_id: null,
            pcode: null,
            parent_sku: null,
            isDefaultProductCode:false,
            isDefaultSKU:false,
            quantity: "",
            cost_per_unit: "",
            images: [],
            text_value: {
              id:this.ids?.text??'',
              value:''
            },
            file_value:  {
              id:this.ids?.file??'',
              value:''
            },
            number_value:  {
              id:this.ids?.number??'',
              value:''
            },
            link_value:  {
              id:this.ids?.link??'',
              value:''
            },
            attribute: this.ids?.attribute??''
          }
        );
      this.setPcode();
      this.FetchSKU();

    },
    async removeItem(item) {
          if (this.form.products.$model.length > 1)
        this.form.products.$model.splice(item, 1);
          this.setPcode();
          // this.FetchSKU();
      },
    selectedBrand(item) {
      this.form.$model.products[0].brand_id = item.id;
    },
    async FetchSKU(){
      this.isFetchingSku = true;
      const url = "single-sales/product-ssp/generateSKU";
      const sku = await this.$axios.get(url)
      this.form.$model.products[ this.form.products.$model.length-1].parent_sku = sku.data;
      this.isFetchingSku = false;
    },
     async FetchProductCode(){
      this.isFetchingProductCode = true;
      const url = "single-sales/product-ssp/last-item-code";
      const pcode = await this.$axios.get(url)
      this.form.$model.products[0].pcode = pcode.data;
      this.item_code = structuredClone(pcode.data);
      this.isFetchingProductCode = false;
    },
    async fetchBrands() {
      this.isFetchingBrand = true;
      try {
        const brand = await this.$axios.get(
          "single-sales/brand-ssp/get?filter_brand=true"
        );
        console.log("brand",brand);
        this.brand_list = brand.data;
        this.brands = this.brand_list;
      } catch (error) {}
      this.isFetchingBrand = false;
    },
    setPcode() {
      var code = this.item_code;
      var pcode = "";
      var flag = false;
      this.form.$model.products.forEach((element) => {
        if (!element.isDefaultProductCode) {
          if(!flag){
            flag = true;
            pcode = this.item_code;
          }else{
          var number = code.substr(2);
          if (number == 99) {
            var firstChar = code.substr(0, 1);
            var secondChar = this.getNextChar(code.substr(1, 1));
            number = 1;
            pcode = firstChar + "" + secondChar + "" + number;
          } else {
            number++;
            pcode = code.substr(0, 2) + "" + number;
          }}
          code = pcode;
          element.pcode = pcode;
          
        }
      });
    },
    async checkSkuUniqeNess() {
      try {
        const url = "single-sales/products-ssp/check-sku";
        const { data } = await this.$axios.get(url, {
          params: {
            sku: this.form.parent_sku.$model,
          },
        });
        if (data.result == true) {
          this.skuError = this.$tr("item_already_exists", this.$tr("sku"));
          console.log(this.skuError);
        } else {
          this.skuError = "";
        }
        return data.result;
      } catch (e) {
        return false;
      }
    },
    async validate() {
      let res = await this.checkSkuUniqeNess();
      console.log(res);
      this.form.products.$touch();
      return !this.form.products.$invalid;
      
    },
  },
};
</script>
