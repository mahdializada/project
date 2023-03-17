<template>
  <div>
    <v-dialog v-model="dialog" width="700" persistent>
    <Dialog @closeDialog="dialog = false">
        <CustomCarousel :items="items" class="pb-2" :height="'700px'" :show_arrows="true" :cycle="true" :show_arrows_on_hover="true"/>
    </Dialog>
  </v-dialog>
  </div>
</template>

<script>
import CustomCarousel from "../../common/CustomCarousel.vue";
import Dialog from "../../design/Dialog/Dialog.vue";
export default {
  components: { Dialog, CustomCarousel },
  data() {
    return {
      dialog: false,
      items:[],
    };
  },
  methods: {
    showDialog(item) {
      this.items = item??[];
      this.dialog = true;
    },
  },
};
</script>

<style>
</style>