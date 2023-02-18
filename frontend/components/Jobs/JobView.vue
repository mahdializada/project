<template>
    <div class="text-center">
        <v-dialog v-model="dialog2" width="1000" height="600" persistent >
          <v-card>
            <div style="background-color: #1991d2a1; height: 150px;">
              <v-card-title>
                <h3 style="color: white;">Job Deatils</h3>
                <v-spacer></v-spacer>
                <v-btn fab x-small @click="colesDialog" style="float: right;">
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
                    <div>
                      <h3 id="h3style">{{ jobDeatil.title }}</h3>
                    </div>
                    <v-spacer></v-spacer>
                    <!-- <i class="fa-solid fa-user" style="color: #1991d2a1;"></i> -->
                    &nbsp;&nbsp;&nbsp;
                    <h5>Closing Date</h5>
                    &nbsp;&nbsp;&nbsp;
                    <v-btn outlined style="border-radius: 50px; color: gray;">
                      <i
                        class="fa-solid fa-calendar-days"
                        style="color: #1991d2a1;"
                      ></i>
                      &nbsp;
                      <h4>{{ jobDeatil.closing_date }}</h4>
                    </v-btn>
                  </v-card-title>
                </v-card>
              </div>
            </div>
            <v-card-text style="padding-top: 80px; background-color: #eeeeee;">
              <v-row>
                 <v-tabs
                    mobile-breakpoint
                    v-model="tab"
                    background-color="transparent"
                    align-with-title
                    style="border-bottom-color: transparent; margin: 2px;">
                    <v-tabs-slider color="white" style="height: 1px;"></v-tabs-slider>
                    <v-tab class="nav-tabs">
                      <h4>General</h4>
                    </v-tab>
                    <v-tab class="nav-tabs">
                      <h4>Deatils</h4>
                    </v-tab>
                    <v-tab-item>
                      <v-card
                        color="basil"
                        width="1200px"
                        height="350px"
                        outlined
                        class="pa-0 ">
                        <template>
                          <v-simple-table dense class="table pa-5">
                            <template>
                              <tr class="text-left">
                                <th id="id1">Title</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.title }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.title }}</span>
                                  </v-tooltip>

                                </td>
                                <th id="id1">Job Locations</th>
                                 <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.job_locations }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.job_locations }}</span>
                                  </v-tooltip>

                                </td>
                                
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Number of Jobs</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.number_of_jobs }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.number_of_jobs }}</span>
                                  </v-tooltip>

                                </td>
                                <th id="id1">Attachments</th>
                                 <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.attachments }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.attachments }}</span>
                                  </v-tooltip>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Functional Area</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.functional_area }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.functional_area }}</span>
                                  </v-tooltip>

                                </td>
                                <th id="id1">Submission Email</th>
                                 <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.submission_email }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.submission_email }}</span>
                                  </v-tooltip>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Gender</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.gender }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.gender }}</span>
                                  </v-tooltip>

                                </td>
                                <th id="id1">User</th>
                                 <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{  user}}</a></span>
                                    </template>
                                    <span>{{ user }}</span>
                                  </v-tooltip>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Job Gategory</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ category  }}</a></span>
                                    </template>
                                    <span>{{ category  }}</span>
                                  </v-tooltip>

                                </td>
                                <th id="id1">Number Of Views</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{jobDeatil.number_of_views}}</a></span>
                                    </template>
                                    <span>{{jobDeatil.number_of_views}}</span>
                                  </v-tooltip>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">slug</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.slug }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.slug }}</span>
                                  </v-tooltip>

                                </td>
                                <th id="id1">Employer</th>
                                 <td>
                                  
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ employer}}</a></span>
                                    </template>
                                    <span>{{employer }}</span>
                                  </v-tooltip>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Salary Range</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.salary_range }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.salary_range }}</span>
                                  </v-tooltip>

                                </td>
                                <th id="id1">Is promot</th>
                                 <td>
                                   <template>
                                      <v-switch
                                        :loading="primary"
                                        :input-value="jobDeatil.is_promoted ?? 0"
                                        @click="changePromote(id)"
                                        class="pa-0"
                                      ></v-switch>
                                   </template>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Reference No</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.reference_no }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.reference_no }}</span>
                                  </v-tooltip>

                                </td>
                               <th id="id1">Is Urgent</th>
                                 <td>
                                 <template>
                                      <v-switch
                                        :loading="primary"
                                        :input-value="jobDeatil.is_urgent ?? 0"
                                        @click="changeUrgent(id)"
                                        class="pa-0"
                                      ></v-switch>
                                   </template>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Contract Duration</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.contract_duration }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.contract_duration }}</span>
                                  </v-tooltip>

                                </td>
                                  <th id="id1">Probation Period</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.probation_period }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.probation_period }}</span>
                                  </v-tooltip>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Work Type</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.work_type }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.work_type }}</span>
                                  </v-tooltip>

                                </td>
                               <th id="id1">Contract Extension</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.contract_extension }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.contract_extension }}</span>
                                  </v-tooltip>

                                </td>
                              </tr>
                              <tr class="text-left">
                                <th id="id1">Nationality</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.nationality }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.nationality }}</span>
                                  </v-tooltip>

                                </td>
                                  <th id="id1">Education</th>
                                  <td>
                                    <v-tooltip bottom>
                                      <template v-slot:activator="{ on, attrs }">
                                        <span
                                          v-bind="attrs"
                                          v-on="on"
                                        ><a style="text-decoration:none">{{ jobDeatil.education }}</a></span>
                                      </template>
                                      <span>{{ jobDeatil.education }}</span>
                                    </v-tooltip>

                                  </td>
                                </tr>
                              <tr class="text-left">
                                <th id="id1">experience</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.experience }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.experience }}</span>
                                  </v-tooltip>

                                </td>
                                <th id="id1">Closing Date</th>
                                <td>
                                  <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                      <span
                                        v-bind="attrs"
                                        v-on="on"
                                      ><a style="text-decoration:none">{{ jobDeatil.closing_date }}</a></span>
                                    </template>
                                    <span>{{ jobDeatil.closing_date }}</span>
                                  </v-tooltip>

                                </td>
                              </tr>
                            </template> 
                          </v-simple-table>
                        </template>
                      </v-card>
                    </v-tab-item>
                    <v-tab-item>
                      <v-card
                        color="basil"
                        width="1200px"
                        height="350px"
                        outlined
                        class="pa-0 ">
                        <template>
                          <v-simple-table dense class="table pa-5">
                            <template>
                              <tr class="text-left">
                                <th id="id1">Job Descriptions</th>
                              </tr>
                              <tr>
                                 <td>
                                  <span v-html="jobDeatil.job_descriptions"></span>

                                </td>
                              </tr>
                            </template> 
                            <template>
                              <tr class="text-left">
                                <th id="id1">Job Requireements</th>
                              </tr>
                              <tr>
                                 <td>
                                  <span v-html="jobDeatil.job_requirements"></span>

                                </td>
                              </tr>
                            </template> 
                            <template>
                              <tr class="text-left">
                                <th id="id1">Submission Guideline</th>
                              </tr>
                              <tr>
                                 <td>
                                  <span v-html="jobDeatil.submission_guideline"></span>

                                </td>
                              </tr>
                            </template> 
                          </v-simple-table>
                        </template>
                      </v-card>
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
      load: false,
      dialog2: false,
      jobDeatil:"",
      id:"",
      user:"",
      employer:"",
      category:"",

    };
  },
  methods:{
    openView(item) {

      console.log(item);
      if(item){
        this.category=item.category.category;
        this.employer=item.employer.name;
        this.user=item.user.name;
        this.id=item.id;
      }
      this.dialog2 = true;
      this.jobDeatil=item;
      this.id=item.id;
      
    },
    colesDialog()
    {
        this.dialog2=false;
    },
    async changeUrgent(id){
        console.log(id);
    //   this.$emit("changeUrgent", id);
    const newUrgent = await this.$axios.post(`urgent/${id}`);
    if(newUrgent){
        console.log("success");
    }else{
        console.log("erroro");
    }
        
    },
    async changePromote(id){
        console.log(id);
      const newUrgent = await this.$axios.post(`promot/${id}`);
    if(newUrgent){
        console.log("success");
    }else{
        console.log("erroro");
    }

    }
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

:deep().v-btn--fab {
    box-shadow:none !important;
}
:deep().theme--light.v-btn.v-btn--has-bg {
    background-color:transparent;
    color:white !important;
}
</style>
