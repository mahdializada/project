<template>
  <div>
    <v-dialog v-model="downDialog" width="680">
      <v-card style="height: 590px">
        <v-card-title style="background-color: white">
          <v-row>
            <v-col cols="10">

              <div class="d-block">
                <h4>Select file type</h4>
              </div>
              <div class="d-block">
                <p style="font-size: 15px">
                  Choose one or multiple file type you want to download
                </p>
              </div>
            </v-col>
            <v-col cols="2">
              <v-btn
                solo
                dense
                fab
                small
                @click="closeDownloadDialog"
                class="mt-3 float-right"
                style="border: 1px solid black; background-color: white"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text class="mb-0">
          <v-row class="mb-2">
            <v-col cols="3">
              <v-card
                width="130px"
                height="130px"
                class="elevation-3"
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox
                    v-model="pdf"
                    class="pa-0 ma-0"
                    style="background-color: white"
                    dense
                    solo
                  >
                  </v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 ma-1 mt-4"
                  >
                    <span style="width: 50px; height: 55px">
                      <img src="pdf.jpeg" alt="" />
                    </span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">PDF File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="130px"
                class="elevation-3"
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox
                    v-model="txt"
                    class="pa-0 ma-0"
                    style="background-color: white"
                    dense
                    solo
                  >
                  </v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 ma-1 mt-4"
                  >
                    <span style="width: 50px; height: 55px">
                      <img src="txt.jpeg" alt="" />
                    </span>
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
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox
                    v-model="csv"
                    class="pa-0 ma-0"
                    style="background-color: white"
                    dense
                    solo
                  >
                  </v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 ma-1 mt-4"
                  >
                    <span style="width: 50px; height: 55px">
                      <img src="csv.jpeg" alt="" />
                    </span>
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
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox
                    v-model="excel"
                    class="pa-0 ma-0"
                    style="background-color: white"
                    dense
                    solo
                  >
                  </v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 ma-1 mt-4"
                  >
                    <span style="width: 50px; height: 55px">
                      <img src="excel.jpeg" alt="" />
                    </span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">EXCEL File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="130px"
                class="elevation-3"
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox
                    v-model="json"
                    class="pa-0 ma-0"
                    style="background-color: white"
                    dense
                    solo
                  >
                  </v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    width="80px"
                    height="80px"
                    class="grey lighten-4 ma-1 mt-4"
                  >
                    <span style="width: 50px; height: 55px">
                      <img src="json.jpeg" alt="" />
                    </span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">JSON File</h4>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
          <v-divider> </v-divider>

        </v-card-text>
        <v-card-actions class="ma-0">
            <v-row>

                <v-col cols="12" class="mt-0 d-block">
                  <div class="d-flex">
                    <h4>Show who download this file</h4>
                    <v-spacer></v-spacer>
                    <v-switch v-model="user" inset class="pa-0 ma-0"></v-switch>
                  </div>
                  <div class="d-flex">
                    <h4>Show the current time & date of the downloaded file</h4>
                    <v-spacer></v-spacer>
                    <v-switch v-model="date" inset class="pa-0 ma-0"></v-switch>
                  </div>
                </v-col>
                <v-col cols="12" class="d-block mt-0" >
                  <v-row>
                  <v-col cols="7" class="mt-0"></v-col>
                  <v-col cols="5" class="mt-0">
                    <div class="d-flex float-right">
                      <v-btn
                        class="mr-2 mt-0"
                        color="primary"
                        outlined
                        dense
                        solo
                        small
                        @click="closeDownloadDialog"
                        >Cancel
                      </v-btn>
                      <v-btn
                        class="primary mt-0"
                        @click="download"
                        dense
                        solo
                        small
                        >Download</v-btn
                      >
                      <vue-csv-downloader :data="d_data" :fields="fields" id="downCSV" style="display:none;" >
                      download
                    </vue-csv-downloader>
                    </div>
                  </v-col>
                </v-row>
                </v-col>


            </v-row>

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
import VueCsvDownloader from 'vue-csv-downloader';
import { flash_key } from "vue-session";
export default {
  components: {
        VueCsvDownloader,
    },
  data() {
    return {
      pdf: false,
      excel: false,
      csv: false,
      txt: false,
      json: false,
      xml: false,
      personSwitch: false,
      downDialog: false,
      date: false,
      user: false,
      d_data: [],
      fields:[
        "id","logo","company_name","email","systems","location.country.name","investment_type","status"
      ],
    };
  },
  methods: {
    openModelDownload(data) {
      this.downDialog = true;
      this.d_data = data.data;
    },
    closeDownloadDialog() {
      this.downDialog = false;
    },
    download() {
      if (this.pdf == true) {
        const doc = new jsPDF("p", "pt", "letter");

        autoTable(doc, {
          styles: { fillColor: [255, 255, 255], textColor: [0, 0, 0] },
          columnStyles: { 0: { halign: "center" } },
          margin: { top: 10 },

          head: [
            [
              "ID",
              "LOGO",
              "NAME",
              "EMAIL",
              "SYSTEMS",
              "COUNTRY",
              "INVESTMENT TYPE",
              "STATUS",
            ],
          ],
          body: this.d_data,
          columns: [
            {
              text: "ID",
              dataKey: "id",
            },
            {
              text: "LOGO",
              dataKey: "logo",
            },
            {
              text: "NAME",
              dataKey: "company_name",
            },
            {
              text: "EMAIL",
              dataKey: "email",
            },
            {
              text: "SYSTEMS",
              dataKey: "systems",
            },
            {
              text: "COUNTRY",
              dataKey: "location.country.name",
            },
            {
              text: "INVESTMENT TYPE",
              dataKey: "investment_type",
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

        doc.save("companies.pdf");
        (this.downDialog = false), (this.pdf = null), (this.user = false),
          (this.date = false);;
      }
      if (this.excel == true) {
        saveExcel({
          data: this.d_data,
          fileName: "Companies",
          columns: [
            {
              title: "ID",
              field: "id",
            },
            {
              title: "LOGO",
              dataKey: "logo",
            },
            {
              title: "NAME",
              field: "company_name",
            },
            {
              title: "EMAIL",
              field: "email",
            },
            {
              title: "SYSTEMS",
              field: "systems",
            },
            {
              title: "COUNTRY",
              field: "location.country.name",
            },
            {
              title: "INVESTMENT TYPE",
              field: "investment_type",
            },
            {
              title: "STATUS",
              field: "status",
            },
          ],
        });
        (this.downDialog = false), (this.excel = null);
      }
      if (this.txt == true) {
        const FileSaver = require("file-saver");
        const data = this.d_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "companies.txt");
        (this.downDialog = false), (this.txt = null);
      }
      if (this.json == true) {
        const FileSaver = require("file-saver");
        const data = this.d_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "companies.json");
        (this.downDialog = false), (this.json = null);
      }
      if (this.csv == true){
        this.exportCSV();
        (this.downDialog = false), (this.csv=null);
      }

    },
    exportCSV(){
      var CSV_data = document.getElementById("downCSV");
      CSV_data.click();
    },
  },
};
</script>

<style>
</style>
