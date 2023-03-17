<template>
  <div class="w-full h-full d-flex justify-center pt-5">
    <div class="w-full">
      <div class="connection__container">
        <div class="mb-3">
          <!-- <InputCard
            :title="this.$tr('platforms')"
            :hasSearch="true"
            :hasPagination="false"
            @search="(v) => (searchPlaform = v)"
          >
            <NoCheckboxItemContainer
              v-model="form.platform_id.$model"
              :items="filterPlatform"
              :loading="isFetchingplatforms"
              :no_data="filterPlatform.length < 1 && !isFetchingplatforms"
              item_text="platform_name"
              height="150px"
              :hasLogo="true"
            ></NoCheckboxItemContainer>
          </InputCard> -->
        </div>
        <div class="mb-3">
          <!-- <InputCard
            :title="this.$tr('ad_accounts')"
            :hasSearch="true"
            :hasPagination="false"
            @search="(v) => (searchAdAccount = v)"
          >
            <NoCheckboxItemContainer
              v-model="form.ad_account_id.$model"
              :items="filterAdAccount"
              :loading="isFetchingadAccounts"
              :no_data="filterAdAccount.length < 1 && !isFetchingadAccounts"
              height="150px"
              :hasLogo="false"
              nameLogo="name"
            ></NoCheckboxItemContainer>
          </InputCard> -->
        </div>
      </div>
      <div class="pb-5">
        <v-expand-transition>
          <InputCard :title="$tr('download_connection_template')">
            <div
              class="download-item mx-auto d-flex align-center justify-space-between my-2"
            >
              <div class="d-flex align-center">
                <div
                  class="file-icon bg-custom-gray rounded-circle me-1 d-flex align-center justify-center"
                >
                  <svg viewBox="0 0 20.193 26.964" height="20px">
                    <g transform="translate(-1031.701 -196.518)">
                      <path
                        id="Path_1134"
                        data-name="Path 1134"
                        d="M256.233,271.468c.207.105.148.3.148.466,0,5.019.013,10.038,0,15.057a3.5,3.5,0,0,1-3.62,3.566q-6.475.007-12.951,0a3.507,3.507,0,0,1-3.6-3.586q-.016-9.9,0-19.8a3.5,3.5,0,0,1,3.606-3.58c2.965,0,5.93.012,8.9.019,0,.14.012.28.012.42q0,3.5,0,6.994c0,.263-.011.473.368.47C251.467,271.478,253.85,271.476,256.233,271.468Zm-5.692,11.82A1.719,1.719,0,0,0,252.4,282c.127-.95-.357-1.576-1.439-1.859-.236-.062-.475-.113-.709-.182a.311.311,0,0,1-.258-.3c0-.187.142-.267.307-.308a.693.693,0,0,1,.849.352c.145.291.293.276.525.145a1.539,1.539,0,0,1,.389-.154c.436-.107.34-.339.162-.609a2.2,2.2,0,0,0-2.459-.693,1.45,1.45,0,0,0-1.035,1.26,1.369,1.369,0,0,0,.957,1.359c.338.14.693.237,1.038.36.24.086.486.2.41.513s-.359.353-.63.342a.645.645,0,0,1-.612-.347c-.222-.664-.645-.332-1.022-.256-.243.049-.29.181-.217.419A1.967,1.967,0,0,0,250.541,283.288Zm-5.929-.031c-.506-.856-.92-1.583-1.365-2.292a.571.571,0,0,1,.006-.7c.389-.614.748-1.246,1.107-1.851-1.042-.366-1.605.131-1.935,1.129-.289-.462-.434-.923-.746-1.129-.334-.22-.824-.063-1.31-.07.4.674.744,1.282,1.126,1.863a.629.629,0,0,1,.011.8c-.449.7-.856,1.428-1.287,2.156.241.112.455.033.657.067a.779.779,0,0,0,.943-.523,8.733,8.733,0,0,1,.58-.964,11.142,11.142,0,0,0,.773,1.281C243.526,283.462,244.041,283.122,244.612,283.258Zm.432-2.493c0,.7.012,1.4-.006,2.105-.007.279.087.362.358.358.895-.013,1.79-.022,2.684,0,.516.013.315-.348.336-.579.023-.25.088-.545-.361-.506-.4.035-.809-.022-1.209.015-.424.039-.582-.071-.558-.532.042-.8.012-1.614.012-2.421,0-.9,0-.883-.877-.876-.294,0-.4.078-.386.381C245.058,279.4,245.044,280.08,245.044,280.765Z"
                        transform="translate(795.505 -67.078)"
                        fill="#1d622d"
                      />
                      <path
                        id="Path_1135"
                        data-name="Path 1135"
                        d="M362.416,271.632c-2.383.008-4.767.011-7.15.031-.379,0-.369-.206-.368-.47q.005-3.5,0-6.994c0-.14-.007-.28-.011-.42.877.781,1.641,1.672,2.456,2.513,1.6,1.65,3.184,3.316,4.772,4.978C362.224,271.384,362.317,271.512,362.416,271.632Z"
                        transform="translate(689.322 -67.242)"
                        fill="#468d63"
                      />
                    </g>
                  </svg>
                </div>
                <div>
                  <div style="font-size: 14px">connection_template.xlsx</div>
                  <div style="font-size: 12px">248 KB</div>
                </div>
              </div>
              <div class="d-flex align-center">
                <v-btn
                  rounded
                  color="primary"
                  small
                  dark
                  :loading="isFileDownloaded"
                  @click="onDownloadTemplate"
                >
                  {{ $tr("download") }}
                </v-btn>
              </div>
            </div>
          </InputCard>
        </v-expand-transition>
      </div>
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import InputCard from "../../new_form_components/components/InputCard.vue";
import NoCheckboxItemContainer from "../../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import { onDownload } from "./helpers/helper";
export default {
  props: {
    form: Object,
    selectedTabData: {
      type: Object,
      default: () => {},
    },
    submit: {
      type: Function,
      default: () => {},
    },
  },

  data() {
    return {
      onDownload: onDownload,
      download_url: "advertisement/export-connection-template/",
      isFileDownloaded: false,

      searchPlaform: "",
      searchAdAccount: "",

      validateRule: GlobalRules.validate,
      isFetchingplatforms: false,
      isFetchingadAccounts: false,

      platforms: [],
      adAccounts: [],
    };
  },
  computed: {
    filterPlatform() {
      if (this.searchPlaform) {
        const filter = (item) =>
          item?.platform_name?.toLowerCase()?.includes(this.searchPlaform?.toLowerCase());
        return this.platforms.filter(filter);
      }

      return this.platforms;
    },
    filterAdAccount() {
      if (this.searchAdAccount) {
        const filter = (item) =>
          item?.name?.toLowerCase()?.includes(this.searchAdAccount?.toLowerCase());
        return this.adAccounts.filter(filter);
      }
      return this.adAccounts;
    },
  },
  watch: {
    // "form.platform_id": {
    //   handler: async function (platform_id) {
    //     await this.onPlatformClickHandler(platform_id.$model);
    //   },
    //   deep: true,
    // },
  },
  methods: {
    async onDownloadTemplate() {
      const result = await this.onDownload(this.download_url + `null`, this);
      this.submit(result);
    },
    async fetchPlatforms(company_id) {
      try {
        this.isFetchingplatforms = true;
        const url = `advertisement/connection/generate_link/platforms/${company_id}`;
        const { data } = await this.$axios.get(url);
        this.platforms = data.items;
      } catch (error) {}
      this.isFetchingplatforms = false;
    },
    async loaded() {
      // try {
      //   if (this.selectedTabData?.key == "platform") {
      //     this.platforms = this.selectedTabData.platform;
      //   } else if (this.selectedTabData && "company" in this.selectedTabData) {
      //     const company_id = this.selectedTabData.company[0].id;
      //     await this.fetchPlatforms(company_id);
      //   } else if (!(this.selectedTabData && "company" in this.selectedTabData)) {
      //     this.$toastr.w(this.$tr("please_select_an_item", this.$tr("company")));
      //   }
      // } catch (error) {
      // }
    },
    validate() {
      return true;
      // let isValid = !this.form.platform_id.$invalid && !this.form.ad_account_id.$invalid;
      // return isValid;
    },
    async onPlatformClickHandler(platform_id) {
      try {
        this.isFetchingadAccounts = true;
        const response = await this.$axios.get(
          `advertisement/application-accounts/${platform_id}`
        );
        if (response?.status == 200) {
          this.adAccounts = response?.data?.ad_accounts;
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.isFetchingadAccounts = false;
    },
    async fetchItems({ type, id }) {
      try {
        this["isFetching" + type] = true;
        const url = `advertisement/connection/generate_link/${type}/${id}`;
        const { data } = await this.$axios.get(url);
        this[data.type] = data.items;
      } catch (error) {}
      this["isFetching" + type] = false;
    },
  },
  components: { InputCard, NoCheckboxItemContainer, CTitle },
};
</script>
<style scoped>
.download-item {
  padding: 12px;
  height: 54px;
  border-radius: 27px;
  background: var(--v-background-base);
  border: 1px solid var(--v-background-darken1);
  font-size: 12px;
}
.file-icon {
  height: 34px;
  width: 34px;
}
</style>
