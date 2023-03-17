<template>
  <v-container fluid class="pa-0">
    <v-row no-gutters>
      <v-col cols="12">
        <PageHeader
          :Icon="breadcrumb[2].icon"
          :Title="
            breadcrumb[2].text == '' ? 'design_request' : breadcrumb[2].text
          "
          :Breadcrumb="breadcrumb"
        >
        </PageHeader>
      </v-col>
      <v-col cols="12">
        <template v-if="profileData == null">
          <Loader />
        </template>
        <Profile
          v-if="profileData != null"
          :profileData="profileData"
          @fetchNewData="() => {}"
          :hasDrawer="false"
          ref="desingRequestProfile"
        >
          <v-icon size="20" color="white" @click="onEdit(), toggleDrawerEdit()">
            mdi-pencil
          </v-icon>
        </Profile>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import PageHeader from "~/components/design/PageHeader.vue";
import Profile from "~/components/landing/profile.vue";
import Loader from "~/components/design/Loader.vue";

export default {
  async asyncData({ params, error, $axios }) {
    let profileData = null;
    try {
      const response = await $axios.get(`design-request/${params.slug}`, {
        params: {
          with_code: true,
        },
      });
      if (response?.status === 200) {
        profileData = response.data;
      }
    } catch (err) {
      error({ statusCode: 404 });
    }
    return {
      slug: params.slug,
      profileData,
    };
  },
  async created() {
    this.breadcrumb[2].text = this.profileData.product_name;
    // try {
    //   const response = await this.$axios.get(
    //     `order_request-files/${this.profileData.design_request_order.id}`
    //   );
    //   if (response.data.result) {
    //     this.profileData.files = response.data.images;
    //   }
    // } catch (error) {
    // }
  },
  components: {
    Profile,
    PageHeader,
    Loader,
  },
  data() {
    return {
      breadcrumb: [
        { text: "dashboard", exact: true, to: "/" },
        {
          text: "design_request",
          to: "/",
          exact: true,
          icon: "mdi-note-edit",
        },
        {
          text: "",
          disabled: true,
          to: "",
          icon: "mdi-note-edit",
        },
      ],
    };
  },
};
</script>
