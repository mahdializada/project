<template>
  <v-app
    class=""
    style="height:100%;background-color: blue; border-radius: 0px 15px 15px 0px"
  >
    <div class="">
      <span text="" style="hieght: 20%">
        <v-img
          src="logo.png"
          max-width="80px"
          max-height="80px"
          style="margin-left: 10%; margin-top: 10%"
        ></v-img>
      </span>
    </div>
    <v-divider color="white" style="margin-top: 20px"></v-divider>
    <div>

        <v-list-item to="/">
          <span style="width: 10%; margin-right: 5px"
            ><i class="fa fa-bars" style="color: white"></i
          ></span>
          <v-list-item-title
            :value="true"
            style="color: white"
            class="text-bold"
            >Dashboard</v-list-item-title
          >
        </v-list-item>
        <v-list-group>
          <template v-slot:activator>
            <span style="width: 15%; margin-right: 5px"
              ><i style="color: white" class="fa-solid fa-gear"></i
            ></span>
            <v-list-item-title  style="color: white"
              >Master Management System</v-list-item-title
            >
          </template>
          <v-list-item style="margin-left: 10%" to="country">
            <span style="margin-left: 0"
              ><i style="color: white" class="fa fa-globe"></i
            ></span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >Countries</v-list-item-title
            >
          </v-list-item>

          <v-list-item style="margin-left: 10%" to="company">
            <span style="margin-left: 0"
              ><i style="color: white" class="fa fa-building"></i
            ></span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >Companies</v-list-item-title
            >
          </v-list-item>

          <v-list-item style="margin-left: 10%" to="department">
            <span style="margin-left: 0"
              ><i style="color: white" class="fa fa-sitemap"></i
            ></span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >Departments</v-list-item-title
            >
          </v-list-item>

          <v-list-item style="margin-left: 10%" to="socialmedia">
            <v-span style="margin-left: 0"
              ><v-i style="color: white" class="fa fa-thumbs-up"></v-i
            ></v-span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >Social Media</v-list-item-title
            >
          </v-list-item>
        </v-list-group>

        <v-list-group>
          <template v-slot:activator>
            <span style="width: 15%; margin-right: 5px"
              ><i style="color: white" class="fa-solid fa-user"></i
            ></span>
            <v-list-item-title style="color: white"
              >User Management System</v-list-item-title
            >
          </template>
          <v-list-item style="margin-left: 10%" to="user">
            <v-span style="margin-left: 0"
              ><v-i style="color: white" class="fa fa-user"></v-i
            ></v-span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >User List</v-list-item-title
            >
          </v-list-item>

          <v-list-item style="margin-left: 10%" to="team">
            <span style="margin-left: 0"
              ><i style="color: white" class="fa fa-user-group"></i
            ></span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >Team List</v-list-item-title
            >
          </v-list-item>

          <v-list-item style="margin-left: 10%" to="countries">
            <span style="margin-left: 0"
              ><i style="color: white" class="fa fa-circle-check"></i
            ></span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >Role List</v-list-item-title
            >
          </v-list-item>

          <v-list-item style="margin-left: 10%" to="countries">
            <span style="margin-left: 0"
              ><i style="color: white" class="fa fa-gear"></i
            ></span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >Setting</v-list-item-title
            >
          </v-list-item>
          <v-list-item style="margin-left: 10%" to="countries">
            <span style="margin-left: 0"
              ><i style="color: white" class="fa fa-user"></i
            ></span>
            <v-list-item-title style="margin-left: 5%; color: white"
              >User Activity</v-list-item-title
            >
          </v-list-item>
        </v-list-group>

    </div>
  </v-app>
</template>
<script>
export default {

};
</script>
<style scoped>
.v-application--wrap {
  min-height: 100vh;
}
.v-list-item--link:before {
  background-color: none;
}
.v-list-item_icon v-list-groupheader_append-icon {
  margin: 0;
}
.v-list-group__header v-list-item v-list-item--link theme--light {
  margin: 0;
}
</style>
