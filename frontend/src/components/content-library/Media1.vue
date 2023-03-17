<template>
  <div>
    <CTitle :text="$tr('media')" />
    <MultiInputItem :items="form.media.$each.$iter" @addMore="addOneMore" @removeItem="removeItem">
      <template v-slot:content>
        {{ item }}
        <v-row>
          <v-col class="px-1 py-2" cols="8">
            <!-- <CTextField
              px="px-0"
              @blur="item.project_url.$touch()"
              v-model="item.project_url.$model"
              :title="$tr('landing_page_url')"
              placeholder="https://www.canva.com"
              prepend-inner-icon="mdi-link-variant "
              :rules="validateRule(item.project_url, $root, $tr('url'))"
              :hasCustomBtn="true"
              btnIcon="mdi-content-paste "
              :invalid="item.project_url.$invalid"
            /> -->
            <InputCard
              :title="$tr('media_size')"
              :hasSearch="false"
              :hasPagination="false"
            >
              <!-- <NoCheckboxItemContainer
                v-model="item.media_size.$model"
                @blur="item.media_size.$touch()"
                :items="mediaSize"
                :bgWhite="true"
                :fullImg="true"
                :isHashmat="true"
                class="px-0"
                :rules="
                  validateRule(
                    item.media_size, // validate objec
                    $root, // context
                    $tr('media_size') // lable for feedback
                  )
                "
                :invalid="item.media_size.$invalid"
                :customWidth="true"
              ></NoCheckboxItemContainer> -->
            </InputCard>
          </v-col>
          <v-col class="ps-1 pe-3 pt-9" cols="4">
            <iframe
              loading="lazy"
              style="
                width: 100%;
                height: 40vh;
                top: 0;
                left: 0;
                border: none; 
                padding: 0;
                margin: 0;
                border-radius: 15px;
              " 
              src="https://www.canva.com/design/DAFT4GjS1Qg/view?embed"
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
import MultiInputItem from "../new_form_components/cat_product_selection/MultiInputItem.vue";
import NoCheckboxItemContainer from "../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
import CTitle from "../new_form_components/Inputs/CTitle.vue";

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
          id: "story",
          name: "story",
          details: "1080 x 1920",
          image: "/icons/icons/story-size.png",
          active: true,
        },
        {
          id: "instagram_portrait",
          name: "instagram_portrait",
          details: "1080 x 1350",
          image: "/icons/icons/instagram-portriat-size.png",
          active: true,
        },
        {
          id: "instagram_post",
          name: "instagram_post",
          details: "1080 x 1350",
          image: "/icons/icons/instagram-post-size.png",
          active: true,
        },
        {
          id: "youtube",
          name: "youtube",
          details: "1080 x 1350",
          image: "/icons/icons/youtube-size.png",
          active: true,
        },
        {
          id: "facebook",
          name: "facebook",
          details: "1080 x 1350",
          image: "/icons/icons/facebook-size.png",
          active: true,
        },
      ],
    };
  },
  methods:{
    async validate() {},
    async loaded() {},
    removeItem(item){
      if(this.form.media.$model.length>l)
      this.form.media.$model.splice(item, 1);
    },
    addOneMore() {
      this.form.media.$model.push({
        project_url: "",
        media_size: "",
      });
    },
  }
};
</script>

<style>
</style>