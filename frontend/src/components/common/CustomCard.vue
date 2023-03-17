<template>
  <v-card class="mb-4" elevation="24" v-bind="$attrs" v-on="$listeners">
    <slot> </slot>
  </v-card>
</template>

<script>
export default {
  name: "CustomCard",
};
</script>

<style scoped></style>
