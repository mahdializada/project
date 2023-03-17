<template>
    <div style="background-color: transparent">
      <v-dialog v-model="showCanvaVideoDialog" width="900" persistent>
      <span style="position: relative; left: 17px; top: 48px">
        <CloseBtn
          @click="openVideo"
          class="primary--text"
        />
      </span>
      <iframe
        loading="lazy"
        style="
          width: 100%;
          height: 70vh;
          top: 0;
          left: 0;
          border: none;
          padding: 0;
          margin: 0;
          border-radius: 15px;
          background: url('/dat-loader.svg');
          background-position: center;
          background-size: cover;
          background-repeat: no-repeat;
        "
        :src="path"
        allowfullscreen="allowfullscreen"
        allow="fullscreen"
      >
      </iframe>
    </v-dialog>
    </div>
  </template>
    
    <script>
import CloseBtn from '../design/Dialog/CloseBtn.vue';
  export default {
  components: { CloseBtn },
    data() {
      return {
        showCanvaVideoDialog:false,
        path:'',
        key:0,
      };
    },
    methods: {
      openVideo(item){
        this.key++;
        this.path = item??'';
        this.showCanvaVideoDialog = !this.showCanvaVideoDialog;
      }
    },
  };
  </script>
    
    <style>
  </style>