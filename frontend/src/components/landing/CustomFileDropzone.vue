<template>
  <div v-if="$attrs.showFiles" class="dropzone mt-2">
    <v-simple-table
      fixed-header
      height="300px"
      class="ma-0"
      dense
      style="background-color: #eff1f5"
    >
      <template v-slot:default>
        <thead class="pt-0 pb-0" style="line-height: 10px">
          <tr class="py-0">
            <th class="text-left blue lighten-4 primary--text">
              {{ $tr("file_name") }}
            </th>
            <th class="text-left blue lighten-4 primary--text">
              {{ $tr("type") }}
            </th>
            <th class="text-left blue lighten-4 primary--text">
              {{ $tr("size") }}
            </th>
            <th class="text-left blue lighten-4 primary--text">
              {{ $tr("created_by") }}
            </th>
            <th class="text-left blue lighten-4 primary--text">
              {{ $tr("date") }}
            </th>
            <th class="text-left blue lighten-4 primary--text">
              {{ $tr("actions") }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr class="text-center" v-if="files.length < 1">
            <td colspan="6">{{ $tr("no_file_uploaded") }}</td>
          </tr>

          <tr
            v-for="(serverFile, index) in files"
            :key="index"
            style="white-space: nowrap"
            v-else
          >
            <td class="text-left">
              <div class="d-flex align-center">
                <v-icon
                  dense
                  color="green lighten-7"
                  v-if="
                    ['svg', 'png', 'jpeg', 'jpg', 'gif'].includes(
                      serverFile.type
                    )
                  "
                  >fa-file-image</v-icon
                >
                <v-icon
                  dense
                  color="#2b579a"
                  v-else-if="['doc', 'docm', 'docx'].includes(serverFile.type)"
                  >fa-file-word</v-icon
                >
                <v-icon
                  dense
                  color="#217346"
                  v-else-if="['xlsx', 'xlsm', 'xls'].includes(serverFile.type)"
                  >fa-file-excel</v-icon
                >
                <v-icon
                  dense
                  color="#d24726"
                  v-else-if="['pptx', 'ppt', 'pptm'].includes(serverFile.type)"
                  >fa-file-powerpoint</v-icon
                >
                <v-icon
                  dense
                  color="#4A148C"
                  v-else-if="['tar.gz', 'rar', 'zip'].includes(serverFile.type)"
                  >fa-file-archive</v-icon
                >
                <v-icon dense color="error" v-else-if="serverFile.type == 'pdf'"
                  >fa-file-pdf</v-icon
                >
                <v-icon dense color="#217346" v-else>fa-file</v-icon>

                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      style="width: 200px"
                      class="ms-1 text-ellipsis"
                      v-bind="attrs"
                      v-on="on"
                      >{{
                        getServerFileName(serverFile.name, serverFile.type)
                      }}</span
                    >
                  </template>
                  <span>{{ serverFile.name }}</span>
                </v-tooltip>
              </div>
            </td>

            <td class="text-left">
              {{ serverFile.type }}
            </td>
            <td>{{ (serverFile.size / 1024).toFixed(1) }}KB</td>
            <td>
              <template>
                <div
                  v-if="serverFile.changed_by"
                  class="rounded-lg d-flex justify-center align-center file_selected px-1"
                  style="border: 1px solid var(--v-primary-base)"
                >
                  <v-avatar class="me-1" color="green" size="22">
                    <span style="color: white">
                      {{
                        serverFile.changed_by
                          ? serverFile.changed_by.firstname[0].toUpperCase()
                          : ""
                      }}
                    </span>
                  </v-avatar>
                  <p v-if="serverFile.changed_by" class="ma-0" color="white">
                    {{
                      serverFile.changed_by.firstname +
                      ` ` +
                      serverFile.changed_by.lastname
                    }}
                  </p>
                </div>
                <div
                  v-else-if="serverFile.created_by"
                  class="rounded-lg d-flex justify-center align-center file_selected px-1"
                  style="border: 1px solid var(--v-primary-base)"
                >
                  <v-avatar class="me-1" color="green" size="22">
                    <span style="color: white">
                      {{
                        serverFile.created_by
                          ? serverFile.created_by.firstname[0].toUpperCase()
                          : ""
                      }}
                    </span>
                  </v-avatar>
                  <p v-if="serverFile.created_by" class="ma-0" color="white">
                    {{
                      serverFile.created_by.firstname +
                      ` ` +
                      serverFile.created_by.lastname
                    }}
                  </p>
                </div>
              </template>
            </td>
            <td class="text-left">
              <span class="me-1">{{
                $formatDateTime(serverFile.created_at)
              }}</span>
            </td>

            <td>
              <div style="width: 32px">
                <v-menu bottom :left="!$vuetify.rtl" offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color="primary" icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>

                  <v-list dense>
                    <v-list-item @click="onFileDelete(serverFile)" link>
                      <v-list-item-title>
                        <v-icon small color="error">fa-trash</v-icon>
                        <span class="text-body-2 font-weight-medium ms-1">{{
                          $tr("delete")
                        }}</span>
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      v-if="$attrs.cantDownload"
                      @click="$emit('downloadFile', serverFile)"
                    >
                      <v-list-item-title>
                        <v-icon small color="success">fa-download</v-icon>
                        <span class="text-body-2 font-weight-medium ms-1">{{
                          $tr("download")
                        }}</span>
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      v-else
                      :href="serverFile.url"
                      target="_blank"
                      link
                    >
                      <v-list-item-title>
                        <v-icon small color="success">fa-download</v-icon>
                        <span class="text-body-2 font-weight-medium ms-1">{{
                          $tr("download")
                        }}</span>
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
  </div>
  <div v-else class="dropzone mt-2">
    <input
      type="file"
      class="d-none"
      ref="fileInputRef"
      multiple
      :accept="allowedExtensions"
      @change="onFileInputChange"
    />
    <div
      v-if="selecedFilesInFileObject.length < 1"
      class="dropzone__empty dropzone__border"
      :class="{ 'dropzone__has-error': errorTexts != null }"
      @click="$refs.fileInputRef.click()"
      @dragover="dragover"
      @drop="dropFiles"
    >
      <div class="dropzone__text">
        <div class="d-flex align-center">
          <div class="dropzone__button">
            <v-btn
              class="stepper-btn px-1"
              style="min-width: 120px; height: 31px"
              color="primary"
              :loading="isLoading"
            >
              {{ $tr("select_file") }}
            </v-btn>
          </div>
          <div class="ms-1 dropzone__text-title">
            {{ $tr("drag_and_drop") }}
          </div>
        </div>
        <div>
          {{
            $tr(
              "allowed_extensions",
              "pdf, .doc, .docx, .png, .jpeg, .jpg, .zip".toUpperCase()
            )
          }}
        </div>
      </div>
    </div>
    <div
      v-else
      class="dropzone_not_empty dropzone__border"
      :class="{ 'dropzone__has-error': errorTexts != null }"
      @dragover="dragover"
      @drop="dropFiles"
    >
      <div class="w-full d-flex">
        <div
          class="ml-2 d-flex rounded-lg justify-center align-center primary white--text file_selected px-1"
        >
          <v-avatar class="me-1" color="white" size="22">
            <span style="color: var(--v-primary-base)">
              {{ selecedFilesInFileObject.length }}
            </span>
          </v-avatar>
          <p class="ma-0" color="white">
            {{ $tr("file_selected") }}
          </p>
        </div>
        <v-btn
          @click="$refs.fileInputRef.click()"
          :loading="isLoading"
          class="stepper-btn px-1 ms-2"
          style="min-width: 120px; height: 31px"
          color="primary"
        >
          {{ $tr("select_file") }}
        </v-btn>
      </div>
      <v-simple-table fixed-header dense class="my-1 w-full" height="320">
        <template v-slot:default>
          <thead class="pt-0 pb-0" style="line-height: 10px">
            <tr class="py-0">
              <th class="text-left blue lighten-4">
                {{ $tr("file_name") }}
              </th>
              <th class="text-left blue lighten-4">
                {{ $tr("type") }}
              </th>
              <th class="text-left blue lighten-4">
                {{ $tr("size") }}
              </th>
              <th class="text-left blue lighten-4">{{ $tr("actions") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(selectedFile, index) in selecedFilesInFileObject"
              :key="index"
              class=""
            >
              <td class="text-left">{{ selectedFile.name }}</td>
              <td class="text-left">{{ selectedFile.type }}</td>
              <td class="text-left">
                {{ (selectedFile.size / 1024).toFixed(1) }} KB
              </td>

              <td class="text-right">
                <v-btn
                  @click="removeSelectedFile(index)"
                  class="not-hover-button"
                  icon
                  color="red"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </div>
    <div class="v-text-field__details mt-1">
      <div class="v-messages theme--light error--text" role="alert">
        <div v-if="errorTexts" class="v-messages__wrapper">
          <div class="v-messages__message message-transition-enter-to">
            {{ errorTexts }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Alert from "~/helpers/alert";

export default {
  props: {
    files: {
      type: Array,
      default: () => [],
    },
    fileSize: {
      type: Number,
      default: 20000,
    },
    allowedExtensions: {
      type: String,
      default:
        "application/pdf, .pdf, .doc, .docx, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, .zip, application/x-zip-compressed, image/png, image/jpeg, image/jpg",
    },
    selectedFilesProps: {},
  },
  data() {
    return {
      // dropzone section
      errorTexts: null,
      selectedFiles: this.selectedFilesProps ? this.selectedFilesProps : [],
      selecedFilesInFileObject: [],
      isLoading: false,
    };
  },

  methods: {
    onFileDelete(file) {
      const app = this;
      Alert.removeDialogAlert(
        this,
        () => {
          app.$emit("onDelete", file);
        },
        "swal_remove_text"
      );
    },

    clearItems() {
      this.selectedFiles = [];
      this.selecedFilesInFileObject = [];
      this.errorTexts = null;
      this.isLoading = false;
    },
    dragover(event) {
      event.preventDefault();
    },

    dropFiles(event) {
      event.preventDefault();
      const files = event.dataTransfer.files;
      event.target.files = files;
      this.onFileInputChange(event);
    },

    async onFileInputChange(event) {
      this.isLoading = true;
      event.preventDefault();
      const uploadedFiles = event.target.files;
      for (let fileIndex = 0; fileIndex < uploadedFiles.length; fileIndex++) {
        const file = uploadedFiles[fileIndex];
        const fileExtension = file.type;
        const allowedExtensions = this.allowedExtensions.split(", ");
        if (
          this.allowedExtensions &&
          !allowedExtensions.includes(fileExtension)
        ) {
          this.errorTexts = this.$tr(
            "select_only_file_format",
            "pdf, .doc, .docx, .png, .jpeg, .jpg, .zip".toUpperCase()
          );
          this.isLoading = false;
          return;
        }
        if (this.fileSize && (file.size / 1024).toFixed(1) > this.fileSize) {
          this.isLoading = false;
          this.errorTexts = this.$tr("file_max_size_error", 20);
          return;
        }

        try {
          const parsedFile = await this.fileToBase64(file);
          this.selecedFilesInFileObject.unshift(file);
          this.selectedFiles.push(parsedFile);
        } catch (error) {
          this.errorTexts = this.$tr("file_upload_error");
          this.isLoading = false;
          return;
        }
      }
      this.errorTexts = null;
      this.$emit("filesSelected", this.selectedFiles);
      this.$emit("filesSelectedFileObject", this.selecedFilesInFileObject);
      this.$refs.fileInputRef.value = null;
      this.isLoading = false;
    },

    async fileToBase64(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
      });
    },

    // delete the selected file
    removeSelectedFile(fileIndex) {
      const selectedFile = this.selectedFiles[fileIndex];
      this.selecedFilesInFileObject.splice(fileIndex, 1);
      this.selectedFiles.splice(fileIndex, 1);
      this.$emit("fileRemoved", { file: selectedFile, index: fileIndex });
      this.errorTexts = null;
    },
    getServerFileName(filename, type) {
      if (filename.length > 20) {
        return (
          filename.substring(0, 15) +
          "..." +
          filename.substring(15, 20) +
          "." +
          type
        );
      } else {
        return filename;
      }

      return filename;
    },
  },
};
</script>

<style scoped>
.cls-2 {
  fill: var(--v-primary-base);
}
.cls-1 {
  fill: var(--v-background-base);
}
/* NOT EMPTY DROPZONE */
.dropzone_not_empty {
  padding: 1rem 0.5rem;
  min-height: 400px;
  max-height: 400px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.dropzone__selected-image .v-image {
  border-radius: 15px !important;
  height: 250px;
  width: 300px;
  background-size: cover;
}
.image-item {
  position: relative;
}
.remove-image_btn {
  position: absolute;
  top: -10px;
  right: -5px;
  z-index: 1;
  width: 20px;
  height: 20px;
}
.dropzone__all-selected-images .image-item {
  margin: 0.7rem 0.4rem;
}

.dropzone__current-image .v-image {
  outline: 3px solid var(--v-primary-base);
}
.dropzone_not_empty ::-webkit-scrollbar {
  height: 0;
  background: transparent;
}

.dropzone__all-selected-images {
  display: flex;
  width: 300px;
  overflow: auto;
  justify-content: space-between;
}
.dropzone__all-selected-images .v-image {
  border-radius: 5px !important;
  height: 80px;
  width: 80px;
  background-size: cover;
}
.upload-image_btn {
  margin: 0.5rem;
  background-color: var(--gray-cyan);
  min-width: 80px;
  max-width: 80px;
  height: 80px;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* EMPTY DROPZONE */
.dropzone__empty {
  padding: 1rem;
  min-height: 250px;
  max-height: 250px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.dropzone__has-error {
  border-color: var(--v-error-base) !important;
}
.dropzone__has-error .dropzone__text .dropzone__text-title {
  color: var(--v-error-base) !important;
}
.dropzone__has-error .cls-2 {
  fill: var(--v-error-base) !important;
}
.dropzone__border {
  cursor: pointer;
  border-radius: 5px;
  border: 3px dashed var(--v-info-lighten4);
  transition: border 200ms ease-in-out;
}
.dropzone__border:hover {
  border-color: var(--v-primary-base) !important;
}
.dropzone__text {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}
.dropzone__text .dropzone__text-title {
  font-size: 1.6rem;
  color: var(--v-primary-base);
  font-weight: 500;
}
</style>
