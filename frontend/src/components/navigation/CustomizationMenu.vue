<template>
  <div>
    <v-btn
      ref="button"
      class="drawer-button"
      color="#ee44aa"
      dark
      @click="openMenu"
    >
      <v-icon class="fa-spin">mdi-cog-outline</v-icon>
    </v-btn>

    <v-navigation-drawer
      v-model="right"
      fixed
      :right="!$vuetify.rtl"
      hide-overlay
      temporary
      width="310"
    >
      <div class="d-flex align-center pa-2">
        <div class="title">{{ $tr("settings") }}</div>
        <v-spacer></v-spacer>
        <CloseBtn @click="closeMenu" />
      </div>

      <v-divider></v-divider>

      <div class="pa-2 overflow-auto" style="height: 90vh">
        <div class="font-weight-bold my-1">{{ $tr("default_setting") }}</div>
        <v-btn-toggle color="primary" mandatory class="mb-2">
          <v-btn x-large @click="defaultSetting()">{{
            $tr("set_default")
          }}</v-btn>
        </v-btn-toggle>
        <!-- <v-btn
          elevation="0"
          large
          class="blue lighten-5 blue--text text--darken-4"
          @click="defaultSetting()"
          >{{ $tr("set_default") }}</v-btn
        > -->

        <div class="font-weight-bold my-1">{{ $tr("global_theme") }}</div>
        <v-btn-toggle v-model="theme" color="primary" mandatory class="mb-2">
          <v-btn x-large>{{ $tr("light") }}</v-btn>
          <v-btn x-large>{{ $tr("dark") }}</v-btn>
        </v-btn-toggle>

        <div class="font-weight-bold my-1">{{ $tr("toolbar_theme") }}</div>
        <v-btn-toggle
          v-model="toolbarTheme"
          color="primary"
          mandatory
          class="mb-2"
        >
          <v-btn x-large>{{ $tr("global") }}</v-btn>
          <v-btn x-large>{{ $tr("light") }}</v-btn>
          <v-btn x-large>{{ $tr("dark") }}</v-btn>
        </v-btn-toggle>

        <div class="font-weight-bold my-1">{{ $tr("toolbar_style") }}</div>
        <v-btn-toggle
          v-model="toolbarStyle"
          color="primary"
          mandatory
          class="mb-2"
        >
          <v-btn x-large>{{ $tr("full") }}</v-btn>
          <v-btn x-large>{{ $tr("solo") }}</v-btn>
        </v-btn-toggle>

        <div class="font-weight-bold my-1">{{ $tr("content_layout") }}</div>
        <v-btn-toggle
          v-model="contentBoxed"
          color="primary"
          mandatory
          class="mb-2"
        >
          <v-btn x-large>{{ $tr("fluid") }}</v-btn>
          <v-btn x-large>{{ $tr("boxed") }}</v-btn>
        </v-btn-toggle>

        <div class="font-weight-bold my-1">{{ $tr("menu_theme") }}</div>
        <v-btn-toggle
          v-model="menuTheme"
          color="primary"
          mandatory
          class="mb-2"
        >
          <v-btn x-large>{{ $tr("global") }}</v-btn>
          <v-btn x-large>{{ $tr("light") }}</v-btn>
          <v-btn x-large>{{ $tr("dark") }}</v-btn>
        </v-btn-toggle>

        <div class="font-weight-bold my-1">RTL</div>
        <v-switch v-model="rtl" inset :label="$tr('right_to_left')"> </v-switch>

        <div class="font-weight-bold my-1">{{ $tr("primary_color") }}</div>

        <v-color-picker
          v-model="color"
          mode="hexa"
          :swatches="swatches"
          show-swatches
        ></v-color-picker>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import CloseBtn from "../design/Dialog/CloseBtn.vue";

