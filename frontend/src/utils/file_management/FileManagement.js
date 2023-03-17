import Alert from "~/helpers/alert";

class FileManagement {
  axiosSource;
  #axios;
  #downloadEvent;
  #context;

  constructor(context = null) {
    const { $axios } = context;
    this.#axios = $axios;
    this.#context = context;
    this.axiosSource = $axios.CancelToken.source();
  }

  /** ==========> COMMON METHODS START <========== */

  filterAsFileAndFolder(arrayItems) {
    let files = [];
    let folders = [];
    if (Array.isArray(arrayItems)) {
      const filterItems = (item) =>
        item.type == "folder" ? folders.push(item) : files.push(item);
      arrayItems.forEach(filterItems);
    }
    return [files, folders];
  }

  removeItemsAndReturn({ items, files, folders }) {
    let removedItems = [];
    for (let index = 0; index < items.length; index++) {
      const { id, type } = items[index];
      const filterItem = (item, item_index) => {
        let isExists = item.id == id;
        if (isExists) {
          removedItems.push({ ...item, item_index, isSelected: false });
        }
        return !isExists;
      };
      if (type == "folder") folders = folders.filter(filterItem);
      else files = files.filter(filterItem);
    }
    return { files, folders, removedItems };
  }

  pushItemsAndReturn({ items, files, folders }) {
    for (let index = 0; index < items.length; index++) {
      const element = items[index];
      if (element.type == "folder")
        folders.splice(element.item_index, 0, element);
      else files.splice(element.item_index, 0, element);
    }
    return { files, folders };
  }

  /** ==========> COPY AND MOVE SECTION START <========== */

  async pasteItems(body, onSuccess = null) {
    try {
      const { url, parentName, parent, ...requestBody } = body;
      const { data } = await this.#axios.post(body.url, requestBody);
      if (data.result) {
        const message = body.action == "copy" ? "copied_items" : "movied_items";
        if (onSuccess) {
          onSuccess(data);
        }
        // this.#context.$toastr.s(this.#context.$tr(message, body.parentName));
			this.#context.$toasterNA("green", this.#context.$tr(message, body.parentName));

      } else if (data.unauthorized) {
        let errorText = "unauthorized_to_do_this_operation";
        // this.#context.$toastr.e(this.#context.$tr(errorText));
				this.#context.$toasterNA("red",this.#context.$tr(errorText));

        throw new Error("no_message");
      } else if (data.not_allowed_limited_size) {
        Alert.customAlert(
          this.#context,
          "your_storage_is_full",
          "storage_full"
        );
        throw new Error("no_message");
      } else if (data.same_destination) {
        let errorText = "folder_same_destination";
        // this.#context.$toastr.e(this.#context.$tr(errorText));
				this.#context.$toasterNA("red",this.#context.$tr(errorText));

        throw new Error("no_message");
      } else {
        let errorText = "something_went_wrong";
        // this.#context.$toastr.e(this.#context.$tr(errorText));
				this.#context.$toasterNA("red",this.#context.$tr(errorText));

      }
    } catch (error) {
      if (error.message != "no_message") {
        let errorText = "something_went_wrong";
        // this.#context.$toastr.e(this.#context.$tr(errorText));
				this.#context.$toasterNA("red",this.#context.$tr(errorText));

      }
      throw new Error(error);
    }
  }

  /** ==========> COPY AND MOVE SECTION END <========== */

