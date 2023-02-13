<template>
  <div>
    <v-dialog v-model="dialog" max-width="600">
      <v-card>
        <v-card-title class="font-weight-bold" style="font-size: medium">
          Select File Type <br />
          Choose one or multiple type you wnat to download
          <v-spacer></v-spacer>
          <v-btn fab x-small @click="cancel" style="float: right">
            <svg
              width="50"
              height="60"
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
          </v-btn>
        </v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="3">
              <v-card
                width="130px"
                height="130px"
                class="elevation-3"
                style="border-radius: 15px 15px 15px 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 10px"
                >
                  <v-checkbox v-model="json" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px"
                      ><img src="icons/json.png" alt=""
                    /></span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">Json File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="130px"
                class="elevation-3"
                style="border-radius: 15px 15px 15px 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 10px"
                >
                  <v-checkbox v-model="csv" class="pa-0 ma-0"> </v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px"
                      ><img src="icons/csv.png" alt=""
                    /></span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">CSV File</h4>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="3">
              <v-card
                width="130px"
                height="130px"
                class="elevation-3"
                style="border-radius: 15px 15px 15px 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 10px"
                >
                  <v-checkbox v-model="txt" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px"
                      ><img src="icons/txt.png" alt=""
                    /></span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">TXT File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="130px"
                class="elevation-3"
                style="border-radius: 15px 15px 15px 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 10px"
                >
                  <v-checkbox v-model="excel" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px"
                      ><img src="icons/excel.png" alt=""
                    /></span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">Excel File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="130px"
                class="elevation-3"
                style="border-radius: 15px 15px 15px 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 10px"
                >
                  <v-checkbox v-model="pdf" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px"
                      ><img src="icons/pdf.png" alt=""
                    /></span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">PDF File</h4>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
          <!-- <div class="d-flex">
            <h4 class="mt-4">Show who download this file</h4>
            <v-spacer></v-spacer>
            <v-switch input-value="true" inset class="pa-0"></v-switch>
          </div>
          <div class="d-flex">
            <h4 class="mt-5">
              Show the current time & date of the downloaded file
            </h4>
            <v-spacer></v-spacer>
            <v-switch input-value="true" inset class="pa-0" style=""></v-switch>
          </div> -->
          <div class="d-flex">
            <h4 class="mt-4">Show who download this file</h4>
            <v-spacer></v-spacer>
            <v-switch v-model="user" inset class="pa-0"></v-switch>
          </div>
          <div class="d-flex">
            <h4 class="mt-5">
              Show the current time & date of the downloaded file
            </h4>
            <v-spacer></v-spacer>
            <v-switch v-model="date" inset class="pa-0" style=""></v-switch>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn
            color="primary darken-1"
            depressed
            style="height: 35px; font-weight: 900; width: 90px"
            class="elevation-2"
            dense
            small
            solo
            outlined
            @click="cancel"
          >
            cancel
          </v-btn>

          <v-btn
            class="elevation-2 primary"
            style="height: 35px; font-weight: 900; width: 110px"
            depressed
            dense
            small
            color="white darken-1"
            solo
            text
            @click="downloadPDF"
          >
            Download
          </v-btn>
          <vue-csv-downloader
            :data="download_data"
            :fields="fields"
            id="dwnCsv"
            style="display: none"
          >
            download
          </vue-csv-downloader>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import { saveExcel } from "@progress/kendo-vue-excel-export";
