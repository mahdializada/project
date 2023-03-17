<template>
     <v-card class=" pb-3" elevation="1" width="100%">
          <v-card-title class="px-2 pt-2 pb-0">
               <span class="dialog-title ps-1"> {{ $tr("System Tab") }}</span>
               <v-spacer />
               <svg @click="closeDialog()" width="40px" height="40px" viewBox="0 0 700 560" fill="currentColor"
                    style="transform: scaleY(-1); cursor: pointer">
                    <g>
                         <path
                              d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z" />
                         <path
                              d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z" />
                    </g>
               </svg>
          </v-card-title>
          <v-container fluid class="pt-0 pb-0">
               <v-row class="px-2">

                    <!-- for columns  -->
                    <v-col cols="12" md="5" class="px-1">
                         <v-card elevation="5" height="450px" class="py-1 ps-3 position-relative">
                              <p class="mb-0 custom-card-title-1 pt-1">{{ $tr("Tabs") }}</p>


                              <div class="search" :class="$vuetify.rtl ? 'rtl' : ''">
                                   <SearchBtn @searchHeader="
                                     (event) => {
                                       search = event;
                                     }
                                   " :placeholder="'search'" />
                              </div>

                              <div class="me-1 mt-2 list-card" id="custom-scroll">
                                   <v-list-item dense class="me-2 custom-column-list2 selected px-0 align-center"
                                        color="primary" v-for="(header, index) in 10" :key="index"
                                        @click="toggleHeader(header, index)">

                                        <v-list-item-content>
                                             <v-list-item-title class="custom-card-title-3 "
                                                  style="line-height:25px;z-index: 33333;"
                                                  v-html="$tr('header texyt')" />
                                        </v-list-item-content>

                                        <v-list-item-action>
                                             <!-- checkSelectedHeader(header.id) -->
                                             <div class="d-flex align-center">
                                                  <v-icon color="primary" class="ms-1" v-html="
                                                  3>4  ? 'mdi-checkbox-marked'
                                                      : 'mdi-checkbox-blank-outline'
                                                  " small></v-icon>
                                             </div>
                                        </v-list-item-action>
                                   </v-list-item>
                              </div>
                         </v-card>
                    </v-col>
                    <!-- for settings -->
                    <v-col cols="12" md="7" class="px-1">
                         <v-card elevation="5" height="450px" class="py-1 px-3">
                              <p class="mb-2 custom-card-title-1 pt-1">
                                   {{ $tr("tab_arrangement") }}
                              </p>
                              <!-- mdi-pan-vertical -->

                              <div class="mt-2 py-2 list-card bg-custom-gray rounded" style="height: "
                                   id="custom-scroll">
                                   <draggable v-model="all_tab" group="people" @start="() => {}" @end="() => {}"
                                        animation="200" ghostClass="ghostClass" chosenClass="chosenClass"
                                        dragClass="dragClass">
                                        <v-list-item dense class="px-1 mb-1 mx-2 selected-header-list"
                                             v-for="(header, index) in 10" :key="index" :ripple="false">
                                             <v-list-item-content>
                                                  <div class="d-flex">
                                                       <v-icon class="icon-color">mdi-pan-vertical</v-icon>
                                                       <v-list-item-title class="custom-card-title-3 pb-1"
                                                            v-html="$tr('text')" />
                                                  </div>
                                             </v-list-item-content>

                                             <v-list-item-action>
                                                  <v-icon class="ms-1 icon-color" @click="toggleHeader(header)">
                                                       mdi-close-circle-outline</v-icon>
                                             </v-list-item-action>
                                        </v-list-item>
                                   </draggable>
                              </div>
                         </v-card>
                    </v-col>
               </v-row>
               <div class="d-flex">
                    <!-- <div style="width: 60%" class="d-flex align-center">
                         <CSelect :for_column="true" title="" v-model="selected_setting" :items.sync="all_views"
                              :placeholder="$tr('shared_views')" item-text="name" item-value="id" dense :rounded="false"
                              hide-details class="me-1 pt-0" style="padding: 0px !important" :checkbox="false"
                              :menuTitle="$tr('shared_views')" @deleteView="deleteView($event)"
                              @defaultView="defaultView($event)">
                         </CSelect>
                         <CTextField :rounded="false" px="px-0" py="py-0" dense title="" hide-details
                              v-model.trim="$v.view_name.$model" :placeholder="$tr('view_name')"></CTextField>

                         <p class="mb-0 me-2 ms-2">{{ $tr("public") }}</p>
                         <v-switch v-model="scope_type" inset color="primary" class="me-1 mt-0" hide-details></v-switch>

                         <v-btn class="primary custom-btn" style="" @click="submit()" :loading="loading">
                              {{ $tr("submit") }}
                         </v-btn>
                    </div>

                    <v-spacer></v-spacer>
                    <v-btn @click="saveChanges('cancel')" class="me-1 custom-btn" color="primary" outlined>
                         {{ $tr("cancel") }}
                    </v-btn>
                    <PopOver v-model="showPopOver" color="primary" :right="true" :clickOutside="false">
                         <template v-slot:activator>
                              <v-btn class="primary custom-btn" style="" @click="saveChanges()">
                                   {{ $tr("save") }}
                              </v-btn>
                         </template>
                         <template v-slot:body>
                              <div>
                                   {{ $tr("please_save_your_changes_first") }}
                              </div>
                         </template>
                    </PopOver> -->
               </div>
          </v-container>
     </v-card>
