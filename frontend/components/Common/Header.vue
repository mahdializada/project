<template>
  <div>
    <v-toolbar >
      <v-card class="d-flex v-card--flat v-sheet">
        <div class="d-flex">
          <div class="d-flex justify-content-bettween">
            <div><i class="fa-solid fa-bars-sort"></i></div>
          <v-app-bar-nav-icon @click.stop="$emit('drawing')" />

          </div>
          <div class="flex-column mt-1" style="width: 300px">
            <v-text-field
              style="border-radius: 30px 30px 30px 30px"
              label="Search (press 'control + /'to focus)"
              solo
              dense
              class="text-center"
            ></v-text-field>
          </div>
          <div
            class="flex-column mt-1"
            style="width: 220px; margin-left: 150px"
          >
            <v-select
              :items="company"
              label="Select"
              dense
              solo
              style="border-radius: 30px 30px 30px 30px"
            >
              <template v-slot:selection="{ item }">
                <img
                  :src="item.image"
                  width="25px"
                  height="20px"
                  style="border-radius: 50% 50%"
                />{{ item.name }}
              </template>

              <template v-slot:item="{ item }">
                <img
                  :src="item.image"
                  width="25px"
                  style="border-radius: 50% 50%; margin-right: 5px"
                  height="20  px"
                />{{ item.name }}
                <v-switch
                  input-value="true"
                  disabled
                  inset
                  style="float: right; margin-left: 30px"
                  >on disabled</v-switch
                >
              </template>

            </v-select>
          </div>
          <div class="flex-column mt-1 ml-5" style="width: 220px">
            <v-select
              :items="items"
              label="Select"
              dense
              solo
              style="border-radius: 30px 30px 30px 30px"
            >
              <template v-slot:selection="{ item }">
                <img :src="item.image" width="25px" height="15px" />{{
                  item.name
                }}
              </template>

              <template v-slot:item="{ item }">
                <img
                  :src="item.image"
                  width="25px"
                  height="15px"
                  style="margin-right: 5px"
                />{{ item.name }}
              </template>
            </v-select>
          </div>
          <div class="flex-column mt-1" style="width: 30px">
            <i
              class="fa fa-bell"
              style="font-size: 20px; margin-top: 10px; margin-left: 20px"
            ></i>
          </div>
          <div class="flex-column" style="width: 80px">
            <v-menu bottom min-width="100px" rounded offset-y>
              <template v-slot:activator="{ on }">
                <v-btn icon v-on="on" style="float: right">
                  <v-avatar size="40">
                    <img :src="$auth.user.data.image" alt="no image" />
                    <span class="white--text text-h5"></span>
                  </v-avatar>
                </v-btn>
              </template>
              <v-card max-width="100">
                <v-list-item-content class="justify-center">
                  <v-btn depressed rounded text>
                    <span
                      ><i class="fa fa-user-circle" style="font-size: 15px"></i
                    ></span>
                    Profile</v-btn
                  >
                  <v-divider class="my-3"></v-divider>
                  <div v-if="!$auth.LoggedIn">
                    <v-btn
                      depressed
                      rounded
                      text
                      style="font-size: 15px"
                      type="button"
                      @click="logout"
                    >
                      <v-span
                        ><i
                          style="font-size: 15px"
                          class="fa fa-arrow-right-from-bracket"
                        ></i
                      ></v-span>
                      Logout</v-btn
                    >
                  </div>
                </v-list-item-content>

              </v-card>
            </v-menu>
          </div>
        </div>
      </v-card>
    </v-toolbar>
  </div>
</template>

<script>
export default {
  middleware: "auth",
  data: () => ({
    items: [{ name: "English(us)", image: "flag.png" }],
    company: [{ name: "Smart Friqi", image: "smart.png" }],
    select: null,
    btns: [
      ["Removed", "0"],
      ["Large", "lg"],
      ["Custom", "b-xl"],
    ],
    colors: ["deep-purple accent-4", "error", "teal darken-1"],
  }),
  methods: {
    async logout() {
      await this.$auth.logout();
      this.$router.push("/auth/login");
    },
  },
};
</script>

<style >
.v-text-field__details {
  display: none;
}
.v-toolbar__content{
  padding-right: 0px;
  padding-left: 0px;
}
</style>
