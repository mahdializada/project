<template>
    <div>
      <v-card>
          <v-card-title>
            <!-- <div class="d-flex">
              <div style="width:90%">
                <h5 class="">Note Name</h5>
  
              </div>
              <div style="width:10%">
                <v-icon color="primary">
                  mdi-pencil
                </v-icon>
              </div>
            </div> -->
            <div class="d-flex">
                <h5 class="">Note Name</h5>
            </div>
            <div class="d-flex" style="padding-left:70px;">
              <v-icon color="primary">
                  mdi-pencil
              </v-icon>
            </div>
            <div class="d-flex" style="padding-left:10px;">
              <div style="border-left:1px solid lightgray; height:20px; padding-right:7px; margin-top:2px;">
              </div>
                <v-icon color="primary" >
                    mdi-close
                </v-icon>
            </div>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
           <div class="ml-2 mr-2 mt-1">
              <span>
               Lorem, ipsum dolor sit amet consectetur adipisicing elit. Suscipit amet expedita accusantium,
               assumenda dignissimos, obcaecati aspernatur impedit quo voluptatum. 
              </span>
              <br>
              <div style="background-color:#EEEEEE; height:35px">
                <div class="d-flex">
                  <div style="width:10%">
                    <v-icon>
                      mdi-play
                    </v-icon>
                  </div>
                  <div style="width:90%">
                    <v-range-slider
                      strict
                    ></v-range-slider>
                  </div>
                </div>
                
               
              </div>
              <br>
              <div style="background-color:#EEEEEE; height:35px">
                <div class="d-flex">
                  <div style="width:10%">
                    <v-icon>
                      mdi-play
                    </v-icon>
                  </div>
                  <div style="width:90%">
                    <v-range-slider
                      strict
                    ></v-range-slider>
                  </div>
                </div>
               
              </div>
                <br>
           </div>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
              <div class="d-flex pa-1">
                <div style="width:80%"></div>
                <div style="width:20%">
                  <v-avatar  style="background-color: #FD928A;">
                    <h4 style="color:#F6F4F4">H</h4>
                  </v-avatar>
  
                </div>
              </div>
          </v-card-actions>
      </v-card>
    </div>
  </template>
  
  <script>
  
  export default {
    data(){
      return {
        value:null,
      }
    }
  }
  </script>
  
  <style>
  
  </style>