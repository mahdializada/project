<template>
  <v-dialog v-model="showDialog" width="800" persistent>
    <v-card>
      <v-card-title class="pa-2 pb-1" style="color: #777">
        <span class="dialog-title"> {{ $tr("labels") }}</span>
        <v-spacer />
        <svg
          @click="toggleDialog()"
          width="48px"
          height="48px"
          viewBox="0 0 700 560"
          fill="currentColor"
          style="transform: scaleY(-1); cursor: pointer"
        >
          <g>
            <path
              d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
            />
            <path
              d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
            />
          </g>
        </svg>
      </v-card-title>
      <v-card-text class="ps-2 pe-1">
        <div
          id="custom-scroll"
          class="custom-wraper pe-1"
          style="position: relative"
        >
          <template v-if="!skeleton_loading">
            <div v-if="label_categories.length > 0 && has_label.length > 0">
              <div v-for="(category, index) in label_categories" :key="index">
                <div v-if="category.label.length > 0">
                  <div class="d-flex align-center flex-nowrap mb-2 mt-1">
                    <p
                      class="mb-0 me-1 custom-card-title-2"
                      style="white-space: nowrap"
                    >
                      {{ category.title }}
                    </p>
                    <div
                      class="rounded me-1"
                      style="
                        padding-top: 1px;
                        width: 100%;
                        background-color: rgb(212, 212, 212);
                      "
                    ></div>
                  </div>
                  <div class="d-flex flex-wrap">
                    <div
                      outlined
                      class="me-1 mb-1 custom-chip d-flex align-center"
                      :class="
                        selected_labels.includes(label.id) ? 'selected' : ''
                      "
                      v-for="(label, index) in category.label"
                      :key="index"
                      @click="selectLabel(label.id)"
                    >
                      <div
                        class="label-color me-1 white--text text-center text-uppercase"
                        :style="'background-color:' + label.color"
                      >
                        {{ label.label.charAt(0) }}
                      </div>
                      <span style="line-height: 10px">{{ label.label }}</span>
                      <v-icon
                        small
                        style="color: inherit; line-height: 5px"
                        class="mx-1"
                      >
                        {{
                          selected_labels.includes(label.id)
                            ? "mdi-minus"
                            : "mdi-plus"
                        }}
                      </v-icon>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              v-else
              class="text-center"
              style="
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
              "
            >
              <NoLabel />
            </div>
          </template>
          <template v-else>
            <span v-for="i in 3" :key="i">
              <v-skeleton-loader
                v-bind="attrs_sk"
                type=" card-heading, list-item-three-line"
              ></v-skeleton-loader>
            </span>
          </template>
        </div>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          class="custom-btn"
          color="primary"
          outlined
          @click="toggleDialog()"
        >
          {{ $tr("cancel") }}
        </v-btn>
        <v-btn
          class="primary custom-btn"
          style=""
          @click="saveChanges()"
          :loading="api_loading"
        >
          {{ $tr("save") }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import NoLabel from "./NoLabel.vue";
export default {
  data() {
    return {
      showDialog: false,
      api_loading: false,
      selected_labels: [],
      label_categories: [],
      has_label: [],
      skeleton_loading: false,
      attrs_sk: {
        style: "margin-bottom:0.2px",
        boilerplate: true,
        elevation: 0,
      },
    };
  },
  props: {
    noModel: {
      type: Boolean,
      default: false,
    },
    subsystem_name: {
      type: String,
      require: true,
    },
    model_name: {
      type: String,
      require: true,
    },
    selected_item: {
      type: Array,
      require: true,
    },
  },
  methods: {
    toggleDialog(item_data, tab) {
      if (!this.showDialog) {
        this.setSelectedLabels();
        this.getLabels();
      }
      this.showDialog = !this.showDialog;
    },
    selectLabel(id) {
      if (!this.selected_labels.includes(id)) this.selected_labels.push(id);
      else
        this.selected_labels = this.selected_labels.filter((row) => row != id);
    },
    async getLabels() {
      try {
        this.skeleton_loading = true;
        const response = await this.$axios.get("labels-with-category", {
          params: {
            subsystem_name: this.subsystem_name,
            tab: this.model_name,
          },
        });
        this.label_categories = response.data;
        this.has_label = this.label_categories.filter(
          (row) => row.label.length > 0
        );
      } catch (error) {}
      this.skeleton_loading = false;
    },
    async saveChanges() {
      try {
        this.api_loading = true;
        let target_id = "";

        switch (this.model_name) {
          case "ad_set":
            target_id = "adset_id";
            break;
          case "ad":
            target_id = "ad_id";
            break;
          case "campaign":
            target_id = "campaign_id";
            break;
          case "item_code":
            target_id = "pcode";
            break;
          default:
            target_id = "id";
            break;
        }
        if (this.subsystem_name == "online_sales") {
          switch (this.model_name) {
            case "country":
              target_id = "country_id";
              break;
            case "company":
              target_id = "company_id";
              break;
            case "project":
              target_id = "project_id";
              break;
            case "sales_type":
              target_id = "sales_type";
              break;
            case "item_code":
              target_id = "product_code";
              break;
            default:
              target_id = "id";
              break;
          }
        }
        const data = {
          selected_labels: this.selected_labels,
          model_name: this.noModel ? this.subsystem_name : this.model_name,
          id: this.selected_item[0][target_id],
          sub_system: this.subsystem_name,
        };
        const response = await this.$axios.post("labels-assign", data);
        if (response.status == 200) {
          this.api_loading = false;
          this.$toasterNA("green", this.$tr("successfully_updated"));
          this.selected_labels = [];

          if (this.subsystem_name == "advertisement") {
            this.$root.$emit("apply_label_changes", response.data.labels);
          } else {
            this.$emit("applyLabelChanges", response.data.labels);
          }

          this.toggleDialog();
        }
      } catch (error) {
        console.log("error", error);
        this.api_loading = false;
      }
    },
    setSelectedLabels() {
      this.selected_labels = [];
      let selectItem = this.selected_item;
      if (this.selected_item[0].labels) {
        selectItem = this.selected_item[0].labels;
      }
      selectItem.forEach((row) => {
        this.selected_labels.push(row.id);
      });
    },
  },
  components: { NoLabel },
};
</script>

<style scoped>
.dialog-title {
  font-size: 20px;
  font-weight: 600;
  color: #777;
}
.custom-chip {
  color: #777;
  padding: 5px 5px;
  border-radius: 20px;
  border: solid 1.5px rgb(212, 212, 212);
  cursor: pointer;
}
.custom-chip.selected {
  background: var(--v-primary-base);
  color: var(--bg-white);
  border: unset;
}
.label-color {
  display: inline-block;
  width: 23px;
  height: 23px;

  border-radius: 50px;
}
.custom-chip.selected .label-color {
  border: 2.3px solid var(--bg-white);
  width: 25px !important;
  height: 25px !important;
}
.custom-card-title-2 {
  font-size: 16px;
  font-weight: 400;
  color: #777;
}
.custom-wraper {
  height: 500px;
  overflow-y: auto;

  scroll-behavior: smooth;
}
.custom-btn {
  font-size: 14px;
  font-weight: 400;
}
</style>
<style>
#custom-scroll::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}
#custom-scroll::-webkit-scrollbar {
  width: 10px;
  background-color: var(--v-surface-base);
}
#custom-scroll::-webkit-scrollbar:hover {
  cursor: alias !important;
}
#custom-scroll::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

#custom-scroll::-webkit-scrollbar-thumb {
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-background-darken2);
}
</style>
