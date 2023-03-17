<template>
  <div class="files-container d-flex background">
    <div
      :class="`custom-transition library-companies lib-border-right ${
        $vuetify.breakpoint.width < 700
          ? hideCompanies
            ? 'custom-w-0'
            : 'min-w-full'
          : ''
      }`"
    >
      <LibraryCompanies
        @toggleCompanies="toggleCompanies"
        @companyChanged="onCompanyChanged"
      />
    </div>
    <div
      :class="`custom-transition children  ${
        $vuetify.breakpoint.width < 700
          ? !hideCompanies
            ? 'custom-w-0'
            : 'min-w-full'
          : 'w-full'
      }`"
    >
      <div
        :class="$vuetify.breakpoint.width < 700 ? 'custom-min-w-screen' : ''"
      >
        <LibraryTopTitle
          :hideCompanies="$vuetify.breakpoint.width < 700 && hideCompanies"
          @toggleCompanies="toggleCompanies"
          :loading="fetchingItems"
          :company="history.current_page.company"
        />
        <div class="pa-2">
          <RightClickMenu />
          <NuxtChild
            :selectedCompany="selectedCompany"
            :currentPage="history.current_page"
            :loading="fetchingItems"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LibraryCompanies from "../../../components/files/library/LibraryCompanies.vue";
import SearchFiles from "../../../components/files/common/SearchFiles.vue";
import LibraryTopTitle from "../../../components/files/library/LibraryTopTitle.vue";
import RightClickMenu from "~/components/files/common/RightClickMenu";
import { mapActions, mapMutations, mapState } from "vuex";

export default {
  data() {
    return {
      hideCompanies: true,
      selectedCompany: null,
      fetchingItems: false,
      isSearching: false,
      history: {
        current_page: {
          sortBy: "name",
          unFilterdFiles: [],
          unFilterdFolders: [],
          page: 1,
          total_items: 0,
          company: {},
          files: [],
          folders: [],
          breadcrumb: [
            {
              to: "/files/library/drive",
              exact: false,
              text: "library",
              parent: true,
            },
          ],
        },
      },
    };
  },

  computed: {
    ...mapState("files", [
      "uploadedFiles",
      "uploadedFolders",
      "newFolderUploaded",
    ]),
  },

  watch: {
    "$route.params.slug"() {
      this.onRouteChange();
    },
    uploadedFiles(value) {
      if (value[value.length - 1]) {
        let {
          response: { data },
        } = value[value.length - 1];
        this.addFilesOrFolders(data, true);
      }
    },
    newFolderUploaded(value) {
      let folders = this.uploadedFolders[value];
      // let { current_page, parent } = this.history;
      Object.values(folders).forEach((item) => {
        this.addFilesOrFolders(item, false);
      });
    },
  },

  methods: {
    ...mapMutations("files", ["changeLayout"]),
    toggleCompanies() {
      this.hideCompanies = !this.hideCompanies;
    },
    onCompanyChanged(companyId) {
      if (companyId !== this.selectedCompany) {
        this.selectedCompany = companyId;
        this.fetchParentItems(companyId);
      }
    },
    clearFilter() {
      this.resetFilterData();
    },

    resetFilterData() {
      this.history.current_page.files =
        this.history.current_page.unFilterdFiles;
      this.history.current_page.folders =
        this.history.current_page.unFilterdFolders;
    },

    submitFilter(filterData) {
      this.resetFilterData();

      if (filterData.selectedfileTypesId == "any") {
        if (filterData.selecteddateModifiedsId == "any_time") {
          this.resetFilterData();
        } else {
          this.swtichFilters(filterData);
        }
      } else if (filterData.selectedfileTypesId == "folder") {
        if (filterData.selecteddateModifiedsId == "any_time") {
          this.resetFilterData();
        } else {
          this.swtichFilters(filterData);
        }
        this.history.current_page.files = [];
      } else if (
        filterData.selectedfileTypesId == "document" &&
        filterData.selectedfileTypesDataTypeId == "any"
      ) {
        this.history.current_page.folders = [];

        this.swtichFilters(filterData);
        this.history.current_page.files = filterData.selectedfileTypesId
          ? this.history.current_page.files.filter((row) => {
              return (
                row.mime_type.split("/")[0] === "application" ||
                row.mime_type.split("/")[0] === "text"
              );
            })
          : this.history.current_page.files;
      }
      // files filters
      else {
        this.filesFilter(filterData);
      }
    },

    filesFilter(filterData) {
      this.history.current_page.folders = [];
      if (filterData.selectedfileTypesDataTypeId == "any") {
        this.swtichFilters(filterData);
        this.history.current_page.files = filterData.selectedfileTypesId
          ? this.history.current_page.files.filter(
              (row) =>
                row.mime_type.split("/")[0] === filterData.selectedfileTypesId
            )
          : this.history.current_page.files;
      } else {
        this.swtichFilters(filterData);
        this.history.current_page.files = filterData.selectedfileTypesDataTypeId
          ? this.history.current_page.files.filter(
              (row) => row.extension === filterData.selectedfileTypesDataTypeId
            )
          : this.history.current_page.files;
      }
    },

    swtichFilters(val) {
      switch (val.selecteddateModifiedsId) {
        case "custom":
          if (val.enddate == "") {
            this.filterByDate(val.startdate);
          } else {
            this.history.current_page.files =
              this.history.current_page.files.filter((row) => {
                return (
                  this.localeHumanReadableTime(row.created_at) >=
                    this.localeHumanReadableTime(val.startdate) &&
                  this.localeHumanReadableTime(row.created_at) <=
                    this.localeHumanReadableTime(val.enddate)
                );
              });

            this.history.current_page.folders =
              this.history.current_page.folders.filter((row) => {
                return (
                  this.localeHumanReadableTime(row.created_at) >=
                    this.localeHumanReadableTime(val.startdate) &&
                  this.localeHumanReadableTime(row.created_at) <=
                    this.localeHumanReadableTime(val.enddate)
                );
              });
          }
          break;
        case "yesterday":
          var date = new Date();
          date.setDate(date.getDate() - 1);

          this.filterByDate(date);

          break;
        case "today":
          let today = new Date().toLocaleDateString();
          this.filterByDate(today);

          break;
        case "last_seven_day":
          var date = new Date();
          date.setDate(date.getDate() - 7);

          this.history.current_page.files =
            this.history.current_page.files.filter(
              (row) =>
                this.localeHumanReadableTime(row.created_at) >=
                this.localeHumanReadableTime(date)
            );
          this.history.current_page.folders =
            this.history.current_page.folders.filter(
              (row) =>
                this.localeHumanReadableTime(row.created_at) >=
                this.localeHumanReadableTime(date)
            );

          break;
        default:
      }
    },

    filterByDate(date) {
      this.history.current_page.files = this.history.current_page.files.filter(
        (row) =>
          this.localeHumanReadableTime(row.created_at) ===
          this.localeHumanReadableTime(date)
      );
      this.history.current_page.folders =
        this.history.current_page.folders.filter(
          (row) =>
            this.localeHumanReadableTime(row.created_at) ===
            this.localeHumanReadableTime(date)
        );
    },

    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("MMMM Do YYYY");
    },

    refreshDriveItems() {
      const { slug } = this.$route.params;
      if (slug) {
        this.fetchChildItems(slug);
      } else {
        this.fetchParentItems(this.selectedCompany);
      }
      this.history = {
        current_page: { ...this.history.current_page },
      };
    },

    async onSearchPersonalFiles(searchTerm) {
      if (searchTerm) {
        if (this.isSearching) return;
        try {
          this.isSearching = true;
          const { slug } = this.$route.params;
          let url = `files/personal/files/search?searchTerm=${searchTerm}`;
          if (slug) {
            url += `&parent_id=${slug}`;
          }
          const { data } = await this.$axios.get(url);
          const items = data.items;
          const folders = [];
          const files = [];
          const unFilterdFiles = [];
          const unFilterdFolders = [];
          for (let index = 0; index < items.length; index++) {
            const item = items[index];
            if (item.type == "folder") {
              folders.push(item);
              unFilterdFolders.push(item);
            } else {
              files.push(item);
              unFilterdFiles.push(item);
            }
          }

          this.history.current_page = {
            ...this.history.current_page,
            folders,
            files,
            unFilterdFiles,
            unFilterdFolders,
          };
        } catch (_) {
          this.history.current_page = {
            ...this.history.current_page,
            folders: [],
            files: [],
            unFilterdFiles: [],
            unFilterdFolders: [],
          };
        }
        this.isSearching = false;
      } else {
        this.onRouteChange();
      }
    },

    getBreadcrumbItem(index) {
      const { current_page } = this.history;
      const { breadcrumb } = current_page;
      if (breadcrumb) {
        if (Array.isArray(breadcrumb)) {
          return breadcrumb.slice(index)[0];
        }
        return {};
      }
      return {};
    },

    onSortChanged({ title }) {
      const { folders, files } = this.history.current_page;
      let sortedFolders = [];
      let sortedFiles = [];
      if (this.history.current_page.sortBy == title) {
        sortedFolders = folders.reverse();
        sortedFiles = files.reverse();
        this.history.current_page.folders = sortedFolders;
        this.history.current_page.files = sortedFiles;
        return;
      }
      this.history.current_page.sortBy = title;
      if (title == "name") {
        sortedFolders = folders.sort(this.sortByName);
        sortedFiles = files.sort(this.sortByName);
      } else if (title == "size") {
        sortedFolders = folders.sort(this.sortFolderBySize);
        sortedFiles = files.sort(this.sortFileBySize);
      } else if (title == "date_modified") {
        sortedFolders = folders.sort(this.sortFolderByDateModified);
        sortedFiles = files.sort(this.sortFolderByDateModified);
      }
      this.history.current_page.folders = sortedFolders;
      this.history.current_page.files = sortedFiles;
    },

    sortByName(itemA, itemB) {
      const nameA = itemA.name.toLowerCase();
      const nameB = itemB.name.toLowerCase();
      return nameA.localeCompare(nameB);
    },

    sortFileBySize(itemA, itemB) {
      let sizeA = itemA.size;
      let sizeB = itemB.size;
      if (sizeA < sizeB) return 1;
      if (sizeA > sizeB) return -1;
      return 0;
    },

    sortFolderBySize(itemA, itemB) {
      let sizeA = itemA.info.size;
      let sizeB = itemB.info.size;
      if (sizeA < sizeB) return 1;
      if (sizeA > sizeB) return -1;
      return 0;
    },

    sortFolderByDateModified(itemA, itemB) {
      let dateA = new Date(itemA.created_at);
      let dateB = new Date(itemB.created_at);
      if (dateA < dateB) return -1;
      if (dateA > dateB) return 1;
      return 0;
    },

    onRouteChange() {
      const { slug } = this.$route.params;
      if (slug) {
        if (slug in this.history) {
          const prevPage = this.history[slug];
          this.history.current_page = prevPage;
        } else {
          this.fetchChildItems(slug);
        }
      } else {
        this.fetchingItems = true;
        let parentPage = this.history.parent;
        if (parentPage) {
          parentPage.breadcrumb = [
            {
              to: "/files/personal/drive",
              exact: false,
              text: "drive",
            },
          ];

          this.history.current_page = parentPage;
          this.fetchingItems = false;
        } else {
          this.fetchParentItems(this.selectedCompany);
        }
      }
    },

    async fetchChildItems(slug) {
      try {
        this.fetchingItems = true;
        const response = await this.$axios.get(`files/personal/${slug}`);
        if (response.data.not_found) {
          this.history.current_page = {
            ...this.history.current_page,
            folders: [],
            files: [],
            unFilterdFiles: [],
            unFilterdFolders: [],
          };
          this.fetchingItems = false;
          return;
        }
        const items = response.data.files;
        const breadcrumb = response.data.breadcrumb;
        const parent = response.data.parent;
        const folders = [];
        const files = [];
        const unFilterdFiles = [];
        const unFilterdFolders = [];
        for (let index = 0; index < items.length; index++) {
          const item = items[index];
          if (item.type == "folder") {
            folders.push(item);
            unFilterdFolders.push(item);
          } else {
            files.push(item);
            unFilterdFiles.push(item);
          }
        }

        this.history.current_page = {
          ...this.history.current_page,
          folders,
          files,
          unFilterdFiles,
          unFilterdFolders,
          breadcrumb,
          parent,
          sortBy: "name",
        };

        let currentPage = this.history.current_page;
        this.history[slug] = currentPage;
      } catch (_) {
        this.history.current_page = {
          ...this.history.current_page,
          folders: [],
          files: [],
          unFilterdFiles: [],
          unFilterdFolders: [],
          breadcrumb: [
            {
              to: "/files/library/drive",
              exact: false,
              text: "library",
              parent: true,
            },
          ],
        };
      }
      this.fetchingItems = false;
    },

    async fetchParentItems(companyId) {
      try {
        this.fetchingItems = true;
        const url = `files/library?company_id=${companyId}`;
        const { data } = await this.$axios.get(url);
        const items = data.data;
        const total_items = data.total;
        const company = data.company;
        const folders = [];
        const files = [];
        const unFilterdFiles = [];
        const unFilterdFolders = [];
        for (let index = 0; index < items.length; index++) {
          const item = items[index];
          if (item.type == "folder") {
            folders.push(item);
            unFilterdFolders.push(item);
          } else {
            files.push(item);
            unFilterdFiles.push(item);
          }
        }

        this.history.current_page = {
          ...this.history.current_page,
          sortBy: "name",
          folders,
          files,
          page: 1,
          company,
          total_items,
          unFilterdFiles,
          unFilterdFolders,
          parent: null,
          breadcrumb: [
            {
              to: "/files/library/drive",
              exact: false,
              text: "library",
              parent: true,
            },
          ],
        };

        this.history.parent = this.history.current_page;
      } catch (_) {}
      this.fetchingItems = false;
    },

    addFilesOrFolders(data, isFile) {
      let fileOrFolder = isFile ? "files" : "folders";
      let { current_page, parent } = this.history;
      if (data.parent_id == null && current_page.parent == null) {
        current_page[fileOrFolder].unshift(data);
      } else if (data.parent_id == null && current_page.parent !== null) {
        // for history
        parent[fileOrFolder].unshift(data);
      } else if (
        data.parent_id !== null &&
        current_page.parent !== null &&
        data.parent_id == current_page.parent.id
      ) {
        current_page[fileOrFolder].unshift(data);
      } else if (
        current_page.parent !== null &&
        data.parent_id !== current_page.parent.id
      ) {
        // for history
        if (data.parent_id == null) {
          parent[fileOrFolder].unshift(data);
        } else {
          if (this.history[data.parent.id]) {
            this.history[data.parent.id][fileOrFolder].unshift(data);
          }
        }
      }
    },
  },
  components: {
    LibraryCompanies,
    SearchFiles,
    LibraryTopTitle,
    RightClickMenu,
  },
};
</script>
<style>
.library-companies {
  min-width: 240px;
  width: 240px;
}
.custom-transition {
  transition: all 0.4s;
}
.file-top-bar-title {
  font-size: 18px;
  font-weight: 600 !important;
}
.lib-border-right {
  border-right: 1px solid #ddd;
}

.lib-top-bar {
  height: 54px;
}
.custom-w-0 {
  min-width: 0 !important;
  width: 0 !important;
  overflow: hidden;
}
.custom-min-w-screen {
  min-width: 90vw;
}
</style>
