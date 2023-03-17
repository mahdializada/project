<template>
  <v-menu
    v-model="menu"
    bottom
    :close-on-content-click="false"
    :offset-y="true"
    transition="scale-transition"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-icon
        small
        v-bind="attrs"
        v-on="on"
        class="cursor-pointer me-1"
        @click="fetchCategories()"
        >mdi-cog-outline</v-icon
      >
    </template>

    <v-card elevation="5" class="pa-2" style="width: 250px">
      <p class="mb-0 custom-card-title-2 pb-1">
        {{ $tr("category") }}
      </p>
      <v-select
        class="form-new-item form-custom-select custom-card-title-2"
        background-color="var(--new-input-bg)"
        v-model="selected_column.category_id"
        hide-details
        outlined
        dense
        :menu-props="{
          bottom: true,
          offsetY: true,
        }"
        :items="categories"
        item-text="category_name"
        item-value="id"
        placeholder="select category"
        :loading="category_api_loading"
      >
      </v-select>

      <div>
        <p class="mb-0 mt-2 custom-card-title-2 pb-1">
          {{ $tr("tooltip_text") }}
        </p>
      </div>
      <v-textarea
        v-model="selected_column.tooltip"
        :class="`form-new-item form-custom-text-area  custom-card-title-2 ${
          true ? 'has-append' : ''
        }`"
        background-color="var(--new-input-bg)"
        outlined
        counter
        auto-grow
        hide-details
      >
      </v-textarea>
      <!-- <v-checkbox
        class="my-1 custom-card-title-2"
        v-model="selected_column.sortable"
        :label="$tr('sortable')"
        hide-details
        :false-value="0"
        :true-value="1"
      ></v-checkbox> -->
      <!-- <v-checkbox
        class="my-1 custom-card-title-2"
        v-model="selected_column.visible"
        :label="$tr('visible')"
        hide-details
        :false-value="0"
        :true-value="1"
      ></v-checkbox> -->

      <div class="d-flex">
        <v-spacer></v-spacer>
        <v-btn
          class="primary custom-btn"
          style=""
          @click="saveColumnSetting()"
          :loading="save_loading"
        >
          {{ $tr("save") }}
        </v-btn>
      </div>
    </v-card>
  </v-menu>
</template>

<script>
export default {
  props: {
    props_column: {
      type: Object,
      require: true,
    },
    page_name: {
      type: String,
      require: true,
    },
  },
  data() {
    return {
      save_loading: false,
      category_api_loading: false,
      menu: false,
      categories: [],
      selected_column: {
        id: "",
        text: "",
        tooltip: "",
        category_id: "",
        sortable: 0,
      },
    };
  },

  methods: {
    async saveColumnSetting() {
      this.save_loading = true;
      try {
        const data = {
          id: this.selected_column.id,
          tooltip: this.selected_column.tooltip,
          category_id: this.selected_column.category_id,
          sortable: this.selected_column.sortable,
          visible: this.selected_column.visible,
        };
        const response = await this.$axios.post("save-column-changes", data);
        if (response.status == 200) {
          this.updateRealtime();
          this.$toasterNA("green", this.$tr('successfully_updated'));
          this.menu = false;
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr("something_went_wrong"));

        }
      } catch (e) {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr("something_went_wrong"));

      }
      this.save_loading = false;
    },

    async fetchCategories() {
      try {
        this.selected_column = JSON.parse(JSON.stringify(this.props_column));
        if (this.categories.length > 0) {
          return;
        }

        this.category_api_loading = true;
        const response = await this.$axios.get(
          "subsystem-column-category?sub_system=" + this.page_name
        );
        this.categories = response.data;
      } catch (e) {
      }
      this.category_api_loading = false;
    },
    updateRealtime() {
      const category = this.categories.find(
        (row) => row.id == this.selected_column.category_id
      );
      this.selected_column.category = category;
      this.$emit("saveColumnUpdates", this.selected_column);
    },
  },
};
</script>

<style scoped>
.custom-card-title-2 {
  font-size: 16px;
  font-weight: 400;
  color: #777;
}
</style>