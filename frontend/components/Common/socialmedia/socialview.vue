<template>
  <div>
    <v-dialog v-model="dialog" persistent width="1000">
      <v-card>
        <div style="background-color: #1991d2a1; height: 150px;">
          <v-card-title>
            <h3 style="color: white;">Social Medias Profile</h3>
            <v-spacer></v-spacer>
            <v-btn fab x-small @click="closeView" style="float: right;">
              <svg
                width="50px"
                height="50px"
                viewBox="0 0 700 560"
                fill="currentColor'"
                style="transform: scaleY(-1);"
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
          <div style="padding: 10px;">
            <v-card elevation="2">
              <v-card-title>
                <v-avatar
                  size="120"
                  style="
                    background-color: #1991d2a1;
                    border: 10px solid lightgray;
                  "
                >
                  <span style="color: white;" class="fa-2x">
                    {{ data?.name?.charAt(0).toUpperCase() }}
                  </span>
                </v-avatar>
                <div>
                  <h3 id="h3style">{{ data.name }}</h3>
                  <v-btn
                    outlined
                    class="ml-5"
                    style="border-radius: 50px; color: green;"
                  >
                    {{ data.status }}
                  </v-btn>
                </div>
                <v-spacer></v-spacer>
                <i class="fa-solid fa-user" style="color: #1991d2a1;"></i>
                &nbsp;&nbsp;&nbsp;
                <h5>{{ data?.created_by?.first_name }}</h5>
                &nbsp;&nbsp;&nbsp;
                <v-btn outlined style="border-radius: 50px; color: gray;">
                  <i
                    class="fa-solid fa-calendar-days"
                    style="color: #1991d2a1;"
                  ></i>
                  &nbsp;
                  <h4>{{ data.updated_at }}</h4>
                </v-btn>
              </v-card-title>
            </v-card>
          </div>
        </div>
        <v-card-text
          style="padding-top: 80px; background-color: #eeeeee; height:450px;"
        >
          <v-row>
            <v-col cols="12" md="12"></v-col>
            <v-tabs
              mobile-breakpoint
              v-model="tab"
              background-color="transparent"
              align-with-title
              style="border-bottom-color: transparent; margin: 2px;"
            >
              <v-tabs-slider color="white" style="height: 1px;"></v-tabs-slider>
              <v-tab class="nav-tabs">
                <h4>General</h4>
              </v-tab>
              <v-tab class="nav-tabs">
                <h4>Remarks</h4>
              </v-tab>
              <v-tab class="nav-tabs">
                <h4>Status Information</h4>
              </v-tab>
              <v-tab-item>
                <v-card
                  color="basil"
                  width="1200px"
                  outlined
                  class="pa-0 primary"
                >
                  <template>
                    <v-simple-table dense class="table pa-5">
                      <template>
                        <tr class="text-left">
                          <th id="id1">Name</th>
                          <td>
                            <v-tooltip bottom>
                              <template v-slot:activator="{ on, attrs }">
                                <span
                                  v-bind="attrs"
                                  v-on="on"
                                ><a href="socialmedia" style="text-decoration:none">{{ data.name }}</a></span>
                              </template>
                              <span>{{ data.website }}</span>
                            </v-tooltip>

                          </td>
                          <th id="id1">Created by</th>
                          <td>{{ data?.created_by?.first_name }}</td>
                        </tr>
                        <tr class="text-left hover">
                          <th id="quantity1">Company</th>
                          <td>com</td>
                          <th id="quantity1">Created at</th>
                          <td>{{ data.created_at }}</td>
                        </tr>
                        <tr class="text-left">
                          <th id="quantity1">Industries</th>
                          <td>ind</td>
                          <th id="quantity1">Updated at</th>
                          <td>{{ data.updated_at }}</td>
                        </tr>
                      </template>
                    </v-simple-table>
                  </template>
                </v-card>
              </v-tab-item>
              <v-tab-item
                style="
                  height: 200px;
                  background-color: #eeeeee;
                  border: 1px solid #d3c7c7;
                  padding: 20px;
                "
              >
                <h3>Social Media Note</h3>
              </v-tab-item>
              <v-tab-item>
                <v-data-table  :headers="header" :items="datas">
                </v-data-table>
              </v-tab-item>
            </v-tabs>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  data() {
    return {
      singleSelect: false,
      selected: [],
      tab: null,
      dialog: false,
      users:[],
      data: [],
      datas: [],
      header: [
        { text: 'Status', value: 'status' },
        { text: 'Applied By', value: 'updated_by.first_name' },
        { text: 'Date', value: 'updated_at' },
        // {text:"Date",value:"date"},
      ],
    }
  },
  created() {
  },
  methods: {
    async getUser(){
      const user = await this.$axios.get('user');
      this.users=user.data.data;
    },
    tabchange(item) {
      this.tabkey = item
    },
    openView(item) {
      console.log('in view', item)
      this.data = item
      this.datas = [item]
      console.log(this.datas);
      this.dialog = !this.dialog
    },
    closeView() {
      this.dialog = false
    },
  },
}
</script>
<style scoped>
:deep() #h3style {
  color: #1991d2a1;
  padding-left: 20px;
}
:deep() .nav-tabs {
  /* border: 1px solid rgb(180, 188, 194) !important; */
  border-radius: 4px 4px;
  padding-left: 0px;
  margin-bottom: -1px;
  border-bottom-color: red !important;
}
:deep() .v-tab--active {
  border: 1px solid rgb(180, 188, 194) !important;
  border-bottom-color: red !important;
  margin-left: 0px !important;
}
:deep() .solid {
  border: 1px solid rgb(221, 227, 233);
  border-radius: 4px;
}
:deep() .table th,
.table td {
  padding: 15px;
}
:deep() .table :deep() .hover {
  background-color: #d1d1db;
}
:deep() tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05);
}
:deep() [data-v-17ea1a63].v-tab--active {
  border: 1px solid rgb(180, 188, 194) !important;
  border-bottom-color: #eeeeee !important;
  margin-left: 0px !important;
}
:deep() .v-tab {
  /* border: 1px solid rgb(180, 188, 194) !important; */
  border-bottom-color: #1991d2a1 !important;
  margin-left: 0px !important;
  padding-left: 10px !important;
  /* border-right: 1px solid rgb(205, 213, 220); */
  /* border-bottom-style: inset; */
}
:deep() @media (max-width: 800px) {
  .table td,
  .table th,
  .table tr {
    display: block;
    margin-top: -1px;
  }

  .table th {
    text-align: left;
  }

  .table td {
    padding-left: 30px;
  }
}
:deep() .v-window {
  /* margin-left:40px !important ; */
  margin-top: 10px;
}
:deep() .v-slide-group__content {
  border-bottom: 1px solid #9c9595;
  /* width:10% !important; */
  float: left;
}
:deep() .v-window {
  overflow: auto;
}
:deep() .v-application {
  font-family: 'Times New Roman', Times, serif !important;
  line-height: 1.5;
}
:deep() .primary {
  background-color: #1976d2 !important;
  border-color: #afa9a0 !important;
}
:deep() .v-data-table {
  background-color: #eeeeee;
  color: rgba(0, 0, 0, 0.87);
}
:deep().v-btn--fab {
    box-shadow:none !important;
}
:deep().theme--light.v-btn.v-btn--has-bg {
    background-color:transparent;
    color:white !important;
}
</style>
<style>

</style>