export default {
  data() {
    return {
      right: false,
      theme: 0,
      toolbarTheme: 0,
      toolbarStyle: 0,
      contentBoxed: 0,
      menuTheme: 0,
      timeout: null,
      color: "#2e7be6",
      swatches: [
        ["#2e7be6", "#31944f"],
        ["#EE4f12", "#46BBB1"],
        ["#ee44aa", "#55BB46"],
      ],
      rtl: false,
    };
  },
  components: {
    CloseBtn,
  },
  watch: {
    color(val) {
      this.$vuetify.theme.themes.dark.primary = val;
      this.$vuetify.theme.themes.light.primary = val;
      if (val == "#2E7BE6") {
        this.$vuetify.theme.themes.light.friqiBase = "#115598";
        this.$vuetify.theme.themes.light.BaseLight = "#104982";

        this.$vuetify.theme.themes.dark.friqiBase = "#115598";
        this.$vuetify.theme.themes.dark.BaseLight = "#104982";
      } else {
        this.$vuetify.theme.themes.light.friqiBase = val;
        this.$vuetify.theme.themes.light.BaseLight = this.ColorLuminance(
          val,
          -0.15
        );

        this.$vuetify.theme.themes.dark.friqiBase = val;
        this.$vuetify.theme.themes.dark.BaseLight = this.ColorLuminance(
          val,
          -0.15
        );
      }
      this.color = val;
    },
    theme(val) {
      const theme = val === 0 ? "light" : "dark";
      localStorage.setItem("global_theme", theme);
      this.$vuetify.theme.dark = theme === "dark";
      this.setGlobalTheme(theme);
    },
    toolbarTheme(val) {
      const theme = val === 0 ? "global" : val === 1 ? "light" : "dark";

      this.setToolbarTheme(theme);
    },
    toolbarStyle(val) {
      this.setToolbarDetached(val === 1);
    },
    menuTheme(val) {
      const theme = val === 0 ? "global" : val === 1 ? "light" : "dark";

      this.setMenuTheme(theme);
    },
    contentBoxed(val) {
      this.setContentBoxed(val === 1);
    },
    rtl(val) {
      this.$vuetify.rtl = val;
      this.setRTL(val);
    },
    async right(val) {
      if (val == false) {
        const data = {
          global_theme: this.theme,
          toolbar_theme: this.toolbarTheme,
          toolbar_style: this.toolbarStyle,
          content_layout: this.contentBoxed,
          menu_theme: this.menuTheme,
          primary_color: this.color,
        };
        try {
          await this.$axios.get("/customize-theme/custome-theme", {
            params: data,
          });
        } catch (error) {}
      }
    },
  },

  mounted() {
    this.animate();
  },
  beforeDestroy() {
    if (this.timeout) clearTimeout(this.timeout);
  },
  methods: {
    ...mapMutations("app", [
      "setMenuTheme",
      "setGlobalTheme",
      "setToolbarTheme",
      "setContentBoxed",
      "setRTL",
      "setToolbarDetached",
    ]),

    ColorLuminance(hex, lum) {
      // validate hex string
      hex = String(hex).replace(/[^0-9a-f]/gi, "");
      if (hex.length < 6) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
      }
      lum = lum || 0;

      // convert to decimal and change luminosity
      var rgb = "#",
        c,
        i;
      for (i = 0; i < 3; i++) {
        c = parseInt(hex.substr(i * 2, 2), 16);
        c = Math.round(Math.min(Math.max(0, c + c * lum), 255)).toString(16);
        rgb += ("00" + c).substr(c.length);
      }

      return rgb;
    },

    animate() {
      if (this.timeout) clearTimeout(this.timeout);

      const time = (Math.floor(Math.random() * 10 + 1) + 10) * 1000;

      this.timeout = setTimeout(() => {
        this.$animate(this.$refs.button.$el, "bounce");
        this.animate();
      }, time);
    },
    async closeMenu() {
      this.right = false;
    },
    openMenu() {
      this.right = true;
    },
    async defaultSetting() {
      this.theme = 0;
      this.toolbarTheme = 0;
      this.toolbarStyle = 0;
      this.contentBoxed = 0;
      this.menuTheme = 0;
      this.timeout = null;
      this.color = "#2e7be6";
      this.rtl = false;

      const data = {
        global_theme: this.theme,
        toolbar_theme: this.toolbarTheme,
        toolbar_style: this.toolbarStyle,
        content_layout: this.contentBoxed,
        menu_theme: this.menuTheme,
        primary_color: this.color,
      };

      try {
        await this.$axios.get("/customize-theme/custome-theme", {
          params: data,
        });
      } catch (error) {}
    },
  },

  created() {
    if (this.$auth.user.customize_theme != null) {
      let data = this.$auth.user.customize_theme;
      this.theme = data.global_theme;
      this.toolbarTheme = data.toolbar_theme;
      this.toolbarStyle = data.toolbar_style;
      this.contentBoxed = data.content_layout;
      this.menuTheme = data.menu_theme;
      // this.color = data.primary_color;

      if (process.client) {
        window.setTimeout(() => {
          this.color = data.primary_color;
        }, 0);
      }
    }
  },
};
</script>

<style lang="scss" scoped>
.drawer-button {
  position: fixed;
  bottom: 80px;
  right: -20px;
  z-index: 6;
  box-shadow: 1px 1px 18px #ee44aa;

  .v-icon {
    margin-left: -18px;
    font-size: 1.3rem;
  }
}
.v-application--is-rtl {
  .drawer-button {
    right: unset;
    left: -20px;
    .v-icon {
      margin-right: -18px;
      margin-left: unset;
    }
  }
}
</style>
