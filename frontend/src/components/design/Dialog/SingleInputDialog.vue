<template>
  <v-dialog v-model="dialog" width="400">
    <template>
      <v-card class="mx-auto">
        <v-card-text class="text--primary pt-4 blue lighten-5">
          <h2><v-icon>mdi-pencil</v-icon>{{ $tr("add_item",$tr('price')) }}</h2>
          <CTextField
            py="py-3"
            v-model="item"
            px="pe-1"
            :rounded="false"
            :type="type"
            :title="$tr('product_price')"
            :placeholder="$tr('price')"
            prepend-inner-icon="fa fa-atom"
          />
        </v-card-text>
        <v-card-actions class="pa-2">
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="dialog = false" text>
            {{ $tr("cancel") }}
          </v-btn>
          <v-btn color="primary" @click="addItem"> {{ $tr("add") }} </v-btn>
        </v-card-actions>
      </v-card>
    </template>
  </v-dialog>
</template>

<script>
import CTextField from "../../new_form_components/Inputs/CTextField.vue";

export default {
    props:{
        type: String,
        default: 'text'
    },
  data() {
    return {
      item: "",
      dialog:false,
    };
  },
  methods: {
    openModel(i = ''){
      this.item = i;
        this.dialog = true;
    },
    addItem() {
        this.$emit("addItem", this.item);
        this.dialog = false;
        this.item = '';
    },
  },
  components: { CTextField },
};
</script>

<style  scoped>
.v-application .background {
  background: none !important;
}
</style>