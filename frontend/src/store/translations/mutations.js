export const SET_TRANSLATIONS = "SET_TRANSLATIONS";
export const SET_TRANSLATED_LANGUAGE = "SET_TRANSLATED_LANGUAGE";

export default {
  [SET_TRANSLATIONS](state, value) {
    state.translations = value;
  },
  [SET_TRANSLATED_LANGUAGE](state, value) {
    state.translatedLanguage = value;
  },
};
