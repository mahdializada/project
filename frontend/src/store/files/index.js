import Personal from "../../utils/file_management/personal";
import Alert from "~/helpers/alert";
/* ================================> States <================================*/
var filters = {
  // Toggle Filter Section
  is_opened: false,

  active_filters: false,

  // File Type Section
  file_type: "any",

  // Owner Section
  owner: "anyone",
  owner_name_email: null,

  // Date Modified Section
  modified: "any_time",
  custom: {
    from: null,
    to: null,
  },

  // Search Section
  search: null,
};

var selectedFiles = [];
var uploadQueue = [];
var uploadingFiles = [];
var uploadedFiles = [];
var retryFiles = [];
var folders = {};
var uploadedFolders = {};
var newFolderUploaded = "";
export const state = () => ({
  layout: "thumbnail",
  createFolder: false,
  uploadingDialog: false,
  isUploading: false,
  filters,
  selectedFiles,
  uploadQueue,
  uploadingFiles,
  uploadedFiles,
  retryFiles,
  folders,
  uploadedFolders,
  newFolderUploaded,
  zippingFiles: [],
  zippingFileModel: "hide",
  canShowAlert: false,
});

export const getters = {
  getZippingTotal: (state) => {
    const { zippingFiles } = state;
    if (zippingFiles.length) {
      const totalPercent = zippingFiles.length * 100;
      const reduce = (itemA, { progress }) => itemA + progress;
      const total = zippingFiles.reduce(reduce, 0);
      const percent = (total / totalPercent) * 100;
      let progress = parseInt(Math.round(percent));
      return progress;
    }
    return 0;
  },

  getZippingInfo: (state) => (tr) => {
    const { zippingFiles } = state;
    if (zippingFiles.length) {
      let compress = 0;
      let download = 0;
      zippingFiles.forEach((item) => {
        item.type == "zip" ? (compress += 1) : (download += 1);
      });
      if (compress > 0 && download == 0) {
        return tr("compressing_files", compress);
      } else if (compress == 0 && download > 0) {
        return tr("downloading_files", download);
      }
      return tr("compress_download_files", compress, download);
    }
    return "";
  },
};

/* ===================>Mutations <===================*/

