<template>
  <div
    class="custom-card-header py-1 px-2 mt-1 d-flex justify-space-between align-center"
  >
    <div class="d-flex align-center">
      <v-avatar
        size="35"
        class="me-2"
        v-if="$attrs.icon"
        color="blue lighten-5"
      >
        <v-icon dark style="font-size: 20px" class="primary--text">
          {{ $attrs.icon }}
        </v-icon>
      </v-avatar>

      <h3 class="text-capitalize text-ellipsis">
        {{ $tr($attrs.text) }}
      </h3>
    </div>

    <v-btn
      v-if="$attrs.uploadFile && !$attrs.showEditBtn"
      class="stepper-btn px-1"
      style="min-width: 130px; height: 31px"
      @click="$emit('onEdit')"
      color="primary"
    >
      {{ $tr('upload_files') }}
      <v-icon right dark> mdi-cloud-upload </v-icon>
    </v-btn>

    <v-btn
      v-else-if="!$attrs.showEditBtn"
      @click="$emit('onEdit')"
      color="primary"
      fab
      x-small
      dark
    >
      <v-icon>
        {{ $attrs.editIcon ? $attrs.editIcon : 'mdi-pencil' }}
      </v-icon>
    </v-btn>
    <div
      v-else-if="hasSaveBtn"
      class="d-flex align-center justify-content-between"
    >
      <v-btn
        color="primary"
        class="stepper-btn px-1"
        style="min-width: 130px; height: 31px"
        v-if="$attrs.loading"
        :loading="$attrs.loading"
      >
        <template v-slot:loader>
          <span>
            <span>
              {{ $attrs.uploadFile ? $tr('uploading') : $tr('submitting') }}
            </span>
            <v-progress-circular
              class="ma-0"
              indeterminate
              color="white"
              size="20"
              :width="2"
            />
          </span>
        </template>
      </v-btn>

      <v-btn
        @click="$emit('onSave')"
        v-else
        style="height: 31px"
        color="primary"
        :loading="$attrs.isSubmitting"
      >
        <v-icon left> mdi-check </v-icon>
        {{ $attrs.uploadFile ? $tr('upload_files') : $tr('save') }}
      </v-btn>
      <CloseBtn
        style="height: 31px"
        @click="$emit('onClose')"
        class="py-0 my-0"
        small
      />
    </div>
    <CloseBtn
      v-else
      style="height: 31px"
      @click="$emit('onClose')"
      class="py-0 my-0"
      small
    />
  </div>
</template>

<script>
import CloseBtn from '~/components/design/Dialog/CloseBtn.vue';
export default {
  components: { CloseBtn },
  props: {
    hasSaveBtn: {
      type: Boolean,
      default: true,
    },
  },
};
</script>

<style></style>