</template>
   
<script>
import SearchBtn from "./SearchBtn.vue";
import draggable from "vuedraggable";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
import CSelect from "../new_form_components/Inputs/CSelect.vue";
import { maxLength, minLength, required } from "vuelidate/lib/validators";
import Alert from "~/helpers/alert";
import CustomizeColumnSetting from "./ColumnSetting/CustomizeColumnSetting.vue";
import PopOver from "../design/PopOver.vue";

export default {
     props: {
          tabs: {
               type: Array,
               require: true,
          },
          page_name: {
               type: String,
               require: true,
          },
     },
     components: {
          SearchBtn,
          draggable,
          CTextField,
          CSelect,
          CustomizeColumnSetting,
          PopOver,
     },

     data() {
          return {
               all_tab: [],
               scope_type: false,
               view_name: "",
               loading: false,
               categories: [{ category_name: "all", id: 0 }],
               selected_category_id: 0,
               all_headers: [],
               search: "",
               extra_setting_fetched: false,

               selected_setting: "",
               showPopOver: false,
               changes: false,
               all_views: [],
          };
     },
     validations: {
          view_name: { required, minLength: minLength(2) },
     },
     watch: {
          selected_setting(id) {
               // this.applySharedSetting(id);
          },
     },
     methods: {
          // validation rules
          viewNameRule(name) {
               if (!name.required)
                    return this.$tr("item_is_required", this.$tr("view_name"));
               if (!name.minLength)
                    return this.$tr("min_length", this.$tr("view_name"), "2");
          },
          closeDialog() {
               if (this.changes) {
                    this.showPopOver = true;
                    return;
               }
               this.$emit("closeColumnDialog");
          },
          getCategories() {
               this.categories = [{ category_name: this.$tr("all"), id: 0 }];
               this.all_headers.headers.forEach((col) => {
                    if (
                         !this.categories.find(
                              (row) => JSON.stringify(row) == JSON.stringify(col.category)
                         ) &&
                         col.category
                    ) {
                         this.categories.push(col.category);
                    }
               });
          },
          getHeaders() {
               if (this.search != "") {
                    return this.searchHeader();
               }
               if (this.selected_category_id == 0) {
                    return this.all_headers.headers;
               }
               // filter by category
               return this.all_headers.headers.filter(
                    (row) => row.category_id == this.selected_category_id
               );
          },

          selectCategory(id) {
               this.selected_category_id = id;
          },
          checkSelectedHeader(id) {
               return this.all_headers.selected_headers.find((row) => row.id == id);
          },
          toggleHeader(header, index = 0) {
               if (
                    !this.all_headers.selected_headers.find((row) => row.id == header.id)
               ) {
                    this.all_headers.selected_headers.splice(index, 0, header);
               } else {
                    this.all_headers.selected_headers =
                         this.all_headers.selected_headers.filter(
                              (row) => row.id != header.id
                         );
               }
          },
          getCategoryCount(id) {
               let count = 0;
               let all = 0;
               if (id == 0) {
                    count = this.all_headers.selected_headers.length;
                    all = this.all_headers.headers.length;
                    return { all, count };
               } else {
                    this.all_headers.selected_headers.forEach((header) => {
                         if (header.category_id == id) {
                              count++;
                         }
                    });
                    this.all_headers.headers.forEach((header) => {
                         if (header.category_id == id) {
                              all++;
                         }
                    });
               }
               return { all, count };
          },

          selectAll() {
               const select =
                    this.getCategoryCount(this.selected_category_id).count !=
                    this.getCategoryCount(this.selected_category_id).all;
               if (this.selected_category_id == 0) {
                    if (select) {
                         this.all_headers.selected_headers = this.all_headers.headers;
                    } else {
                         this.all_headers.selected_headers = [];
                    }
                    return;
               }

               this.all_headers.headers.forEach((header) => {
                    if (header.category_id == this.selected_category_id) {
                         if (select) {
                              if (!this.checkSelectedHeader(header.id)) {
                                   this.all_headers.selected_headers.push(header);
                              }
                         } else {
                              this.all_headers.selected_headers =
                                   this.all_headers.selected_headers.filter(
                                        (row) => row.id != header.id
                                   );
                         }
                    }
               });
          },
          async submit() {
               const check = this.all_views.filter(
                    (row) => row.user_id == this.$auth.user.id
               );

               if (this.$v.$invalid) {
                    // this.$toastr.e(this.$tr(this.viewNameRule(this.$v.view_name)));
                    this.$toasterNA("red", this.$tr(this.viewNameRule(this.$v.view_name)));

                    return;
               }
               const data = this.all_views.find(
                    (row) => row.user_id == this.$auth.user.id && row.name == this.view_name
               );
               if (check.length > 2 && !data) {
                    // this.$toastr.e(this.$tr("you_cant_have_more_than_three_setting"));
                    this.$toasterNA("red", this.$tr("you_cant_have_more_than_three_setting"));

                    return;
               }
               if (data) {
                    await Alert.confirmAlert(
                         this,
                         () => {
                              this.finalSubmit(true);
                         },
                         "do_you_want_to_replace_the_setting",
                         "warning",
                         this.$tr("setting_already_exist_in_this_name")
                    );
               } else {
                    await this.finalSubmit(false);
               }
          },
          async finalSubmit(duplicate) {
               this.loading = true;
               try {
                    const data = {
                         name: this.view_name,
                         page_name: this.page_name,
                         columns: this.all_headers.selected_headers.map((row) => row.id),
                         scope_type: this.scope_type,
                    };
                    const response = await this.$axios.post("view_name", data);
                    if (response.status == 200) {
                         this.view_name = "";
                         this.scope_type = false;
                         if (duplicate) {
                              this.all_views = this.all_views.map((row) => {
                                   return row.id == response.data.id ? response.data : row;
                              });
                         } else {
                              this.all_views.unshift(response.data);
                         }
                         // this.$toastr.s(this.$tr("saved_successfully", this.$tr("view")));
                         this.$toasterNA("green", this.$tr('saved_successfully'));

                    }
               } catch (error) {
               }
               this.loading = false;
          },
          saveChanges(type = "save") {
               if (type == "save") {
                    this.changes = false;
                    this.$emit("saveChanges", this.all_headers);
               } else {
                    this.all_headers = JSON.parse(JSON.stringify(this.tabs));
                    this.closeDialog();
               }
          },
          searchHeader() {
               if (this.selected_category_id == 0) {
                    return this.all_headers.headers.filter((row) => {
                         return row.text.toLowerCase().includes(this.search.toLowerCase());
                    });
               } else {
                    const temp_data = this.all_headers.headers.filter(
                         (row) => row.category_id == this.selected_category_id
                    );
                    return temp_data.filter((row) => {
                         return row.text.toLowerCase().includes(this.search.toLowerCase());
                    });
               }
          },
          deleteView(id) {
               Alert.confirmAlert(
                    this,
                    () => this.confirmDelete(id),
                    "",
                    "warning",
                    "are_you_sure"
               );
          },

          async confirmDelete(id) {
               try {
                    const response = await this.$axios.delete("view_name/" + id);
                    if (response.status == 200) {
                         this.all_views = this.all_views.filter((row) => row.id != id);
                         // this.$toastr.s(this.$tr("delete_successfully", this.$tr("view")));
                         this.$toasterNA("green", this.$tr('delete_successfully'));

                    }
               } catch (error) {
               }
          },
          applySharedSetting(id) {
               let columns = this.all_views.filter((row) => row.id == id);
               columns = JSON.parse(columns[0].columns);
               this.all_headers.selected_headers = [];
               columns.forEach((id) => {
                    this.all_headers.headers.forEach((row) => {
                         if (row.id == id) {
                              this.all_headers.selected_headers.push(row);
                         }
                    });
               });
          },

          async fetchExtraSettings() {
               if (this.extra_setting_fetched) return;
               try {
                    const response = await this.$axios.get(
                         "view_name?view_name=" + this.page_name
                    );
                    this.all_views = response.data;
               } catch (error) {
               }
          },
          saveColumnUpdates(data) {
               this.all_headers.headers = this.all_headers.headers.map((row) => {
                    if (row.id == data.id) {
                         return data;
                    }
                    return row;
               });
               this.all_headers.selected_headers = this.all_headers.selected_headers.map(
                    (row) => {
                         if (row.id == data.id) {
                              return data;
                         }
                         return row;
                    }
               );
               this.all_headers = { ...this.all_headers };
               this.getCategories();
               this.changes = true;
          },
          async defaultView(item) {
               let temp_view = JSON.parse(JSON.stringify(this.all_views));
               try {
                    this.all_views = this.all_views.map((row) => {
                         if (row.id == item.id) {
                              return item;
                         }
                         row.default = 0;
                         return row;
                    });

                    const response = await this.$axios.post("view-name-edit", item);
                    if (response.status == 200) {
                         this.all_views = this.all_views.map((row) => {
                              if (row.id == item.id) {
                                   return item;
                              }
                              row.default = 0;
                              return row;
                         });
                    } else {
                         this.all_views = temp_view;
                         // this.$toasterNA("red", this.$tr('something_went_wrong'));
                         this.$toasterNA("red", this.$tr("something_went_wrong"));

                    }
               } catch (error) {
                    this.all_views = temp_view.map((row) => {
                         if (row.id == item.id) {
                              row.default == 0 ? (row.default = 1) : (row.default = 0);
                              return row;
                         }
                         return row;
                    });
                    // this.$toasterNA("red", this.$tr('something_went_wrong'));
                    this.$toasterNA("red", this.$tr("something_went_wrong"));
               }
          },
     },
     created() {
          console.log('tabs', this.tabs);
          // this.all_headers = JSON.parse(JSON.stringify(this.tabs));
          // this.getCategories();
          // this.fetchExtraSettings();
     },
};
</script>
   
