<template>
  <div class="d-flex">
    <div class="media-wrapper">
      <div class="media-item my-1" v-for="i in 4" :key="i">
        <div class="my-1" v-for="i in 2" :key="i">
          <v-skeleton-loader
            type="image"
            :class="`sk-${Math.floor(Math.random() * 5)}`"
          ></v-skeleton-loader>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {};
</script>
<style>
.media-wrapper {
  line-height: 0;
  /* 
  -webkit-column-count: 4;
  -moz-column-count: 4;
  column-count:4; */
  /* display: flex; */
  display: flex;
  flex-wrap: wrap;
  width: 100%;
  /* padding: 0 4px; */
}
.media-wrapper .media-item {
  /* width: 100%;
  height: auto; */
  flex: 25%;
  max-width: 25%;
  padding: 0 10px;

  /* -webkit-column-break-inside: avoid; */
}
.sk-0 .v-skeleton-loader__image {
  height: 200px !important;
}
.sk-1 .v-skeleton-loader__image {
  height: 250px !important;
}
.sk-2 .v-skeleton-loader__image {
  height: 300px !important;
}
.sk-3 .v-skeleton-loader__image {
  height: 400px !important;
}
.sk-4 .v-skeleton-loader__image {
  height: 500px !important;
}
</style>
