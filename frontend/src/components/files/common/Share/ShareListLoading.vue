<template>
  <v-list class="surface" dense>
    <v-list-item v-for="(item, i) in lenght" :key="i">
      <v-list-item-action class="">
        <v-skeleton-loader
          class="mx-auto"
          tile
          max-width="20"
          max-height="20"
          type="avatar: avatar"
        ></v-skeleton-loader>
      </v-list-item-action>
      <div class="w-full d-flex cursor-pointer">
        <v-list-item-avatar size="30" class="ms-3 mx-2">
          <v-skeleton-loader type="avatar: avatar"></v-skeleton-loader>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-skeleton-loader width="500" type="heading"></v-skeleton-loader>
        </v-list-item-content>
      </div>
    </v-list-item>
  </v-list>
</template>

<script>
export default {
  props: {
    lenght: { default: 6 },
  },
};
</script>

<style></style>
