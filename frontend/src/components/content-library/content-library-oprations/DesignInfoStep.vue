<template>
  <div class="h-full mt-5">
    <div class="w-full d-flex pb-3">
      <InputCard
        :title="this.$tr('connection_deriction')"
        :hasSearch="false"
        :hasPagination="false"
      >
        <NoCheckboxItemContainer
          v-model="form.content_direction.$model"
          @blur="form.content_direction.$touch()"
          :items="contentDirections"
          :bgWhite="true"
          :customIcon="'puzzle'"
          :rules="
            validateRule(
              form.content_direction, // validate objec
              $root, // context
              $tr('connection_deriction') // lable for feedback
            )
          "
          :invalid="form.content_direction.$invalid"
          height="180px"
          :customWidth="false"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div>
    <div class="pb-3">
      <InputCard
        :title="this.$tr('voice_over')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.voice_over.$model"
            v-for="item in voiceOver"
            :key="item.id"
            :item="item"
            :selected="item.id == form.voice_over.$model"
            :disable="!item.active"
            @click="onvoice_overSelected(item)"
            :rules="
              validateRule(
                form.voice_over, // validate objec
                $root, // context
                $tr('voice_over') // lable for feedback
              )
            "
            :invalid="form.voice_over.$invalid"
          >
          </SelectItem>
        </div>
      </InputCard>
    </div>
    <div class="pb-3">
      <InputCard
        :title="this.$tr('music')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.music.$model"
            v-for="item in musics"
            :key="item.id"
            :item="item"
            :selected="item.id == form.music.$model"
            :disable="!item.active"
            @click="onmusicSelected(item)"
            :rules="
              validateRule(
                form.music, // validate objec
                $root, // context
                $tr('music') // lable for feedback
              )
            "
            :invalid="form.music.$invalid"
          >
          </SelectItem>
        </div>
      </InputCard>
    </div>
    <div class="pb-3">
      <InputCard
        :title="this.$tr('shooting')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.shooting.$model"
            v-for="item in shootings"
            :key="item.id"
            :item="item"
            :selected="item.id == form.shooting.$model"
            :disable="!item.active"
            @click="onShootingSelected(item)"
            :rules="
              validateRule(
                form.shooting, // validate objec
                $root, // context
                $tr('shooting') // lable for feedback
              )
            "
            :invalid="form.shooting.$invalid"
          >
          </SelectItem>
        </div>
      </InputCard>
    </div>
    <div class="pb-3">
      <InputCard
        :title="this.$tr('people')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.people.$model"
            v-for="item in people"
            :key="item.id"
            :item="item"
            :selected="item.id == form.people.$model"
            :disable="!item.active"
            @click="onPeopleSelected(item)"
            :rules="
              validateRule(
                form.people, // validate objec
                $root, // context
                $tr('people') // lable for feedback
              )
            "
            :invalid="form.people.$invalid"
          >
          </SelectItem>
        </div>
      </InputCard>
    </div>
    <div class="pb-3">
      <InputCard
        :title="this.$tr('graphics')"
        :hasSearch="false"
        :hasPagination="false"
        class="sales__type__container"
      >
        <div class="d-flex align-center">
          <SelectItem
            v-model="form.graphics.$model"
            v-for="item in graphics"
            :key="item.id"
            :item="item"
            :selected="item.id == form.graphics.$model"
            :disable="!item.active"
            @click="onGraphicsSelected(item)"
            :rules="
              validateRule(
                form.graphics, // validate objec
                $root, // context
                $tr('graphics') // lable for feedback
              )
            "
            :invalid="form.graphics.$invalid"
          >
          </SelectItem>
        </div>
      </InputCard>
    </div>
  </div>
</template>

<script>
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import GlobalRules from "~/helpers/vuelidate";
import SelectItem from "../../new_form_components/components/SelectItem.vue";

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      contentDirections: [
        { id: "interesting", name: "interesting", active: true },
        { id: "suspense", name: "suspense", active: true },
        { id: "attractive", name: "attractive", active: true },
        { id: "stimulate", name: "stimulate", active: true },
        { id: "fanny", name: "fanny", active: true },
        { id: "shocking", name: "shocking", active: true },
        { id: "story", name: "story", active: true },
        { id: "lure", name: "lure", active: true },
        { id: "surprise", name: "surprise", active: true },
      ],
      voiceOver: [
        { id: "none", name: "none", active: true },
        {
          id: "eloquant_arabic",
          name: "eloquant_arabic",
          active: true,
        },
        {
          id: "gulf_emirate",
          name: "gulf_emirate",
          active: true,
        },
        {
          id: "english",
          name: "english",
          active: true,
        },
      ],
      musics: [
        { id: "none", name: "none", active: true },
        { id: "musical_background", name: "musical_background", active: true },
        { id: "trending_audios", name: "trending_audios", active: true },
        { id: "gulf_song", name: "gulf_song", active: true },
        {
          id: "trending_global_songs",
          name: "trending_global_songs",
          active: true,
        },
      ],
      shootings: [
        { id: "none", name: "none", active: true },
        {
          id: "clip_from_the_internet",
          name: "clip_from_the_internet",
          active: true,
        },
        { id: "local_shooting", name: "local_shooting", active: true },
      ],
      people: [
        { id: "none", name: "none", active: true },
        {
          id: "clip_from_the_internet",
          name: "clip_from_the_internet",
          active: true,
        },
        {
          id: "person_infront_of_the_camera",
          name: "person_infront_of_the_camera",
          active: true,
        },
        {
          id: "person_behind_of_the_camera",
          name: "person_behind_of_the_camera",
          active: true,
        },
      ],
      graphics: [
        { id: "none", name: "none", active: true },
        { id: "simple_graphics", name: "simple_graphics", active: true },
        { id: "meduim_graphics", name: "meduim_graphics", active: true },
        { id: "full_graphics", name: "full_graphics", active: true },
      ],
    };
  },
  computed: {},
  methods: {
    async validate() {
      this.form.content_direction.$touch();
      this.form.voice_over.$touch();
      this.form.music.$touch();
      this.form.shooting.$touch();
      this.form.people.$touch();
      this.form.graphics.$touch();
      return (
        !this.form.content_direction.$invalid &&
        !this.form.voice_over.$invalid &&
        !this.form.music.$invalid &&
        !this.form.shooting.$invalid &&
        !this.form.people.$invalid &&
        !this.form.graphics.$invalid
      );
    },
    async loaded() {},
    onvoice_overSelected(item) {
      if (item.active == true) {
        this.form.voice_over.$model = item.id;
      }
    },
    onmusicSelected(item) {
      if (item.active == true) {
        this.form.music.$model = item.id;
      }
    },
    onShootingSelected(item) {
      if (item.active == true) {
        this.form.shooting.$model = item.id;
      }
    },
    onPeopleSelected(item) {
      if (item.active == true) {
        this.form.people.$model = item.id;
      }
    },
    onGraphicsSelected(item) {
      if (item.active == true) {
        this.form.graphics.$model = item.id;
      }
    },
  },
  components: { InputCard, NoCheckboxItemContainer, SelectItem },
};
</script>

<style>
.sales__type__container {
  display: flex;
  justify-content: space-between;
}
</style>