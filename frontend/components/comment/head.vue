<template>
<div>
  <template>
    <v-card class="mt-2 mb-6">
      <v-row>
        <v-card-title>
          <div class="d-flex flex-grow- align-center">
        <div class="d-flex flex-column align-center" style="width:100px">
          <v-avatar
            size="90"
            style="
              margin-left: 20px;
              background-color: lightblue;
            "
          >
            <span><i class="fa fa-comments" style="font-size: 30px"></i></span>
          </v-avatar>
        </div>
      </div>
      <div class="d-flex" style="width:500px">
        <span>
          <h2 class="ml-4 mt-5" style="height: 20px">Comment</h2>
          <span style="font-size: 15px"
            ><i
              class="fa fa-comments ml-4 mt-5"
              style="font-size: 15px; margin-right: 4px"
            ></i
            >All Comments >>
            <i
              class="fa fa-comments"
              style="font-size: 15px; margin-right: 4px"
            ></i
            >Comments</span
          >
        </span>
      </div>
        </v-card-title>
        <v-card-text></v-card-text>
      </v-row>
    </v-card>
</template>
</div>
</template>
<script>
export default{
  data(){
    return{

    };
  },
  methods:{

  },
}
</script>
