<template>
  <div>
  
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <v-file-input
          class="custom-file"
          outlined
          accept="image/jpeg,image/jpg,image/png"
          prepend-icon=""
          dense
          :rules="validateRule(form.logo, $root, 'logo')"
          @change="validatePathImg"
          @click:clear="clearImg"
          :success="logoPath !== ''"
        >
          <template slot="prepend-inner">
            <div
              name="prepend-inner"
              style="max-width: 260px; text-align: center"
            >
              <p class="ma-0">
                <svg
                  width="30"
                  height="30"
                  viewBox="100 100 500 140"
                  fill="currentColor"
                  style="transform: translateY(4px)"
                >
                  <g>
                    <path
                      d="m479.71 198.78h-0.86328c-6.1133-51.496-50.938-91.512-102.99-91.512-42.559 0-81.152 26.367-96.625 65.824-2.7773-0.30469-5.5078-0.44141-8.168-0.44141-33.949 0-63.816 23.754-71.656 56.746-32.059 2.8945-56.934 29.938-56.934 62.348 0 34.512 28.094 62.625 62.625 62.625h119.63c4.293 0 7.7695-3.4766 7.7695-7.793 0-4.3164-3.4766-7.793-7.7695-7.793l-119.6-0.003906c-25.922 0-47.039-21.094-47.039-47.039 0-25.898 21.023-47.016 46.828-47.016l1.0039 0.023438c3.8281 0 7.1406-2.8477 7.7227-6.6484 4.0586-28.426 28.746-49.867 57.445-49.867 3.9883 0 7.957 0.42188 11.945 1.2383l1.4023 0.13672c3.4062 0 6.4648-2.2617 7.3984-5.4375 11.738-36.656 45.5-61.32 84.023-61.32 47.414 0 86.078 37.191 88.059 84.699 0.14063 2.0781 1.0977 4.0117 2.7305 5.4375 1.5391 1.3086 3.2422 1.6562 5.9258 1.8203 2.7305-0.30469 5.0859-0.46484 7.1641-0.46484 34.301 0 62.207 27.906 62.207 62.207s-27.906 62.207-62.207 62.207h-104.42c-4.293 0-7.7695 3.4766-7.7695 7.793s3.5 7.793 7.7695 7.793h104.42c42.887 0 77.793-34.883 77.793-77.793s-34.93-77.77-77.816-77.77z"
                    />
                    <path
                      d="m413.02 318.92c0.023437 0 0.023437 0 0 0 2.125 0 4.1289-0.81641 5.5312-2.2383 2.9883-3.0352 2.9883-7.957 0-11.012l-63.047-63.047c-2.9648-2.9414-8.0273-2.918-11.012 0.070312l-63.023 62.977c-1.4688 1.4688-2.2617 3.4297-2.2617 5.5078 0 2.1016 0.81641 4.0586 2.2383 5.4609 1.3984 1.4453 3.4062 2.2852 5.4844 2.3086h0.046875c2.1016 0 4.1055-0.81641 5.5312-2.2617l49.699-49.676v177.92c0 4.3164 3.5 7.793 7.7695 7.793 4.3164 0 7.8164-3.4766 7.8164-7.793v-177.92l49.652 49.652c1.4453 1.4375 3.4492 2.2578 5.5742 2.2578z"
                    />
                  </g>
                </svg>
                {{ $tr("select_logo") }}
              </p>
              <div v-if="logoPath" class="ma-1">
                <p class="mb-0">
                  {{ $tr("name") }}:
                  {{ form.logo.$model.name }}
                </p>
                <p class="mb-0" style="margin-top: 5px">
                  {{ $tr("size") }}:
                  {{ (form.logo.$model.size / 1024).toFixed(2) }}
                  KB
                </p>
              </div>
            </div>
          </template>
        </v-file-input>
      </div>
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      userLoading: false,
      logoPath: "",
      iconPath: "",
      
    };
  },
  methods: {
    async validate() {
      this.form.logo.$touch();
      return !this.form.logo.$invalid;
    },
    async loaded() {
      this.form.$model.logo = null;
      this.logoPath = "";
    },
    validatePathImg(file) {
      if (file) {
        const fileExtension = file.type;
        const allowedExtensions = ["image/jpeg", "image/jpg", "image/png"];
        if (!allowedExtensions.includes(fileExtension)) {
          // this.$toastr.w(this.$tr("inappropriate_uploaded_file"));
					this.$toasterNA("orange",this.$tr('inappropriate_uploaded_file'));

          return;
        }
        this.form.logo.$model = file;
        
        this.logoPath = URL.createObjectURL(file);
      }
    },
    clearImg() {
      this.logoPath = "";
      this.form.$model.logo = null;
    },
  },
//   components: { CTextField },
};
</script>
