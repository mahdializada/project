<template>
  <div>
    <v-card class="d-block mb-1" color="#f1f3f4"  elevation="0">
        <v-card-text class="d-flex pa-0 position-relative" style="padding:7px !important">
          <v-icon
          class="onfocus"
          style="margin-top:2px"
            size="30"
            @click="actions"
            color="primary"
            >{{ playActionIcon }}</v-icon
          >
          <v-progress-linear :value="value" class="me-2 mt-2" style="height:3px"></v-progress-linear>
          <span class="mt-1" v-if="hasCounter">{{ formatTime(counter) }}</span>
          <span class="mt-1 mx-1" v-if="hasCounter">/</span>
          <span :class="`mt-1 ${hasRemove?'me-4':'me-1'}`">{{ formatTime(duration) }}</span>
            <v-icon v-if="hasRemove"  class="rounded-circle pa-1 ms-1 position-absolute" style="right:0;top:5px" color="red" @click="$emit('removeVoice')">mdi-close</v-icon>
        </v-card-text>
      </v-card>
      <audio :src="fetchSource(source)" id="control_audio"></audio>
  </div>
</template>

<script>
export default {
    props:{
        source:String,
        dur:Number,
        hasRemove:{
          type:Boolean,
          default:false,
        },
        hasCounter:{
          type:Boolean,
          default:false,
        }
    },
    data(){
        return{
            value:0,
            counter:0,
            duration: 0,
            timerInterval:null,
            playActionIcon: "mdi-play",
        }
    },
    methods: {
         fetchSource(item){
           this.duration = this.dur??0;
            return item;
        },
        actions() {
      try {
        if (this.playActionIcon == "mdi-play") {
          this.playActionIcon = "mdi-pause";
          if (this.counter == this.duration) {
            this.counter = 0;
            this.value = 0;
          }
          document.getElementById("control_audio").play();
          this.timerInterval = setInterval(() => {
            if (this.counter < this.duration) {
              this.counter++;
              this.value = Math.floor((this.counter * 100) / this.duration);
            } else {
              clearInterval(this.timerInterval);
              this.playActionIcon = "mdi-play";
              this.value = 0;
              this.counter = 0;
              return;
            }
          }, 1000);
        } else {
          clearInterval(this.timerInterval);
          document.getElementById("control_audio").pause();
          this.playActionIcon = "mdi-play";
          return;
        }
      } catch (error) {
        console.log(error);
      }
    },
    formatTime(counter) {
      let seconds = Math.floor(counter % 60),
        minutes = Math.floor(counter / 60) % 60;

      seconds = seconds < 10 ? `0${seconds}` : seconds;
      minutes = minutes < 10 ? `0${minutes}` : minutes;

      return `${minutes}:${seconds}`;
    },
},
}
</script>

<style lang="scss" >
.v-icon:focus::after{
    opacity: 0 !important;
}
</style>