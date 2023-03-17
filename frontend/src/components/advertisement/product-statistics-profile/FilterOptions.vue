<template>
  <div>
    <div>
      <p class="mb-0 ps-1 pt-1 grey--text text--darken-1 text-subtitle-2">
        {{ $tr(title) }}
      </p>

      <div
        class="d-flex align-center flex-wrap"
        :class="justify_center ? 'justify-center' : ''"
      >
        <slot></slot>
      </div>
    </div>
    <v-divider light class="my-1 mx-1"></v-divider>
  </div>
</template>

<script>
import SelectItemRohullah from "../../new_form_components/components/SelectItemRohullah.vue";

export default {
  props: {
    title: String,
    items: Array,
    justify_center: Boolean,
  },
  data() {
    return { riskPalicy: "" };
  },
  components: { SelectItemRohullah },
};
</script>

<style>
</style>