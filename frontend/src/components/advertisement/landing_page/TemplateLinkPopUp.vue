<template>
  <div>
    <v-dialog v-model="dialog" max-width="500">
      <v-card>
        <v-card-title>
          <div class="d-flex">
            <h4>Template ID</h4>
          </div>
          <div class="d-flex ml-auto">
            <v-icon @click="CloseDialog">mdi-close-circle</v-icon>
          </div>
        </v-card-title>
        <v-card-text class="px-0 py-0" :key="key">
          <v-form ref="IDRef" v-model="valid">
            <CTextField
              v-model="id"
              :placeholder="$tr('https://www.example.com')"
              prepend-inner-icon="mdi-link-variant "
              :hasCustomBtn="true"
              btnIcon="mdi-clipboard-multiple"
              @add="PasteTo"
              :rules="urlRules"
            />
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="submitID"> submit </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import Validations from "../../../validations/validations";

export default {
  data() {
    return {
      dialog: false,
      valid: true,
      id: "",
      key: 0,
      urlRules: [
        (v) => !!v || this.$tr("item_is_required", this.$tr("url")),
        (v) =>
          (v &&
            !!v.match(
              /https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,}/gim
            )) ||
          this.$tr("invalid_link"),
      ],
    };
  },
  methods: {
    openDialog(id) {
      this.id = id;
      this.key++;
      this.dialog = !this.dialog;
    },
    async PasteTo() {
      try {
        const clipboard = navigator.clipboard;
        const account_po = await clipboard.readText();
        if (account_po) {
          this.id = account_po;
        }
      } catch (error) {}
    },
    submitID() {
      if (this.$refs.IDRef.validate()) {
        this.$emit("click", this.id);
        this.$refs.IDRef.reset();
        this.dialog = false;
      }
    },
    CloseDialog() {
      this.$refs.IDRef.reset();
      this.dialog = false;
      this.id = "";
    },
  },
  components: { CTextField, Validations },
};
</script>

<style>
</style>