<template>
  <div
    v-if="no_data"
    :class="`items-card Scroll pa-1 ${bgWhite? '' : 'my-3'}  bg-custom-gray d-flex flex-wrap align-center justify-center`"
    :style="`height:${height};${bgWhite?'background:white !important;':''}`"
  >
    {{ $tr("no_data") }}
  </div>
  <div
    v-else-if="loading"
    :class="`items-card Scroll pa-1 ${bgWhite? '' : 'my-3'}  bg-custom-gray d-flex flex-wrap justify-center`"
    :style="`height:${height};${bgWhite?'background:white !important':''}`"
  >
    <ItemCard v-for="i in 21" :key="i" :selected="false" loading />
  </div>
  <div
    v-else
    :class="`items-card Scroll pa-1 ${bgWhite? '' : 'my-3'}  bg-custom-gray`"
    :style="`height:${height};${bgWhite?'background:white !important':''}`"
  >
    <div class="w-full d-flex flex-wrap" >
      <ItemCard
        v-for="(item, i) in items"
        :key="i"
        :item="item"
        :selected="
          multi ? (value ? value.includes(item[item_value]) : false) : value == item[item_value]
        "
        :fullImg="fullImg"
        :customWidth="customWidth"
        :customWidth2="customWidth2"
        :item_text="item_text"
        :checkbox="false"
        :nameLogo="nameLogo"
        :hasLogo="hasLogo"
        :hasDetails="hasDetails"
        :hasImg="hasImg"
        :customIcon="customIcon"
        :supplierIcon="supplierIcon"
        :hasFavorite="hasFavorite"
        @click="handleClick(item)"
        @dblclick="$emit('category_doubleClicked', item)"
        @toggleFavorite="$emit('toggleFavorite', item)"
      >
      <template v-slot:cost>
        <span v-if="isSupplier" class="rounded-sm" :style="`background:${item.cost?'#bde3f6ad':'#bde3f633'};color: black;padding:2px 0;position:relative;top:2px`">
          <span>&nbsp;&nbsp;{{ item.cost?item.cost+' AED':$tr('no_cost') }}&nbsp;&nbsp;</span>
        </span>
          <span v-if="hasDetails">&nbsp;&nbsp;{{ item.details??''}}&nbsp;&nbsp;</span>
        </template>
    </ItemCard>
    </div>
  </div>
</template>
<script>
import ItemCard from "../components/ItemCard.vue";

export default {
  props: {
    value: null,
    customWidth2:Boolean,
    hasDetails:Boolean,
    customWidth:Boolean,
    fullImg:Boolean,
    multi: Boolean,
    bgWhite:Boolean,
    items: Array,
    loading: Boolean,
    no_data: Boolean,
    hasLogo: Boolean,
    hasImg: Boolean,
    hasFavorite: Boolean,
    nameLogo: String,
    supplierIcon: String,
    height: {
      type: String,
      default: "382px",
    },
    item_text: {
      type: String,
      default: "name",
    },
    item_value: {
      type: String,
      default: "id",
    },
    customIcon: {
      type: String,
      default: "",
    },
    type: {
      type: String,
      default: "",
    },
    isSupplier: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    ItemCard,
  },
  data() {
    return {
      selected: [],
    };
  },
  methods: {
    handleClick(item) {
      if (item.type == "category" || item.type == "sub_category") {
        this.$emit("category_clicked", item);
      } else {
        if (this.multi) {
          if (this.value ? this.value.includes(item[this.item_value]) : false) {
            this.$emit(
              "input",
              this.value.filter((id) => id != item[this.item_value])
            );
          } else {
            this.$emit("input", [...this.value, item[this.item_value]]);
          }
        } else {
          if (this.value == item[this.item_value]) {
            this.$emit("input", "");
          } else {
            this.$emit("input", item[this.item_value]);
          }
        }
      }
      this.$emit('selectedItem',item);
      // return selected record
      this.$root.$emit('msItem',item);
    },
  },
};
</script>
<style scoped>
.items-card {
  border-radius: 12px;
  overflow: auto;
}
</style>
