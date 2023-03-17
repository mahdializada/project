<template>
  <v-card
    :color="$vuetify.theme.dark ? 'black' : '#f9fafd'"
    class="height-80-vh"
  >
    <v-card-title class="py-1">
      <span class="custom-dialog-title">{{ $tr("profile") }}</span>
      <v-spacer />
      <CloseBtn @click="$emit('update:dialog')" />
    </v-card-title>

    <v-card-text>
      <v-row class="mt-2">
        <v-col cols="12" class="d-flex py-0">
          <v-card elevation="0" class="pa-2 w-full">
            <div class="d-flex">
              <span class="subtitle-1">{{ $tr("title") }} </span>
              <v-spacer></v-spacer>

              <span>
                <!-- <v-btn
                  color="primary "
                  class="rounded-sm mx-0 px-1"
                  small
                  @click="$emit('onEdit', profileData)"
                >
                  <v-icon class="mr-1" right small> fa-edit </v-icon>
                  {{ $tr("edit") }}
                </v-btn> -->
              </span>
            </div>
            <v-divider class="grey darken-1 mt-1 mb-2"></v-divider>
            <p>{{ profileData.title }}</p>
          </v-card>
        </v-col>
        <v-col cols="12" class="d-flex py-0">
          <v-card elevation="0" class="pa-2 w-full">
            <span class="subtitle-1">{{ $tr("system") }} </span>
            <v-divider class="grey darken-1 mt-1"></v-divider>
            <v-row>
              <template>
                <v-col cols="12" md="4" sm="6" class="py-0">
                  <v-checkbox
                    :label="profileData.system.name"
                    hide-details
                    input-value="true"
                  ></v-checkbox>
                </v-col>
              </template>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import CloseBtn from "~/components/design/Dialog/CloseBtn";

export default {
  name: "ReasonProfile",
  components: { CloseBtn },
  props: {
    dialog: {
      type: Boolean,
      required: true,
    },
    profileData: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      showMore: false,
    };
  },

  created() {},
};
</script>

<style scoped>
.logo-status {
  padding: 5px;
  position: absolute !important;
  right: 100px !important;
  top: 0;
  transform: translateY(-50%);
  box-shadow: 0 0 8px 2px rgb(0 0 0 / 15%);
}

.active-status-btn {
  width: 4.5rem;
  height: 4.5rem;
  padding: 10px;
  position: absolute !important;
  right: 100px !important;
  top: 0;
  transform: translateY(-50%);
  box-shadow: 0 0 8px 2px rgb(0 0 0 / 15%);
}

.project-details-container {
  margin-top: 10rem !important;
}

.theme--light .btn--cancel {
  background-color: #e5dede !important;
  color: black;
  border-color: #e5dede !important;
}

.flag-icon {
  height: 45px !important;
  width: 45px !important;
}

.remark--textarea .theme--light .v-textarea {
  background-color: white !important;
}
</style>
