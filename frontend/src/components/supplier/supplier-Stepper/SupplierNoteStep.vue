<template>
  <div class="mb-3">
    <div>
      <CTitle :text="$tr('note')" />
      <CTextArea
        px="px-0"
        py="py-0"
        :title="$tr('label_required', $tr('note'))"
        :placeholder="$tr('note_here')"
        @blur="form.note.$touch()"
        v-model="form.note.$model"
        :rules="validateRule(form.note, $root, $tr('note'))"
        :invalid="form.note.$invalid"
        prepend-inner-icon="mdi-clipboard-text-outline"
      />
    </div>
    <div>
      <CTitle :text="`images`" />
      <div class="w-full h-full d-flex align-center justify-center">
        <div class="w-full h-full">
          <CFileUploadCloud2
            system_name="single-sales"
            v-model="form.images.$model"
            :form="form"
            ref="uploadRefs"
          ></CFileUploadCloud2>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CTextArea from '../../new_form_components/Inputs/CTextArea.vue'
import GlobalRules from '~/helpers/vuelidate'
import CTitle from '../../new_form_components/Inputs/CTitle.vue'
import CFileUploadCloud2 from '../../new_form_components/Inputs/CFileUploadCloud2.vue'

export default {
    
  components: { CTextArea, CTitle, CFileUploadCloud2 },
  props: { form: Object },
  data() {
    return {
      validateRule: GlobalRules.validate,
    }
  },
  methods: {
    async validate() {
      this.form.note.$touch()
      return !this.form.note.$invalid
    },
  },
  validate() {
      this.form.images.$touch();

      return !this.form.images.$invalid && !this.$refs.uploadRefs.uploading;
    },
}
</script>

<style></style>