<style scoped>
.dialog-title {
     font-size: 20px;
     font-weight: 600;
     color: #777;
}

.custom-card-title-1 {
     font-size: 17px;
     font-weight: 500;
     color: #777;
}

.custom-card-title-2 {
     font-size: 16px;
     font-weight: 400;
     color: #777;
}

.custom-card-title-3 {
     font-size: 15px;
     font-weight: 400;
     color: #777;
}

.custom-btn {
     font-size: 14px;
     font-weight: 400;
}

.list-card {
     height: 380px;
     overflow-y: auto;
}

.custom-category-list {
     border: 1px solid #d8d5d5;
     color: #777;
     border-radius: 5px;
     margin-bottom: 8px;
     cursor: pointer;
}

.custom-category-list.selected {
     border: 1px solid var(--v-primary-base);
     color: var(--v-primary-base);
     box-shadow: 0 0 10px #2e7be633;
}

.custom-category-list.selected * {
     color: var(--v-primary-base);
}

.custom-category-list:hover {
     color: var(--v-primary-base);
     box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
}

.custom-category-list::before {
     background: unset !important;
}

.select-all-btn {
     display: inline-block;
     text-decoration: underline;
     cursor: pointer;
     color: var(--v-primary-base);
     position: absolute;
     top: 17px;
     right: 30px;
     z-index: 1;
     font-weight: 400;
}

