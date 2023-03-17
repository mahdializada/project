<template>
  <div class="mt-5">
    <CTitle :text="`sub_systems`" style="margin-top: 15% !important" />
    <div class="w-full d-flex">
      <InputCard
        :title="$tr('label_required', $tr('sub_systems'))"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchSubSystem = v)"
      >
        <ItemsContainer
          v-model="form.sub_systems.$model"
          :multi="true"
          :items="filterSubSystems"
          :loading="loading"
          :no_data="subSystems.length < 1"
          height="200px"
        ></ItemsContainer>
      </InputCard>
    </div>
    <div class="w-full d-flex mt-3" v-if="hasTab">
      <InputCard
        :title="this.$tr('tab_system')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchTabs = v)"
      >
        <NoCheckboxItemContainer
          v-model="tabs"
          :items="filterTab"
          :loading="isFetchingTabs"
          :no_data="filterTab.length < 1 && !isFetchingTabs"
          height="200px"
          :hasLogo="false"
          text-value="name"
          :multi="true"
          nameLogo="name"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div>
    <!-- <div class="w-full d-flex mt-3" v-if="isContent">
      <InputCard
        :title="this.$tr('tab_system')"
        :hasSearch="true"
        :hasPagination="false"
        @search="(v) => (searchTabs = v)"
      >
        <NoCheckboxItemContainer
          v-model="tabs"
          :items="filterContentTab"
          :loading="isFetchingTabs"
          :no_data="filterContentTab.length < 1 && !isFetchingTabs"
          height="200px"
          :hasLogo="false"
          text-value="name"
          :multi="true"
          nameLogo="name"
        ></NoCheckboxItemContainer>
      </InputCard>
    </div> -->
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import ItemsContainer from "../../new_form_components/cat_product_selection/ItemsContainer.vue";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

