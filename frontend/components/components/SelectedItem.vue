<template>
  <div
    :class="`selected-item d-flex align-center me-2 mb-2 ${
      small ? 'small' : ''
    }`"
  >
    <CloseBtn selectedClose @click="close" :xsmall="small" />
    <p
      class="ma-0 me-1"
      style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden"
    >
      {{ title }}
    </p>
    <div class="icon align-center d-flex" v-if="logoUrl">
      <v-avatar v-if="logoName" :size="30" color="blue-grey darken-3">
        <span class="white--text text-h6 pa-1">
          {{ title ? title[0].toUpperCase() : '' }}
        </span>
      </v-avatar>

      <v-avatar v-else :size="small ? '18' : '30'">
        <img :src="logoUrl" alt="" />
      </v-avatar>
    </div>
    <div class="icon align-center d-flex" v-if="flag || icon">
      <FlagIcon v-if="flag" :flag="flag" round />
      <v-icon v-if="icon">{{ icon }}</v-icon>
    </div>
  </div>
</template>
<script>
import CloseBtn from '../Dialog/CloseBtn.vue';
import FlagIcon from '../../common/FlagIcon.vue';

export default {
  props: {
    title: {
      type: String,
    },
    flag: {
      type: String,
    },
    icon: {
      type: String,
    },
    logoUrl: {
      type: String,
      default: null,
    },
    logoName: {
      type: String,
      default: 'name',
    },

    small: {
      type: Boolean,
    },
  },
  components: {
    CloseBtn,
    FlagIcon,
  },
  methods: {
    close(event) {
      if (event) {
        event?.preventDefault();
      }
      this.$emit('close');
    },
  },
};
</script>
<style scoped>
.icon {
  min-width: 28px;
}

.small .icon {
  min-width: 18px;
}

.selected-item {
  height: 40px;
  padding: 0 12px 0 6px;
  border-radius: 20px;
  border: 2px solid var(--stepper-header-item-border);
}

.selected-item p {
  color: var(--selected-item-icon-color);
  font-size: 18px;
}

.small.selected-item {
  height: 24px !important;
  margin-bottom: 8px !important;
  margin-left: 4px !important;
  margin-right: 4px !important;
  padding: 0 6px 0 3px;
}

.small.selected-item p {
  font-size: 10px;
  border-width: 1px !important;
}
</style>
<style>
.selected-item .icon .v-icon {
  color: var(--selected-item-icon-color) !important;
}
</style>
