<template>
  <v-card class="pb-2">
    <v-card-title class="pt-1 pb-0">
      <v-spacer />
      <CloseBtn class="pt-0" @click="onClose" />
    </v-card-title>

    <v-card-text>
      <download-csv
        style="display: none"
        id="dwnCsv"
        :name="
          new Date(this.date_now).toLocaleDateString() +
          ' - ' +
          filename +
          '.csv'
        "
        :data="jsonData"
      >
      </download-csv>

      <div class="d-flex flex-column" style="align-items: center">
        <svg
          :fill="`${$vuetify.theme.dark ? '#A6A7A8' : '#535353'}`"
          xmlns="http://www.w3.org/2000/svg"
          width="70"
          height="70"
          viewBox="0 0 24 24"
        >
          <path
            d="M18.2848192,17.5777124 L20.8535534,20.1464466 C21.0488155,20.3417088 21.0488155,20.6582912 20.8535534,20.8535534 C20.6582912,21.0488155 20.3417088,21.0488155 20.1464466,20.8535534 L17.5777124,18.2848192 L15.9160251,20.7773501 C15.6899572,21.116452 15.1749357,21.0571624 15.0318354,20.6755617 L12.0318354,12.6755617 C11.8811067,12.2736185 12.2736185,11.8811067 12.6755617,12.0318354 L20.6755617,15.0318354 C21.0571624,15.1749357 21.116452,15.6899572 20.7773501,15.9160251 L18.2848192,17.5777124 L18.2848192,17.5777124 Z M17.2312404,17.0782479 L19.4104716,15.6254271 L13.3544004,13.3544004 L15.6254271,19.4104716 L17.0782479,17.2312404 C17.0974475,17.2011742 17.1201804,17.1727128 17.1464466,17.1464466 C17.1727128,17.1201804 17.2011742,17.0974475 17.2312404,17.0782479 L17.2312404,17.0782479 Z M5.5,3 C5.77614237,3 6,3.22385763 6,3.5 C6,3.77614237 5.77614237,4 5.5,4 C4.67157288,4 4,4.67157288 4,5.5 C4,5.77614237 3.77614237,6 3.5,6 C3.22385763,6 3,5.77614237 3,5.5 C3,4.11928813 4.11928813,3 5.5,3 Z M8.5,4 C8.22385763,4 8,3.77614237 8,3.5 C8,3.22385763 8.22385763,3 8.5,3 L10.5,3 C10.7761424,3 11,3.22385763 11,3.5 C11,3.77614237 10.7761424,4 10.5,4 L8.5,4 Z M13.5,4 C13.2238576,4 13,3.77614237 13,3.5 C13,3.22385763 13.2238576,3 13.5,3 L15.5,3 C15.7761424,3 16,3.22385763 16,3.5 C16,3.77614237 15.7761424,4 15.5,4 L13.5,4 Z M8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L10.5,20 C10.7761424,20 11,20.2238576 11,20.5 C11,20.7761424 10.7761424,21 10.5,21 L8.5,21 Z M3,8.5 C3,8.22385763 3.22385763,8 3.5,8 C3.77614237,8 4,8.22385763 4,8.5 L4,10.5 C4,10.7761424 3.77614237,11 3.5,11 C3.22385763,11 3,10.7761424 3,10.5 L3,8.5 Z M3,13.5 C3,13.2238576 3.22385763,13 3.5,13 C3.77614237,13 4,13.2238576 4,13.5 L4,15.5 C4,15.7761424 3.77614237,16 3.5,16 C3.22385763,16 3,15.7761424 3,15.5 L3,13.5 Z M3,18.5 C3,18.2238576 3.22385763,18 3.5,18 C3.77614237,18 4,18.2238576 4,18.5 C4,19.3284271 4.67157288,20 5.5,20 C5.77614237,20 6,20.2238576 6,20.5 C6,20.7761424 5.77614237,21 5.5,21 C4.11928813,21 3,19.8807119 3,18.5 Z M21,10.5 C21,10.7761424 20.7761424,11 20.5,11 C20.2238576,11 20,10.7761424 20,10.5 L20,8.5 C20,8.22385763 20.2238576,8 20.5,8 C20.7761424,8 21,8.22385763 21,8.5 L21,10.5 Z M21,5.5 C21,5.77614237 20.7761424,6 20.5,6 C20.2238576,6 20,5.77614237 20,5.5 C20,4.67157288 19.3284271,4 18.5,4 C18.2238576,4 18,3.77614237 18,3.5 C18,3.22385763 18.2238576,3 18.5,3 C19.8807119,3 21,4.11928813 21,5.5 Z"
          />
        </svg>
      </div>
      <div class="d-flex flex-column" style="align-items: center">
        <div class="text-h4">{{ $tr("select_file_type") }}</div>
        <div>{{ $tr("choose_download_types") }}</div>
      </div>
      <div>
        <v-list dense>
          <v-list-item v-for="(item, i) in items" :key="i">
            <v-checkbox
              :value="item.text"
              v-model="selected_types"
              :label="item.text"
              color="primary"
              hide-details
            ></v-checkbox>
          </v-list-item>
        </v-list>
      </div>
      <div style="font-size: 12px !important">
        <v-checkbox
          v-model="isCreated"
          :label="`${$tr('created_by')} :  ${created_by}`"
          color="primary"
          hide-details
        ></v-checkbox>

        <v-checkbox
          v-model="isTimeDate"
          :label="`${$tr('time_and_date')} : ${time_date}`"
          color="primary"
          hide-details
        ></v-checkbox>
      </div>
    </v-card-text>

    <v-card-actions>
      <v-spacer></v-spacer>

      <TextButton
        type="primary"
        class="float-right"
        :text="$tr('download')"
        @click="onDownload"
      >
      </TextButton>
      <TextButton
        type="secondary"
        class="ms-1 float-right"
        :text="$tr('cancel')"
        @click="onClose"
      >
      </TextButton>
      <v-spacer></v-spacer>
    </v-card-actions>
  </v-card>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn";
