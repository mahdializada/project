<template>
  <div>
    <v-divider class="mb-1 grey lighten-1"/>
    <div class="row justify-space-between mx-1">
      <v-btn
        color="cyan darken-4"
        class="white--text"
        small
        :disabled="disableBack"
        @click="onBack"
      >
        <v-icon left>
          mdi-arrow-left
        </v-icon>
        {{ $tr("back") }}
      </v-btn>

      <v-btn
        class="mx-1"
        color="primary"
        :disabled="isSubmitting"
        :loading="isSubmitting"
        small
        v-if="showSubmit"
        @click="onSubmit"
      >
        {{ $tr("submit") }}
      </v-btn>

      <v-btn
        v-else
        class="mx-1"
        color="primary"
        small
        :disabled="disableNext"
        @click="onNext"
      >
        {{ $tr("next") }}
        <v-icon right>
          mdi-arrow-right
        </v-icon>
      </v-btn>
    </div>
  </div>
</template>

<script>
export default {
  name: "StepperActionButtons",

  props: {
    disableBack: {
      type: Boolean,
      default: false,
    },

    disableNext: {
      type: Boolean,
      default: false,
    },

    showSubmit: {
      type: Boolean,
      default: false,
    },

    isSubmitting: {
      type: Boolean,
      default: false,
    }
  },

  methods: {
    onSubmit() {
      this.$emit("onSubmit")
    },
    onBack() {
      this.$emit("onBack")
    },
    onNext() {
      this.$emit("onNext")
    },

  }


}
</script>

<style scoped>

</style>
