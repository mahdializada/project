const historyState = {
  history: {
    parent: null,
    currentPage: {
      sortBy: "name",
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

export const state = () => ({
  fetchingItems: false,
  isSearching: false,
  ...historyState,
});

export const getters = {
  folders: ({ history }) => history.currentPage.folders,
  files: ({ history }) => history.currentPage.files,
  breadcrumb: ({ history }) => history.currentPage.breadcrumb,
  parent: ({ history }) => history.parent,
  company: ({ history }) => history.currentPage.company,
  loading: ({ fetchingItems }) => fetchingItems,
  total: ({ history }) => history.currentPage.total_items,
  page: ({ history }) => history.currentPage.page,
  paginateFinished: ({ history }) => {
    const { files, folders } = history.currentPage;
    const items = [...files, ...folders];
    return history.currentPage.total_items == items.length;
  },
};

export const actions = {
  // paginate company items
  async paginateCompanyItems({ commit, getters }) {
    try {
      let { files, folders, company, paginateFinished, page } = getters;
      if (paginateFinished) {
        return;
      }
      const currentPage = page + 1;
      const url = `files/library?company_id=${company.id}&page=${currentPage}`;
      const { data } = await this.$axios.get(url);
      const items = data.data;
      const newFolders = [...folders];
      const newFiles = [...files];
      for (let index = 0; index < items.length; index++) {
        const item = items[index];
        item.type == "folder" ? newFolders.push(item) : newFiles.push(item);
      }
      const result = {
        files: newFiles,
        folders: newFolders,
        page: currentPage,
      };
      commit("updateCurrentPage", result);
    } catch (_) {}
  },

  // fetch company items
  async fetchCompanyItems({ commit }, companyId) {
    try {
      commit("toggleLoader");
      const url = `files/library?company_id=${companyId}`;
      const { data } = await this.$axios.get(url);
      const items = data.data;
      const total_items = data.total;
      const company = data.company;
      const folders = [];
      const files = [];
      for (let index = 0; index < items.length; index++) {
        const item = items[index];
        item.type == "folder" ? folders.push(item) : files.push(item);
      }
      const result = {
        files,
        page: 1,
        sortBy: "name",
        folders,
        total_items,
        company,
      };
      commit("updateCurrentPage", result);
    } catch (_) {}
    commit("toggleLoader");
  },
};

export const mutations = {
  resetLibraryStates(state) {
    state.history = { ...historyState };
  },

  toggleLoader(state, value = null) {
    state.fetchingItems = value || !state.fetchingItems;
  },

  updateCurrentPage(
    state,
    { files, folders, total_items, breadcrumb, company, sortBy, page }
  ) {
    state.history.currentPage = {
      ...state.history.currentPage,
      files: files,
      folders: folders,
    };
    if (total_items) {
      state.history.currentPage.total_items = total_items;
    }
    if (breadcrumb) {
      state.history.currentPage.breadcrumb = breadcrumb;
    }
    if (company) {
      state.history.currentPage.company = company;
    }
    if (sortBy) {
      state.history.currentPage.sortBy = sortBy;
    }
    if (page) {
      state.history.currentPage.page = page;
    }
    state.history.parent = state.history.currentPage;
  },
};

export default {
  namespace: true,
  state,
  getters,
  mutations,
  actions,
};
