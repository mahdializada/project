<template>
  <div class="connection__container pa-5" v-if="showLoader">
    <lottie-player
      src="/assets/man-on-rocket.json"
      background="transparent"
      speed="3"
      style="width: 300px; height: 300px"
      loop
      autoplay
    >
    </lottie-player>
    <h2 class="pa-2 primary--text">
      {{
        // $tr("pulish_ads", published, form.$model.ads.length) +
        "Processing ..."
      }}
    </h2>
  </div>
  <div class="connection__container2" v-else>
    <div v-for="(item, ind) in form.ads.$each.$iter" :key="ind">
      <CTextField
        class="mb-4 pt-1"
        v-model="item.generated_link.$model"
        :title="$tr('generated_link')"
        :placeholder="$tr('generated_link')"
        prepend-inner-icon="mdi-link-variant "
        :hasCustomBtn="true"
        btnIcon="mdi-clipboard-multiple"
        readonly
        @add="copyGeneratedLinkToClipboard"
        :rounded="false"
      />

      <CTextField
        class="mb-3 pt-1"
        v-model="item.ad_id.$model"
        :title="$tr('label_required', $tr('ad_id'))"
        :placeholder="$tr('ad_id')"
        prepend-inner-icon="mdi-link-variant "
        :rules="validateRule(item.ad_id, $root, $tr('ad_id'))"
        :invalid="item.ad_id.$invalid"
        :hasCustomBtn="true"
        btnIcon="mdi-content-paste "
        @add="copyToAdId"
        :rounded="false"
      />
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import MultiInputItem from "~/components/new_form_components/cat_product_selection/MultiInputItem.vue";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import CTextField from "~/components/new_form_components/Inputs/CTextField.vue";

export default {
  props: {
    form: Object,
    data: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      loading: false,
      showLoader: false,
      published: 0,
      submit: false,
    };
  },
  mounted() {},
  computed: {
    publishe: function () {
      if (this.submit) {
        if (this.published < this.form.$model.ads.length)
          setTimeout(
            () => {
              this.published = this.published + 1;
            },
            this.form.$model.ads.length > 2 ? 1000 : 500
          );
      } else {
        this.published = 1;
      }

      return this.published;
    },
  },
  methods: {
    async copyGeneratedLinkToClipboard(link) {
      try {
        const clipboard = navigator.clipboard;
        await clipboard.writeText(link);
        // this.$toastr.i(this.$tr("link_has_copied_to_your_clipboard"));
        this.$toasterNA(
          "primary",
          this.$tr("link_has_copied_to_your_clipboard")
        );
      } catch (error) {
        // this.$toastr.e(this.$tr("link_is_not_copied"));
        this.$toasterNA("red", this.$tr("link_is_not_copied"));
      }
    },

    async copyToAdId(isCanva = false) {
      // try {
      // 	const clipboard = navigator.clipboard;
      // 	const ad_id = await clipboard.readText();
      // 	if (ad_id) {
      // 		if (isCanva) this.form.media_link.$model = ad_id;
      // 		else this.form.ad_id.$model = ad_id;
      // 	}
      // } catch (error) {}
    },

    validate() {
      this.form.ads.$touch();
      if (!this.form.ads.$invalid) {
        this.submit = true;
        this.published = 1;
      }
      // setTimeout(() => {}, 300);
      return !this.form.ads.$invalid;
    },
    async addOneMore() {
      this.loading = true;
      let generateLink = await this.generateLink();
      if (generateLink)
        this.form.ads.$model.push({
          media_link: null,
          ad_id: null,
          generated_link: generateLink.generated_link,
          connection_id: generateLink.id,
        });
      this.loading = false;
    },

    async generateLink() {
      try {
        const connection = this.data;
        const url = "advertisement/connection/generate_link";
        const { data } = await this.$axios.post(url, connection);
        if (data.result) {
          this.$toasterNA("green", this.$tr("successfully_inserted"));
          const insertedRecord = data.connection;
          return insertedRecord;
        } else {
          this.$toasterNA(
            "red",
            this.$tr(data.message || "something_went_wrong")
          );
          return false;
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },
    loaded() {
      this.showLoader = false;
      this.submit = false;
      this.published = 1;
    },
    async removeItem(item) {
      this.loading = true;
      let param = { ids: [this.form.$model.ads[item].connection_id] };
      const { data } = await this.$axios.post(
        "advertisement/connection/delete_generated_links",
        param
      );

      if (data) this.form.ads.$model.splice(item, 1);
      this.loading = false;
    },
    onColseDialog() {
      if (!this.submit && this.form.$model.ads[0].connection_id) {
        let ids = this.form.$model.ads.map((ad) => ad.connection_id);
        if (ids.length > 0) {
          let param = { ids: ids };
          const { data } = this.$axios.post(
            "advertisement/connection/delete_generated_links",
            param
          );
        }
      }
    },
    onError() {
      this.submit = false;
      this.published = 0;
      this.showLoader = false;
    },
  },
  components: {
    CTitle,
    CTextField,
    MultiInputItem,
  },
};
</script>

<style scoped lang="scss">
.connection__container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 100%;
  min-height: 450px;
  background-color: #f7f8fc;
}
.connection__container2 {
  min-height: 450px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding-right: 30px;
  padding-left: 30px;
  // padding-bottom: 100px !important;
  background-color: #f7f8fc;
}
</style>
