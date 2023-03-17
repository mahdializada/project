<template>
  <div class="w-full h-50 d-flex align-center justify-center">
    <InputCard
      :title="$tr('label_required', $tr('platform_placement'))"
      :sub_title="$tr('select_one_or_multiple_placements')"
    >
      <div class="mt-4 d-flex flex-wrap">
        <ShowPlatform
          class="my-1"
          :itemsShow="showitem"
          v-for="(showitem, indexShow) in itemToShow"
          :key="indexShow"
          @closeShowCard="closeShowCard"
        ></ShowPlatform>

        <CardPlatfrom
          class="my-1"
          :platform="items"
          @close="closeHandler"
          @done="done"
          v-for="(item, index) in cardItems"
          :cardItem="item"
          :key="index"
          ref="platformCardRef"
        ></CardPlatfrom>
        <AddNewCard
          class="my-1"
          @addNewPlatform="addNewPlatform()"
        ></AddNewCard>
      </div>
    </InputCard>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CardPlatfrom from "./CardPlatfrom.vue";
import AddNewCard from "./AddNewCard.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import ShowPlatform from "./ShowPlatform.vue";

export default {
  props: {
    form: Object, // default prop
    isEdit: Boolean,
  },
  data() {
    return {
      allItems: [
        {
          title: "facebook",
          icon: "mdi-facebook",
          placement: [
            { title: "story", icon: "mdi-heart" },
            { title: "newsfeed", icon: "mdi-heart" },
            { title: "video", icon: "mdi-heart" },
            { title: "livestrem", icon: "mdi-heart" },
          ],
        },
        {
          title: "instagram",
          icon: "mdi-instagram",
          placement: [
            { title: "story", icon: "mdi-heart" },
            { title: "newsfeed", icon: "mdi-heart" },
            { title: "video", icon: "mdi-heart" },
          ],
        },
        {
          title: "linkedIn",
          icon: "mdi-linkedin",
          placement: [{ title: "story", icon: "mdi-heart" }],
        },
        {
          title: "twitter",
          icon: "mdi-twitter",
          placement: [
            { title: "video", icon: "mdi-heart" },
            { title: "livestrem", icon: "mdi-heart" },
          ],
        },
      ],
      items: [],
      cardItems: [],
      itemToShow: [],
      finalData: [],
      validateRule: GlobalRules.validate, // gloabl function fro validate
    };
  },
  methods: {
    async loaded() {
      this.items = this.allItems;
      this.cardItems = [];
      this.itemToShow = [];
      this.finalData = [];
      if (this.isEdit) await this.addEditData();
    },
    addEditData() {
      for (let i = 0; i < this.form.$model.platforms.length; i++) {
        let placement = JSON.parse(
          this.form.$model.platforms[i].platform_placement
        );
        this.form.$model.platforms[i].platform_placement = placement;
        const data = this.items.find(
          (item) => item.title == this.form.$model.platforms[i].platform_name
        );
        this.itemToShow.push({
          selectedPlacement: placement,
          target_CPO: this.form.$model.platforms[i].target_cpo,
          budget: this.form.$model.platforms[i].budget,
          id: this.form.$model.platforms[i].ipa_id,
          stepCounter: i + 1,
          title: "Platform " + i + 1,
          selectedPlatform: {
            icon: data.icon,
            placement: data.placement,
            title: data.title,
          },
        });
        const el = this.itemToShow[i];
        this.finalData.push({
          platform_name: el.selectedPlatform.title,
          platform_placement: el.selectedPlacement,
          budget: el.budget,
          target_cpo: el.target_CPO,
        });
        this.items = this.items.filter((r) => r.title !== data.title);
      }
    },
    async validate() {
      // validate function must validate this step here and return true of false
      this.form.platforms.$touch();
      return !this.form.platforms.$invalid;
    },
    addNewPlatform() {
      if (this.cardItems.length == 0) {
        let idCard = this.cardItems.length + 1;
        this.cardItems.push({
          id: Math.floor(Math.random() * 1000000000),
          title: this.$tr('platform')+' '+ idCard,
          selectedPlatform: { icon: "", title: "" },
          selectedPlacement: [],
          budget: "",
          target_CPO: "",
          stepCounter: 0,
        });
      }
    },
    closeHandler(item_id) {
      this.cardItems = this.cardItems.filter((r) => r.id !== item_id);
    },
    done(item) {
      this.itemToShow.push(item);
      this.items = this.items.filter(
        (r) => r.title !== item.selectedPlatform.title
      );
      this.finalData = [];
      for (let index = 0; index < this.itemToShow.length; index++) {
        const el = this.itemToShow[index];
        this.finalData.push({
          platform_name: el.selectedPlatform.title,
          platform_placement: el.selectedPlacement,
          budget: el.budget,
          target_cpo: el.target_CPO,
        });
      }
      this.form.$model.platforms = this.finalData;
      this.closeHandler(item.id);
    },
    closeShowCard(item) {
      this.itemToShow = this.itemToShow.filter(
        (r) => r.selectedPlatform.title !== item.selectedPlatform.title
      );
      this.items.unshift(item.selectedPlatform);
      this.finalData = this.finalData.filter(
        (r) => r.platform_name !== item.selectedPlatform.title
      );
      this.form.$model.platforms = this.finalData;
    },
  },
  components: { AddNewCard, CardPlatfrom, InputCard, ShowPlatform },
};
</script>