.select-all-btn.rtl {
     right: unset !important;
     left: 30px !important;
}

/* for search btn */
.search {
     position: absolute;
     top: 10px;
     right: 15px;
     z-index: 2;
}

.search.rtl {
     right: unset !important;
     left: 15px;
}

.custom-column-list2 {
     border-bottom: 1px solid #eeebeb;
     color: #777;
     cursor: pointer;
}

.custom-column-list2 .custom-card-title-3:hover {
     color: var(--v-primary-base) !important;
}

.custom-column-list2:hover {
     border-bottom-color: var(--v-primary-base) !important;
     color: var(--v-primary-base) !important;
}

.custom-column-list2::before {
     background: unset !important;
}

.tooltip-icon {
     color: #777;
}

.tooltip-icon:hover {
     color: var(--v-primary-base) !important;
}

.selected-header-list {
     color: #777;
     cursor: pointer;
     background: #ffff;
     border-radius: 5px;
     box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
     border-left: 3px solid transparent;
}

.selected-header-list:hover * {
     color: var(--v-primary-base) !important;
}

.selected-header-list:hover {
     box-shadow: 0 0 10px #2e7be633;
}

.custom-column-list2::before {
     background: unset !important;
}

.selected-header-list::before {
     background: unset !important;
}

.ghostClass {
     opacity: 1 !important;
     background: #c8ebfb;
     border-top-left-radius: 0px;
     border-bottom-left-radius: 0px;
     border-left-color: var(--v-primary-base);
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
   
   <!-- for tooltip -->
<style scoped>
.v-tooltip__content::after {
     content: " ";
     position: absolute;
     top: 100%;
     /* At the bottom of the tooltip */
     left: 50%;
     margin-left: -5px;
     border-width: 5px;
     border-style: solid;
     border-color: var(--v-primary-base) transparent transparent transparent;
}

.v-tooltip__content {
     opacity: 1 !important;
}
</style>
   