import { saveExcel } from "@progress/kendo-vue-excel-export";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import TextButton from "../common/buttons/TextButton.vue";

export default {
  name: "DownloadPopUp",
  components: { CloseBtn, TextButton },
  props: {
    selected_header: {
      type: Array,
      required: true,
    },
    content: {
      type: Array,
      required: true,
    },
    filename: {
      type: String,
      default: "Filename",
    },
  },
  data: () => ({
    items: [
      { text: "JSON" },
      { text: "XML" },
      { text: "CSV" },
      { text: "TXT" },
      { text: "SQL" },
      { text: "MS-Excel" },
      { text: "PDF" },
    ],
    selected_types: [],
    isCreated: false,
    created_by: "Fistname lastname",
    isTimeDate: false,
    time_date: "",
    jsonData: [],
    date_now: new Date().toJSON().slice(0, 10).replace(/-/g, "-"),
  }),

  created() {
    this.FilterData();
    this.created_by =
      this.$auth.user.firstname + " " + this.$auth.user.lastname;
    this.time_date =
      new Date(this.date_now).toLocaleTimeString(navigator.language, {
        hour: "2-digit",
        minute: "2-digit",
      }) +
      " | " +
      this.date_now;
  },

  methods: {
    onClose() {
      this.selected_types = [];
      this.isTimeDate     = false,
      this.isCreated      = false,
      this.$emit('closeDownload');
    },

    async onDownload() {
      await this.getJsonData();
      this.selected_types.forEach((type) => {
        switch (type) {
          case "JSON":
            this.exportJson();
            break;
          case "XML":
            this.exportXML();
            break;
          case "CSV":
            this.exportCSV();
            break;
          case "TXT":
            this.exportTXT();
            break;
          case "SQL":
            this.exportSQL();
            break;
          case "MS-Excel":
            this.exportExcel();
            break;
          case "PDF":
            this.exportPDF();
            break;
        }
      });
    },
    exportJson() {
      let data = this.jsonData;
      let json = JSON.stringify(data);
      let file = new Blob([json], { type: "text/json" });
      saveAs(
        file,
        new Date(this.date_now).toLocaleDateString() +
          " - " +
          this.filename +
          ".json"
      );
    },
    exportXML() {
      let xml = this.OBJtoXML(this.jsonData);
      let blob = new Blob([xml], { type: "octet/stream" });
      saveAs(
        blob,
        new Date(this.date_now).toLocaleDateString() +
          " - " +
          this.filename +
          ".xml"
      );
    },
    OBJtoXML(obj) {
      var xml = "";
      for (var prop in obj) {
        xml += "<" + prop + ">";
        if (Array.isArray(obj[prop])) {
          for (var array of obj[prop]) {
            // A real botch fix here
            xml += "</" + prop + ">";
            xml += "<" + prop + ">";

            xml += this.OBJtoXML(new Object(array));
          }
        } else if (typeof obj[prop] == "object") {
          xml += this.OBJtoXML(new Object(obj[prop]));
        } else {
          xml += obj[prop];
        }
        xml += "</" + prop + ">";
      }
      var xml = xml.replace(/<\/?[0-9]{1,}>/g, "");
      return xml;
    },
    exportCSV() {
      var link = document.getElementById("dwnCsv");
      link.click();
    },

    exportTXT() {
      let data = this.jsonData;
      let json = JSON.stringify(data);
      let file = new Blob([json], { type: "text/plain;charset=utf-8" });
      saveAs(
        file,
        new Date(this.date_now).toLocaleDateString() +
          " - " +
          this.filename +
          ".txt"
      );
    },

    exportSQL() {},

    async exportExcel() {
      await saveExcel({
        data: this.jsonData,
        fileName:
          new Date(this.date_now).toLocaleDateString() + " - " + this.filename,
        columns: this.get_headers(),
      });
    },

    exportPDF() {
      const doc = new jsPDF("l", "mm", [297, 210]);
      const halign = this.$vuetify.rtl ? "right" : "left";
      doc.autoTable({
        // Change the theme {grid, plain, striped}
        theme: "grid",
        margin: { top: 20, right: 5, bottom: 15, left: 5 },
        styles: {
          fontSize: 8,
          halign: halign,
          cellPadding: { top: 1, right: 0.5, bottom: 1, left: 0.5 },
        },
        didDrawPage: () => {
          var pageSize = doc.internal.pageSize;
          var pageHeight = pageSize.height
            ? pageSize.height
            : pageSize.getHeight();
          doc.setFontSize(12);
          doc.text(
            this.filename,
            doc.internal.pageSize.getWidth() / 2 -
              (this.$options.name.length + 5),
            15
          );
          doc.setFontSize(8);
          var str = !this.$vuetify.rtl
            ? this.$tr("page") + ":  " + doc.internal.getNumberOfPages()
            : doc.internal.getNumberOfPages() + " :" + this.$tr("page");
          var details = !this.$vuetify.rtl
            ? this.$tr("created_by") + ":  " + this.created_by
            : this.created_by + " :" + this.$tr("created_by");
          doc.text(str, 21, pageHeight - 10);
          if (this.isCreated) {
            doc.text(details, 137, pageHeight - 10);
          }
          if (this.isTimeDate) {
            doc.text(
              !this.$vuetify.rtl
                ? this.$tr("date") +
                    ":  " +
                    new Date(this.date_now).toLocaleDateString()
                : new Date(this.date_now).toLocaleDateString() +
                    " :" +
                    this.$tr("date"),
              260,
              pageHeight - 10
            );
          }
        },
        headStyles: { fillColor: "#2562e0" },
        columns: this.get_headers().map((column) => {
          return {
            header: column.title,
            dataKey: column.field,
          };
        }),
        body: this.jsonData,
      });
      doc.save(
        new Date(this.date_now).toLocaleDateString() +
          " - " +
          this.filename +
          ".pdf"
      );
    },

    get_headers() {
      let items = this.selected_header;
      return items.map((item) => {
        if (item.value.split(".").length > 1) {
          return {
            field: item.value.split(".")[0],
            title: item.text,
          };
        } else {
          return {
            field: item.value,
            title: item.text,
          };
        }
      });
    },

    getJsonData() {
      if (this.jsonData.length != this.content.length) {
        this.jsonData = this.FilterData();
      }
    },

    get_items() {
      let items = this.content;
      return JSON.parse(JSON.stringify(items)).map((item) => {
        let entries = Object.entries(item);
        for (let x = 0; x < entries.length; x++) {
          if (Array.isArray(entries[x][1])) {
            item[entries[x][0]] = this.get_comma_str(item[entries[x][0]]);
          } else if (
            typeof entries[x][1] === "object" &&
            entries[x][1] !== null
          ) {
            item[entries[x][0]] =
              entries[x][1].name ??
              entries[x][1].title ??
              entries[x][1].firstname + " " + entries[x][1].lastname;
          }
        }
        return item;
      });
    },
    get_comma_str(arr) {
      let arrStr = "";
      const length = arr.length;
      for (let index = 0; index < length; index++) {
        arrStr +=
          length - 1 === index ? arr[index]?.name : arr[index]?.name + ", ";
      }
      return arrStr;
    },
    FilterData() {
      let items = this.get_items(this.content);
      let headers = this.get_headers(this.selected_header);
      let newItems = items.map((item) => {
        let entries = Object.entries(item);
        let obj = {};
        for (let x = 0; x < entries.length; x++) {
          if (this.isInHeaders(entries[x][0], headers)) {
            obj[entries[x][0]] = entries[x][1];
          }
        }
        return obj;
      });
      return newItems;
    },
    isInHeaders(title, headers) {
      for (let x = 0; x < headers.length; x++) {
        if (headers[x].field == title) {
          return true;
        }
      }
      return false;
    },
  },
};
</script>

<style scoped></style>
