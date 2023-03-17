const locale = "en";

export default {
  // current locale
  locale,

  // when translation is not available fallback to that locale
  fallbackLocale: "en",

  // available locales for user selection
  availableLocales: [
    {
      code: "en",
      flag: "us",
      name: "English",
      file: "en/",
    },
    {
      code: "ar",
      flag: "sa",
      name: "العربية",
      file: "ar.js",
    },
  ],
};
