<template>
  <div
    v-if="no_data"
    class="items-card Scroll pa-1 my-3 bg-custom-gray d-flex flex-wrap align-center justify-center"
    :style="`height:${height}`"
  >
    No Items
  </div>
  <div
    v-else-if="loading"
    class="items-card Scroll pa-1 my-3 bg-custom-gray d-flex flex-wrap justify-center"
    :style="`height:${height}`"
  >
    <HorizontalCard v-for="i in 21" :key="i" :selected="false" loading />
  </div>
  <div
    v-else
    :class="`items-card Scroll ${
      bgWhite || hasCustomoColor ? '' : 'pa-1'
    } my-3 bg-custom-gray`"
    :style="`height:${height};${bgWhite ? 'background:white !important' : ''}`"
  >
    <div :class="`w-full d-flex flex-wrap ${mt}`">
      <div
        v-for="(item, index) in items"
        :key="index"
        :style="`${
          value == item.id && hasCustomoColor
            ? 'border: 2px solid ' +
              item.color +
              '!important;box-shadow: 0 0 1px ' +
              item.color +
              '!important;'
            : ''
        };${item?.notAllowed ? 'opacity:0.5' : ''}`"
        @click="handleClick(item)"
        :class="`item__card py-2 ${
          item?.notAllowed ? 'cursor-not-allowed' : 'cursor-pointer'
        } px-1 mx-1 d-flex align-center ${
          multi
            ? value.includes(item.id)
              ? 'selected'
              : ''
            : value == item.id && !hasCustomoColor
            ? 'selected'
            : ''
        }`"
      >
        <v-icon v-if="hasIcon" color="primary" size="23">
          {{ item.icon }}</v-icon
        >
        <span
          class="mx-1"
          :style="`${hasCustomoColor ? 'color:' + item.color : ''}`"
          >{{ $tr(item[item_value]) }}</span
        >
        <v-spacer></v-spacer>
        <v-icon
          :color="hasCustomoColor ? item.color : 'primary'"
          size="21"
          style="margin-right: 3px"
          >{{
            multi
              ? value.includes(item.id)
                ? "mdi-check-circle"
                : ""
              : value == item.id
              ? "mdi-check-circle"
              : ""
          }}</v-icon
        >
      </div>
    </div>
  </div>
</template>
<script>
import HorizontalCard from "../components/HorizontalCard.vue";
export default {
  props: {
    value: null,
    multi: Boolean,
    items: Array,
    loading: Boolean,
    no_data: Boolean,
    hasIcon: Boolean,
    bgWhite: Boolean,
    hasCustomoColor: {
      type: Boolean,
      default: false,
    },
    height: {
      type: String,
      default: "382px",
    },
    item_value: {
      type: String,
      default: "name",
    },
    mt: {
      type: String,
      default: "mt-0",
    },
  },
  components: {
    HorizontalCard,
  },
  data() {
    return {
      selected: [],
    };
  },
  methods: {
    handleClick(item) {
      if (item?.notAllowed) return;
      if (item.type == "category" || item.type == "sub_category") {
        this.$emit("category_clicked", item);
      } else {
        if (this.multi) {
          if (this.value ? this.value.includes(item.id) : false) {
            this.$emit(
              "input",
              this.value.filter((id) => id != item.id)
            );
          } else {
            this.$emit("input", [...this.value, item.id]);
          }
        } else {
          if (this.value == item.id) {
            this.$emit("input", "");
          } else {
            this.$emit("input", item.id);
          }
        }
      }
    },
  },
};
</script>
<style scoped>
.items-card {
  border-radius: 12px;
  overflow: auto;
}

.item__card {
  min-width: 210px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin: 6px !important;
  border-radius: 8px;
  transition: all 0.1s;
  border: 1px solid #2e7be62b !important;
}
.item__card.selected {
  border: 2px solid #2e7be680 !important;
  box-shadow: 0 0 10px #2e7be633;
}
</style>
