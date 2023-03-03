<template>
  <div>
    <v-btn
      v-if="custom_btn"
      v-bind="$attrs"
      v-on="$listeners"
      style="
        border: var(--v-primary-base) solid 1px !important;
        padding: 1.15rem 1.6rem;
        margin: 0 0.2rem;
        border-radius: 5px !important;
      "
    >
      {{ $tr(text) }}
    </v-btn>
    <v-btn
      v-if="type === 'primary'"
      v-bind="$attrs"
      v-on="$listeners"
      style="
        border: var(--v-primary-base) solid 1px !important;
        padding: 1.15rem 1.6rem;
        margin: 0 0.2rem;
        border-radius: 5px !important
      "
      small
      class="text-subtitle-2 primary"
      :loading="loading"
    >
      <div>
        {{ $tr(text) }}
      </div>
    </v-btn>
    <v-btn
      v-if="type == 'success'"
      v-bind="$attrs"
      v-on="$listeners"
      style="
        border: var(--v-success-base) solid 1px !important;
        padding: 1.15rem 1.6rem;
        margin: 0 0.2rem;
        border-radius: 5px !important;
      "
      small
      class="text-subtitle-2 success"
      :loading="loading"
    >
      <div>
        {{ $tr(text) }}
      </div>
    </v-btn>

    <v-btn
      v-if="type === 'danger'"
      :outlined="outlined"
      v-bind="$attrs"
      v-on="$listeners"
      style="
        border: #e82c5d solid 1px !important;
        padding: 1.15rem 1.6rem;
        margin: 0 0.2rem;
        border-radius: 5px !important;
      "
      small
      class="text-subtitle-2 error"
    >
      <div>
        {{ $tr(text) }}
      </div>
    </v-btn>

    <v-btn
      v-if="type === 'secondary'"
      v-bind="$attrs"
      v-on="$listeners"
      :style="`
        border: #78909C solid 1px !important;
        padding: 1.15rem 1.6rem;
        ${$vuetify.theme.dark ? '' : 'background-color: #DEE8EB !important;'}
        border-radius: 5px !important;
      `"
      small
      class="text-subtitle-2"
    >
      <div>
        {{ $tr(text) }}
      </div>
    </v-btn>
    <v-btn
      v-if="type === 'disabled'"
      v-bind="$attrs"
      v-on="$listeners"
      :style="` 
        padding: 1.15rem 1.6rem;
        ${$vuetify.theme.dark ? '' : 'background-color: #DEE8EB !important;'}
        border-radius: 5px !important;
      `"
      disabled
      small
      class="text-subtitle-2"
    >
      <div>
        {{ $tr(text) }}
      </div>
    </v-btn>
  </div>
</template>

<script>
export default {
  name: "TextButton",
  props: {
    text: {
      type: String,
      require: true,
    },
    type: {
      type: String,
      default: "primary",
    },
    loading: {
      type: Boolean,
      default: false,
    },
    outlined: {
      type: Boolean,
      default: false,
    },
    custom_btn: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style></style>