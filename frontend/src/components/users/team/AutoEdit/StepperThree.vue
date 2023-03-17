<template>
  <div class="ma-0 pa-0 topDiv">
    <div class="topTitle">
      <h1
        class="text-center text-h4 font-weight-bold border-bottom py-2"
        style="color: #58595b"
      >
        {{ $tr('roles') }}
      </h1>
    </div>
    <v-row class="mx-auto mt-3">
      <v-col cols="12 px-4 py-0 my-0" md="9" lg="10">
        <label class="subtitle-1">{{
          $tr('label_required', $tr('project'))
        }}</label>
        <v-autocomplete
          item-text="Project"
          item-value="id"
          dense
          outlined
          :placeholder="$tr('choose_item', $tr('project'))"
        ></v-autocomplete>
      </v-col>
      <v-col cols="12 px-4 pt-0 pt-md-3" md="3" lg="2">
        <FormBtn title="add_plus" class="" />
      </v-col>
      <v-col cols="12 py-0 my-0 px-4 d-flex flex-wrap">
        <SelectedItem
          @close="Itemclose"
          title="Teeb Alhoor_Oman"
          icon="fa-globe"
        />
      </v-col>
      <v-col cols="12 px-4 py-0 my-0" md="9" lg="10">
        <label class="subtitle-1">{{ $tr('permission') }}</label>
        <v-autocomplete
          item-text="permission"
          item-value="id"
          dense
          outlined
          :placeholder="$tr('choose_item', $tr('permission'))"
        ></v-autocomplete>
      </v-col>
      <v-col cols="12 px-4 pt-0 pt-md-3" md="3" lg="2">
        <FormBtn title="add_plus" class="" />
      </v-col>
      <v-col cols="12 py-0 my-0 px-4 d-flex flex-wrap">
        <SelectedItem @close="Itemclose" title="View" icon="fa-globe" />
        <SelectedItem @close="Itemclose" title="Download" icon="fa-download" />
      </v-col>
    </v-row>
  </div>
</template>

<script>
import FormBtn from '~/components/design/components/FormBtn.vue';
import SelectedItem from '~/components/design/components/SelectedItem.vue';

export default {
  components: {
    FormBtn,
    SelectedItem,
  },
  props: {
    autoEditData: {
      require: true,
      type: Object,
    },
  },
  data() {
    return {};
  },
  methods: {
    setNull(data) {
      // for (const [key, value] of Object.entries(this.RolesForm)) {
      //   this.RolesForm[key] = data[key];
      // }
    },
  },
};
</script>

<style>
.topDiv {
  position: relative;
}
.topTitle {
  top: 0px;
  position: sticky;
  z-index: 2;
  background-color: white;
}
</style>
