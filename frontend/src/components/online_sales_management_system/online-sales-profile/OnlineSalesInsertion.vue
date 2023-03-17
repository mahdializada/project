<template>
  <div>
    <v-card
      style="height: 100vh; overflow-y: auto"
      class="pa-1 pb-10 position-relative"
      :key="resetVoice"
      elevation="0"
    >
      <v-card-title
        class="pa-1 mx-1 my-1"
        style="background-color: #f7f8fc; color: #3279db"
      >
        <div class="d-flex">{{ isEdit ? "UPDATE" : "CREATE" }} NOTE</div>
        <div class="ml-auto">
          <v-icon @click="$emit('closeInsertNote')">mdi-close</v-icon>
        </div>
      </v-card-title>
      <v-card-text>
        <v-form ref="form">
          <div class="w-full">
            <CTextField
              :title="$tr('label_required', $tr('name'))"
              placeholder="name"
              type="textfield"
              :class="`mt-2 position-relative ${isEdit ? 'animations1' : ''}`"
              :rounded="false"
              v-model="name"
              :rules="nameRules"
              style="
                padding-left: 0px !important;
                padding-right: 0px !important;
              "
            />
          </div>
          <div class="w-full">
            <Editor
              :class="` position-relative ${isEdit ? 'animations1' : ''}`"
              v-model="note"
              :label="$tr('note')"
            />
          </div>
          <div class="w-full" >
              <OnlineSalesImageUpload
              @onFileChange="onFileChange"
              ref="onlineSalesUploadImageRef"
              @onDeleteImg="onDeleteImg"
              :system_name="'online-sales-note-image'"
              />
          </div>
          <div class="w-full">
            <AudioPlayer
              :class="` position-relative ${isEdit ? 'animations1' : ''}`"
              v-for="(voice, index) in voice"
              :key="index"
              :source="isEdit ? voice.path : makePlayAble(voice)"
              :dur="isEdit ? parseInt(voice.name) : voice.duration"
              :hasRemove="true"
              :hasCounter="true"
              @removeVoice="removeVoice(index)"
            />
            <VoiceRecorder
              :title="'voice'"
              :reset="resetVoice"
              @onRecording="onRecording"
              @hasVoice="hasVoice"
              @addMoreVice="addMoreVice"
            />
          </div>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          outlined
          rounded
          style="font-size: 15px"
          @click="reset"
          >cancel</v-btn
        >
        <v-btn
          color="primary"
          rounded
          style="font-size: 1rem"
          @click="onSubmit"
          :loading="loading"
          >Add</v-btn
        >
      </v-card-actions>
    </v-card>
  </div>
</template>
  
  <script>
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import Editor from "../../design/Editor.vue";
import VoiceRecorder from "../../new_form_components/components/VoiceRecorder.vue";
import AudioPlayer from "../../new_form_components/components/AudioPlayer.vue";
import OnlineSalesImageUpload from "./OnlineSalesImageUpload.vue";

export default {
  props: {
    productCode: String,
  },
  components: {
    CTextField,
    Editor,
    VoiceRecorder,
    AudioPlayer,
    OnlineSalesImageUpload,
  },
  data() {
    return {
      isEdit: false,
      id: "",
      resetVoice: 0,
      isRecording: false,
      valid: true,
      recordVoice: [],
      loading: false,
      items: ["Note is required"],
      name: "",
      note: "",
      voice: [],
      images: [],
      nameRules: [
        (v) => !!v || "Name is required",
        (v) => (v && v.length <= 30) || "Name must be less than 14 characters",
      ],
      noteRules: [
        (v) => !!v || "Note is required",
        (v) =>
          (v && v.length >= 10) || "Note must be greater than 10 characters",
      ],
    };
  },
  methods: {
    onRecording(flag) {
      this.isRecording = flag;
    },
    checkEditOnDelete(id) {
      if (id == this.id) {
        this.reset();
      }
    },
    reset() {
      this.resetVoice++;
      this.name = "";
      this.note = "";
      this.voice = [];
      this.images = [];
      this.recordVoice = [];
      this.isEdit = false;
      this.id = "";
    },
    async onSubmit() {
      if (!this.$refs.form.validate()) return;
      if (!this.note.trim() && this.voice.length > 0 && !this.images == []) {
        this.$toasterNA("red", this.$tr("item_is_required", this.$tr("note")));
        return;
      }

      if (this.isRecording) {
        this.$toasterNA("orange", this.$tr("first_stop_recording"));
        return;
      }
      try {
        this.loading = true;
        const formData = new FormData();
        formData.append("product_code", this.productCode);
        formData.append("name", this.name);
        if (this.note.trim()) formData.append("note", this.note.trim());
        if (!this.images == []) formData.append("images", this.images);

        const voiceData = structuredClone(this.voice);
        if (this.recordVoice.length > 0) {
          voiceData.push(this.recordVoice[0]);
        }
        voiceData.forEach((voice) => {
          if (voice.attachmentable_id)
            formData.append("prevVoice[]", voice.path);
          else formData.append("voice[]", voice);
        });
        if (this.isEdit) {
          formData.append("id", this.id);
          return this.updateNote(formData);
        }

        const result = await this.$axios.post(
          "online-sales/store-note",
          formData
        );

        if (result.status == 201) {
          this.$emit("pushNote", result.data);
          this.reset();
          this.$toasterNA("green", this.$tr("successfully_inserted"));
        }
        this.loading = false;
      } catch (error) {
        this.loading = false;
      }
    },
    async updateNote(item) {
      try {
        const result = await this.$axios.post("online-sales/store-note", item);
        if (result.status == 200) {
          this.$emit("updateNote", result.data);
          this.$toasterNA("green", this.$tr("successfully_updated"));
          this.reset();
        }
        this.loading = false;
      } catch (error) {
        this.loading = false;
      }
    },
    onDeleteImg(link) {
      this.images = this.images.filter((i) => i != link);
    },
    hasVoice(v) {
      this.recordVoice[0] = v;
    },
    addMoreVice(item) {
      this.voice.push(item);
      this.recordVoice = [];
    },
    removeVoice(index) {
      this.voice.splice(index, 1);
    },
    makePlayAble(v) {
      return URL.createObjectURL(v);
    },
    editNote(item) {
      this.isEdit = true;
      this.id = item.id;
      this.name = item.name;
      this.note = item.note;
      if (item.attachments.length > 0)
        this.voice = structuredClone(
          item.attachments.filter((i) => i.file_type == "raw")
        );
      item.attachments.map((i) => {
        if (i.file_type == "image") this.images.push(i.path);
      });
      this.$refs.onlineSalesUploadImageRef.onEdit(
        structuredClone(item.attachments.filter((i) => i.file_type == "image"))
      );
    },
    onFileChange(data) {
      this.images.push(data);
    },
  },
};
</script>
  
  <style>
.animations1 {
  animation-name: example;
  animation-duration: 0.4s;
}

@keyframes example {
  0% {
    left: 0px;
  }
  25% {
    left: 3px;
  }
  50% {
    left: -3px;
  }
  75% {
    left: 3px;
  }
  100% {
    left: 0px;
  }
}
</style>
  