<template>
  <v-dialog v-model="model" persistent width="1100" scrollable>
    <v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
      <v-card-title
        class=""
        style="border-bottom: 1px solid var(--v-surface-darken2)"
      >
        <v-app-bar-title>
          <span class="">{{ $tr("product View") }}</span>
        </v-app-bar-title>
        <v-spacer />
        <CloseBtn @click="closeDialog()" />
      </v-card-title>

      <v-card-text class="pa-0">
        <div class="d-flex" style="min-height: 550px">
          <div class="white" style="width: 30%; height: 100%">
            <div class="">
              <div class="" style="width: 100%">
                <CustomCarousel
                  :items="uploadedFiles"
                  class="pb-2"
                  :height="'200px'"
                  :show_arrows="true"
                  :cycle="false"
                  :rounded="false"
                  :show-delimiters="false"
                  :show_arrows_on_hover="true"
                  ref="CarouselRef"
                />
              </div>
              <div class="d-flex align-center justify-space-between">
                <p
                  class="mb-0 ps-1"
                  style="font-size: 16px; font-weight: 500; opacity: 0.8"
                >
                  {{ $tr("images") }}
                </p>
                <div class="d-flex align-center" v-if="!edit_image">
                  <BackgroundButton
                    @click="editImage()"
                    :icon="editIcon"
                    :iconType="'svg'"
                  />
                </div>
                <div class="d-flex align-center" v-else>
                  <input
                    ref="uploader"
                    class="d-none"
                    type="file"
                    @change="onFileChanged"
                  />
                  <BackgroundButton
                    :color="'blue'"
                    :icon="'mdi-upload'"
                    @click="uploadImage()"
                  />
                  <v-divider vertical class="mx-1"></v-divider>
                  <BackgroundButton
                    :color="'red'"
                    :icon="'mdi-close'"
                    class="me-1"
                    @click="closeEdit()"
                  />
                  <BackgroundButton
                    :icon="'mdi-check'"
                    :loading="isUpdateImage"
                    @click="saveImage()"
                  />
                </div>
              </div>

              <div
                class="d-flex flex-wrap"
                style="
                  width: 100%;
                  padding: 4px;
                  max-height: 260px;
                  overflow-y: auto;
                "
              >
                <div
                  v-for="(item, index) in uploadedFiles"
                  :key="index + 'i'"
                  class="d-flex mt-1 uploaded-img rounded"
                  style=""
                >
                  <v-img
                    class="
                      rounded
                      cursor-pointer
                      d-flex
                      align-center
                      justify-center
                    "
                    :src="item.path"
                    @click="$refs.CarouselRef.current_index = index"
                  >
                    <div class="d-flex justify-center">
                      <v-avatar
                        color="#9e9e9e57"
                        size="28"
                        v-if="edit_image == false"
                      >
                        <svg
                          v-if="$refs.CarouselRef?.current_index == index"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 512 512"
                          style="width: 15px; height: 15px; color: white"
                        >
                          <path
                            fill="currentColor"
                            d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"
                          />
                        </svg>
                      </v-avatar>
                      <v-progress-circular
                        v-else
                        :size="35"
                        :value="item.progress"
                        :color="
                          item.isUploading ? 'primary' : 'blue-grey lighten-3'
                        "
                        :rotate="-90"
                        width="4"
                      >
                        <v-btn
                          class="blue-grey lighten-3"
                          style="height: 28px; width: 28px"
                          fab
                          x-small
                          @click="deleteFileBackend(item)"
                        >
                          <v-icon dark color="white" size="20">
                            mdi-close
                          </v-icon>
                        </v-btn>
                      </v-progress-circular>
                    </div>
                  </v-img>
                </div>
              </div>
            </div>
          </div>
          <!-- main content -->
          <div class="" style="width: 70%; height: 100%; background: #f7f8fa">
            <tab-skew
              :tabs="tabs"
              ref="tabRefs"
              @tabChange="tabChange($event)"
              class="ms-2"
            ></tab-skew>
            <div class="ms-2 white">
              <v-tabs-items v-model="active_tab">
                <v-tab-item>
                  <v-card
                    flat
                    class="rounded-0 h-100"
                    style="min-height: 480px"
                  >
                    <v-card-text
                      class="white"
                      style="overflow-y: auto; width: 100%; max-height: 490px"
                    >
                      <div v-for="(item, index) in properties" :key="index">
                        <div
                          class="w-100 d-flex align center rounded px-2"
                          :style="`min-height: 50px; background:${
                            index % 2 == 0 ? '#f9f9f9' : ''
                          } `"
                        >
                          <div
                            class="d-flex align-center"
                            style="
                              width: 200px;
                              font-size: 16px;
                              font-weight: 400;
                            "
                          >
                            {{ $tr(item.name) }}
                          </div>

                          <div
                            class="d-flex align-center"
                            style="
                              font-size: 16px;
                              font-weight: 500;
                              width: 100%;
                            "
                            v-if="
                              profile_data[item.name] ||
                              profile_data[item.name] >= 0
                            "
                          >
                            <div
                              v-if="edited_index == index && active_tab == 0"
                              class="w-full"
                            >
                              <ProductEditForm
                                :property="item"
                                :profile_data="profile_data"
                                @close="edited_index = null"
                              ></ProductEditForm>
                            </div>
                            <v-hover v-slot="{ hover }" v-else>
                              <div class="w-full">
                                <div
                                  class="
                                    d-flex
                                    align-center
                                    justify-space-between
                                    w-full
                                  "
                                >
                                  <div>
                                    {{
                                      $tr(
                                        item.type == "string" && !item.attribute
                                          ? profile_data[item.name]
                                          : profile_data[item.name]
                                          ? profile_data[item.name][
                                              item.attribute
                                            ]
                                          : "N/A"
                                      )
                                    }}
                                  </div>

                                  <div class="">
                                    <BackgroundButton
                                      @click="editForm(index)"
                                      v-if="hover && !item.not_edit"
                                      :icon="editIcon"
                                      :iconType="'svg'"
                                    />
                                  </div>
                                </div>
                              </div>
                            </v-hover>
                          </div>
                        </div>
                      </div>
                    </v-card-text>
                  </v-card>
                </v-tab-item>

                <v-tab-item>
                  <v-card
                    flat
                    class="rounded-0 white"
                    style="min-height: 480px"
                  >
                    <v-card-text
                      class="white"
                      style="
                        max-height: 490px;
                        overflow: hidden;
                        overflow-y: auto;
                      "
                    >
                      <v-expansion-panels class="mt-2">
                        <v-expansion-panel
                          v-for="(inventory, index) in inventories"
                          :key="index"
                        >
                          <v-expansion-panel-header class="pa-1 px-2">
                            <template v-slot:actions>
                              <v-icon color="primary"
                                >mdi-chevron-down-circle-outline
                              </v-icon>
                            </template>
                            <div
                              class="d-flex align-center justify-space-between"
                            >
                              <div class="d-flex align-center">
                                <v-avatar color="primary" size="32">
                                  <v-icon color="white" size="20">
                                    mdi-warehouse
                                  </v-icon>
                                </v-avatar>

                                <p
                                  class="mb-0 ps-2 text-capitalize"
                                  style="
                                    font-size: 16px;
                                    font-weight: 600;
                                    opacity: 0.7;
                                  "
                                >
                                  {{ $tr("inventroy") }}
                                </p>
                              </div>

                              <v-btn
                                color="error"
                                icon
                                @click.stop="
                                  confirmDelation(inventory.id, index)
                                "
                                :loading="deleting"
                                ><v-icon>mdi-delete</v-icon></v-btn
                              >
                            </div>
                          </v-expansion-panel-header>
                          <v-expansion-panel-content class="px-1">
                            <p
                              class="mb-1"
                              style="
                                opacity: 0.5;
                                font-size: 17px;
                                font-weight: 500;
                              "
                            >
                              {{ $tr("general_info") }}
                            </p>
                            <div
                              v-for="(item, index) in inventory_properties"
                              :key="index"
                            >
                              <div
                                class="w-100 d-flex align center rounded px-2"
                                :style="`min-height: 50px; background:${
                                  index % 2 == 0 ? '#f9f9f9' : ''
                                } `"
                              >
                                <div
                                  class="d-flex align-center"
                                  style="
                                    width: 200px;
                                    font-size: 16px;
                                    font-weight: 400;
                                  "
                                >
                                  {{ $tr(item.name) }}
                                </div>

                                <div
                                  class="d-flex align-center"
                                  style="
                                    font-size: 16px;
                                    font-weight: 500;
                                    width: 100%;
                                  "
                                  v-if="
                                    inventory[item.name] ||
                                    inventory[item.name] >= 0
                                  "
                                >
                                  <div
                                    v-if="
                                      edited_index == index && active_tab == 1
                                    "
                                    class="w-full"
                                  >
                                    <ProductEditForm
                                      :property="item"
                                      :profile_data="inventory"
                                      edit-type="inventory"
                                      @close="edited_index = null"
                                    ></ProductEditForm>
                                  </div>
                                  <v-hover v-slot="{ hover }" v-else>
                                    <div class="w-full">
                                      <div
                                        class="
                                          d-flex
                                          align-center
                                          justify-space-between
                                          w-full
                                        "
                                      >
                                        <div>
                                          {{
                                            $tr(
                                              item.type == "string"
                                                ? inventory[item.name]
                                                : inventory[item.name][
                                                    item.attribute
                                                  ]
                                            )
                                          }}
                                        </div>

                                        <div class="">
                                          <BackgroundButton
                                            @click="editForm(index)"
                                            v-if="hover && !item.not_edit"
                                            :icon="editIcon"
                                            :iconType="'svg'"
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </v-hover>
                                </div>
                              </div>
                            </div>
                          </v-expansion-panel-content>
                        </v-expansion-panel>
                      </v-expansion-panels>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
              </v-tabs-items>
            </div>
          </div>
        </div>
      </v-card-text>
      <v-card-actions
        class="d-flex justify-end"
        style="border-top: 1px solid var(--v-surface-darken2)"
      >
        <v-btn outlined color="primary" @click="closeDialog">
          {{ $tr("cancel") }}
        </v-btn>
        <v-btn class="primary" @click="closeDialog">{{ $tr("Done") }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
   
   <script>
import BackgroundButton from "../../common/buttons/BackgroundButton.vue";
import CustomCarousel from "../../common/CustomCarousel.vue";
import TabSkew from "../../common/TabSkew.vue";
import ContentTabs from "../../content-library/content-library-page/ContentTabs.vue";
import CloseBtn from "../../design/Dialog/CloseBtn.vue";
import ProductEditForm from "./ProductEditForm.vue";

export default {
  components: {
    CloseBtn,
    CustomCarousel,
    ContentTabs,
    TabSkew,
    ProductEditForm,
    BackgroundButton,
  },
  props: {
    currentTab: Object,
  },
  data() {
    return {
      tabs: ["general_info"],
      model: false,
      active_tab: 0,
      profile_data: {},
      properties: [
        { name: "code", type: "string", not_edit: true },
        { name: "arabic_name", type: "string" },
        { name: "name", type: "string" },
        { name: "arabic_description", type: "string" },
        { name: "cost_per_unit", type: "string" },
        { name: "parent_sku", type: "string" },
        { name: "pcode", type: "string" },
        { name: "brand", type: "object", attribute: "name" },
        { name: "category", type: "object", attribute: "name" },
        { name: "description", type: "string" },
        { name: "status", type: "string", not_edit: true },
        { name: "quantity", type: "string" },
        { name: "unit", type: "string" },
        { name: "is_published", type: "string" },
      ],
      inventory_properties: [
        { name: "sku", type: "string" },
        { name: "quantity", type: "string" },
        { name: "price_per_unit", type: "string" },
      ],
      inventories: [],
      editIcon: `<svg xmlns="http://www.w3.org/2000/svg" width="14.453" height="13.821" viewBox="0 0 14.453 13.821">
          <path id="edit-icon" d="M7.227,13.821a.75.75,0,1,1,0-1.5H13.7a.75.75,0,0,1,0,1.5ZM.22,13.6a.751.751,0,0,1-.2-.713l.719-2.878a.755.755,0,0,1,.2-.348l9-9a2.276,2.276,0,1,1,3.219,3.219l-9,9a.746.746,0,0,1-.348.2L.932,13.8a.749.749,0,0,1-.712-.2ZM11,1.727,2.147,10.576,1.781,12.04l1.464-.365,8.849-8.849a.777.777,0,0,0-1.1-1.1Z" fill="currentColor"/>
        </svg>`,
      edited_index: null,
      deleting: false,
      edit_image: false,
      isUpdateImage: false,
      uploading: false,
      uploadedFiles: [],
      deletedPath: [],
      allPath: [],
    };
  },
  computed: {},
  methods: {
    showViewDialog(items) {
      this.uploadedFiles = [];
      this.allPath = [];
      this.model = !this.model;
      this.profile_data = items;
      this.uploadedFiles = items.attachments;
      this.allPath = items.attachments.map((i) => i.path);
      this.inventories = items.inventory_ssp || [];
    },
    async closeDialog() {
      this.edited_index = null;
      this.resetAll();
      this.model = !this.model;
    },
    tabChange(index) {
      this.active_tab = index;
    },
    editForm(index) {
      this.edited_index = index;
    },

    confirmDelet() {
      console.log("delete");
    },

    confirmDelation(id, index) {
      this.$TalkhAlertNA(
        "Are you sure to delete inventory?", //text
        "delete", //icon
        () => this.onDelete(id, index), // accept callback functions
        "yes_delete",
        "swal_remove_text"
      );
    },
    async onDelete(id, index) {
      this.deleting = true;
      let res = await this.$axios.delete("single-sales/products-ssp/" + id, {
        params: {
          type: "inventroy",
        },
      });
      this.deleting = false;
      if (res.status == 200) {
        this.inventories.splice(index, 1);
      }
    },
    editImage() {
      this.edit_image = true;
    },
    async saveImage() {
      this.isUpdateImage = true;
      try {
        if (this.allPath.length > 0) {
          let data = {
            path: this.allPath,
            editType: "updateFile",
          };
          let res = await this.$axios.put(
            "single-sales/products-ssp/" + this.profile_data.id,
            data
          );
          if (res.status == 200) {
            this.uploadedFiles = this.uploadedFiles.map((item) => {
              if (item.new_path) {
                delete item.new_path;
              }
              return item;
            });
          }
          this.$emit("updateFile", res.data.updateData);
        }
        this.resetAll();
        this.edit_image = false;
        this.isUpdateImage = false;
      } catch (error) {
        this.isUpdateImage = false;
      }
    },
    uploadImage() {
      this.$refs.uploader.click();
    },

    onFileChanged(e) {
      const files = e.target.files;
      let temp = [];
      for (let i = 0; i < files?.length; i++) {
        const file = files[i];
        const data = {
          isUploading: true,
          file: file,
          progress: 0,
          path: URL.createObjectURL(file),
        };
        this.uploadedFiles.unshift(data);
        temp.push(data);
      }

      temp.forEach((file) => {
        this.upload(file);
      });
    },

    async upload(item) {
      try {
        this.uploading = true;
        const formData = new FormData();
        formData.append("file", item.file);
        formData.append("system_name", "test");
        let source = this.$axios.CancelToken.source();
        item.source = source;
        let config = {
          cancelToken: source.token,
          onUploadProgress: function (progressEvent) {
            let uploadProgress =
              parseInt(
                Math.round((progressEvent.loaded / progressEvent.total) * 100)
              ) - 5;

            this.updateProgress(item.path, uploadProgress);
          }.bind(this),
        };

        const response = await this.$axios.post(
          "/upload-to-cloud",
          formData,
          config
        );

        if (response.status == 200) {
          this.updateProgress(item.path, 100, response.data.path);
        } else {
          console.log("error");
        }
      } catch (e) {
        console.error("error", e);
      }
      this.uploading = false;
    },

    updateProgress(prev_path, progress, new_path = "") {
      this.uploadedFiles = this.uploadedFiles.map((item) => {
        if (item.path == prev_path) {
          item.progress = progress;
          if (new_path != "") {
            item.new_path = new_path;
            item.isUploading = false;
            this.allPath.unshift(new_path);
          }
        }
        return item;
      });
    },

    async deleteFileBackend(item) {
      try {
        if (item.new_path) {
          this.uploadedFiles = this.uploadedFiles.filter(
            (i) => i.new_path != item.new_path
          );
          let res = await this.$axios.delete("delete-upload-to-cloud", {
            params: { path: item.new_path },
          });
          if (res.data.result) {
            this.allPath = this.allPath.filter((i) => i != item.new_path);
          } else {
            console.log("error");
          }
        } else {
          this.uploadedFiles = this.uploadedFiles.filter(
            (i) => i.path != item.path
          );
          this.deletedPath.push(item.path);
          this.allPath = this.allPath.filter((i) => i != item.path);
        }
      } catch (e) {
        console.log("errrror", e);
      }
    },
    closeEdit() {
      this.resetAll();
      this.edit_image = false;
    },
    resetAll() {
      this.uploadedFiles = this.uploadedFiles.filter((item) => {
        if (item.new_path) {
          this.$axios.delete("delete-upload-to-cloud", {
            params: { path: item.new_path },
          });
        }
        return !item.new_path;
      });
      this.edit_image = false;
      this.isUpdateImage = false;
      this.uploading = false;
      this.deletedPath = [];
      this.allPath = [];
    },
  },
};
</script>
<style scoped>
.custome .v-tab {
  border-bottom: 1px solid var(--v-surface-darken1);
  border-top: 1px solid transparent;
}
.customTab.v-tab {
  max-width: unset !important;
}
.customTab {
  display: flex;
  justify-content: space-between;
  border-top-right-radius: 8px;
  border-top-left-radius: 8px;
  position: relative;
  background-color: var(--v-primary-base);
  border-top: 0.2px solid var(--v-surface-lighten1);
  border-left: 0.2px solid var(--v-surface-lighten1);
  width: 100%;
}
.customTab::before {
  background-color: unset !important;
}
.customTab:after {
  content: " ";
  position: absolute;
  display: block;
  width: 30px;
  height: 100%;
  top: 0;
  right: 0;
  z-index: -1;
  background-color: var(--v-primary-base);
  border-top-right-radius: 8px;
  border-top-left-radius: 8px;
  transform-origin: top right;
  -ms-transform: skew(25deg, 0);
  -webkit-transform: skew(25deg);
  transform: skew(25deg);
  border-right: 0.2px solid var(--v-surface-lighten1);
}
.activeClass:after {
  background-color: var(--v-surface-lighten1);
  border-right: 0.2px solid var(--v-tabBackground-darken2);
}

.activeClass {
  background-color: var(--v-surface-lighten1);
  color: var(--v-primary-base) !important;
  border-top: 0.2px solid var(--v-tabBackground-darken2);
  border-left: 0.2px solid var(--v-tabBackground-darken2);
}

.uploaded-img {
  border: 2px solid #d9e0f4;
  width: 98px;
  height: 98px;
  margin-left: 3px;
  margin-right: 3px;
}
</style>

   