import { saveAs } from "file-saver";
import VueCsvDownloader from "vue-csv-downloader";
export default {
  components: {
    VueCsvDownloader,
  },
  data() {
    return {
      dialog: false,
      json: false,
      csv: false,
      txt: false,
      excel: false,
      pdf: false,
      user: false,
      date: false,
      download_data: [],
      fields: [
        "id",
        "name",
        "capital",
        ,
        "phone_code",
        "currency_name",
        "currency_symbol",
        "region",
        "subregion",
        "subregion",
        "status",
      ],
    };
  },
  methods: {
    openDialog(data) {
      this.dialog = true;
      this.download_data = data;
    },
    downloadPDF() {
      if (this.pdf == true) {
        const doc = new jsPDF("1", "mm", [297, 210]);

        autoTable(doc, {
          startY: doc.autoTable() + 70,

          margin: { horizontal: 10 },
          styles: { overflow: "linebreak" },
          bodyStyles: { valign: "top" },
          columnStyles: { email: { columnWidth: "wrap" } },
          theme: "striped",
          showHead: "everyPage",
          didDrawPage: function (data) {
            // Header
            doc.setFontSize(20);
            doc.setTextColor(40);
            doc.text("Country Report", data.settings.margin.left, 10);

            // Footer
            var str = "Page " + doc.internal.getNumberOfPages();
            doc.setFontSize(12);

            // jsPDF 1.4+ uses getWidth, <1.4 uses .width
            var pageSize = doc.internal.pageSize;
            var pageHeight = pageSize.height
              ? pageSize.height
              : pageSize.getHeight();
            doc.text(str, data.settings.margin.left, pageHeight - 10);
          },

          head: [
            ["ID", "Name", "Capital", "Phone", "Currency", "Region", "Status"],
          ],
          body: this.download_data,
          columns: [
            {
              text: "ID",
              dataKey: "id",
            },
            {
              text: "NAME",
              dataKey: "name",
            },
            {
              text: "CAPITAL",
              dataKey: "capital",
            },
            {
              text: "PHONE CODE",
              dataKey: "phone_code",
            },
            {
              text: "CURRENCY",
              dataKey: "currency_name",
            },

            {
              text: " ",
              dataKey: "region",
            },
            {
              text: "STATUS",
              dataKey: "status",
            },
          ],
        });

        if (this.date == true) {
          var date = new Date();
          var current_date =
            "Date : " +
            date.getFullYear() +
            "-" +
            (date.getMonth() + 1) +
            "-" +
            date.getDate();
          doc.setFontSize(12);

          doc.setFont("times");
          doc.setFontSize(12);
          // doc.setFontStyle("italic");
          doc.setTextColor(0, 0, 255);
          doc.text(
            current_date,
            250,
            doc.internal.pageSize.height - 10,
            "center"
          );
        }
        if (this.user == true) {
          const info = "User : " + this.$auth.user.data.first_name;
          doc.setFontSize(12);

          doc.setFont("times");
          doc.setFontSize(12);
          // doc.setFontStyle("italic");
          doc.setTextColor(0, 0, 0);
          doc.text(info, 150, doc.internal.pageSize.height - 10, "center");
        }
        doc.save("Countey.pdf");

        (this.dialog = false),
          (this.pdf = null),
          (this.user = false),
          (this.date = false);
      }
      if (this.excel == true) {
        saveExcel({
          data: this.download_data,
          fileName: "Country",
          columns: [
            {
              field: "id",
              title: "ID",
            },
            {
              field: "name",
              title: "NAME",
            },
            {
              field: "capital",
              title: "CAPITAL",
            },
            {
              field: "phone_code",
              title: "PHONE CODE",
            },
            {
              field: "currency_name",
              title: "CURRENCY",
            },

            {
              field: "region",
              title: "REGION",
            },

            {
              field: "status",
              title: "STATUS",
            },
          ],
        });
        (this.dialog = false), (this.excel = null), (this.date = null);
      }
      if (this.txt == true) {
        const FileSaver = require("file-saver");
        const data = this.download_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "country.txt");
        (this.dialog = false), (this.txt = null);
      }
      if (this.json == true) {
        const FileSaver = require("file-saver");
        const data = this.download_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "country.json");
        (this.dialog = false), (this.json = null);
      }
      if (this.csv == true) {
        this.exportCsv();
        (this.dialog = false), (this.csv = null);
      }
    },
    exportCsv() {
      var link = document.getElementById("dwnCsv");
      link.click();
    },

    cancel() {
      (this.dialog = false),
        (this.json = null),
        (this.csv = null),
        (this.txt = null),
        (this.excel = null),
        (this.pdf = null);
    },
  },
};
</script>

<style>
.v-messages {
  display: none;
}
.v-input--selection-controls__input {
  margin-right: 0px !important;
}
</style>
