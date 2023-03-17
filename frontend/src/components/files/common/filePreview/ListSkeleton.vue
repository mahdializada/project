<template>
  <div>
    <v-skeleton-loader
      v-for="item in length"
      :key="item"
      class=""
      type="sentences, divider, table-heading"
    ></v-skeleton-loader>
  </div>
</template>
<script>
export default {
  name: "list-skeleton",
  props: ["length"],
};
</script>