export const mutations = {
  // change zipping model status
  changeZippingModel(state, value) {
    state.zippingFileModel = value;
  },
  // cancel from zipping file
  cancelZippingFile(state, fileIndex) {
    const { object } = state.zippingFiles[fileIndex];
    object.cancelRequest();
  },

  // remove from zipping file
  removeZippingFile(state, itemId) {
    const { zippingFiles } = state;
    const index = zippingFiles.findIndex(({ id }) => id == itemId);
    if (index !== -1) {
      const { object } = zippingFiles[index];
      object.cancelRequest();
      state.zippingFiles.splice(index, 1);
      if (state.zippingFiles.length < 1) {
        state.zippingFileModel = "hide";
      }
    }
  },

  // fire on zipping file changes
  onZippingFileChange(state, { event: { progress, total }, id }) {
    const { zippingFiles } = state;
    const mappedItems = zippingFiles.map((item) => {
      if (item.id == id) {
        return { ...item, progress, total };
      }
      return item;
    });

    state.zippingFiles = mappedItems;
  },

  // push new item into zipping files
  pushZippingFile(state, { url, item, items, context }) {
    try {
      const personal = new Personal(context);
      let zipFileObject = {
        item_id: null,
        name: "",
        id: new Date().getTime(),
        object: personal,
        progress: 0,
        total: 0,
      };

      if (item) {
        const { id, type, name } = item;
        items = [{ id, type, name }];
        zipFileObject.id = id;
        zipFileObject.name = name;
        if (type == "folder") {
          zipFileObject.type = "zip";
        } else {
          zipFileObject.type = "file";
        }
      } else {
        zipFileObject.name = personal.multiFileName;
        zipFileObject.type = "zip";
      }

      const callback = (event) => {
        this.commit("files/onZippingFileChange", {
          event,
          id: zipFileObject.id,
        });
      };
      personal.downloadFiles(url, items, callback);
      state.zippingFiles.unshift(zipFileObject);
      if (state.zippingFiles.length > 0) {
        state.zippingFileModel = "maximize";
      }
    } catch (error) {}
  },

  // clear all zipping files
  clearZippingFiles(state) {
    state.zippingFileModel = "hide";
    const { zippingFiles } = state;
    zippingFiles.forEach(({ object }) => object.cancelRequest());
    state.zippingFiles = [];
  },

  // context menu
  changeLayout(state, layout) {
    state.layout = layout;
  },

  changeFilter(state, object) {
    state.filters[object.key] = object.value;
  },

  resetFilterState(state, value) {
    const isOpened = state.filters.is_opened;
    const customFilters = filters;
    customFilters.is_opened = isOpened;
    customFilters.active_filters = false;
    if (value == true) {
      customFilters.is_opened = false;
    }
    state.filters = JSON.parse(JSON.stringify(customFilters));
  },

  toggleCreateFolder(state, value) {
    state.createFolder = value;
  },

  toggleUploadigDialog(state, param = false) {
    state.uploadingDialog = param;
  },

  toggleIsUploading(state, param) {
    state.isUploading = param;
  },

  toggleAlert(state, value) {
    state.canShowAlert = value;
  },

  removeSelectedFile(state, passedItem) {
    state.selectedFiles = state.selectedFiles.filter((item) => {
      if (item.id !== passedItem.id) {
        return item;
      }
    });
  },
  addSelectedFile(state, file) {
    state.selectedFiles.push(file);
  },

  removeAllSelectedFiles(state) {
    state.selectedFiles = [];
    state.folders = {};
  },

  addFilesToQueue(state) {
    state.uploadQueue = [...state.uploadQueue, ...state.selectedFiles];
  },

  startUploading(state) {
    state.isUploading = true;
    for (let i = 0; i < 6; i++) {
      let item = state.uploadQueue.shift();
      if (item) {
        this.commit("files/upload", item);
      }
    }
  },

  addOneItemToUpload(state) {
    let item = state.uploadQueue.shift();
    if (item) {
      this.commit("files/upload", item);
    }
  },

  async upload(state, item) {
    item.status = "in_progress";
    if (item.parent_temp_id !== null) {
      item.parent_id =
        state.uploadedFolders[item.folder_chunk_id][item.parent_temp_id].id;
    }
    const app = this;
    const request = new Promise(async function (resolve, reject) {
      const formData = new FormData();
      formData.append("file", item.file);
      formData.append("parent_id", item.parent_id);
      formData.append("quality", item.quality);
      let source = app.$axios.CancelToken.source();
      const url = `files/personal`;
      app.$axios
        .post(url, formData, {
          cancelToken: source.token,
          onUploadProgress: function (progressEvent) {
            let uploadProgress =
              parseInt(
                Math.round((progressEvent.loaded / progressEvent.total) * 100)
              ) - 5;
            app.commit("files/updateProgress", {
              id: item.id,
              progress: uploadProgress,
            });
          }.bind(app),
        })
        .then((response) => {
          app.commit("files/updateProgress", {
            id: item.id,
            progress: 100,
          });
          app.commit("files/done", {
            id: item.id,
            response,
          });
        })
        .catch(({ response }) => {
          if (
            response &&
            response.data &&
            response.data.not_allowed_limited_size &&
            state.canShowAlert
          ) {
            app.commit("files/toggleAlert", false);
            Alert.customAlert(app, "your_storage_is_full", "storage_full");
          }
          app.commit("files/addToRetry", item);
        });

      item.source = source;
      state.uploadingFiles.push(item);
    });
    return await request;
  },

  updateProgress(state, obj) {
    state.uploadingFiles = state.uploadingFiles.map((item) => {
      if (item.id == obj.id) {
        item.progress = obj.progress;
      }
      return item;
    });
  },

  done(state, obj) {
    state.uploadingFiles = state.uploadingFiles.filter((item) => {
      if (item.id !== obj.id) {
        return item;
      } else {
        item.status = "completed";
        item.file = null;
        item.response = obj.response;
        state.uploadedFiles.push(item);
      }
    });
  },

  cancelUpload(state, passedItem) {
    if (passedItem.status == "in_progress") {
      let item = state.uploadingFiles.find((item) => item.id == passedItem.id);
      if (item) {
        item.source.cancel("file-cancled");
        delete item.source;
      }
    } else {
      state.uploadQueue = state.uploadQueue.filter((item) => {
        if (item.id !== passedItem.id) {
          return item;
        }
      });
      passedItem.status = "retry";
      passedItem.progress = 0;
      state.retryFiles.push(passedItem);
    }
  },

  cancelAllUpload(state) {
    state.uploadQueue = [];
    state.uploadingFiles = [];
    state.retryFiles = [];
    state.uploadedFiles = [];
    // let uploading_files = state.uploadingFiles;
    // uploading_files.forEach((item) => {
    // 	item.source.cancel("file-cancled");
    // });
    // uploading_files = null;
  },

  retry(state, passedItem) {
    state.retryFiles = state.retryFiles.filter((item) => {
      if (item.id !== passedItem.id) {
        return item;
      }
    });
    passedItem.status = null;
    state.uploadQueue.push(passedItem);
    state.canShowAlert = true;
  },

  addToRetry(state, passedItem) {
    state.uploadingFiles = state.uploadingFiles.filter((item) => {
      if (item.id !== passedItem.id) {
        return item;
      }
    });
    state.uploadedFiles = state.uploadedFiles.filter((item) => {
      if (item.id !== passedItem.id) {
        return item;
      }
    });
    passedItem.status = "retry";
    passedItem.progress = 0;
    state.retryFiles.push(passedItem);
  },

  changeQuality(state, obj) {
    state.selectedFiles = state.selectedFiles.map((item) => {
      if (item.id == obj.id) {
        item.quality = obj.value;
      }
      return item;
    });
  },

  addFolder(state, obj) {
    state.folders[obj.id] = obj.folders;
  },

  async addUploadedFolders(state, obj) {
    state.folders = {};
    state.uploadedFolders[obj.id] = obj.folders;
    state.newFolderUploaded = obj.id;
  },
};

export default {
  namespace: true,
  state,
  getters,
  mutations,
};
