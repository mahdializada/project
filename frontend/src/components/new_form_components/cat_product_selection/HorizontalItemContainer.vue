<template>
  <div
    v-if="no_data"
    class="
      items-card
      Scroll
      pa-1
      my-3
      bg-custom-gray
      d-flex
      flex-wrap
      align-center
      justify-center
    "
    :style="`height:${height}`"
  >
    No Items
  </div>
  <div
    v-else-if="loading"
    class="
      items-card
      Scroll
      pa-1
      my-3
      bg-custom-gray
      d-flex
      flex-wrap
      justify-center
    "
    :style="`height:${height}`"
  >
    <HorizontalCard v-for="i in 21" :key="i" :selected="false" loading />
  </div>
  <div
    v-else
    class="items-card Scroll pa-1 my-3 bg-custom-gray"
    :style="`height:${height}`"
  >
    <div class="w-full d-flex flex-wrap">
      <HorizontalCard
        v-for="(item, i) in items"
        :key="i"
        :item="item"
        :logoName="logoName"
        :item_text="item_text"
        :selected="
          multi ? (value ? value.includes(item.id) : false) : value == item.id
        "
        @click="handleClick(item)"
        @dblclick="$emit('category_doubleClicked', item)"
      />
    </div>
  </div>
</template>
<script>
import HorizontalCard from "../components/HorizontalCard.vue";
import UserCard from "../components/UserCard.vue";

export default {
  props: {
    value: null,
    logoName: {
      type:String,
      default:"logo"
    },
    item_text:{
      type:String,
      default:'name'
    },
    multi: Boolean,
    items: Array,
    loading: Boolean,
    no_data: Boolean,
    height: {
      type: String,
      default: "382px",
    },
  },
  components: {
    HorizontalCard,
    UserCard,
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
</style>
