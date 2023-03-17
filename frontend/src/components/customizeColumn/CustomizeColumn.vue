<template>
  <v-card class="rounded-0" elevation="1" width="100%">
    <v-card-title primary-title class="px-2 py-1">
      <span
        ><v-icon class="me-1">fa-line-columns</v-icon>
        {{ $tr("customize_columns") }}</span
      >
      <v-spacer />
      <span @click="closeDialog()" style="cursor: pointer">
        <svg
          width="48px"
          height="48px"
          viewBox="0 0 700 560"
          fill="currentColor"
          style="transform: scaleY(-1)"
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
      </span>
    </v-card-title>
    <v-divider class="mt-2" />

    <v-container fluid class="pt-1 pb-0">
      <v-row justify="center">
        <v-col cols="12" md="8" sm="12" class="pt-0 pb-2">
          <v-text-field
            outlined
            v-model="search"
            dense
            prepend-inner-icon="mdi-magnify"
            :placeholder="$tr('search')"
            hide-details="auto"
            @keyup="searchColumn"
            @click:prepend-inner="searchColumn"
            class="custom_search_textfield"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4" class="pa-0">
          <v-card class="mx-auto rounded-0">
            <div class="scroll">
              <v-list-item
                v-for="item in category"
                :key="item.id"
                :class="item.selected ? 'grey lighten-4' : ''"
                class="pe-1"
                dense
              >
                <v-list-item-content class="py-0">
                  <v-list-item-title class="d-flex align-center">
                    <span
                      class="custom_hover"
                      :class="
                        item.selected && $vuetify.theme.dark
                          ? 'black--text'
                          : ''
                      "
                      @click="filterByCategory(item)"
                      style="font-size: 1.1rem !important"
                      >{{ $tr(item.text) }}</span
                    >
                    <v-chip
                      label
                      color="grey darken-2 text-subtitle-2"
                      class="ms-2"
                      outlined
                      text-color="grey darken-1"
                      small
                    >
                      {{ item.value }}
                    </v-chip>
                    <v-icon
                      class="ms-auto"
                      v-if="item.selected"
                      color="grey darken-2"
                      >mdi-chevron-right</v-icon
                    >
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" md="4" class="pa-0">
          <v-card class="mx-auto rounded-0">
            <div class="scroll">
              <div class="d-flex justify-spacebetw">
                <div class="ps-2 py-1">
                  <strong>{{ $tr(title) }}</strong>
                </div>
                <div class="ms-auto me-2 py-1" v-if="show_select_all">
                  <v-icon
                    dense
                    v-if="this.header.length == this.categoryLength"
                    style="cursor: pointer"
                    color="primary"
                    @click="unSelectAllByCategory()"
                    >mdi-checkbox-marked
                  </v-icon>
                  <v-icon
                    dense
                    v-else
                    style="cursor: pointer"
                    @click="selectAllByCategory()"
                    color="primary"
                    >mdi-checkbox-blank-outline
                  </v-icon>
                  <strong
                    style="cursor: pointer"
                    class="ps-1"
                    v-if="this.header.length == this.categoryLength"
                    @click="unSelectAllByCategory()"
                  >
                    {{ $tr("select_all") }}
                  </strong>
                  <strong
                    style="cursor: pointer"
                    class="ps-1"
                    v-else
                    @click="selectAllByCategory()"
                  >
                    {{ $tr("select_all") }}
                  </strong>
                </div>
              </div>
              <v-divider />
              <v-list>
                <v-list-item
                  v-for="item in header"
                  :key="item.id"
                  class="custom-item"
                >
                  <v-list-item-action>
                    <v-icon
                      dense
                      v-if="item.select"
                      color="primary"
                      @click="toggleHeader(item)"
                      >mdi-checkbox-marked
                    </v-icon>
                    <v-icon
                      dense
                      v-else
                      color="primary"
                      @click="toggleHeader(item)"
                      >mdi-checkbox-blank-outline
                    </v-icon>
                  </v-list-item-action>
                  <v-list-item-content class="py-0">
                    <v-list-item-title>
                      <span
                        @click="toggleHeader(item)"
                        style="font-size: 1.1rem !important"
                        >{{ $tr(item.text) }}</span
                      >
                      <v-tooltip top color="primary" max-width="220px">
                        <template v-slot:activator="{ on, attrs }">
                          <v-icon small color="primary" v-bind="attrs" v-on="on"
                            >mdi-information
                          </v-icon>
                        </template>
                        <span>some information about column</span>
                      </v-tooltip>
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" md="4" class="pa-0">
          <v-card class="mx-auto rounded-0">
            <div class="scroll">
              <div class="px-3 py-1">
                <strong class="mb-0" v-if="selected_columns.length > 1">
                  {{ $tr("columns_selected", selected_columns.length) }}
                </strong>
                <strong class="mb-0" v-else>
                  {{ $tr("columns_selected", selected_columns.length) }}
                </strong>
              </div>
              <draggable
                v-model="selected_header"
                group="people"
                @start="drag = true"
                @end="drag = false"
                animation="200"
                class="draggable-list"
                ghostClass="ghost"
              >
                <v-list-item
                  v-for="item in selected_header"
                  :key="item.id"
                  class="
                    draggable-item
                    rounded
                    mx-3
                    px-1
                    mb-2
                    custom-border
                    d-flex
                    justify-space-between
                    align-center
                    py-2
                  "
                  dense
                  style="cursor: pointer"
                >
                  <v-icon>mdi-menu</v-icon>
                  <p class="mb-0 ms-auto" style="font-size: 1.1rem">
                    {{ $tr(item.text) }}
                  </p>
                  <span
                    @click="toggleHeader(item)"
                    class="ms-auto d-flex align-center"
                    ><SvgIcon closeIcon="true"
                  /></span>
                  <!-- <v-icon  class="ms-auto"   @click="toggleHeader(item)"> mdi-close-circle-outline </v-icon> -->
                </v-list-item>
              </draggable>
            </div>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="6" class="py-1">
          <v-form ref="form" class="d-flex align-center">
            <v-menu
              offset-y
              v-model="menu"
              max-width="270px"
              max-height="500px"
            >
              <template v-slot:activator="{ attrs }">
                <v-btn
                  color="primary"
                  v-bind="attrs"
                  v-on:click="menu = !menu"
                  outlined
                  style="
                    border: #2e7be6 solid 1px !important;
                    padding: 1.15rem 1.6rem;
                    margin: 0 0.2rem;
                    border-radius: 5px !important;
                  "
                  small
                  class="text-subtitle-2 me-2"
                >
                  {{ $tr("shared_views") }}
                  <v-icon class="ms-1" v-if="menu">mdi-chevron-up</v-icon>
                  <v-icon class="ms-1" v-else>mdi-chevron-down</v-icon>
                </v-btn>
              </template>
              <v-list>
                <v-list-item
                  v-if="shared_data.length < 1 && personal_data.length < 1"
                >
                  {{ $tr("no_custom_view_saved") }}
                </v-list-item>
                <template
                  v-for="(item, index) in personal_data.concat(shared_data)"
                >
                  <v-subheader
                    v-if="index == 0 ? true : false"
                    :key="index + 1100"
                    v-text="prev_setting ? $tr('my_view') : $tr('shared_views')"
                  ></v-subheader>
                  <v-divider v-if="index > 0" :key="index" inset />
                  <template>
                    <v-list-item :key="index + 1233" style="cursor: pointer">
                      <v-list-item-avatar
                        @click="applySharedSetting(item.columns)"
                      >
                        <v-img :src="item.user.image" />
                      </v-list-item-avatar>

                      <v-list-item-content
                        @click="applySharedSetting(item.columns)"
                      >
                        <v-list-item-title
                          v-html="
                            item.user.firstname + ' ' + item.user.lastname
                          "
                        />
                        <v-list-item-subtitle v-html="item.name" />
                        <span v-if="index < personal_data.length">
                          <v-list-item-subtitle
                            v-html="'share'"
                            v-if="item.scope_type"
                          />
                          <v-list-item-subtitle v-else v-html="'personal'" />
                        </span>
                      </v-list-item-content>
                      <v-list-item-action
                        v-if="index < personal_data.length"
                        @click="deleteSetting(item.id)"
                      >
                        <v-icon>mdi-delete</v-icon></v-list-item-action
                      >
                    </v-list-item>
                  </template>

                  <v-subheader
                    v-if="
                      index == personal_data.length - 1 &&
                      shared_data.length > 0
                        ? true
                        : false
                    "
                    :key="index + 134"
                    v-text="$tr('shared_views')"
                  ></v-subheader>
                </template>
              </v-list>
            </v-menu>
            <!-- <v-select label="View" :items="[1,2,3]" outlined dense hide-details class="me-1" style="min-width:120px"></v-select> -->
            <div class="custom-input">
              <v-text-field
                :placeholder="$tr('view_name')"
                outlined
                dense
                hide-details
                v-model.trim="$v.view_name.$model"
                class="me-3 pa-0"
              ></v-text-field>
            </div>
            <v-radio-group
              v-model="scope_type"
              class="me-3 mt-0 pt-0"
              dense
              hide-details="auto"
            >
              <v-radio :label="$tr('shared')" :value="1" class="mb-0"></v-radio>
              <v-radio :label="$tr('personal')" :value="0" class=""></v-radio>
            </v-radio-group>

            <TextButton class="me-2" @click="submit" :text="$tr('submit')" />
          </v-form>
        </v-col>
        <v-col
          cols="12"
          md="6"
          class="d-flex justify-md-end justify-sm-start align-center py-1"
        >
          <TextButton @click="saveChanges(true)" :text="$tr('save_changes')" />
          <TextButton
            type="secondary"
            class="ms-2"
            :text="$tr('cancel')"
            @click="closeDialog()"
          >
          </TextButton>
        </v-col>
      </v-row>
    </v-container>
    <ProgressBar v-if="showProgressBar" />
  </v-card>
