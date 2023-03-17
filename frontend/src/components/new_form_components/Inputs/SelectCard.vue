<template>
  <div class="single-text-filed-values d-flex flex-wrap Scroll" v-if="items">
    <div
      v-for="(item, index) in items"
      :key="index"
      class="single-text-filed-value ps-1 d-flex align-center justify-center"
    >
      {{ item.name ? item.name : item }}
      <v-btn
        @click="onRemoveItem(item, index)"
        fab
        text
        x-small
        color="primary"
        class="ma-0"
      >
        <v-icon dark size="16"> mdi-close </v-icon>
      </v-btn>
    </div>
  </div>
</template>

<script>
export default {
  props: ["items"],

  methods: {
    onRemoveItem(item, index) {
      this.$emit("removeByIndex", index);
      this.$emit("removeByItem", item);
    },
  },
};
</script>