import Icons from "~/configs/menus/menuIcons.js";
import HandleException from "~/helpers/handle-exception";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
export default {
  props: {
    form: Object,
  },

  data() {
    return {
      hasTab: false,
      loading: false,
      isFetchingTabs: false,
      targetId: "",
      contentId: "",
      searchSubSystem: "",
      searchTabs: "",
      subSystems: [],
      tabs: [],
      validateRule: GlobalRules.validate,
      tabItems: [
        {
          id: 1,
          name: "country",
        },
        {
          id: 2,
          name: "company",
        },
        {
          id: 3,
          name: "sales_type",
        },
        {
          id: 4,
          name: "item_code",
        },
        {
          id: 5,
          name: "issp_code",
        },
        {
          id: 6,
          name: "platform",
        },
        {
          id: 7,
          name: "organization",
        },
        {
          id: 8,
          name: "ad_account",
        },
        {
          id: 9,
          name: "campaign",
        },
        {
          id: 10,
          name: "ad_set",
        },
        {
          id: 11,
          name: "ad",
        },
        {
          id: 12,
          name: "project",
        },
      ],
      contentTab: [
        {
          id: 1,
          name: "pending",
        },
        {
          id: 2,
          name: "publish",
        },
        {
          id: 3,
          name: "not publish",
        },
        {
          id: 4,
          name: "rejected",
        },
      ],
      online_sales: [
        {
          id: 1,
          name: "country",
        },
        {
          id: 2,
          name: "company",
        },
        {
          id: 3,
          name: "sales_type",
        },
        {
          id: 4,
          name: "item_code",
        }
      ],
    };
  },
  watch: {
    "form.sub_systems.$model": function (id) {
      console.log(id);
      if (id.find((item) => item == this.targetId)) {
        this.hasTab = true;
      } else {
        this.hasTab = false;
      }
    },
  },
  computed: {
    filterSubSystems() {
      if (this.searchSubSystem) {
        const filter = (item) =>
          item?.name
            ?.toLowerCase()
            ?.includes(this.searchSubSystem?.toLowerCase());
        return this.subSystems.filter(filter);
      }
      return this.subSystems;
    },
    filterTab() {
      if (this.searchTabs) {
        const filter = (item) =>
          item?.name?.toLowerCase()?.includes(this.searchTabs?.toLowerCase());
        return this.tabItems.filter(filter);
      }
      return this.tabItems;
    },
    filterContentTab() {
      if (this.contentTab) {
        const filter = (item) =>
          item?.name?.toLowerCase()?.includes(this.searchTabs?.toLowerCase());
        return this.contentTab.filter(filter);
      }
      return this.contentTab;
    },
  },
  methods: {
    async validate() {
      if (!this.hasTab) {
        this.form.$model.tab = [];
      } else {
        for (let i = 0; i < this.tabs.length; i++) {
          if (this.tabItems.find((item) => item.id == this.tabs[i]))
            this.form.$model.tab.push(
              this.tabItems.find((item) => item.id == this.tabs[i]).name
            );
        }
      }

      this.form.sub_systems.$touch();
      return !this.form.sub_systems.$invalid;
    },
    async loaded() {
      this.tabs = [];
      this.loading = true;
      await this.fetchSubSystems();

      this.loading = false;
      console.log(this.subSystems);
      const advertisement = this.subSystems.find(
        (item) => item.name == "Advertisement"
      );

      if (advertisement) {
        this.targetId = advertisement.id;
      }
      
      const content = this.subSystems.find(
        (item) => item.name == "Content library"
      );

      if (content) {
        this.targetId = content.id;
        this.tabItems = [
          {
            id: 1,
            name: "new",
          },
          {
            id: 2,
            name: "pending",
          },
          {
            id: 3,
            name: "publish",
          },
          {
            id: 4,
            name: "not publish",
          },
          {
            id: 5,
            name: "rejected",
          },
        ];
      }

      const onlineSales = this.subSystems.find(
        (item) => item.name == "Online sales"
      );
      console.log("Online sales", onlineSales);

      if (onlineSales) {
        this.targetId = onlineSales.id;
        this.tabItems = [
          {
            id: 1,
            name: "country",
          },
          {
            id: 2,
            name: "company",
          },
          {
            id: 3,
            name: "sales_type",
          },
          {
            id: 4,
            name: "item_code",
          },

          {
            id: 5,
            name: "project",
          },
        ];
      }

      if (this.form.$model.tab.length > 0) {
        let i = 0;
        this.tabs = this.tabItems.map((item) => {
          if (item.name == this.form.$model.tab[i]) {
            i++;
            return item.id;
          }
        });
      }
    },
    async fetchSubSystems() {
      try {
        const response = await this.$axios.get(
          `systems_sub_systems?system_name=${this.$route.params.slug}`
        );
        if (response?.status === 200 && response?.data?.result) {
          const subs = response?.data?.data;
          for (let i = 0; i < subs.length; i++) {
            if (!(subs[i].name == "Labels" || subs[i].name == "Reasons")) {
              subs[i];
              subs[i].icon = "";
              let b = subs[i].phrase;
              if (Icons[b]) subs[i].icon = Icons[b];
              else if (Icons[b + "s"]) subs[i].icon = Icons[b + "s"];
              else if (Icons[b + "_management_system"])
                subs[i].icon = Icons[b + "_management_system"];
              else if (Icons[b.substr(0, b.length - 1) + "_list"])
                subs[i].icon = Icons[b.substr(0, b.length - 1) + "_list"];
              else {
                subs[i].icon =
                  "<h1 style='color:#2e7be6'>" +
                  b.charAt(0).toUpperCase() +
                  "</h1>";
              }
            }
          }
          this.subSystems = subs;
        } else {
          this.$toasterNA("orange", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        if (error?.response?.status === 404 && !error?.response?.data?.result) {
          this.$toasterNA("orange", this.$tr("sub_system_not_found"));
        } else {
          HandleException.handleApiException(this, error);
        }
      }
    },
  },

  components: {
    InputCard,
    ItemsContainer,
    CAutoComplete,
    NoCheckboxItemContainer,
    CTitle,
  },
};
</script>