</template>

<script>
import draggable from "vuedraggable";
import { maxLength, minLength, required } from "vuelidate/lib/validators";
import { mapGetters, mapActions } from "vuex";
import CloseDialogButton from "../common/buttons/CloseDialogButton.vue";
import TextButton from "../common/buttons/TextButton.vue";
import ProgressBar from "../common/ProgressBar.vue";
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import SvgIcon from "../common/SvgIcons/Icons.vue";
import ColumnHelper from "~/helpers/column-helper";
import Alert from "~/helpers/alert";

export default {
  name: "CustomizeColumn",
  components: {
    draggable,
    ProgressBar,
    TextButton,
    CloseDialogButton,
    CloseBtn,
    SvgIcon,
  },
  props: {
    custom_columns: {
      type: Array,
      require: false,
      default: [],
    },
    tableHeader: {
      type: Array,
      require: true,
    },
    page_name: {
      type: String,
      require: true,
    },
    categoryHeader: {
      type: Array,
      require: true,
    },
  },
  data() {
    return {
      //
      company_id: "19e01087-5b53-477d-90eb-00a558128bf0",
      categoryLength: 0,
      selected_category_id: 0,
      showProgressBar: false,
      search: "",
      view_name: "",
      scope_type: 0,
      header: [],
      category: [],

      temp_header: [],
      selected_header: [],
      selected_columns: this.custom_columns,
      title: "all",
      menu: false,
      show_select_all: true,
    };
  },
  validations: {
    view_name: { required, minLength: minLength(2) },
  },
  async created() {
    if (process.client) {
      this.category = this.categoryHeader;
      await this.getselectedHeader();
      this.getCategoryLength(this.selected_category_id);
      if (this.prev_setting) {
        this.scope_type = this._.clone(this.view_scope);
      }
    }
  },
  computed: {
    ...mapGetters({
      view_id: "customColumns/get_view_id",
      prev_setting: "customColumns/get_prev_setting",
      setting_name: "customColumns/get_setting_name",
      view_scope: "customColumns/get_view_scope",
      shared_data: "customColumns/get_shared_setting",
      personal_data: "customColumns/get_personal_setting",
    }),
  },
  methods: {
    ...mapActions({
      fetchCustomColumns: "customColumns/fetchCustomColumns",
      deleteSettings: "customColumns/deleteSetting",
    }),
    async deleteSetting(id) {
      await Alert.confirmAlert(
        this,
        () => this.confirmDelete(id),
        "",
        "warning",
        "are_you_sure"
      );
    },

    async confirmDelete(id) {
      this.showProgressBar = true;
      const response = await this.deleteSettings(id);
      if (response) {
        // 				this.$toasterNA("green", this.$tr('successfully_deleted'));

				this.$toasterNA("green", this.$tr('successfully_deleted'));

        await this.fetchCustomColumns({ view_name: this.page_name });
        this.selectAllByCategory();
      }
      this.showProgressBar = false;
    },

    applySharedSetting(columns) {
      if (process.client) {
        this.selected_columns = this._.cloneDeep(columns);
      }
      this.header.forEach((row) => {
        if (row.id != 1) {
          row.select = false;
        }
      });
      this.selected_header = [];
      this.getselectedHeader();
      this.getCategory();
      this.saveChanges();
    },

    async getCategory() {
      this.category = await ColumnHelper.getCategoryValue(
        this.selected_header,
        this.category
      );
    },
    selectAllByCategory() {
      this.header.forEach((row) => {
        if (!row.select && row.id != 1) {
          this.selected_columns.push(row.id);
          row.select = true;
          this.selected_header.push(row);
          this.category.forEach((item) => {
            row.category_id == item.category_id ? item.value++ : "";
            item.category_id == 0 ? item.value++ : "";
          });
          this.getCategoryLength(this.selected_category_id);
        }
      });
    },
    unSelectAllByCategory() {
      this.header.forEach((row) => {
        if (row.select && row.id != 1) {
          this.selected_columns = this.selected_columns.filter(
            (col) => col != row.id
          );
          row.select = false;
          this.selected_header = this.selected_header.filter(
            (col) => col.id != row.id
          );
          this.category.forEach((item) => {
            row.category_id == item.category_id ? item.value-- : "";
            item.category_id == 0 ? item.value-- : "";
          });
          this.getCategoryLength(this.selected_category_id);
        }
      });
    },

    // get category length from header
    getCategoryLength(id) {
      this.categoryLength =
        id == 0
          ? this.temp_header.filter((row) => row.select == true).length
          : this.temp_header.filter(
              (row) => row.category_id == id && row.select == true
            ).length;
    },

    // filter header by category
    filterByCategory(param) {
      this.search = "";
      this.selected_category_id = param.category_id;
      this.header = this.temp_header;
      if (param.category_id == 0) {
        this.header = this.temp_header;
      } else {
        this.header = this.header.filter(
          (item) => item.category_id == param.category_id
        );
      }
      this.title = param.text;
      this.getCategoryLength(param.category_id);
      this.category.forEach((row) => {
        if (row.category_id == this.selected_category_id) {
          row.selected = true;
        } else {
          row.selected = false;
        }
      });
    },
    closeDialog() {
      this.$emit("closeDialog");
    },
    saveChanges(closeForm = false) {
      // this.selected_columns = this.selected_header.map(row=>row.id);
      this.$emit("saveChanges", {
        selected_columns: this.selected_header.map((row) => row.id),
        selected_header: this.selected_header,
        close_form: closeForm,
      });
    },

    // insertElement() {
    //   let array = [1, 2, 3, 4, 5];

    //   // index to add to
    //   let index = 3;

    //   // element that you want to add
    //   let element = 8;

    //   array.splice(index, 0, element);
    // },

    toggleHeader(item) {
      if (!this.selected_columns.includes(item.id)) {
        this.selected_columns.push(item.id);
        const i = this.header.indexOf(item);
        this.header[i].select = true;
        this.selected_header.push(item);
        this.category.forEach((element) => {
          if (this.header[i].category_id == element.category_id) {
            element.value++;
          }
          if (element.category_id == 0) {
            element.value++;
          }
        });

        this.getCategoryLength(this.selected_category_id);
      } else {
        if (item.id != 1) {
          const j = this.selected_header.indexOf(item);
          this.selected_columns = this.selected_columns.filter(
            (e) => e != item.id
          );
          const i = this.temp_header.indexOf(item);
          if (i) {
            this.temp_header[i].select = false;
            this.selected_header.splice(j, 1);
            this.category.forEach((element) => {
              if (this.temp_header[i].category_id == element.category_id) {
                element.value--;
              }
              if (element.category_id == 0) {
                element.value--;
              }
            });
          }
        }
        this.getCategoryLength(this.selected_category_id);
      }
    },

    getselectedHeader() {
      const response = ColumnHelper.getselectedHeader(
        this.tableHeader,
        this.selected_columns
      );
      this.selected_header = response.selected_header;
      this.header = response.header;
      this.temp_header = response.header;
    },

    searchColumn() {
      this.header = this.temp_header.filter((item) => {
        return item.text.toLowerCase().includes(this.search.toLowerCase());
      });
      this.show_select_all = false;
      this.title = "search_result";

      if (this.search == "") {
        this.show_select_all = true;
        this.header = this.temp_header;
        this.title = "all";
        this.selected_category_id = 0;
        this.category.forEach((row) => {
          if (row.category_id == this.selected_category_id) {
            row.selected = true;
          } else {
            row.selected = false;
          }
        });
      }
    },

    async submit() {
      if (this.personal_data.length == 3) {
        // this.$toastr.w(this.$tr("you_cant_have_more_than_three_setting"));
        this.$toasterNA("orange", this.$tr("you_cant_have_more_than_three_setting"));

        return;
      }
      this.showProgressBar = true;
      this.$refs.form.validate();
      this.$v.$touch();
      if (!this.$v.$invalid) {
        const data = this.personal_data.find(
          (row) => row.name == this.view_name
        );
        if (data) {
          await Alert.confirmAlert(
            this,
            () => {
              this.updateView();
            },
            "do_you_want_to_replace_the_setting",
            "warning",
            this.$tr("setting_already_exist_in_this_name")
          );
        } else {
          await this.saveView();
        }
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
			this.$toasterNA("red",this.$tr("please_fill_all_fields_correctly"));
        
      }
      this.showProgressBar = false;
    },

    async saveView() {
      try {
        const data = {
          name: this.view_name,
          page_name: this.page_name,
          columns: this.selected_header.map((row) => row.id),
          scope_type: this.scope_type,
          company_id: this.company_id,
        };
        const response = await this.$axios.post("view_name", data);
        if (response.status == 200) {
          this.resetForm();
          this.saveChanges();
          // this.$toastr.s(this.$tr("saved_successfully", this.$tr("view")));
				this.$toasterNA("green", this.$tr('saved_successfully'));

          await this.fetchCustomColumns({ view_name: this.page_name });
        }
      } catch (error) {
        if (error.response.status == 422) {
          const errors = error.response.data.errors;
          for (let error in errors) {
            // this.$toastr.e(this.$tr(errors[error]));
			      this.$toasterNA("red",this.$tr(errors[error]));

          }
        }
      }
      // this.ProgressBar = false;
    },

    async updateView() {
      try {
        const data = {
          name: this.view_name,
          page_name: this.page_name,
          columns: this.selected_header.map((row) => row.id),
          scope_type: this.scope_type,
        };
        const response = await this.$axios.put("view_name/id", data);
        if (response.data) {
          // this.$toastr.s(this.$tr("updated_successfully", this.$tr("view")));
				this.$toasterNA("green", this.$tr('updated_successfully'));

          await this.fetchCustomColumns({ view_name: this.page_name });
          this.resetForm();
          this.saveChanges();
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red",this.$tr("something_went_wrong"));

        }
      } catch (error) {
        if (error.response.status == 422) {
          const errors = error.response.data.errors;
          for (let error in errors) {
            // this.$toastr.e(this.$tr(errors[error]));
			this.$toasterNA("red",this.$tr(errors[error]));

          }
        }
      }
    },

    resetForm() {
      this.$refs.form.reset();
      this.$v.view_name.$reset();
      this.view_name = "";
      this.scope_type = 0;
    },

    // validation rules
    viewNameRule(name) {
      return [
        (_) =>
          name.required || this.$tr("item_is_required", this.$tr("view_name")),
        (_) =>
          name.minLength || this.$tr("min_length", this.$tr("view_name"), "2"),
      ];
    },
  },
};
</script>

<style scoped>
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
div.scroll {
  width: 100%;
  max-height: 350px !important;
  height: 350px;
  overflow-y: scroll !important;
}
div.custom-border {
  border: 1px solid #757575;
  height: 20px !important;
}
.custom-item {
  cursor: pointer;
  height: 20px !important;
  min-height: 30px !important;
  margin-bottom: 0.3rem !important;
}
.custom-input {
  min-width: 15rem;
}
.custom_hover:hover {
  text-decoration: underline !important;
  cursor: pointer !important;
}
/* .custom_search_textfield {
	box-shadow: 0px 4px 0px var(--v-primary-base);
} */
</style>

<style>
.custom_search_textfield fieldset {
  border-bottom: var(--v-primary-base) 3.5px solid !important;
}

.custom_search_textfield input {
  padding-left: 20px !important;
}
</style>

<style scoped>
.v-tooltip__content::after {
  content: " ";
  position: absolute;
  top: 100%; /* At the bottom of the tooltip */
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: var(--v-primary-base) transparent transparent transparent;
}
</style>
