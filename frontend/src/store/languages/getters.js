import state from "./state";

export default {
  getLanguages: (state) => {
    return state.languages;
  },

  getActiveLanguages: (state) => {
    return state.activeLanguages;
  },
  getTranslations: (state) => {
    return state.translations;
  },

  getBaseTranslations: (state) => {
    return state.baseTranslations;
  },

  getBaseLanguage: (state) => {
    return state.baseLanguage;
  },

  getTranslatedLanguage: (state) => {
    return state.translatedLanguage;
  },

  getAllLanguages: (state) => {
    return state.allLanguages;
  },

  getPhrases: (state) => {
    return state.phrases;
  },

  // return api calling status
  isApiCalling: (state) => state.isApiCalling,
  isApiTranslationCalling: (state) => state.isApiTranslationCalling,
  isApiBaseTranslationCalling: (state) => state.isApiBaseTranslationCalling,
  itemsTotal: (state) => state.itemsTotal,

  // return only totals  according to tab changes
  getTotal: (state) => (tabKey) => {
    switch (tabKey) {
      case "removed":
        return state.extraData?.removedTotal || 0;
      case "inactive":
        return state.extraData?.inactiveTotal || 0;
      case "blocked":
        return state.extraData?.blockedTotal || 0;
      case "active":
        return state.extraData?.activeTotal || 0;
      case "pending":
        return state.extraData?.pendingTotal || 0;
      case "all":
        return state.extraData?.allTotal || 0;
      case "warning":
        return state.extraData?.warningTotal || 0;
      case "totalWords":
        return state.extraData?.totalWords || 0;
      case "frontendWords":
        return state.extraData?.frontendWords || 0;
      case "backendWords":
        return state.extraData?.backendWords || 0;
    }
  },
};
