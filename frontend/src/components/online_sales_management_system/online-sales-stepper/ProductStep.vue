<template>
    <div>
      <MultiInputItem
        :items="form.products.$each.$iter"
        @addMore="addMore"
        @removeItem="removeItem"
        :title="'item_sales'"
        :otherTitel="'product_code'"
        :loading.sync="loading"
      >
        <template v-slot:content="{ item, index }">
          <div class="d-flex flex-column flex-md-row position-relative">
            <CTextField
              py="py-3"
              v-model="item.product_code.$model"
              @blur="checked(item.product_code.$model, index)"
              :rules="
                validateRule(item.product_code, $root, $tr('item_sales_code'))
              "
              px="ps-1"
              :invalid="item.product_code.$invalid"
              :title="$tr('label_required', $tr('item_sales_code'))"
              :placeholder="$tr('item_sales_code')"
              prepend-inner-icon="fa fa-atom"
              :disabled="!item.isDefaultProductCode.$model"
              :loading="isFetchingProductCode"
            />
            <v-switch
              v-model="item.isDefaultProductCode.$model"
              hide-details
              inset
              class="position-absolute"
              style="right: 0; margin-top: 27px"
              :label="$tr(`costum_item_sales_code`)"
            ></v-switch>
          </div>
          <div class="d-flex flex-column flex-md-row position-relative">
            <CTextField
            py="py-3"
            v-model="item.product_name_arabic.$model"
            @blur="item.product_name_arabic.$touch()"
            :rules="
              validateRule(
                item.product_name_arabic,
                $root,
                $tr('item_sales_arabic_name')
              )
            "
            px="pe-1"
            :invalid="item.product_name_arabic.$invalid"
            :title="$tr('label_required', $tr('item_sales_arabic_name'))"
            :placeholder="$tr('item_sales_arabic_name')"
            prepend-inner-icon="fa fa-atom"
            />
            <CTextField
              py="py-3"
              v-model="item.product_name.$model"
              @blur="item.product_name.$touch()"
              :rules="
                validateRule(
                  item.product_name,
                  $root,
                  $tr('item_sales_english_name')
                )
              "
              px="pe-1"
              :invalid="item.product_name.$invalid"
              :title="$tr('label_required', $tr('item_sales_english_name'))"
              :placeholder="$tr('item_sales_english_name')"
              prepend-inner-icon="fa fa-atom"
            />
          </div>
        </template>
      </MultiInputItem>
    </div>
  </template>
    
    <script>
  import GlobalRules from "~/helpers/vuelidate";
  import MultiInputItem from "../../new_form_components/cat_product_selection/MultiInputItem.vue";
  import CTextField from "../../new_form_components/Inputs/CTextField.vue";
  
  export default {
    props: {
      form: Object,
    },
    data() {
      return {
        loading: false,
        isFetchingProductCode: false,
        validateRule: GlobalRules.validate,
        model: "",
        item_code: "",
      };
    },
    methods: {
      async validate() {
        this.form.products.$touch();
        return !this.form.products.$invalid;
      },
      async loaded() {
        this.FetchProductCode();
      },
      async FetchProductCode() {
        this.isFetchingProductCode = true;
        const url = "last-item-code";
        const pcode = await this.$axios.get(url);
        this.form.$model.products[0].product_code = pcode.data;
        this.item_code = structuredClone(pcode.data);
        this.isFetchingProductCode = false;
      },
      getNextChar(char) {
        if (char == "Z") return "A";
        return String.fromCharCode(char.charCodeAt(0) + 1);
      },
      setPcode() {
        var code = this.item_code;
        var pcode = "";
        var flag = false;
        this.form.$model.products.forEach((element) => {
          if (!element.isDefaultProductCode) {
            if (!flag) {
              flag = true;
              pcode = this.item_code;
            } else {
              var number = code.substr(2);
              if (number == 99) {
                var firstChar = code.substr(0, 1);
                var secondChar = this.getNextChar(code.substr(1, 1));
                number = 1;
                pcode = firstChar + "" + secondChar + "" + number;
              } else {
                number++;
                pcode = code.substr(0, 2) + "" + number;
              }
            }
            code = pcode;
            element.product_code = pcode;
          }
        });
      },
      setIncrement(code) {
        var pcode = "";
        var number = code.substr(2);
        if (number == 99) {
          var firstChar = code.substr(0, 1);
          var secondChar = this.getNextChar(code.substr(1, 1));
          number = 1;
          pcode = firstChar + "" + secondChar + "" + number;
        } else {
          number++;
          pcode = code.substr(0, 2) + "" + number;
        }
        var find_code = this.form.products.$model.find(
          (i) => i.product_code == pcode
        );
        if (find_code) return this.setIncrement(pcode);
        return pcode;
      },
      async addMore() {
        this.validate();
        if (!this.form.products.$invalid) {
          this.loading = true;
          this.form.products.$model.push({
            product_name: "",
            product_name_arabic: "",
            product_code: await this.getIncrementCode(),
            isDefaultProductCode: false,
          });
          this.loading = false;
        } else {
          this.$toasterNA("red", this.$tr("please_fill_all_fields_correctly"));
        }
      },
      async getIncrementCode() {
        try {
          var code = 'SF0';
          this.form.$model.products.map((i)=>{
            if(!i.isDefaultProductCode)
              code = i.product_code
              return i;
          })
          code = this.setIncrement(code);
          const res = await this.$axios.get(
            `online-sales/increment-item/${code}`
          );
          this.item_code = structuredClone(res.data);
          return res.data;
        } catch (error) {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      },
      async checked(code, index) {
        if (!code) return;
        var isInIndex = false;
        this.isFetchingProductCode = true;
        var find_code = this.form.products.$model.find((i, j) => {
          if (i.product_code == code) {
            if (j == index) isInIndex = true;
            return i;
          }
        });
        if (find_code && !isInIndex) {
          this.$toasterNA("red", this.$tr("item_already_exists", this.$tr(code)));
        } else {
          const res = await this.$axios.get(
            `online-sales/check-item-unique/${code}`
          );
          if (res.data == "exist") {
            this.$toasterNA(
              "red",
              this.$tr("item_already_exists", this.$tr(code))
            );
          }
        }
        this.isFetchingProductCode = false;
      },
      removeItem(item) {
        if (this.form.products.$model.length > 1)
          this.form.products.$model.splice(item, 1);
      },
    },
    components: { MultiInputItem, CTextField },
  };
  </script>
    