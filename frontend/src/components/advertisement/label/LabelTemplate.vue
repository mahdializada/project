<template>
  <div class="px-2">
    <div
      id="custom-scroll"
      class="custom-wraper pe-1"
      style="position: relative; height: 600px; overflow-y: auto"
    >
      <span class="dialog-title"> {{ $tr("labels") }}</span>

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
                  :class="selected_labels.includes(label.id) ? 'selected' : ''"
                  v-for="(label, index) in category.label"
                  :key="index"
                  @click="selectLabel(label.id)"
                >
                  <div
                    class="
                      label-color
                      me-1
                      white--text
                      text-center text-uppercase
                    "
                    :style="'background-color:' + label.color"
                  >
                    {{ label.label.charAt(0) }}
                  </div>
                  <span style="line-height: 10px">{{ label.label }}</span>
                  <v-icon
                    small
                    v-html="
                      selected_labels.includes(label.id)
                        ? 'mdi-minus'
                        : 'mdi-plus'
                    "
                    style="color: inherit; line-height: 5px"
                    class="mx-1"
                  ></v-icon>
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
    <div class="d-flex justify-end">
      <v-btn
        class="primary custom-btn"
        style=""
        @click="saveChanges()"
        :loading="api_loading"
      >
        {{ $tr("save") }}
      </v-btn>
    </div>
  </div>
</template>

<script>
import NoLabel from "./NoLabel.vue";

export default {
  data() {
    return {
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
    model_name: {
      type: String,
      default: "content_library",
    },
    selected_item: {
      type: Object,
      require: true,
    },
    subsystem_name: {
      type: String,
      default: "Content Library",
    },
  },
  methods: {
    fetchLabels() {
      console.log("label coalled");
      this.getAssignedLabels();
      this.getLabels();
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
            tab: "new",
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
          default:
            target_id = "id";
            break;
        }
        const data = {
          selected_labels: this.selected_labels,
          model_name: this.model_name,
          id: this.selected_item[target_id],
        };
        const response = await this.$axios.post("labels-assign", data);
        if (response.status == 200) {
          this.api_loading = false;
          this.$toasterNA("green", this.$tr("successfully_updated"));
        }
      } catch (error) {
        console.log("error");
        this.api_loading = false;
      }
    },
    setSelectedLabels() {
      this.selected_labels = [];
      let selectItem = [];
      // if (this.selected_item?.labels) {
      //   selectItem = this.selected_item?.labels;
      // }
      selectItem.forEach((row) => {
        this.selected_labels?.push(row.id);
      });
    },
    async getAssignedLabels() {
      try {
        const response = await this.$axios.get("get-assigned-label", {
          params: {
            id: this.selected_item.id,
          },
        });
        this.selected_labels = response.data;
      } catch (error) {
        console.log("error", error);
      }
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