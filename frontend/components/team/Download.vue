<template>
  <div>
    <template>
      <div class="text-center">
        <v-dialog v-model="dialog" width="800">
          <v-card class="grey lighten-5 pa-3">
            <v-card-title>
              <v-row>
                <v-col cols="10">
                  <div class="d-block" style="font-size: 25px">
                    <b>Select file type</b> <br />
                  </div>
                  <div style="font-size: 15px" class="">
                    Choose one or multiple type you want to download
                  </div>
                </v-col>
                <v-col cols="2">
                  <v-btn fab x-small @click="close" style="float: right">
                    <svg
                      width="60"
                      height="70"
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
                </v-col>
              </v-row>
            </v-card-title>
            <v-card-text>
              <br />
              <v-row>
                <div>
                  <v-card
                    width="130px"
                    height="130px"
                    class="elevation-5 pa-1"
                    style="border-radius: 15px"
                  >
                    <v-card-title
                      class="pa-1 mt-0 float-right"
                      style="height: 10px"
                    >
                      <v-checkbox v-model="json" class="pa-0 ma-0">
                      </v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 40px; height: 50px">
                          <img src="icons/Json.png" alt="" />
                        </span>
                      </v-avatar>
                      <h4 class="text-center font-weight">JSON File</h4>
                    </v-card-text>
                  </v-card>
                </div>
                <div class="ml-5">
                  <v-card
                    width="130px"
                    height="130px"
                    class="elevation-5 pa-1"
                    style="border-radius: 15px"
                  >
                    <v-card-title
                      class="pa-1 mt-0 float-right"
                      style="height: 10px"
                    >
                      <v-checkbox v-model="csv" disabled class="pa-0 ma-0"> </v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 40px; height: 50px">
                          <img src="icons/csv.png" alt="" />
                        </span>
                      </v-avatar>
                      <h4 class="text-center font-weight">CSV File</h4>
                    </v-card-text>
                  </v-card>
                </div>
                <div class="ml-5">
                  <v-card
                    width="130px"
                    height="130px"
                    class="elevation-5 pa-1"
                    style="border-radius: 15px"
                  >
                    <v-card-title
                      class="pa-1 mt-0 float-right"
                      style="height: 10px"
                    >
                      <v-checkbox v-model="txt" class="pa-0 ma-0"> </v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 40px; height: 50px">
                          <img src="icons/txt.png" alt="" />
                        </span>
                      </v-avatar>
                      <h4 class="text-center font-weight">TXT File</h4>
                    </v-card-text>
                  </v-card>
                </div>
                <div class="ml-5">
                  <v-card
                    width="130px"
                    height="130px"
                    class="elevation-5 pa-1"
                    style="border-radius: 15px"
                  >
                    <v-card-title
                      class="pa-1 mt-0 float-right"
                      style="height: 10px"
                    >
                      <v-checkbox v-model="xml" disabled class="pa-0 ma-0"> </v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 40px; height: 50px">
                          <img src="icons/xml.png" alt="" />
                        </span>
                      </v-avatar>
                      <h4 class="text-center font-weight">XML File</h4>
                    </v-card-text>
                  </v-card>
                </div>
                <div class="ml-5">
                  <v-card
                    width="130px"
                    height="130px"
                    class="elevation-5 pa-1"
                    style="border-radius: 15px"
                  >
                    <v-card-title
                      class="pa-1 mt-0 float-right"
                      style="height: 10px"
                    >
                      <v-checkbox v-model="sql" disabled class="pa-0 ma-0"> </v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 40px; height: 50px">
                          <img src="icons/sql.png" alt="" />
                        </span>
                      </v-avatar>
                      <h4 class="text-center font-weight">SQL File</h4>
                    </v-card-text>
                  </v-card>
                </div>
                <div class="mt-5">
                  <v-card
                    width="130px"
                    height="130px"
                    class="elevation-5 pa-1"
                    style="border-radius: 15px"
                  >
                    <v-card-title
                      class="pa-1 mt-0 float-right"
                      style="height: 10px"
                    >
                      <v-checkbox v-model="excel" class="pa-0 ma-0">
                      </v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 40px; height: 50px">
                          <img src="icons/excel.png" alt="" />
                        </span>
                      </v-avatar>
                      <h4 class="text-center font-weight">EXCEL File</h4>
                    </v-card-text>
                  </v-card>
                </div>
                <div class="ml-5 mt-5">
                  <v-card
                    width="130px"
                    height="130px"
                    class="elevation-5 pa-1"
                    style="border-radius: 15px"
                  >
                    <v-card-title
                      class="pa-1 mt-0 float-right"
                      style="height: 10px"
                    >
                      <v-checkbox v-model="pdf" class="pa-0 ma-0"> </v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 40px; height: 50px">
                          <img src="icons/pdf.png" alt="" />
                        </span>
                      </v-avatar>
                      <h4 class="text-center font-weight">pdf File</h4>
                    </v-card-text>
                  </v-card>
                </div>
              </v-row>
              <br /><br />
              <v-divider></v-divider>
              <div class="d-flex ">
                <h4 class="mt-5">Show who downloaded this file</h4>
                <v-spacer></v-spacer>
                <v-switch
                  input-value="false"
                  v-model="switch1"
                  x-small
                  inset
                  class="pa-0"
                ></v-switch>
              </div>
              <div class="d-flex">
                <h4>
                  Show the current time & date of the downloaded file
                </h4>
                <v-spacer></v-spacer>
                <v-switch
                  input-value="false"
                  v-model="switch2"
                  inset
                  flat
                  x-small
                  class="pa-0 ma-0"
                ></v-switch>
              </div>
            </v-card-text>
            <v-card-actions class="mr-5">
              <v-spacer></v-spacer>
              <br /><br /><br />
              <v-btn
                color="primary darken-1"
                style="height: 35px; font-weight: 900; width: 90px"
                class="elevation-2"
                dense
                solo
                outlined
                @click="close"
              >
                Cancel
              </v-btn>
              <v-btn
                color="white  darken-1"
                class="elevation-2 primary"
                style="height: 35px; font-weight: 900; width: 110px"
                depressed
                dense
                solo
                text
                @click="downloadFile"
              >
                Download
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
    </template>
  </div>
