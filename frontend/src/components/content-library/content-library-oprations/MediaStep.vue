<template>
  <div>
    <CTitle :text="$tr('media')" />
    <MultiInputItem
      :items="form.media.$each.$iter"
      @addMore="addOneMore"
      @removeItem="removeItem"
      :title="'media'"
    >
      <template v-slot:content="{ item }">
        <v-row>
          <v-col class="px-0 pb-0" cols="8">
            <CTextField
              px="px-0"
              @blur="item.project_url.$touch()"
              v-model="item.project_url.$model"
              :title="$tr('project_url')"
              placeholder="https://www.canva.com/example . . ."
              prepend-inner-icon="mdi-link-variant"
              :rules="validateRule(item.project_url, $root, $tr('project_url'))"
              :hasCustomBtn="true"
              btnIcon="mdi-content-paste "
              @add="copyToAdIdCanva(item)"
              :invalid="item.project_url.$invalid"
            />
            <InputCard
              :title="$tr('media_size')"
              :hasSearch="false"
              py="pt-0"
              px="px-0"
              :hasPagination="false"
            >
              <NoCheckboxItemContainer
                v-model="item.media_size.$model"
                @blur="item.media_size.$touch()"
                :items="mediaSize"
                :bgWhite="true"
                :fullImg="true"
                :hasDetails="true"
                class="px-0 pt-0"
                :rules="
                  validateRule(
                    item.media_size, // validate objec
                    $root, // context
                    $tr('media_size') // lable for feedback
                  )
                "
                :invalid="item.media_size.$invalid"
                :customWidth="true"
              ></NoCheckboxItemContainer>
            </InputCard>
          </v-col>
          <v-col class="pe-1 ps-3 pt-11 pb-0" cols="4">
            <iframe
              loading="lazy"
              class="canvaView"
              :src="
                item.project_url.$model &&
                item.project_url.$model?.includes('https://www.canva.com')
                  ? item?.project_url?.$model?.slice(0, 41) + 'view?embed'
                  : ''
              "
              allowfullscreen="allowfullscreen"
              allow="fullscreen"
            >
            </iframe>
          </v-col>
        </v-row>
      </template>
    </MultiInputItem>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import MultiInputItem from "../../new_form_components/cat_product_selection/MultiInputItem.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

export default {
  components: {
    CTitle,
    MultiInputItem,
    CTextField,
    InputCard,
    NoCheckboxItemContainer,
  },
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      mediaSize: [
        {
          id: "story 1080x1920",
          name: "story",
          details: "1080 x 1920",
          image: "/icons/icons/story-size.png",
          active: true,
        },
        {
          id: "instagram_portrait 1080x1350",
          name: "instagram_portrait",
          details: "1080 x 1350",
          image: "/icons/icons/instagram-portriat-size.png",
          active: true,
        },
        {
          id: "instagram_post 1080x1080",
          name: "instagram_post",
          details: "1080 x 1080",
          image: "/icons/icons/instagram-post-size.png",
          active: true,
        },
        {
          id: "youtube 1920x1080",
          name: "youtube",
          details: "1920 x 1080",
          image: "/icons/icons/youtube-size.png",
          active: true,
        },
        {
          id: "facebook 1200x628",
          name: "facebook",
          details: "1200 x 628",
          image: "/icons/icons/facebook-size.png",
          active: true,
        },
      ],
      path: "",
    };
  },
  methods: {
    async validate() {
      this.form.media.$touch();
      return !this.form.media.$invalid;
    },
    async loaded() {},
    removeItem(item) {
      if(this.form.media.$model.length>1)
      this.form.media.$model.splice(item, 1);
    },
    addOneMore() {
      this.form.media.$model.push({
        project_url: "",
        media_size: "",
      });
    },
    async copyToAdIdCanva(item) {
      try {
        const clipboard = navigator.clipboard;
        const account_po = await clipboard.readText();
        if (account_po) {
          item.project_url.$model = account_po;
        }
      } catch (error) {}
    },
  },
};
</script>

<style  scoped>
.canvaView {
  width: 90%;
  height: 74%;
  top: 0;
  left: 0;
  border: 1px solid #f5f5f5;
  padding: 0;
  margin: 0;
  border-radius: 15px;
}
</style>
