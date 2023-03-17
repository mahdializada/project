export default {
  getTranslations: (state) => {
    return state.translations;
  },
  getTranslation: (state) => (phrase) => {
    return state.translations != []
      ? state.translations[phrase]
        ? state.translations[phrase]
        : phrase
      : phrase;
  },
  getTranslatedLanguage: (state) => {
    return state.translatedLanguage;
  },
};
