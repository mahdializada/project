<template>
  <div @click="startEditor()">hello worldfffff</div>
</template>

<script>
export default {
  created() {
    console.log("createddd");
  },
  mounted() {
    let tiktokScript = document.createElement("script");
    tiktokScript.setAttribute(
      "src",
      "https://sf16-unpkg-va.ibytedtos.com/latest/byted/instant_page_sdk/dist/index.js"
    );
    tiktokScript.setAttribute("crossorigin", "anonymous");
    //     tiktokScript.setAttribute("defer");
    //     tiktokScript.setAttribute("async");
    document.head.appendChild(tiktokScript);
    window.InstantPageAsyncInit = (val) => {
      InstantPage.init({
        appId: "your_appid",
        version: "v1.0", // Only v1.0 for now
      });
    };
  },
  methods: {
    startEditor() {
      InstantPage.start(
        {
          accountId: "your_adv_id",
          accessToken: "your_access_token",
          display: "popup",
          actionType: "create",
          // pageId: "pageid",
          language: "en",
          businessType: 1,
          appId: "appId",
          countryCode: ["US"],
          siteType: 0,
          externalAction: 0,
        },
        (response) => {
          console.log("response", response);
          // pageId
          // status
          // accountId
          // ...
        }
      );
    },
  },
};
</script>
<!-- <script>
window.InstantPageAsyncInit = (val) => {
  InstantPage.init({
    appId: "your_appid",
    version: "v1.0", // Only v1.0 for now
  });
};
</script> -->

<style>
</style>