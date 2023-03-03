<template>
  <v-btn
    v-bind="$attrs"
    v-on="isDisabled ? '' : $listeners"
    style="
      padding: 1.2rem 1.5rem;
      margin: 0 0.2rem;
      border-radius: 3px !important;
    "
    :style="isDisabled ? btnStyle : ''"
    small
  >
    <div class="flex-column justify-items-center">
      <div v-if="icon">
        <v-icon x-small>{{ icon }}</v-icon>
      </div>
      <div>
        {{ $tr(text) }}
      </div>
    </div>
  </v-btn>
</template>

<script>
export default {
  name: "IconButton",
  props: {
    isDisabled: {
      type: Boolean,
      default: false,
    },
    icon: {
      required: true,
      type: String,
    },
    text: {
      required: true,
      type: String,
    },
  },

  data() {
    return {
      btnStyle: `background-color: #${
        this.$attrs.color === "primary"
          ? "2c7be5a1"
          : this.$attrs.color === "error"
          ? "ef476fa1"
          : ""
      } !important;  cursor: not-allowed !important;`,
    };
  },
};
</script>

<style>
.v-btn--disabled,
.primaryDisabledButton {
  color: #ffffff99 !important;
  opacity: 0.6;
  background-color: #2c7be599 !important;
}

.v-btn .v-btn--disabled .v-icon {
  color: white !important;
}
</style>
