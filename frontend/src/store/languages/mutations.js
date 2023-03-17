export const SET_LANGUAGES = "SET_LANGUAGES";
export const SET_ACTIVE_LANGUAGES = "SET_ACTIVE_LANGUAGES";
export const SET_TRANSLATIONS = "SET_TRANSLATIONS";
export const SET_PHRASES = "SET_PHRASES";
export const SET_BASE_TRANSLATIONS = "SET_BASE_TRANSLATIONS";
export const SET_TRANSLATED_LANGUAGE = "SET_TRANSLATED_LANGUAGE";
export const SET_BASE_LANGUAGE = "SET_BASE_LANGUAGE";
export const SET_ITEMS_TOTAL = "SET_ITEMS_TOTAL";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const TOGGLE_TRANSLATION_API_CALLING = "TOGGLE_TRANSLATION_API_CALLING";
export const TOGGLE_BASE_TRANSLATION_API_CALLING =
	"TOGGLE_BASE_TRANSLATION_API_CALLING";
export const SET_ALL_LANGUAGES = "SET_ALL_LANGUAGES";
export const INSERT_ITEM = "INSERT_ITEM";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const REMOVE_ITEM_FROM_ALL = "REMOVE_ITEM_FROM_ALL";

// deleted items

export default {
	//update record's total when deleted
	// toggle api
	[SET_LANGUAGES](state, value) {
		state.languages = value;
	},

	[SET_ACTIVE_LANGUAGES](state, value) {
		state.activeLanguages = value;
	},

	[SET_BASE_LANGUAGE](state, value) {
		state.baseLanguage = value;
	},

	[SET_TRANSLATED_LANGUAGE](state, value) {
		state.translatedLanguage = value;
	},

	[SET_TRANSLATIONS](state, value) {
		state.translations = value;
	},

	[SET_BASE_TRANSLATIONS](state, value) {
		state.baseTranslations = value;
	},

	[SET_PHRASES](state, value) {
		state.phrases = value;
	},

	[SET_ALL_LANGUAGES](state, value) {
		state.allLanguages = value;
	},

	[TOGGLE_API_CALLING](state, value) {
		state.isApiCalling = value;
	},

	[TOGGLE_TRANSLATION_API_CALLING](state, value) {
		state.isApiTranslationCalling = value;
	},

	[TOGGLE_BASE_TRANSLATION_API_CALLING](state, value) {
		state.isApiBaseTranslationCalling = value;
	},

	[SET_EXTRA_DATA](state, extraData) {
		state.extraData = extraData;
	},
	// set the total number of items in to store
	[SET_ITEMS_TOTAL](state, total) {
		state.itemsTotal = total;
	},

	[INSERT_ITEM](state, newItem) {
		if (newItem.tabKey == "all" || newItem.tabKey == "pending") {
			state.languages?.unshift(newItem.data);
		}
		state.extraData.allTotal = state.extraData.allTotal + 1;
		state.extraData.pendingTotal = state.extraData.pendingTotal + 1;
	},

	// deleted items
	[REMOVE_ITEM](state, depsId) {
		state.languages = state.languages.filter((deps) => deps?.id !== depsId);
	},

	[REMOVE_ITEM_FROM_ALL](state, newItem) {
		state.allLanguages = state.allLanguages?.filter((lang) => {
			return lang.country_language_id != newItem.country_language_id;
		});
	},
};
