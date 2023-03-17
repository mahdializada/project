<template>
  <div :key="reset">
    <v-card class="d-block" color="#f1f3f4" elevation="0">
      <v-card-text class="d-flex pa-0" style="padding:7px !important">
        <v-avatar color="red" style="height: 40px !important;width: 40px !important;min-width:40px !important">
          <v-icon size="20" class="white--text" @click="Record">{{
            recordIcon
          }}</v-icon>
        </v-avatar>
        <v-icon
          size="30"
          :disabled="
            duration > 0 && recordIcon == 'mdi-microphone' ? false : true
          "
          @click="actions"
          color="primary"
          >{{ playActionIcon }}</v-icon
        >
        <v-progress-linear :value="value" class="me-1" style="height:3px;margin-top:18px"></v-progress-linear>
        <span class="mt-1">{{ formatTime(counter) }}</span>
        <span class="mt-1 mx-1">/</span>
        <span class="mt-1">{{ formatTime(duration) }}</span>
          <v-icon 
          @click="addMoreVice"  
          color="white" 
          class="rounded-circle pa-1 ms-1"  
          style="background:#2E7BE6;">
           mdi-plus
        </v-icon>
      </v-card-text>
    </v-card>
    <div id="result"></div>
  </div>
</template>
<script>
export default {
  props: {
    reset: Number,
  },
  data() {
    return {
      isRecord: true,
      recordIcon: "mdi-microphone",
      playActionIcon: "mdi-play",
      counter: 0,
      timerInterval: null,
      durationInterval: null,
      duration: 0,
      value: 0,
      mediaRecorder: null,
      chuck: [],
      voice: "",
      isLoading: false,
    };
  },
  methods: {
    async checkMicroPhone() {
      let state = await navigator.permissions
        .query({ name: "microphone" })
        .then(function (permissionStatus) {
          return permissionStatus.state; // granted, denied
        });
      console.log(state);
    },
    addMoreVice(){
      if(this.isRecord && this.voice){
        this.voice.duration = this.duration;
        this.$emit('addMoreVice',this.voice);
        this.voice = "";
        this.mediaRecorder = null;
        this.counter = 0;
        this.duration = 0;
        this.value = 0;
      }else{
        this.$toasterNA("orange", this.$tr('first_stop_recording'));
      }
    },
    Record() {
      try {
        if (this.isRecord) {
          clearInterval(this.timerInterval);
          navigator.mediaDevices
            .getUserMedia({ audio: true })
            .then((stream) => {
              this.mediaRecorder = new MediaRecorder(stream);
              this.mediaRecorder.start();
              this.$emit('onRecording',true);
              this.recordIcon = "mdi-pause";
              this.playActionIcon = "mdi-play";
              this.duration = 0;
              this.durationInterval = setInterval(() => {
                this.duration++;
              }, 1000);
              this.counter = 0;
              this.value = 0;
              this.mediaRecorder.addEventListener("dataavailable", (e) => {
                this.chuck.push(e.data);
              });
              this.mediaRecorder.addEventListener("stop", (e) => {
                const blob = new Blob(this.chuck);
                const audio_url = URL.createObjectURL(blob);
                const audio = new Audio(audio_url);
                audio.setAttribute("id", "control_audio");
                document.getElementById("result").appendChild(audio);
                this.$emit('onRecording',false);
                this.voice = this.blobToFile(blob);
                this.$emit("hasVoice", this.voice);
                this.chuck = [];
              });
            })
            .catch((error) => {
              this.$toastr.e(this.$tr("allow_microphone"));
            });
          this.isRecord = !this.isRecord;
        } else {
          clearInterval(this.durationInterval);
          this.recordIcon = "mdi-microphone";
          if (this.mediaRecorder && this.mediaRecorder.stream.active) {
            this.mediaRecorder.stop();
          }
          this.isRecord = !this.isRecord;
        }
      } catch (error) {
        console.log(error);
      }
    },
    blobToFile(theBlob) {
      theBlob.lastModified = new Date();
      const myFile = new File([theBlob], this.duration+".ogg", {
        type: theBlob.type,
      });
      return myFile;
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
};
</script>
<style></style>
