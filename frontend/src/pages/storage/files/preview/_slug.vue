<template>
  <v-container fluid class="pa-0">
    <client-only>
      <div class="video__container">
        <vue-plyr>
          <video controls autoplay>
            <source :src="path" />
          </video>
        </vue-plyr>
      </div>
    </client-only>
  </v-container>
</template>

<script>
export default {
  layout: "public",
  auth: false,

  async asyncData({ params, error, $axios }) {
    const slug = params.slug;
    try {
      const url = `public/storage/files/preview/${slug}`;
      const { data } = await $axios(url);
      if (data.result) {
        return { file: data.file };
      }
      error({ statusCode: 404 });
    } catch (err) {
      console.log(err, err.response);
      const statusCode = err?.response?.status;
      error({ statusCode });
    }
    return {};
  },
};
</script>

<style lang="scss" scoped >
.video__container {
  height: 70vh;
  width: 100%;
}
</style>