</template>

<script>
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import { saveExcel } from "@progress/kendo-vue-excel-export";
import { saveAs } from "file-saver";
export default {
  data() {
    return {
      dialog: false,
      switch1: false,
      switch2: false,
      json: false,
      csv: false,
      txt: false,
      excel: false,
      pdf: false,
      xml: false,
      sql: false,
      download_data: [],
    };
  },
  methods: {
    openDialog(data) {
      this.dialog = true;
      this.download_data = data;
    },
    close() {
        (this.dialog = false);
        (this.json = false),
        (this.csv = false),
        (this.txt = false),
        (this.excel = false),
        (this.pdf = false),
        (this.xml = false),
        (this.sql = false),
        (this.switch1 = false),
        (this.switch2 = false);
    },
    downloadFile(){
      if (this.excel == true) {
        saveExcel({
          data: this.download_data,
          fileName: "Team",
          columns: [
            {
              field: "id",
              title: "ID",
              width:40
            },
            {
              field: "team_name",
              title: "Name",
            },
            {
              field: "note",
              title:"Note",
            },
            {
              field: "department.department_name",
              title: "Department",
            },
            {
              field: "status",
              title: "Status",
              width:90,
            },
          ],
        });
        (this.dialog = false);
        (this.excel = false);
        (this.switch1 = false);
        (this.switch2 = false);
      }
      if (this.pdf == true) {
        const doc = new jsPDF("1","mm",[297, 210]);

        autoTable(doc, {
          theme: "grid",
          styles: {
            fontSize: 12,
            cellPadding:{top:1, right:0.5, bottom:1, left:0.5 },
            fillColor: [255, 255, 255],
            textColor: [0, 0, 0]
          },
          columnStyles:{0: {halign:"center"}},
          margin: {top: 20, right: 5, bottom: 15, left: 5},

          head: [
            ["ID", "Name", "Note", "Department","Status"]
          ],
          body: this.download_data,
          columns:[
            {
              text: "ID",
              dataKey: "id",
            },
            {
              text: "Name",
              dataKey: "team_name",
            },{
              text: "Note",
              dataKey: "note",
            },{
              text: "Department",
              dataKey: "department_id",
            },{
              text: "Status",
              dataKey: "status",
            },
          ]
        });
        doc.save("Team");
        (this.dialog = false), (this.pdf = false);
        (this.switch1 = false);
        (this.switch2 = false);
      }
      if (this.txt == true) {
        const FileSaver = require("file-saver");
        const data = this.download_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "Team.txt");
        (this.dialog = false), (this.json = false);
        (this.switch1 = false);
        (this.switch2 = false);
      }
      if (this.json == true) {
        const FileSaver = require("file-saver");
        const data = this.download_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "Team.json");
        (this.dialog = false), (this.txt = false);
        (this.switch1 = false);
        (this.switch2 = false);
      }
    },
  },
};
</script>

<style scoped>
</style>