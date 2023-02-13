<template>
  <div>
    <template>
      <div class="text-center">
        <v-dialog v-model="dialog2" width="600">
          <v-card>
            <v-card-title class="lighten-2">
              <v-row>
                <v-col cols="10">
                  <div class="d-block" style="font-size: 28px">
                    <b>Select File Type</b> <br />
                  </div>
                  <div style="font-size: 15px">
                    Choose one or multiple type you want to download
                  </div>
                </v-col>
                <v-col cols="2">
                  <v-btn
                    outlined
                    fab
                    small
                    color="indigo"
                    @click="cancel"
                    style="float: right"
                  >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-title>
            <v-card-text>
              <br />
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
                      style="height: 20px"
                    >
                      <v-checkbox
                        v-model="excel"
                        class="pa-0 ma-0"
                      ></v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 50px; height: 55px"
                          ><img src="Excel.jpeg" alt=""
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
                      style="height: 20px"
                    >
                      <v-checkbox
                        v-model="json"
                        class="pa-0 ma-0"
                      ></v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 50px; height: 55px"
                          ><img src="json.jpeg" alt=""
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
                      style="height: 20px"
                    >
                      <v-checkbox
                        v-model="csv"
                        class="pa-0 ma-0"
                      ></v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 50px; height: 55px"
                          ><img src="csv.jpeg" alt=""
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
                      style="height: 20px"
                    >
                      <v-checkbox
                        v-model="txt"
                        class="pa-0 ma-0"
                      ></v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 50px; height: 55px"
                          ><img src="txt.jpeg" alt=""
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
                      style="height: 20px"
                    >
                      <v-checkbox
                        v-model="pdf"
                        class="pa-0 ma-0"
                      ></v-checkbox>
                    </v-card-title>
                    <v-card-text>
                      <v-avatar
                        width="80px"
                        height="80px"
                        class="grey lighten-4 ma-1"
                      >
                        <span style="width: 50px; height: 55px"
                          ><img src="pdf.jpeg" alt=""
                        /></span>
                      </v-avatar>
                      <h4 class="text-center font-weight-black">PDF File</h4>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
              <br>
            <v-divider></v-divider>
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
             <br>

              <v-btn color="primary" outlined @click="dialog2 = false">
                Cancel
              </v-btn>
              <v-btn color="primary" @click="downloadFile"> Download </v-btn>
               <vue-csv-downloader :data="d_data" :fields="fields" id="dwnCsv" style="display:none">
                      download
                    </vue-csv-downloader>
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
import VueCsvDownloader from 'vue-csv-downloader';
export default {
  components: {
        VueCsvDownloader,
    },
data(){
    return {
      dialog2: false,
      json: false,
      csv: false,
      txt: false,
      excel: false,
      pdf: false,
      d_data: [],
      user:false,
      date:false
    };
},
methods:{
    opneModleD(data){
        this.dialog2=true;
        this.d_data= data.data;
    },
    downloadFile(){
        if(this.pdf==true)
        {
             const doc = new jsPDF("p", "pt", "letter");

        autoTable(doc, {
          styles: { fillColor: [255, 255, 255], textColor: [0, 0, 0] },
          columnStyles: { 0: { halign: "center" } },
          margin: { top: 10 },

          head: [
            ["ID", "First Name", "Lastname", "Username", "Email", "Gender", "Birth date"],
          ],
          body: this.d_data,
          columns: [
            {
              text: "ID",
              dataKey: "id",
            },
            {
              text: "First Name",
              dataKey: "first_name",
            },
            {
              text: "Lastname",
              dataKey: "last_name",
            },
            {
              text: "username",
              dataKey: "user_name",
            },
            {
              text: "Email",
              dataKey: "email",
            },

            {
              text: "Gender",
              dataKey: "gender",
            },

            {
              text: "Birth Date",
              dataKey: "birth_date",
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
        doc.save("User.pdf");
        (this.dialog2 = false), (this.pdf = null);
        }
        if (this.excel == true) {
        saveExcel({
          data: this.d_data,
          fileName: "User",
              columns: [
            {
              title: "ID",
              field: "id",
            },
            {
              title: "First Name",
              field: "first_name",
            },
            {
              title: "Lastname",
              field: "last_name",
            },
            {
              title: "username",
              field: "user_name",
            },
            {
              title: "Email",
              field: "email",
            },

            {
              title: "Gender",
              field: "gender",
            },

            {
              title: "Birth Date",
              field: "birth_date",
            },
          ],
        });
        (this.dialog2 = false), (this.excel = null);
      }
      if (this.txt == true) {
        const FileSaver = require("file-saver");
        const data = this.d_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "user.txt");
        (this.dialog2 = false), (this.txt = null);
      }
        if (this.json == true) {
        const FileSaver = require("file-saver");
        const data = this.d_data;
        const json = JSON.stringify(data);
        const file = new Blob([json], {
          type: "text/plain;charset=utf-8",
        });
        saveAs(file, "user.json");
        (this.dialog2 = false), (this.json = null);
      }
       if (this.csv == true) {
        this.exportCsv();
        (this.dialog2 = false), (this.csv = null);
      }
    },
     exportCsv(){
      var link = document.getElementById("dwnCsv");
			link.click();
    },
     cancel() {
      (this.dialog2 = false),
        (this.json = null),
        (this.csv = null),
        (this.txt = null),
        (this.excel = null),
        (this.pdf = null);
    },

}
}
</script>

<style scoped>

</style>
