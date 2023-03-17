<template>
  <div>
    <div class="mb-3">
      <InputCard
        :title="this.$tr('landing_page_language')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.landing_language.$model"
            v-for="item in landingPageLanguage"
            :key="item.id"
            :item="item"
            :icon="true"
            :selected="item.id == form.landing_language.$model"
            :disable="!item.active"
            @click="onlanding_languageTypeSelected(item)"
            :rules="
              validateRule(
                form.landing_language, // validate objec
                $root, // context
                $tr('landing_language') // lable for feedback
              )
            "
            :invalid="form.landing_language.$invalid"
          >
            <img :src="item.image" alt="" />
          </SelectItem>
        </div>
      </InputCard>
    </div>
    <div class="mb-3">
      <InputCard
        :title="this.$tr('product_type')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.product_Type.$model"
            v-for="item in productType"
            :key="item.id"
            :item="item"
            :selected="item.id == form.product_Type.$model"
            :disable="!item.active"
            @click="onProductTypeSelected(item)"
            :rules="
              validateRule(
                form.product_Type, // validate objec
                $root, // context
                $tr('product_Type') // lable for feedback
              )
            "
            :invalid="form.product_Type.$invalid"
          />
        </div>
      </InputCard>
    </div>
    <div class="mb-3">
      <CAutoComplete
        @blur="form.product_code.$touch()"
        :title="this.$tr('product_code')"
        v-model="form.product_code.$model"
        :items="products"
        item-value="name"
        item-text="name"
        p_name="id"
        return-object
        :loading="isFetchingproducts"
        :placeholder="$tr('code')"
        prepend-inner-icon="fa fa-box"
        :rules="
          validateRule(
            form.product_code, // validate objec
            $root, // context
            $tr('product_code') // lable for feedback
          )
        "
        :invalid="form.product_code.$invalid"
      />
    </div>
    <div class="d-flex flex-column flex-md-row">
      <div class="w-full" v-if="!isEnglish">
        <CTextField
          @blur="form.product_title_ar.$touch()"
          v-model="form.product_title_ar.$model"
          :title="$tr('product_title_ar')"
          :placeholder="$tr('product_name_in_arabic')"
          prepend-inner-icon="fa fa-box"
          :rules="
            validateRule(
              form.product_title_ar, // validate objec
              $root, // context
              $tr('product_title_ar') // lable for feedback
            )
          "
          :invalid="form.product_title_ar.$invalid"
        />
      </div>
      <div class="w-full" v-if="!isArabic">
        <CTextField
          @blur="form.product_title_en.$touch()"
          v-model="form.product_title_en.$model"
          :title="$tr('product_title_en')"
          :placeholder="$tr('product_name_in_english')"
          prepend-inner-icon="fa fa-box"
          :rules="
            validateRule(
              form.product_title_en, // validate objec
              $root, // context
              $tr('product_title_en') // lable for feedback
            )
          "
          :invalid="form.product_title_en.$invalid"
        />
      </div>
    </div>
    <div class="w-full" v-if="!isEnglish">
      <CTextArea
        @blur="form.product_note_ar.$touch()"
        v-model="form.product_note_ar.$model"
        px="px-0"
        py="py-0"
        class="mt-3"
        :title="$tr('product_note_ar')"
        :placeholder="$tr('note_here')"
        prepend-inner-icon="mdi-clipboard-text-outline"
        :rules="
          validateRule(
            form.product_note_ar, // validate objec
            $root, // context
            $tr('product_note_ar') // lable for feedback
          )
        "
        :invalid="form.product_note_ar.$invalid"
      />
    </div>
    <div class="w-full" v-if="!isArabic">
      <CTextArea
        @blur="form.product_note_en.$touch()"
        v-model="form.product_note_en.$model"
        px="px-0"
        py="py-0"
        class="mt-3"
        :title="$tr('product_note_en')"
        :placeholder="$tr('note_here')"
        prepend-inner-icon="mdi-clipboard-text-outline"
        :rules="
          validateRule(
            form.product_note_en, // validate objec
            $root, // context
            $tr('product_note_en') // lable for feedback
          )
        "
        :invalid="form.product_note_en.$invalid"
      />
    </div>
    <div class="w-full">
      <CTextField
        @blur="form.tranfer_link.$touch()"
        v-model="form.tranfer_link.$model"
        :title="$tr('tranfer_link')"
        placeholder="http:://www.website.com"
        prepend-inner-icon="mdi-link-variant"
        class="mt-3"
        :hasCustomBtn="true"
        @add="pasteItem"
        :btnIcon="'mdi-content-paste'"
        :rules="
          validateRule(
            form.tranfer_link, // validate objec
            $root, // context
            $tr('tranfer_link') // lable for feedback
          )
        "
        :invalid="form.tranfer_link.$invalid"
      />
    </div>
    <div class="w-full" v-if="!isEnglish">
      <CTextArea
        @blur="form.transfar_message_ar.$touch()"
        v-model="form.transfar_message_ar.$model"
        px="px-0"
        py="py-0"
        class="mt-3"
        :title="$tr('transfar_message_ar')"
        :placeholder="$tr('message_here')"
        prepend-inner-icon="mdi-clipboard-text-outline"
        :rules="
          validateRule(
            form.transfar_message_ar, // validate objec
            $root, // context
            $tr('transfar_message_ar') // lable for feedback
          )
        "
        :invalid="form.transfar_message_ar.$invalid"
      />
    </div>
    <div class="w-full" v-if="!isArabic">
      <CTextArea
        v-model="form.transfar_message_en.$model"
        @blur="form.transfar_message_en.$touch()"
        px="px-0"
        py="py-0"
        class="mt-3"
        :title="$tr('transfar_message_en')"
        :placeholder="$tr('message_here')"
        prepend-inner-icon="mdi-clipboard-text-outline"
        :rules="
          validateRule(
            form.transfar_message_en, // validate objec
            $root, // context
            $tr('transfar_message_en') // lable for feedback
          )
        "
        :invalid="form.transfar_message_en.$invalid"
      />
    </div>
    <div class="w-full" v-if="!isPiece">
      <CTextField
        @blur="form.product_collection_items.$touch()"
        v-model.trim="product_collection_items"
        :items="form.product_collection_items.$model"
        :title="$tr('product_collection_items')"
        :placeholder="$tr('items')"
        prepend-inner-icon="mdi-bullseye-arrow"
        class="mt-3"
        :hasItems="true"
        @add="addItem"
        @removeItem="removeItem"
        :rules="
          validateRule(
            form.product_collection_items, // validate objec
            $root, // context
            $tr('product_collection_items') // lable for feedback
          )
        "
        :invalid="form.product_collection_items.$invalid"
      />
    </div>

    <div class="mt-3" style="height: 100%" v-if="!isPiece">
      <InputCard>
        <v-timeline class="pt-0" dense flat>
          <v-timeline-item
            class="pb-0"
            v-for="(item, ind) in form.landing_detail.$each.$iter"
            :key="ind"
          >
            <div class="d-flex flex-column flex-md-row">
              <CTextField
                @blur="item.quantity.$touch()"
                v-model="item.quantity.$model"
                px="pe-1"
                type="number"
                min="0"
                :title="$tr('quantity')"
                :placeholder="$tr('no')"
                prepend-inner-icon="fa fa-atom"
                :rules="
                  validateRule(
                    item.quantity, // validate objec
                    $root, // context
                    $tr('quantity') // lable for feedback
                  )
                "
                :invalid="item.quantity.$invalid"
              />
              <CTextField
                v-model="item.price.$model"
                @blur="item.price.$touch()"
                px="ps-1"
                type="number"
                min="0"
                :title="$tr('price')"
                :placeholder="$tr('price')"
                prepend-inner-icon="fa fa-box"
                :rules="
                  validateRule(
                    item.price, // validate objec
                    $root, // context
                    $tr('price') // lable for feedback
                  )
                "
                :invalid="item.price.$invalid"
              />
              <v-icon
                :class="`${
                  ind == form.$model.landing_detail.length - 1
                    ? 'primary'
                    : 'red--text'
                } rounded-circle pa-1 mt-4 mr-1`"
                color="white"
                style="
                  left: 30px !important;
                  top: 30px !important; 
                  width: 35px;
                  height: 40px;
                "
                @click="
                  clicked(ind, form.$model.landing_detail.length, item.id)
                "
                >{{
                  ind == form.$model.landing_detail.length - 1
                    ? "mdi-plus"
                    : "mdi-close"
                }}
              </v-icon>
            </div>
          </v-timeline-item>
        </v-timeline>
      </InputCard>
    </div>

    <div class="mb-3">
      <InputCard
        :title="this.$tr('discount_type')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container mt-3"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.discount_type.$model"
            v-for="item in discountType"
            :key="item.id"
            :item="item"
            :selected="item.id == form.discount_type.$model"
            :disable="!item.active"
            @click="ondiscountTypeSelected(item)"
            :rules="
              validateRule(
                form.discount_type, // validate objec
                $root, // context
                $tr('discount_type') // lable for feedback
              )
            "
            :invalid="form.discount_type.$invalid"
          />
        </div>
      </InputCard>
    </div>
    <div class="w-full">
      <CTextField
        v-model="form.discount_amount.$model"
        @blur="form.discount_amount.$touch()"
        :title="$tr('discount_amount')"
        :placeholder="$tr('amount')"
        type="number"
        min="0"
        prepend-inner-icon="fa fa-box"
        :rules="
          validateRule(
            form.discount_amount, // validate objec
            $root, // context
            $tr('discount_amount') // lable for feedback
          )
        "
        :invalid="form.discount_amount.$invalid"
      />
    </div>
  </div>
</template>
  
  <script>
import GlobalRules from "~/helpers/vuelidate";
import CustomButton from "../../design/components/CustomButton.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import SelectItem from "../../new_form_components/components/SelectItem.vue";
import CTextArea from "../../new_form_components/Inputs/CTextArea.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";

export default {
  props: {
    form: Object,
  },
  components: {
    InputCard,
    SelectItem,
    CAutoComplete,
    CTextField,
    CTextArea,
    CustomButton,
  },
  data() {
    return {
      i: 0,
      key: 0,
      landin_detail: [],
      isEnglish: false,
      isArabic: false,
      isPiece:true,
      isFetchingproducts: false,
      validateRule: GlobalRules.validate,
      product_collection_items: "",
      landingPageLanguage: [
        { id: "Arabic", name: "Arabic", image: "/uae.jpg", active: true },
        {
          id: "English",
          name: "English",
          image: "/english.jpg",
          active: true,
        },
        { id: "Both", name: "Both", image: "/white.jpg", active: true },
      ],
      productType: [
        { id: "Piece", name: "Piece", active: true },
        { id: "Collection", name: "Collection", active: true },
      ],
      discountType: [
        { id: "Percentage", name: "Percentage", active: true },
        { id: "Price", name: "Price", active: true },
      ],
      products: [],
    };
  },
  methods: {
    validate() {
      this.form.landing_language.$touch();
      this.form.product_Type.$touch();
      this.form.product_code.$touch();
      this.form.product_title_ar.$touch();
      this.form.product_title_en.$touch();
      this.form.product_note_ar.$touch();
      this.form.product_note_en.$touch();
      this.form.transfar_message_ar.$touch();
      this.form.transfar_message_en.$touch();
      this.form.tranfer_link.$touch();
      this.form.product_collection_items.$touch();
      this.form.landing_detail.$touch();
      this.form.discount_type.$touch();
      this.form.discount_amount.$touch();
      console.log(this.form);

      let isValid =
        !this.form.landing_language.$invalid &&
        !this.form.product_Type.$invalid &&
        !this.form.product_code.$invalid &&
        !this.form.product_title_ar.$invalid &&
        !this.form.product_title_en.$invalid &&
        !this.form.product_note_ar.$invalid &&
        !this.form.product_note_en.$invalid &&
        !this.form.transfar_message_ar.$invalid &&
        !this.form.transfar_message_en.$invalid &&
        !this.form.tranfer_link.$invalid &&
        !this.form.product_collection_items.$invalid &&
        !this.form.landing_detail.$invalid &&
        !this.form.discount_type.$invalid &&
        !this.form.discount_amount.$invalid;

      return isValid;
    },
    onlanding_languageTypeSelected(item) {
      if (item.active == true) this.form.landing_language.$model = item.id;
      this.isEnglish = false;
      this.isArabic = false;
      this.form.$model.language_type = item.id;
      if (item.id == "English") {
        this.form.$model.product_title_ar = "";
        this.form.$model.product_note_ar = "";
        this.form.$model.transfar_message_ar = "";
        this.isEnglish = true;
      }
      if (item.id == "Arabic") {
        this.form.$model.product_title_en = "";
        this.form.$model.product_note_en = "";
        this.form.$model.transfar_message_en = "";
        this.isArabic = true;
      }
    },
    async loaded() {
      this.fetchItems(), (this.products = []);
    },

    async fetchItems() {
      try {
        const url = `advertisement/connection/generate_link/products/${
          this.form.$model.company_id +
          "," +
          this.form.$model.country_id +
          "," +
          0
        }`;
        const { data } = await this.$axios.get(url);
        this.products = data.items;
      } catch (error) {}
    },
    onProductTypeSelected(item) {
      if (item.active == true) this.form.product_Type.$model = item.id;
      this.isPiece = false
      if (item.id == "Collection") {
        this.isPiece = false
      }
      if (item.id == "Piece") {
        this.isPiece = true
      }
    },
    ondiscountTypeSelected(item) {
      if (item.active == true) this.form.discount_type.$model = item.id;
    },
    addItem() {
      if (this.product_collection_items?.length > 0) {
        this.form.$model.product_collection_items.push(
          this.product_collection_items
        );

        this.product_collection_items = "";
      }
      console.log(this.form.$model.product_collection_items);
    },

    removeItem(index) {
      this.form.$model.product_collection_items =
        this.form.$model.product_collection_items.filter(
          (item, i) => i !== index
        );
    },
    pasteItem() {
      navigator.clipboard
        .readText()
        .then((text) => {
          this.form.$model.tranfer_link = text;
        })
        .catch((err) => {
          console.error("Failed to read clipboard contents: ", err);
        });
    },
    clicked(index, length, id) {
      if (index == length - 1) {
        this.addOneMore();
      } else {
        this.removeItem1(id);
      }
    },
    removeItem1(id) {
      this.form.landing_detail.$model = this.form.landing_detail.$model.filter(
        (item) => item.id != id.$model
      );
    },
    addOneMore() {
      this.form.landing_detail.$model.push({
        id: this.generateID(),
        quantity: "",
        price: "",
      });
    },

    generateID() {
      return (
        "Id" +
        Math.floor(
          (Date.now() *
            Math.floor(
              Math.random() * Math.floor(Math.random() * Date.now())
            )) /
            (Math.random() * 1000000000)
        )
      );
    },
  },
};
</script>
  
  <style scoped>
.sales__type__container {
  display: flex;
  justify-content: space-between;
}
::v-deep(.v-timeline-item) {
  flex-direction: row !important;
}

::v-deep(.v-timeline-item__divider) {
  display: none !important;
}
.v-timeline::before {
  display: none;
}
.v-timeline .v-timeline--dense .theme--light {
  padding-top: 0px !important;
}
::v-deep(.v-timeline--dense .v-timeline-item__body) {
  max-width: 100%;
}
</style>