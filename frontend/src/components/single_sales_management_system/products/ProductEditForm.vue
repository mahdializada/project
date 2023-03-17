<template>
  <div style="width: 100%">
    <div class="d-flex align-center justify-space-between w-full">
      <v-text-field
        v-if="!select_values[property.name]"
        :class="`pa-0`"
        v-model="edit_value"
        background-color="var(--new-input-bg)"
        outlined
        dense
        hide-details
        :type="input_type.includes(property.name) ? 'number' : ''"
        style="max-width: 250px"
      />
      <v-combobox
        :loading="loading"
        style="max-width: 250px"
        v-if="select_values[property.name]"
        :items="select_values[property.name]"
        outlined
        :placeholder="$tr('choose_item',$tr('item'))"
        item-text="name"
        v-model="edit_value"
        background-color="var(--new-input-bg)"
        dense
        hide-details
      ></v-combobox>
      <div class="d-flex align-center">
        <BackgroundButton
          :color="'red'"
          :icon="'mdi-close'"
          class="me-1"
          @click="$emit('close')"
        />
        <BackgroundButton
          :icon="'mdi-check'"
          @click="updateProperty()"
          :loading.sync="submitting"
        />
      </div>
    </div>
  </div>
</template>

<script>
import BackgroundButton from "../../common/buttons/BackgroundButton.vue";
export default {
  components: {
    BackgroundButton,
  },
  props: {
    profile_data: { required: true },
    property: { required: true },
    editType: {
      default: "product",
    },
  },
  data() {
    return {
      editIcon: `<svg xmlns="http://www.w3.org/2000/svg" width="14.453" height="13.821" viewBox="0 0 14.453 13.821">
  <path id="edit-icon" d="M7.227,13.821a.75.75,0,1,1,0-1.5H13.7a.75.75,0,0,1,0,1.5ZM.22,13.6a.751.751,0,0,1-.2-.713l.719-2.878a.755.755,0,0,1,.2-.348l9-9a2.276,2.276,0,1,1,3.219,3.219l-9,9a.746.746,0,0,1-.348.2L.932,13.8a.749.749,0,0,1-.712-.2ZM11,1.727,2.147,10.576,1.781,12.04l1.464-.365,8.849-8.849a.777.777,0,0,0-1.1-1.1Z" fill="currentColor"/>
</svg>`,
      isEdit: false,
      edit_value: "",
      input_type: ["VAT_tax", "number_of_sales", "quantity", "price_per_unit"],
      select_values: {
        unit: ["kg", "cm", "liter", "cc"],
        type: ["phsycal", "digital"],
        available: ["now", "comming_soon"],
        brand: [],
        category: [],
      },
      loading: false,
      submitting: false,
    };
  },
  methods: {
    async fetchBrands() {
      this.loading = true;
      try {
        const response = await this.$axios.get(
          "single-sales/brand-ssp/get?filter_brand=true"
        );
        this.select_values.brand = response.data;
      } catch (error) {}
      this.loading = false;
    },

    async fetchCategories() {
      this.loading = true;
      try {
        let res = await this.$axios.get("single-sales/categories-ssp", {
          params: {
            get_for_dropdown: true,
          },
        });
        this.select_values.category = res.data;
      } catch (error) {
        console.error(error);
      }
      this.loading = false;
    },

    async updateProperty() {
      const prev_val =
        this.property.type == "string"
          ? this.profile_data[this.property.name]
          : this.profile_data[this.property.name]?this.profile_data[this.property.name][this.property.attribute]:'';
      if (this.edit_value == prev_val) {
        this.$emit("close");
        return;
      }
      this.submitting = true;
      try {
        let value = "";
        if (typeof this.edit_value == "object") {
          value = this.edit_value.id;
        } else {
          value = this.edit_value;
        }
        let data = {
          property: this.property.name,
          value: value,
          editType: this.editType,
        };
        let res = await this.$axios.put(
          "single-sales/products-ssp/" + this.profile_data.id,
          data
        );
        this.profile_data[this.property.name] =
          res.data.data[this.property.name];
        this.$emit("close");
      } catch (error) {
        console.error(error);
      }
      this.submitting = false;
    },
  },

  created() {
    if (this.property.name == "brand") {
      this.fetchBrands();
    } else if (this.property.name == "category") {
      this.fetchCategories();
    }
    try {
      this.edit_value = structuredClone(
        this.property.type == "string"
          ? this.profile_data[this.property.name]
          : this.profile_data[this.property.name]?this.profile_data[this.property.name][this.property.attribute]:''
      );
    } catch (error) {
      console.error(error);
    }
  },
};
</script>

<style>
</style>
BackgroundButton