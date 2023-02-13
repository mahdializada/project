<template>
  <div>
    <v-dialog v-model="dialog" max-width="600">
      <v-card>
        <v-card-title class="font-weight-bold" style="font-size:medium">
          Choose File Type <br>
          You can download one or many files the same time
          <v-spacer></v-spacer>
          <v-btn fab x-small @click="cancel">
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
          <!-- <v-icon @click="dialog = false">mdi-close-circle-outline</v-icon> -->
        </v-card-title>

        <v-card-text>
          <v-row>
            <v-col cols="3">
              <v-card
                width="130px"
                height="135px"
                class="elevation-3"
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox v-model="json" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    size="80"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px">
                      <img src="icons/json.png" alt="">
                    </span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">Json File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="135px"
                class="elevation-3"
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox v-model="csv" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    size="80"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px">
                      <img src="icons/csv.png" alt="">
                    </span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">Csv File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="135px"
                class="elevation-3"
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox v-model="txt" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    size="80"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px">
                      <img src="icons/txt.png" alt="">
                    </span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">Txt File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="135px"
                class="elevation-3"
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox v-model="excel" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    size="80"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px">
                      <img src="icons/excel.png" alt="">
                    </span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">Excel File</h4>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="3">
              <v-card
                width="130px"
                height="135px"
                class="elevation-3"
                style="border-radius: 15px"
              >
                <v-card-title
                  class="pa-0 mt-0 float-right"
                  style="height: 20px"
                >
                  <v-checkbox v-model="pdf" class="pa-0 ma-0"></v-checkbox>
                </v-card-title>
                <v-card-text>
                  <v-avatar
                    size="80"
                    class="grey lighten-4 mt-4"
                  >
                    <span style="width: 40px; height: 45px">
                      <img src="icons/pdf.png" alt="">
                    </span>
                  </v-avatar>
                  <h4 class="text-center font-weight-black">Pdf File</h4>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary darken-1"
            depressed
            outlined
            class="elevation-2"
            dense
            small
            solo
            @click="dialog= false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary darken-1"
            depressed
            outlined
            class="elevation-2"
            dense
            small
            solo
            @click="downloadFormat"
          >
            Download
          </v-btn>
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
export default {
  data() {
    return {
      dialog: false,
      json: false,
      csv: false,
      txt: false,
      excel: false,
      pdf: false,
      download_data: [],
    }
  },
  methods: {
    openDownloadDialog(data){
      this.dialog = true;
      this.download_data = data;
    },
    downloadFormat(){
      if(this.pdf==true)
        {
          console.log(this.download_data.data);
             const doc = new jsPDF("p", "pt", "letter");

        autoTable(doc, {
          styles: { fillColor: [255, 255, 255], textColor: [0, 0, 0] },
          columnStyles: { 0: { halign: "center" } },
          margin: { top: 10 },

          head: [
            ["ID", "Department Name", "Status"],
          ],
          body: this.download_data.data,
          columns: [
            {
              text: "ID",
              dataKey: "id",
            },
            {
              text: "First Name",
              dataKey: "department_name",
            },
            {
              text: "Status",
              dataKey: "status",
            },
          ],
        });

        doc.save("Department.pdf");
        (this.dialog = false), (this.pdf = null);
        }
      if(this.excel == true){
        saveExcel({
          data: this.download_data.data,
          fileName: "Department",
          columns: [
            { field: "id", title: "ID"},
            { field: "department_name", title: "NAME"},
            { field: "status", title: "STATUS"},
          ],
        });
        (this.dialog = false), (this.excel = null);
      }
      if(this.txt == true){
        const FileSaver = require("file-saver");
        const data = this.download_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "Department.txt");
        (this.dialog = false), (this.txt = null);
      }
      if(this.json == true){
        const FileSaver = require("file-saver");
        const data = this.download_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "Department.json");
        (this.dialog = false), (this.json = null);
      }
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

<style scoped>
.v-messages {
  display: none;
}
.v-input--selection-controls__input {
  margin-right: 0px !important;
}
</style>
