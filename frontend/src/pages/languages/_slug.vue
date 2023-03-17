<template>
  <v-container fluid class="pa-0">
    <v-dialog v-model="columnDialog" persistent width="1300" class="rounded-0">
      <CustomizeColumn
        @closeDialog="columnDialog = false"
        @saveChanges="saveColumnSetting($event)"
        :tableHeader="$_.cloneDeep(headers)"
        :custom_columns="$_.cloneDeep(selected_column)"
        :categoryHeader="$_.cloneDeep(category)"
        :key="dialogKey"
        page_name="translation"
      />
    </v-dialog>
    <!-- Language Bulk Upload -->
    <v-dialog v-model="bulkUpload" width="1300" v-if="$isInScope('lnc')">
      <Dialog persistent max-width="1300" @closeDialog="bulkUpload = false">
        <LanguageBulkUpload
          :base_language="baseLanguage"
          :language_fetched="languageFetched"
          v-if="bulkUpload"
          @fetchNewData="fetchNewData"
        />
      </Dialog>
    </v-dialog>
    <v-dialog
      v-model="translateDialog"
      width="1300"
      persistent
      v-if="$isInScope('lnc')"
    >
      <Dialog @closeDialog="toggleTranslate">
        <Translate
          :rtl="
            (languageFetched.direction ? languageFetched.direction : '') ==
            'rtl'
              ? true
              : false
          "
          v-if="translateDialog"
          :data="getNotTranslatedData()"
          ref="addLanguage"
          @close="translateDialog = false"
          :language="languageFetched"
          @insertRecord="insertRecord"
        />
      </Dialog>
    </v-dialog>
    <!-- language filter dialog -->
    <v-dialog
      v-model="languageFilter"
      persistent
      max-width="1300"
      width="1300"
      v-if="$isInScope('lnv')"
    >
      <LanguageFilter
        @close="onFilter"
        v-if="languageFilter"
        :prevFilterData="filterData"
        @getRecord="getRecord"
      />
    </v-dialog>
    <v-dialog
      v-model="editDialog"
      width="1300"
      persistent
      v-if="$isInScope('lnu')"
    >
      <Dialog @closeDialog="toggleEdit">
        <Translate
          :rtl="
            (languageFetched.direction ? languageFetched.direction : '') ==
            'rtl'
              ? true
              : false
          "
          v-if="editDialog"
          :data="this.selectedItems"
          ref="addLanguage"
          @close="editDialog = false"
          :language="languageFetched"
          edit
          @insertRecord="insertRecord"
        />
      </Dialog>
    </v-dialog>
    <v-dialog
      v-model="autoEditDialog"
      width="1300"
      persistent
      v-if="$isInScope('lnu')"
    >
      <Dialog @closeDialog="toggleAutoEdit">
        <Translate
          :rtl="
            (languageFetched.direction ? languageFetched.direction : '') ==
            'rtl'
              ? true
              : false
          "
          v-if="autoEditDialog"
          :data="this.selectedItems"
          ref="addLanguage"
          @close="autoEditDialog = false"
          :language="languageFetched"
          autoEdit
          @insertRecord="insertRecord"
        />
      </Dialog>
    </v-dialog>

    <v-row no-gutters>
      <ProgressBar v-if="showProgress" />
      <v-col cols="12">
        <PageHeader
          :Icon="
            languageFetched
              ? languageFetched.country_language.country.iso2.toLowerCase()
              : ''
          "
          flag
          :Title="
            languageFetched
              ? `${languageFetched.country_language.language.native} (${languageFetched.country_language.country.name})`
              : ''
          "
          :Breadcrumb="breadcrumb"
        >
          <template slot="title-button">
            <v-menu offset-x>
              <template v-slot:activator="{ on: menu, attrs }">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on: tooltip }">
                    <v-btn
                      color="primary"
                      text
                      v-bind="attrs"
                      v-on="{ ...tooltip, ...menu }"
                    >
                      <v-icon>fa-angle-double-right</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ $tr("change_language") }}</span>
                </v-tooltip>
              </template>
              <v-list>
                <v-list-item
                  dense
                  v-for="(item, index) in selectLanguages"
                  :key="index"
                >
                  <nuxt-link class="link" :to="`/languages/${item.id}`">
                    <v-list-item-title>
                      {{ item.name }}
                    </v-list-item-title>
                  </nuxt-link>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
        </PageHeader>
      </v-col>
      <v-col cols="12">
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="download_header"
          :downloadContent="download_content"
          :downloadDialog="downloadDialog"
          :filename="$tr('countries')"
          :show-add-note="true"       
          :show-bulk-upload="true"
          :showFilter="true"
          :showDownload="$isInScope('lne')"
          note-title="add_country_note"
          @unSelect="unSelect"
          @search="searchPhrases"
          @searchAgain="searchAgain"
          @onFilter="onFilter"
          @onDownload="toggleDownload"
          @onColumn="
            () => {
              dialogKey++;
              columnDialog = true;
            }
          "
          @onBulkUpload="onBulkUpload"
        >
          <CustomButton
            v-if="$isInScope('lnc')"
            @click="toggleTranslate"
            icon="fa-language"
            text="translate"
            type="primary"
          />
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :selectedItems="selectedItems"
          :tab-key.sync="tabItems[tabIndex].key"
          @onEdit="toggleEdit"
          @onAutoEdit="toggleAutoEdit"
          :reasons="[]"
          :showReasonDialog="false"
          :showTracing="false"
          :showView="false"
          :showBlock="false"
          :showSelect="false"
          :showApply="false"
          :showDelete="false"
          :showEdit="$isInScope('lnu')"
          :showAutoEdit="$isInScope('lnu')"
        />
      </v-col>
      <v-col cols="12">
        <DataTable
          :key="tableKey"
          v-model="selectedItems"
          :headers="selected_header"
          :ItemsLength="phrases.length"
          :items="tableData"
          :loading="apiCalling"
          @click:row="onRowClicked"
          @onTablePaginate="onTableChanges"
          ref="countryTableRef"
        >
          <template slot="customHeader">
            <v-row class="mx-0">
              <v-col cols="12 pa-0">
                <v-tabs
                  v-model="tabIndex"
                  height="40"
                  background-color="primary"
                  active-class="active-background"
                  show-arrows
                  dark
                >
                  <client-only>
                    <v-tab v-for="(item, i) in tabItems" :key="i" class="px-3">
                      <span
                        :class="`${
                          item.isSelected ? 'selected' : 'not-selected'
                        } tab-icon`"
                      >
                        <v-icon left small class="me-1">{{ item.icon }}</v-icon>
                      </span>
                      <p
                        :class="`${
                          item.isSelected ? 'selected' : 'not-selected'
                        } tab-title text-capitalize mb-0`"
                      >
                        {{ $tr(item.text) }}
                      </p>
                      <v-chip
                        class="ms-1 tab-chip"
                        :color="item.isSelected ? 'primary' : 'white'"
                        :text-color="item.isSelected ? 'white' : 'primary'"
                        small
                        v-text="getTotal(item.key)"
                      />
                    </v-tab>
                  </client-only>
                </v-tabs>
              </v-col>
            </v-row>
          </template>
          <template v-slot:[`item.id`]="{ item }">
            <span class="mx-1 primary--text font-weight-bold">
              {{ item.id }}
            </span>
          </template>
        </DataTable>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import HandleException from "~/helpers/handle-exception";
import { mapActions, mapGetters } from "vuex";
import Translations from "~/configs/pages/language_single";
import ProgressBar from "~/components/common/ProgressBar";
import PageHeader from "~/components/design/PageHeader";
import PageFilters from "~/components/design/PageFilters";
import PageActions from "~/components/design/PageActions";
import DataTable from "~/components/design/DataTable";
import CustomButton from "~/components/design/components/CustomButton";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn.vue";
// devMaster
import Dialog from "~/components/design/Dialog/Dialog.vue";
import Translate from "~/components/languages/Translate.vue";

import LanguageFilter from "~/components/languages/Language_singleFilter";
import LanguageBulkUpload from "../../components/languages/LanguageBulkUpload";
import ColumnHelper from "~/helpers/column-helper";

export default {
  meta: {
    hasAuth: true,
    scope: "lnv",
  },
  async asyncData({ params, store, error }) {
    await store.dispatch("customColumns/fetchCustomColumns", {
      view_name: "translation",
    });
    await store.dispatch("languages/fetchLanguages", {
      key: "all",
      itemsPerPage: -1,
    });
    await store.dispatch("languages/fetchBaseTranslations", {
      itemsPerPage: -1,
      base_language: true,
    });
    let languageFetched = await store.dispatch("languages/fetchLanguage", {
      fetchByCode: true,
      id: params.slug,
    });
    if (languageFetched == false) {
      error({ statusCode: 404 });
    }
    await store.dispatch("languages/fetchPhrases", {
      itemsPerPage: -1,
    });
    await store.dispatch("languages/fetchTranslations", {
      itemsPerPage: -1,
      language_id: languageFetched.id,
    });
    return { slug: params.slug, languageFetched };
  },

  async created() {
    //customize columns
    if (process.client) {
      const response = await ColumnHelper.getselectedHeader(
        this.headers,
        this.custom_columns
      );
      this.selected_header = response.selected_header;
      this.selected_column = this.selected_header.map((row) => row.id);
      this.category = await ColumnHelper.getCategoryValue(
        this.selected_header,
        this.category
      );
    }

    this.setData();
    this.setHeaders();

    this.selectLanguages = this.languages.map((lang) => {
      return {
        id: lang.code,
        name: `${lang.country_language.language.name} (${lang.country_language.country.name})`,
      };
    });
    this.selectedLanguage = this.slug;
    this.$root.$on("getData", (data) => {
      this.filterData = data;
    });
  },

  components: {
    Dialog,
    Translate,
    ProgressBar,
    currentItems: [],
    CustomButton,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    CustomizeColumn,
    LanguageFilter,
    LanguageBulkUpload,
  },
  data() {
    return {
      category: Translations(this).category,
      headers: Translations(this).headers,
      breadcrumb: Translations(this).breadcrumb,
      tabItems: Translations(this).tabItems,

      tableData: [],
      allData: [],

      selectedLanguage: "",
      selectLanguages: [],

      // devmaster
      translateDialog: false,
      editDialog: false,
      autoEditDialog: false,

      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      statuses: ["active", "blocked"],

      // page actions
      downloadDialog: false,
      download_header: [],
      download_content: [],

      // for custom columns
      dialogKey: 0,
      columnDialog: false,

      selected_column: [],
      selected_header: [],
      showProgressBar: false,
      // Bulk upload flag

      uploadDialog: false,
      files: null,
      bulkData: [],
      type: 1,
      content: [],
      contentData: "",
      languageFilter: false,
      filterData: "",
      bulkUpload: false,
    };
  },
  computed: {
    ...mapGetters({
      languages: "languages/getLanguages",
      tranlations: "languages/getTranslations",
      baseTranlations: "languages/getBaseTranslations",
      baseLanguage: "languages/getBaseLanguage",
      translatedLanguage: "languages/getTranslatedLanguage",
      phrases: "languages/getPhrases",
      apiCalling: "languages/isApiTranslationCalling",
      custom_columns: "customColumns/get_custom_columns",
      getTranslations2: "translations/getTranslations",
    }),
  },
  methods: {
    ...mapActions({
      fetchLanguages: "languages/fetchLanguages",
      fetchLanguage: "languages/fetchLanguage",
      fetchTranslations: "languages/fetchTranslations",
      fetchBaseTranslations: "languages/fetchBaseTranslations",
      fetchPhrases: "languages/fetchPhrases",
      fetchTranslations2: "languages/fetchTranslations",
    }),
    async fetchNewData() {
      await this.fetchTranslations2({
        itemsPerPage: -1,
        language_id: this.languageFetched.id,
      });
      this.setData();
      this.setHeaders();
    },
    onBulkUpload() {
      this.bulkUpload = !this.bulkUpload;
    },

    findArr(arr, phrase) {
      let item = arr.find((arrItem) => {
        return arrItem.phrase_id == phrase;
      });
      return item != undefined ? item : false;
    },

    getTotal(key) {
      switch (key) {
        case "all":
          return this.allData.length;
        case "translated":
          return this.getTranslatedData().length;
        case "notTranslated":
          return this.getNotTranslatedData().length;
      }
    },

    setData() {
      this.allData = [];
      this.phrases.forEach((phrase) => {
        let base_lang_word = this.findArr(this.baseTranlations, phrase.id);
        let lang_word = this.findArr(this.tranlations, phrase.id);
        this.allData.push({
          id: phrase.id,
          code: phrase.code,
          phrase: phrase.phrase,
          created_by: lang_word.created_by,
          updated_by: lang_word.updated_by,
          base_lang_word,
          lang_word,
          word_type: phrase.word_type,
        });
        this.tableData = this.allData;
      });
    },

    setHeaders() {
      this.headers.forEach((header) => {
        if (header.id == 3) {
          header.text = `${this.baseLanguage.country_language.language.native} (${this.baseLanguage.country_language.country.name})`;
        } else if (header.id == 4) {
          header.text = `${this.translatedLanguage.country_language.language.native} (${this.translatedLanguage.country_language.country.name})`;
        }
      });
    },

    flterData(key) {
      switch (key) {
        case "all":
          this.tableData = this.allData;
          break;
        case "translated":
          this.tableData = this.getTranslatedData();
          break;
        case "notTranslated":
          this.tableData = this.getNotTranslatedData();
          break;
      }
    },

    getTranslatedData() {
      return this.allData.filter((trans) => {
        return trans.lang_word;
      });
    },

    getNotTranslatedData() {
      return this.allData.filter((trans) => {
        return trans.lang_word == false;
      });
    },

    insertRecord(record) {
      this.allData = this.allData.map((data) => {
        if (data.id == record.phrase_id) {
          data.lang_word = record;
        }
        return data;
      });
    },

    async toggleTranslate() {
      this.translateDialog = !this.translateDialog;
    },

    async toggleEdit() {
      this.editDialog = !this.editDialog;
    },
    async toggleAutoEdit() {
      this.autoEditDialog = !this.autoEditDialog;
    },

    checkSelectedTab(value) {
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    async toggleDownload() {
      this.download_header = [
        { id: 1, text: "Code", value: "code" },
        { id: 2, text: "Phrase", value: "phrase" },
        { id: 3, text: "Base Language", value: "base_lang" },
        { id: 4, text: "Language Word", value: "lang_word" },
        { id: 7, text: "Created By", value: "created_by" },
        { id: 8, text: "Updated By", value: "updated_by" },
      ];
      this.showProgressBar = true;
      this.download_content = await this.tableData.map((row) => {
        const item = {
          code: row.id,
          phrase: row?.phrase,
          base_lang: row?.base_lang_word?.translation,
          lang_word: row?.lang_word?.translation,
          updated_by: row?.updated_by,
          created_by: row?.created_by,
        };
        return item;
      });
      this.showProgressBar = false;
      this.downloadDialog = !this.downloadDialog;
    },

    onTableChanges(options) {
      let data = options;
      data.key = this.getTabKey();
      data.language_id = this.languageFetched.id;
    },

    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
    },

    toggleShowProgress() {
      this.showProgress = !this.showProgress;
    },

    onSearch(term) {
      this.searchQuery = term;
    },
    onFilter() {
      this.languageFilter = !this.languageFilter;
    },
    async searchPhrases() {
      if (this.type == 1) {
        if (this.content.length) {
          this.content.forEach((element) => {
            let newData = this.allData.filter((deps) => deps?.code == element);
            if (newData.length) {
              this.tableData = this.tableData.filter(
                (data) => data?.code !== newData[0].code
              );
              this.tableData.unshift(newData[0]);
              this.selectedItems?.unshift(newData[0]);
              this.$refs.countryTableRef.selected?.unshift(newData[0]);
            } else {
              this.$refs.pageFilterRef.clearInput();
            }
          });
        }
      } else if (this.type == 0) {
        if (this.contentData) {
          this.tableData = this.allData.filter(
            (data) =>
              data?.phrase
                .toLowerCase()
                .includes(this.contentData.toLowerCase()) ||
              data?.base_lang_word.translation
                .toLowerCase()
                .includes(this.contentData.toLowerCase()) ||
              data?.lang_word.translation
                .toLowerCase()
                .includes(this.contentData.toLowerCase())
          );
        }
      }
    },

    searchAgain() {
      this.tableData = this.allData;
    },
    getRecord() {
      if (
        this.filterData?.phrase ||
        this.filterData?.baseLanguage ||
        this.filterData?.translation ||
        this.filterData?.ids.length > 0 ||
        this.filterData?.created_date
      ) {
        let ifTrue = false;
        if (this.filterData.phrase) {
          ifTrue = true;
          this.tableData = this.allData.filter((data) =>
            data?.phrase
              .toLowerCase()
              .includes(this.filterData.phrase.toLowerCase())
          );
        }
        if (this.filterData.baseLanguage) {
          if (ifTrue) {
            this.tableData = this.tableData.filter((data) =>
              data?.base_lang_word.translation
                .toLowerCase()
                .includes(this.filterData.baseLanguage.toLowerCase())
            );
          } else {
            this.tableData = this.allData.filter((data) =>
              data?.base_lang_word.translation
                .toLowerCase()
                .includes(this.filterData.baseLanguage.toLowerCase())
            );
          }
        }
        if (this.filterData.translation) {
          if (ifTrue) {
            this.tableData = this.tableData.filter((data) =>
              data?.lang_word.translation
                .toLowerCase()
                .includes(this.filterData.translation.toLowerCase())
            );
          } else {
            this.tableData = this.allData.filter((data) =>
              data?.lang_word.translation
                .toLowerCase()
                .includes(this.filterData.translation.toLowerCase())
            );
          }
        }
        if (this.filterData?.ids.length > 0) {
          if (ifTrue) {
            if (this.filterData.include == 0) {
              let result = [];
              this.filterData.ids.forEach((element) => {
                this.tableData = this.tableData.filter(
                  (data) => data.code == element
                );
                if (this.tableData[0]?.code) {
                  result.push(this.tableData[0]);
                }
              });
              this.tableData = result;
            } else {
              this.filterData.ids.forEach((element) => {
                this.tableData = this.tableData.filter(
                  (data) => data.code !== element
                );
              });
            }
          } else {
            if (this.filterData.include == 0) {
              let result = [];
              this.filterData.ids.forEach((element) => {
                this.tableData = this.allData.filter(
                  (data) => data.code == element
                );
                if (this.tableData[0]?.code) {
                  result.push(this.tableData[0]);
                }
              });
              this.tableData = result;
            } else {
              this.filterData.ids.forEach((element) => {
                this.tableData = this.allData.filter(
                  (data) => data.code !== element
                );
              });
            }
          }
        }
        if (this.filterData?.created_date) {
          let startDate = "";
          let endDate = "";
          if (this.filterData?.created_date.length == 1) {
            startDate = this.filterData?.created_date[0];
            endDate = null;
          } else {
            if (this.filterData?.created_date[0]) {
              startDate = this.filterData?.created_date[0];
              endDate = this.filterData?.created_date[1];
            } else {
              endDate = this.filterData?.created_date[1];
              let x = this.filterData?.created_date[1].substring(
                0,
                this.filterData?.created_date[1].length - 2
              );
              startDate = x + "01";
            }
          }
          let start = new Date(startDate);
          let end = start;
          if (endDate) {
            end = new Date(endDate);
          }
          if (start > end) {
            let temp = start;
            start = end;
            end = temp;
          }
          this.tableData = this.allData.filter((data) => {
            let date = data.lang_word.created_at.substring(
              0,
              data.lang_word.created_at.length - 9
            );
            date = new Date(date);
            if (date >= start && date <= end) {
              return data;
            }
          });
        }
      } else {
        this.tableData = this.allData;
      }
    },

    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },
    //customize columns: called from child
    saveColumnSetting(data) {
      this.columnDialog = false;
      this.selected_header = data.selected_header;
      this.selected_column = data.selected_header.map((row) => row.id);
      this.category = ColumnHelper.getCategoryValue(
        data.selected_header,
        this.category
      );
    },
    unSelect(key) {
      this.selectedItems = this.selectedItems.filter(
        (data) => data.code !== key
      );
    },
  },
  watch: {
    tabIndex: function (index) {
      this.checkSelectedTab(index);
      this.selectedItems = [];
      this.flterData(this.getTabKey());
    },
  },
};
</script>
<style scoped>
.link {
  color: var(--input-title-color) !important;
  text-decoration: none !important;
}
</style>