  /** ==========> FAVORITES SECTION START <========== */
  async toggleFavorites(url, items, onFinished = null) {
    if (items.length) {
      try {
        const body = { items: items, action: "favorites" };
        const { data } = await this.#axios.put(url, body);
        if (data.result) {
          const updatedItems = data.items;
          if (onFinished) onFinished(updatedItems);
          if (body.action == "favorites" && items.length == 1) {
            const { name, favorites } = updatedItems[0];
            let message = this.#context.$tr(
              "item_removed_from_favorites",
              name
            );
            if (favorites) {
              message = this.#context.$tr("item_added_to_favorites", name);
            }
            // this.#context.$toastr.s(message);
			this.#context.$toasterNA("green", message);

          } else {
            const message = this.#context.$tr("successfully_updated");
            // this.#context.$toastr.s(message);
			this.#context.$toasterNA("green", message);

          }
        } else if (data.unauthorized) {
          // this.#context.$toastr.e(
          //   this.#context.$tr("unauthorized_to_do_this_operation")
          // );
				this.#context.$toasterNA("red",this.#context.$tr('unauthorized_to_do_this_operation'));

          throw new Error("no_meesage");
        } else if (data.not_allowed) {
          // this.#context.$toastr.i(this.#context.$tr("action_not_allowed"));
          this.#context.$toasterNA("primary",this.#context.$tr("action_not_allowed"));

          throw new Error("no_message");
        } else if (data.not_found) {
          // this.#context.$toastr.i(this.#context.$tr("record_not_found"));
          this.#context.$toasterNA("primary",this.#context.$tr("record_not_found"));

          throw new Error("no_message");
        } else {
          // this.#context.$toastr.e(this.#context.$tr("something_went_wrong"));
				this.#context.$toasterNA("red",this.#context.$tr('something_went_wrong'));

          throw new Error("no_message");
        }
      } catch (error) {
        if (error.message == "no_message") {
          // this.#context.$toastr.e(this.#context.$tr("something_went_wrong"));
				this.#context.$toasterNA("red",this.#context.$tr('something_went_wrong'));

        }
        throw new Error(error);
      }
    }
  }
  /** ==========> FAVORITES SECTION END <========== */

  /** ==========> DELETE SECTION START <========== */
  async deleteFiles(url, items, onFinished = null) {
    if (items.length) {
      try {
        const params = { data: { items: items } };
        const response = await this.#axios.delete(url, params);
        if (response.data.result) {
          // this.#context.$toastr.s(this.#context.$tr("successfully_deleted"));
			this.#context.$toasterNA("green", this.#context.$tr("successfully_deleted"));

          if (onFinished) onFinished(response.data);
        } else if (response.data.not_found) {
          // this.#context.$toastr.e(this.#context.$tr("record_not_found"));
				this.#context.$toasterNA("red",this.#context.$tr('record_not_found'));

          throw new Error("no_meesage");
        } else if (response.data.unauthorized) {
          // this.#context.$toastr.e(
          //   this.#context.$tr("unauthorized_to_do_this_operation")
          // );
				this.#context.$toasterNA("red",this.#context.$tr('unauthorized_to_do_this_operation'));

          throw new Error("no_meesage");
        } else {
          // this.#context.$toastr.e(this.#context.$tr("can't_delete_file"));
				this.#context.$toasterNA("red",this.#context.$tr("can't_delete_file"));

          throw new Error("no_message");
        }
      } catch (error) {
        if (error.message === "no_message") {
          // this.#context.$toastr.e(this.#context.$tr("can't_delete_file"));
				this.#context.$toasterNA("red",this.#context.$tr("can't_delete_file"));

        }
        throw new Error(error);
      }
    }
  }

  // restore file
  async restoreFile(url, items, onFinished = null) {
    if (items.length) {
      try {
        const params = { items: items };
        const response = await this.#axios.post(url, params);
        if (response.data.result) {
          // this.#context.$toastr.s(this.#context.$tr("successfully_restored"));
			this.#context.$toasterNA("green", this.#context.$tr("successfully_restored"));

          if (onFinished) onFinished(response.data);
        } else if (response.data.not_found) {
          // this.#context.$toastr.e(this.#context.$tr("record_not_found"));
				this.#context.$toasterNA("red",this.#context.$tr("can'record_not_found"));

          throw new Error("no_meesage");
        } else {
          // this.#context.$toastr.e(this.#context.$tr("can't_restore_file"));
				this.#context.$toasterNA("red",this.#context.$tr("can't_restore_file"));

          throw new Error("no_message");
        }
      } catch (error) {
        if (error.message !== "no_message") {
          // this.#context.$toastr.e(this.#context.$tr("can't_restore_file"));
				this.#context.$toasterNA("red",this.#context.$tr("can't_restore_file"));

        }
        throw new Error(error);
      }
    }
  }

  /** ==========> DOWNLOAD SECTION START <========== */
  get downloadProgress() {
    if (this.#downloadEvent) {
      const { progress } = this.#downloadEvent;
      return progress;
    }
    return 0;
  }

  get downloadTotal() {
    if (this.#downloadEvent) {
      const { total } = this.#downloadEvent;
      return total;
    }
    return 0;
  }

  get isCompleted() {
    if (this.#downloadEvent) {
      const { completed } = this.#downloadEvent;
      return completed;
    }
    return false;
  }

  get multiFileName() {
    const date = new Date().getTime();
    const fileName = `Smart Friqi ${date}.zip`;
    return fileName;
  }

  // cancel a stream request
  cancelRequest() {
    if (this.axiosSource) this.axiosSource.cancel("Download file canceled");
  }

  /**
   * @param {Object} item {path, name}
   * Download file
   */
  async downloadFiles(url, items, callback = null, onFinished = null) {
    if (items && items.length) {
      try {
        const body = { items: items };
        const header = {
          responseType: "blob",
          onDownloadProgress: (progressEvent) => {
            this.onDownloadProgress(progressEvent, callback);
          },
          cancelToken: this.axiosSource.token,
        };

        const { data } = await this.#axios.post(url, body, header);
        if (data.error) {
          const event = { ...this.#downloadEvent, error: true };
          this.#downloadEvent = event;
          return;
        }
        const event = {
          ...this.#downloadEvent,
          progress: 100,
          completed: true,
        };
        this.#downloadEvent = event;
        if (callback) callback(event);
        if (onFinished) onFinished();
        this.onDownloadFinished(items, data);
      } catch (error) {
        const event = { ...this.#downloadEvent, error: true };
        this.#downloadEvent = event;
        const { response } = error;
        if (response.status == 401) {
          let errorText = "unauthorized_to_do_this_operation";
          // this.#context.$toastr.e(this.#context.$tr(errorText));
				this.#context.$toasterNA("red",this.#context.$tr(errorText));

        } else {
          let errorText = "something_went_wrong";
          // this.#context.$toastr.e(this.#context.$tr(errorText));
				this.#context.$toasterNA("red",this.#context.$tr(errorText));

          throw new Error(error);
        }
      }
    }
  }

  // fire on download process with a callback function
  onDownloadProgress(progressEvent, callback) {
    const percent = (progressEvent.loaded / progressEvent.total) * 100;
    let progress = parseInt(Math.round(percent));
    if (progress >= 98) progress = 97;
    const event = {
      total: progressEvent.total,
      progress: progress,
      completed: false,
    };
    this.#downloadEvent = event;
    if (callback) callback(event);
  }

  // download or zip file when stream request finished
  onDownloadFinished(items, data) {
    const itemLink = window.URL.createObjectURL(new Blob([data]));
    const link = document.createElement("a");
    link.href = itemLink;
    if (items.length == 1) {
      const item = items[0];
      const { name, type } = item;
      link.setAttribute("download", type == "folder" ? `${name}.zip` : name);
    } else {
      link.setAttribute("download", this.multiFileName);
    }
    document.body.appendChild(link);
    link.click();
  }

  /** ==========> DOWNLOAD SECTION END <========== */

  /** ==========> REMOVE FROM SHARE START <========== */
  async removeFromShare(share_id, item, items) {
    let itemInner = item;
    try {
      const res = await this.#axios.delete(`files/personal/shared/${share_id}`);
      if (res.status == 200) {
        item.file_shares = this.filterShareArr(item.file_shares, res?.data?.id);
        let foundItem = items.find((item2) => item2.id == item.id);
        if (foundItem) {
          foundItem.file_shares = this.filterShareArr(
            foundItem.file_shares,
            res?.data?.id
          );
        }
      }
      return { res, length: item.file_shares.length };
    } catch (err) {
      item = itemInner;
    }
  }

  filterShareArr(arr, id) {
    arr = arr.filter((share) => {
      if (share.id !== id) {
        return share;
      }
    });
    return arr;
  }
  /** ==========> REMOVE FROM SHARE END <========== */
  /** ==========> REAL TIME START <========== */

  async getSharedFileRealTime(id) {
    let res = await this.#axios.get(`files/personal/shared/${id}`, {
      params: {
        for_real_time: true,
      },
    });
    if (res?.status == 200 && res.data.result) {
      return res?.data?.item;
    }
    return false;
  }

  async getFileRealTime(id) {
    let res = await this.#axios.get(`files/personal/${id}`, {
      params: {
        for_real_time: true,
      },
    });
    if (res?.status == 200 && res.data.result) {
      return res?.data?.item;
    }
    return false;
  }

  isInArr(arr, passedId) {
    let res = arr.find((item) => passedId == item.id);
    return res !== undefined;
  }

  renameItemHistory(history, { item_id, name, updated_at, parent_id }, type) {
    let { current_page } = history;
    let mapFunc = (item) => {
      if (item.id == item_id) {
        item.name = name;
        item.updated_at = updated_at;
      }
      return item;
    };
    // check if renamed ite is in root start
    if (current_page.parent == null) {
      current_page[type] = current_page[type].map(mapFunc);
    }
    if (history.parent) {
      history.parent[type] = history.parent[type].map(mapFunc);
    }
    // check if renamed ite is in root end
    // check if renamed ite is not in root start
    if (current_page.parent !== null) {
      if (current_page.parent.id == parent_id) {
        current_page[type] = current_page[type].map(mapFunc);
      } else if (history[parent_id]) {
        history[parent_id][type] = history[parent_id][type].map(mapFunc);
      }
      // rename the folder if is open
      if (current_page.parent.id == item_id) {
        current_page.parent.name = name;
        current_page.parent.updated_at = updated_at;
        current_page.breadcrumb[current_page?.breadcrumb?.length - 1].text =
          name;
      } else if (history[item_id]) {
        history[item_id].parent.name = name;
        history[item_id].parent.updated_at = updated_at;
        history[item_id].breadcrumb[
          history[item_id]?.breadcrumb?.length - 1
        ].text = name;
      }
    }
  }

  addItemToRoot(history, item, seen = true) {
    let { parent, current_page } = history;
    if (seen == false) {
      item = { ...item, seen: false };
    }
    if (item.type == "folder") {
      if (current_page.parent == null) {
        if (current_page && !this.isInArr(current_page?.folders, item.id)) {
          current_page.folders.unshift(item);
        }
      } else {
        if (parent && !this.isInArr(parent?.folders, item.id)) {
          parent.folders.unshift(item);
        }
      }
    } else {
      if (current_page && current_page.parent == null) {
        if (!this.isInArr(current_page?.files, item.id)) {
          current_page.files.unshift(item);
        }
      } else {
        if (parent && !this.isInArr(parent?.files, item.id)) {
          parent.files.unshift(item);
        }
      }
    }
  }

  deleteItemHistory(
    history,
    { item_id, parent_id, old_parent },
    type,
    move = false
  ) {
    if (move) {
      parent_id = old_parent;
    }
    let { current_page } = history;
    let filterFunc = (item) => item.id !== item_id;
    // check if deleted ite is in root -- start
    if (current_page.parent == null) {
      current_page[type] = current_page[type].filter(filterFunc);
    }
    if (history.parent) {
      history.parent[type] = history.parent[type].filter(filterFunc);
    }
    // check if deleted item is in root -- end
    // check if deleted item is not in root -- start
    if (current_page.parent !== null) {
      if (current_page.parent.id == parent_id) {
        current_page[type] = current_page[type].filter(filterFunc);
      } else if (history[parent_id]) {
        history[parent_id][type] = history[parent_id][type].filter(filterFunc);
      }
    }

    if (history[item_id]) {
      delete history[item_id];
    }
  }

  updateParent(history, data) {
    var { id, parent_id } = data;
    const mapFunc = (item) => {
      if (item.id == data.id) {
        item = { ...item, ...data };
      }
      return item;
    };
    if (history[id]) {
      history.parent = { ...history?.parent, ...data };
    }
    if (parent_id == null) {
      history.parent[data.type + "s"] =
        history?.parent[data.type + "s"]?.map(mapFunc);
      if (history.current_page.parent == null) {
        history.current_page[data.type + "s"] =
          history?.current_page[data.type + "s"]?.map(mapFunc);
      }
    } else if (history[parent_id]) {
      history[parent_id][data.type + "s"] =
        history[parent_id][data.type + "s"].map(mapFunc);
      if (history.current_page.parent == parent_id) {
        history[parent_id][data.type + "s"] =
          history[parent_id][data.type + "s"]?.map(mapFunc);
      }
    }
  }
}

export default FileManagement;
