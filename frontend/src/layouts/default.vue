<template>
  <v-app>
    <TalkhAlert></TalkhAlert>
    <div
      style="
        position: fixed;
        top: 60px;
        right: 10px;
        min-width: 22%;
        max-width: 40%;
        z-index: 10000000;
      "
    >
      <NasTosater></NasTosater>
    </div>
    <div
      v-shortkey="['ctrl', '/']"
      class="d-flex flex-grow-1"
      @shortkey="onKeyup"
    >
      <!-- Navigation -->
      <div
        style=""
        :class="`navbar-drawer ${small ? 'small' : ''} friqiBase`"
        :right="$vuetify.rtl"
      >
        <!-- Navigation menu info -->
        <div
          :class="`pa-2 custom-logo-class ${
            small ? 'd-flex justify-center' : ''
          }`"
        >
          <div class="mt-2" style="height: 50px" v-if="small">
            <svg viewBox="0 0 101.33 72.94" height="40px">
              <g id="Layer_1-2">
                <g>
                  <path
                    class="cls-2"
                    d="M61.39,66.53l-1.41-.73h.01l-17.06-9c-1.97,.64-4.07,1-6.26,1-11.2,0-20.27-9.08-20.27-20.27s9.08-20.27,20.27-20.27,20.27,9.08,20.27,20.27c0,6.37-2.95,12.05-7.54,15.77l15.19,8.01c2-2.35,3.71-4.98,5.1-7.91,2.36-4.96,3.55-10.38,3.55-16.26,0-7.02-1.62-13.32-4.86-18.9-3.24-5.57-7.65-9.91-13.22-13.02C49.59,2.12,43.43,.56,36.68,.56s-12.83,1.55-18.44,4.66c-5.61,3.11-10.05,7.45-13.32,13.02C1.64,23.81,0,30.11,0,37.14s1.64,13.32,4.91,18.9c3.28,5.57,7.72,9.93,13.32,13.07,3.21,1.8,6.61,3.07,10.18,3.84h45.12l-12.15-6.4Z"
                    fill="#e9640b"
                  />
                  <circle
                    class="cls-1"
                    cx="36.77"
                    cy="37.86"
                    r="11.61"
                    fill="#ffffff"
                  />
                  <path
                    class="cls-2"
                    d="M90.26,22.13c-6.1,0-11.07-4.96-11.07-11.07S84.16,0,90.26,0s11.07,4.96,11.07,11.07-4.96,11.07-11.07,11.07Zm0-17.01c-3.28,0-5.94,2.67-5.94,5.94s2.67,5.94,5.94,5.94,5.94-2.67,5.94-5.94-2.67-5.94-5.94-5.94Z"
                    fill="#e9640b"
                  />
                  <rect
                    class="cls-2"
                    x="65.44"
                    y="17.45"
                    width="15.89"
                    height="3"
                    transform="translate(-1.46 30.88) rotate(-23.54)"
                    fill="#e9640b"
                  />
                </g>
              </g>
            </svg>
          </div>
          <svg
            viewBox="0 0 210.23 132.15"
            class="mt-2 ms-2"
            height="50px"
            v-else
          >
            <g id="Layer_1-2">
              <g>
                <path
                  class="cls-1"
                  d="M209.67,52.49v34.88c0,5.5-3.75,9.96-8.38,9.96h-12.63V52.49h21.01Z"
                  fill="#ffffff"
                />
                <g>
                  <path
                    class="cls-1"
                    d="M21.04,127.81c-1.13-.52-2.01-1.23-2.65-2.15-.64-.92-.99-1.95-1.03-3.09h2.38c.11,1.09,.59,2.05,1.45,2.88,.86,.83,2.11,1.24,3.76,1.24,1.52,0,2.73-.39,3.62-1.17,.89-.78,1.33-1.77,1.33-2.97,0-.97-.25-1.74-.74-2.33-.49-.59-1.1-1.03-1.83-1.32-.73-.29-1.74-.61-3.02-.95-1.5-.41-2.69-.8-3.57-1.19-.88-.39-1.63-.99-2.25-1.8-.62-.81-.93-1.92-.93-3.31,0-1.16,.3-2.19,.9-3.1,.6-.91,1.45-1.62,2.54-2.14,1.09-.51,2.35-.77,3.76-.77,2.08,0,3.75,.51,5.03,1.53,1.28,1.02,2.01,2.33,2.2,3.94h-2.44c-.15-.92-.66-1.74-1.51-2.46-.86-.72-2.01-1.08-3.47-1.08-1.35,0-2.48,.36-3.38,1.08-.9,.72-1.35,1.7-1.35,2.94,0,.94,.25,1.7,.74,2.28,.49,.58,1.11,1.02,1.85,1.32,.74,.3,1.74,.62,3.01,.97,1.46,.41,2.64,.81,3.54,1.21,.9,.4,1.66,1,2.28,1.82,.62,.82,.93,1.91,.93,3.28,0,1.05-.28,2.05-.84,2.99-.56,.94-1.38,1.7-2.48,2.28-1.09,.58-2.4,.87-3.92,.87s-2.78-.26-3.9-.77Z"
                    fill="#ffffff"
                  />
                  <path
                    class="cls-1"
                    d="M58.47,106.18v22.18h-2.25v-17.71l-7.91,17.71h-1.64l-7.91-17.65v17.65h-2.25v-22.18h2.38l8.58,19.22,8.58-19.22h2.41Z"
                    fill="#ffffff"
                  />
                  <path
                    class="cls-1"
                    d="M76.6,123.02h-10.22l-1.96,5.34h-2.38l8.2-22.18h2.54l8.17,22.18h-2.38l-1.96-5.34Zm-.68-1.86l-4.44-12.15-4.44,12.15h8.87Z"
                    fill="#ffffff"
                  />
                  <path
                    class="cls-1"
                    d="M96.5,128.36l-5.56-9.39h-4.15v9.39h-2.25v-22.47h6.75c2.51,0,4.41,.6,5.71,1.8,1.3,1.2,1.95,2.78,1.95,4.73,0,1.63-.47,3.02-1.4,4.16-.93,1.15-2.31,1.89-4.13,2.23l5.79,9.55h-2.7Zm-9.71-11.22h4.53c1.76,0,3.08-.43,3.97-1.29,.89-.86,1.34-2,1.34-3.44s-.43-2.62-1.29-3.42c-.86-.8-2.21-1.21-4.05-1.21h-4.5v9.35Z"
                    fill="#ffffff"
                  />
                  <path
                    class="cls-1"
                    d="M116.56,105.89v1.86h-6.24v20.61h-2.25v-20.61h-6.27v-1.86h14.76Z"
                    fill="#ffffff"
                  />
                  <path
                    class="cls-2"
                    d="M143.53,105.69v5.02h-9.07v3.99h6.69v4.79h-6.69v8.87h-6.3v-22.66h15.37Z"
                    fill="#e9640b"
                  />
                  <path
                    class="cls-2"
                    d="M157.68,128.36l-4.5-8.36h-.68v8.36h-6.3v-22.66h9.93c1.82,0,3.36,.32,4.63,.95,1.26,.63,2.22,1.5,2.86,2.6,.64,1.11,.96,2.34,.96,3.71,0,1.54-.42,2.9-1.27,4.07-.85,1.17-2.09,2-3.71,2.49l5.11,8.84h-7.04Zm-5.18-12.67h3.12c.86,0,1.5-.2,1.93-.61,.43-.41,.64-1,.64-1.77,0-.73-.22-1.3-.66-1.72-.44-.42-1.08-.63-1.91-.63h-3.12v4.73Z"
                    fill="#e9640b"
                  />
                  <path
                    class="cls-2"
                    d="M173.75,105.69v22.66h-6.3v-22.66h6.3Z"
                    fill="#e9640b"
                  />
                  <path
                    class="cls-2"
                    d="M192.88,132.15l-2.51-3.76c-.71,.13-1.43,.19-2.15,.19-2.12,0-4.07-.5-5.85-1.5-1.78-1-3.19-2.38-4.23-4.15-1.04-1.77-1.56-3.77-1.56-6s.52-4.23,1.56-6c1.04-1.77,2.45-3.14,4.23-4.13,1.78-.99,3.73-1.48,5.85-1.48s4.1,.49,5.87,1.48c1.77,.99,3.17,2.36,4.19,4.13,1.03,1.77,1.54,3.77,1.54,6,0,1.86-.38,3.58-1.13,5.16-.75,1.58-1.78,2.89-3.09,3.94l4.79,6.11h-7.52Zm-8.49-10.98c.92,1.04,2.2,1.56,3.82,1.56s2.88-.52,3.81-1.57c.93-1.05,1.4-2.45,1.4-4.21s-.47-3.22-1.4-4.26c-.93-1.04-2.2-1.56-3.81-1.56s-2.9,.52-3.82,1.56c-.92,1.04-1.38,2.46-1.38,4.26s.46,3.19,1.38,4.23Z"
                    fill="#e9640b"
                  />
                  <path
                    class="cls-2"
                    d="M208.99,105.69v22.66h-6.3v-22.66h6.3Z"
                    fill="#e9640b"
                  />
                </g>
                <path
                  class="cls-1"
                  d="M81.47,97.78V59.69c0-3.8,3.08-6.88,6.88-6.88h14.73v44.97h-21.61Z"
                  fill="#ffffff"
                />
                <path
                  class="cls-1"
                  d="M17.91,97.82V33.27c0-4.12,3.34-7.45,7.45-7.45h18.76V97.82H17.91Z"
                  fill="#ffffff"
                />
                <path
                  class="cls-1"
                  d="M.35,16.77c-.05,.28-.1,.56-.14,.84-.04,.3-.07,.6-.11,.9-.03,.33-.06,.65-.07,.98-.02,.33-.03,.67-.03,1v.06c.03,9.7,6.71,17.84,15.72,20.09-2.81-2.01-4.65-5.29-4.65-9.02,0-6.12,4.96-11.07,11.08-11.07H117.22L131.06,0H20.54C14.87,0,9.74,2.3,6.02,6.02,3.15,8.89,1.12,12.62,.35,16.77H.35Z"
                  fill="#ffffff"
                />
                <path
                  class="cls-1"
                  d="M82.98,25.81c-7.46,.08-13.77,5.02-15.92,11.82,.47-4.82,2.95-9.04,6.59-11.82h-22.19V97.78h22.29V53.25c1.21-3.98,4.88-6.89,9.23-6.97h17.98l13.86-20.47h-31.83Z"
                  fill="#ffffff"
                />
                <path
                  class="cls-2"
                  d="M170.3,91.61l-1.41-.73h.01l-17.06-9c-1.97,.64-4.07,1-6.26,1-11.2,0-20.27-9.08-20.27-20.27s9.08-20.27,20.27-20.27,20.27,9.08,20.27,20.27c0,6.37-2.95,12.05-7.54,15.77l15.19,8.01c2-2.35,3.71-4.98,5.1-7.91,2.36-4.96,3.55-10.38,3.55-16.26,0-7.02-1.62-13.32-4.86-18.9-3.24-5.57-7.65-9.91-13.22-13.02-5.57-3.11-11.74-4.66-18.49-4.66s-12.83,1.55-18.44,4.66c-5.61,3.11-10.05,7.45-13.32,13.02-3.28,5.57-4.91,11.87-4.91,18.9s1.64,13.32,4.91,18.9c3.28,5.57,7.72,9.93,13.32,13.07,3.21,1.8,6.61,3.07,10.18,3.84h45.12l-12.15-6.4Z"
                  fill="#e9640b"
                />
                <circle
                  class="cls-1"
                  cx="145.68"
                  cy="62.93"
                  r="11.61"
                  fill="#ffffff"
                />
                <path
                  class="cls-2"
                  d="M199.17,47.2c-6.1,0-11.07-4.96-11.07-11.07s4.96-11.07,11.07-11.07,11.07,4.96,11.07,11.07-4.96,11.07-11.07,11.07Zm0-17.01c-3.28,0-5.94,2.67-5.94,5.94s2.67,5.94,5.94,5.94,5.94-2.67,5.94-5.94-2.67-5.94-5.94-5.94Z"
                  fill="#e9640b"
                />
                <rect
                  class="cls-2"
                  x="174.35"
                  y="42.52"
                  width="15.89"
                  height="3"
                  transform="translate(-2.42 76.45) rotate(-23.54)"
                  fill="#e9640b"
                />
              </g>
            </g>
          </svg>
        </div>

        <!-- Navigation menu -->
        <!-- <main-menu :menu="navigation.menu" /> -->
        <Navbar :small="small" />
        <!-- Navigation menu footer -->
        <!-- Footer navigation links -->
      </div>

      <!-- Toolbar -->
      <div :class="`app-container w-full ${small ? 'navbar-small' : ''}`">
        <v-app-bar
          :class="`app-header ${small ? 'navbar-small' : ''}`"
          app
          :color="isToolbarDetached ? 'surface' : undefined"
          :flat="isToolbarDetached"
          :light="toolbarTheme === 'light'"
          :dark="toolbarTheme === 'dark'"
        >
          <v-card
            class="flex-grow-1 d-flex"
            :class="[isToolbarDetached ? 'pa-1 mt-3 mx-1' : 'pa-0 ma-0']"
            :flat="!isToolbarDetached"
          >
            <div class="d-flex flex-grow-1 align-center">
              <!-- search input mobile -->
              <v-text-field
                v-if="showSearch"
                append-icon="mdi-close"
                placeholder="Search"
                prepend-inner-icon="mdi-magnify"
                hide-details
                solo
                flat
                autofocus
                @click:append="showSearch = false"
              ></v-text-field>

              <div v-else class="d-flex flex-grow-1 align-center">
                <v-btn small @click.stop="navbarToggle" fab elevation="0">
                  <svg viewBox="0 0 33.258 23.655" width="24px">
                    <path
                      id="menu_icon"
                      data-name="menu icon"
                      d="M2316.5,23.655a2.5,2.5,0,1,1,0-5h12.284a2.5,2.5,0,1,1,0,5Zm0-9.328a2.5,2.5,0,1,1,0-5h22.111a2.5,2.5,0,1,1,0,5Zm0-9.327a2.5,2.5,0,1,1,0-5h28.257a2.5,2.5,0,0,1,0,5Z"
                      transform="translate(-2314)"
                      fill="#105598"
                    />
                  </svg>
                </v-btn>
                <!-- search input desktop -->
                <v-text-field
                  ref="search"
                  class="mx-1 hidden-xs-only"
                  :placeholder="$tr('toolbar_search')"
                  prepend-inner-icon="mdi-magnify"
                  hide-details
                  filled
                  rounded
                  dense
                >
                </v-text-field>

                <v-btn
                  class="d-block d-sm-none"
                  icon
                  @click="showSearch = true"
                >
                  <v-icon>mdi-magnify</v-icon>
                </v-btn>
                <v-spacer></v-spacer>

                <CurrencyToolbar />

                <CompaniesToolbar />

                <toolbar-language />

                <!-- <toolbar-apps /> -->

                <div :class="[$vuetify.rtl ? 'ml-1' : 'mr-1']">
                  <toolbar-notifications />
                </div>

                <toolbar-user />
              </div>
            </div>
          </v-card>
        </v-app-bar>

        <v-main class="fill-height">
          <v-container class="fill-height pa-2" :fluid="!isContentBoxed">
            <v-layout>
              <nuxt />
            </v-layout>
          </v-container>

          <v-footer app inset>
            <v-spacer></v-spacer>
            <div class="overline">
              Build with
              <v-icon small color="pink">mdi-heart</v-icon>
              @SMART FRIQI
            </div>
          </v-footer>
          <!-- <Calculator /> -->
          <customization-menu />
          <jump-to />
          <online-users />
        </v-main>
      </div>
      <LoadingComponent :loadingProps.sync="loading" />
      <PasswordAndCompanyDialog />
      <UploadingFileModal />
      <DownloadModel v-if="zippingFileModel != 'hide'" />
      <OredohSignIn />
    </div>
  </v-app>
</template>

<script>
// navigation menu configurations
import config from "../configs";

import MainMenu from "../components/navigation/MainMenu";
import ToolbarUser from "../components/toolbar/ToolbarUser";
import ToolbarApps from "../components/toolbar/ToolbarApps";
import CompaniesToolbar from "../components/toolbar/CompaniesToolbar";
import ToolbarLanguage from "../components/toolbar/ToolbarLanguage";
import ToolbarNotifications from "../components/toolbar/ToolbarNotifications";
import CustomizationMenu from "../components/navigation/CustomizationMenu";
import OnlineUsers from "../components/onlineUsers/OnlineUsers";
import PasswordAndCompanyDialog from "../components/signin/PasswordAndCompanyDialog";
import { mapState, mapActions } from "vuex";
import LoadingComponent from "../components/LoadingComponent.vue";
import UploadingFileModal from "../components/files/common/UploadingFileModal.vue";
import DownloadModel from "../components/files/common/DownloadModel.vue";
import Navbar from "../components/navigation2/Navbar.vue";
import JumpTo from "../components/jump_to/JumpTo.vue";
import TalkhAlert from "../components/alert/TalkhAlert.vue";
import CurrencyToolbar from "../components/toolbar/CurrencyToolbar.vue";
import NasTosater from "../components/alert/NasTosater.vue";
import Calculator from "../components/navigation/Calculator.vue";
import OredohSignIn from "../components/signin/OredohSignIn.vue";

export default {
  async created() {
    if (process.client) {
      // this.getGeolocation();
      if (this.$auth.loggedIn) {
        await this.fetchTranslations({
          language_id: this.$auth.user.translated_language_id,
        });
      } else {
        await this.fetchTranslations({
          baseLanguage: true,
        });
      }
      this.loading = false;
    }

    this.fetchSystems({ for_select: true });
  },

  components: {
    MainMenu,
    ToolbarUser,
    ToolbarApps,
    ToolbarLanguage,
    ToolbarNotifications,
    CustomizationMenu,
    OnlineUsers,
    LoadingComponent,
    CompaniesToolbar,
    PasswordAndCompanyDialog,
    UploadingFileModal,
    DownloadModel,
    Navbar,
    JumpTo,
    TalkhAlert,
    NasTosater,
    CurrencyToolbar,
    Calculator,
    OredohSignIn,
  },
  data() {
    return {
      small: false,
      showSearch: false,
      navigation: config.navigation,
      loading: true,
      key: 0,
      gettingLocation: false,
      location: null,
      errorStr: null,
      mini: true,
    };
  },

  computed: {
    ...mapState("files", ["zippingFileModel"]),
    ...mapState("app", [
      "application",
      "isContentBoxed",
      "menuTheme",
      "toolbarTheme",
      "isToolbarDetached",
    ]),
    ...mapState(["oredohAuthorized"]),
  },
  methods: {
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
      fetchCountries: "filterData/fetchCountriesWithCompanies",
      fetchSystems: "systems/fetchItems",
      ChangeAuthorization: "ChangeAuthorization",
    }),
    onKeyup(e) {
      this.$refs.search.focus();
    },
    async logout() {
      await this.$auth.logout();
      this.$router.push("/auth/signin");
    },

    async getGeolocation() {
      if (!("geolocation" in navigator)) {
        this.errorStr = "Geolocation is not available.";
      } else {
        this.gettingLocation = true;
        await navigator.geolocation.getCurrentPosition(
          async (pos) => {
            this.gettingLocation = false;
            this.$axios.setHeader("latitude", pos.coords.latitude);
            this.$axios.setHeader("longitude", pos.coords.longitude);
          },
          async (err) => {
            this.gettingLocation = false;
            this.errorStr = err.message;
            return this.$nuxt.error({
              statusCode: 403,
            });
          }
        );
      }
    },

    navbarToggle() {
      this.small = !this.small;
      this.$root.$emit("setWidthHeader");
    },
  },
};
</script>
<style>
:root {
  --transition: all 0.4s;
  --navbar-width: 300px;
  --navbar-width-small: 80px;
  /* --navbar-bg: #0e4781; */
}
.custom-logo-class {
  /* padding-bottom: 35% !important; */
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05);
}
.navbar-drawer {
  /* background: #115598;  */
  border-radius: 0 16px 16px 0;
  width: var(--navbar-width);
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  transition: var(--transition);
  z-index: 10;
}
.v-application--is-rtl .navbar-drawer {
  border-radius: 16px 0 0 16px;
  left: unset;
  right: 0;
}
.navbar-drawer.small {
  width: var(--navbar-width-small);
}
.app-container {
  padding-left: var(--navbar-width);
  transition: var(--transition);
}
.v-application--is-rtl .app-container {
  padding-left: unset;
  padding-right: var(--navbar-width);
}
.app-container.navbar-small {
  padding-left: var(--navbar-width-small);
}
.v-application--is-rtl .app-container.navbar-small {
  padding-right: var(--navbar-width-small);
  padding-left: unset;
}
.app-header {
  margin-left: var(--navbar-width);
  transition: var(--transition);
}
.v-application--is-rtl .app-header {
  margin-right: var(--navbar-width);
  margin-left: unset;
}
.app-header.navbar-small {
  margin-left: var(--navbar-width-small);
}
.v-application--is-rtl .app-header.navbar-small {
  margin-right: var(--navbar-width-small);
  margin-left: unset;
}
</style>
