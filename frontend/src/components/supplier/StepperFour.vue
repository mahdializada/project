<template>
   <v-row class="d-flex flex-column flex-md-row">
    <!-- input company -->
    <v-col cols="12" md="6" class="py-0">
    <!-- input sunject -->
    <div class="w-full">
      <CustomInput
        :label="$tr('label_required',$tr('label'))"
          :placeholder="$tr('choose_item',$tr('label'))"
          type="autocomplete"
        item-text="title"
        item-value="id"
        :items="allLabels"
        v-model="supplier.label_id.$model"
        :rules="rules.labelRule(supplier, $root)"
      />
    </div>
    </v-col>

    <!-- input main Map Link-->
    <v-col cols="12" md="12" class="py-0">
    <div class="w-full">
      <CustomInput
        :label="$tr('label_required',$tr('note'))"
        placeholder="note"
        type="textarea"
        class="me-md-2"
        v-model="supplier.note.$model"
        :rules="rules.noteRule(supplier, $root)"

      />
    </div>
     </v-col>
  </v-row>
</template>

<script>
import CustomInput from "~/components/design/components/CustomInput.vue";
import { mapActions, mapGetters } from "vuex";

export default {
  components: { CustomInput },
 props: {
    supplier: {
      required: true,
      type: Object,
    },
    rules: {
      required: true,
      type: Object,
    },
  },
  data() {
    return {
      allLabels: [],
    };
  },
  async mounted() {
    try {
      const sub_system = "Sourcing Request";
      const response = await this.$axios.get(`labels/id`, {
        params: { sub_system: sub_system },
      });
      this.allLabels = response?.data?.labels;
    } catch (error) {
      if (error?.response?.status === 404 && !error?.response?.data?.result) {
        // this.$toastr.w(this.$tr("sub_system_not_found"));
					this.$toasterNA("orange",this.$tr('sub_system_not_found'));

      }
    }
  },

  methods: {
      checkValidation() {
      return (!this.supplier.label_id.$invalid &&
        !this.supplier.note.$invalid ) ? true  : false;
    },
  },
};
</script>
