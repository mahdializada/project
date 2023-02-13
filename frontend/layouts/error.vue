<template>
  <v-app dark class="err">
    <v-main>
      <v-container>
        <div style="display: block">
          <h1 v-if="error.statusCode === 404">
            {{ pageNotFound }}
          </h1>
          <h1 v-else>
            {{ otherError }}
          </h1>
        </div>
        <div>
          <a @click="back">Back to Home page</a>
        </div>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: "EmptyLayout",
  // layout: "auth",
  props: {
    error: {
      type: Object,
      default: null,
    },
  },
  methods: {
    back() {
      this.$router.push("/");
    },
  },
  data() {
    return {
      pageNotFound: "404 Not Found",
      otherError: "An error occurred",
    };
  },
  head() {
    const title =
      this.error.statusCode === 404 ? this.pageNotFound : this.otherError;
    return {
      title,
    };
  },
};
</script>

<style scoped>
* {
  background-color: pink;
}
h1 {
  font-size: 20px;
  text-align: center;
  color: red;
}

.err {
  background-position: center center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: cover;
}
</style>