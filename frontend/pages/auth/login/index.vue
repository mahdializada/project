<template>
  <v-app
    class="login"
    :style="{ backgroundImage: `url(${backgroundImagePath})` }"
  >
    <v-main>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <v-flex xs8 sm6 md4 >
            <v-card class="elevation-24">
              <v-toolbar dark color="info">
                <v-toolbar-title align-center justify-space-between
                  >Login form</v-toolbar-title
                >
              </v-toolbar>
              <v-card-text>
                <p>Sign in with your email and password:</p>
                <v-form @submit.prevent="login" ref="form" lazy-validation v-model="valid">
                  <div style="display: inline">
                    <v-text-field
                      outline
                      filled
                      full-width
                      single-line
                      background-color="#f4f8f7"
                      color="grey darken-2"
                      prepend-inner-icon="mdi-account-outline"
                      label="Email"
                      type="text"
                      success
                      v-model="form.email"
                      required
                      :rules="emailRule"
                      name="email"
                    ></v-text-field>
                    <!-- <p style="color: red" v-if="errors.email">
                      {{ errors.email?.join("") }}
                    </p> -->
                  </div>
                  <v-text-field
                    :append-icon="
                      show1 ? 'mdi-eye-outline' : 'mdi-eye-off-outline'
                    "
                    :type="show1 ? 'text' : 'password'"
                    filled
                    full-width
                    single-line
                    success
                    background-color="#f4f8f7"
                    color="grey darken-2"
                    prepend-inner-icon="mdi-lock-outline"
                    @click:append="show1 = !show1"
                    outline
                    hide-details
                    label="Password"
                    v-model="form.password"
                    :rules="passRule"
                    required
                  ></v-text-field>
                  <!-- <p style="color: red" v-if="errors.password" class="mt-4">
                    {{ errors.password?.join("") }}
                  </p> -->
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" type="submit">Login</v-btn>
                  </v-card-actions>
                </v-form>
              </v-card-text>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-main>
  </v-app>
</template>
<script>
import backgroundImagePath from "~/static/1.jpg";
export default {
  layout: "auth",
  auth: false,
  data() {
    return {
      show: false,
      show1: false,
      backgroundImagePath,
      darkTheme: true,
      valid:false,
      // errors: {},
      form: {
        email: null,
        password: null,
      },
      emailRule: [
        (v) => !!v || 'Email is required',
        (v) => /.+@.+/.test(v) || "E-mail must be valid",
      ],
      passRule:[
        (v) => !!v || 'Password is required',
      ]
    };
  },
  // mounted() {
  //     // Before loading login page, obtain csrf cookie from the server.
  //     let res= this.$axios.$get('/sanctum/csrf-cookie');
  //     console.log(res);
  //   },
  methods: {
    async login() {
      // this.errors = {};
      this.$nuxt.$loading.start();
      try {
        await this.$auth.loginWith("local", { data: this.form });
        this.$router.push({
          path: "/",
        });
        this.$toast.success('Successfully LoggedIn !')
      } catch (error) {
        // console.log(error);
        // if (error.response.status == 422) {
        //   this.errors = error?.response?.data?.errors;
        //   return;
        // } else
         if (error.response.status == 401) {
          // swal({
          //   title: error.response.data.message,
          //   icon: "error",
          // });
          this.$toast.error('Invalid email or password');
        }
      }
      this.$nuxt.$loading.finish();
      this.$refs.form.resetValidation();
      this.$refs.form.reset();
    },
  },
};
</script>

<style scoped lang="css">
.login {
  background-position: center center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: cover;
}
</style>
