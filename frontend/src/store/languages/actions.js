import Alert from "~/helpers/alert";
import {
	TOGGLE_API_CALLING,
	SET_LANGUAGES,
	SET_ALL_LANGUAGES,
	SET_TRANSLATED_LANGUAGE,
	SET_BASE_LANGUAGE,
	SET_ITEMS_TOTAL,
	SET_EXTRA_DATA,
	INSERT_ITEM,
	REMOVE_ITEM_FROM_ALL,
	TOGGLE_TRANSLATION_API_CALLING,
	TOGGLE_BASE_TRANSLATION_API_CALLING,
	SET_TRANSLATIONS,
	SET_BASE_TRANSLATIONS,
	SET_PHRASES,
	REMOVE_ITEM,
	SET_ACTIVE_LANGUAGES,
} from "./mutations";

export default {
	async fetchAllLanguages({ commit }, data) {
		try {
			commit(TOGGLE_API_CALLING, true);
			const response = await this.$axios.get("languages", {
				params: data,
			});
			if (response?.status === 200) {
				commit(TOGGLE_API_CALLING, false);
				commit(SET_ALL_LANGUAGES, response?.data?.data);
			}
			return response;
		} catch (error) {
			commit(TOGGLE_API_CALLING, false);
			return error;
		}
	},
	async fetchLanguages({ commit }, data) {
		try {
			commit(TOGGLE_API_CALLING, true);
			const response = await this.$axios.get("languages_translated", {
				params: data,
			});
			if (response?.status === 200) {
				commit(TOGGLE_API_CALLING, false);
				commit(SET_LANGUAGES, response?.data?.data);
				const { data, ...extraData } = response?.data;
				commit(SET_ITEMS_TOTAL, response?.data?.total);
				commit(SET_EXTRA_DATA, extraData);
			}
			return response;
		} catch (error) {
			commit(TOGGLE_API_CALLING, false);
			return error;
		}
	},

	async fetchActiveLanguages({ commit }, data) {
		try {
			const response = await this.$axios.get("languages_translated", {
				params: data,
			});
			if (response?.status === 200) {
				commit(SET_ACTIVE_LANGUAGES, response?.data?.data);
			}
			return response;
		} catch (error) {
			return error;
		}
	},

	async insertLanguageDb({ commit }, data) {
		try {
			const response = await this.$axios.post(
				"languages_translated",
				data.formData,
				{
					headers: {
						"Content-Type": "multipart/form-data",
					},
				}
			);
			if (response?.status === 201) {
				commit(INSERT_ITEM, {
					data: response?.data?.data,
					tabKey: data.tabKey,
				});
				commit(REMOVE_ITEM_FROM_ALL, response?.data?.data);
				return true;
			} else {
				return false;
			}
		} catch (error) {
			return false;
		}
	},

	async fetchLanguage({ commit }, data) {
		try {
			let response = await this.$axios.get(`languages_translated/${data.id}`, {
				params: data,
			});
			if (response?.status === 200) {
				return response?.data?.data;
			} else {
				return false;
			}
		} catch (error) {
			return false;
		}
	},

	async fetchTranslations({ commit }, data) {
		try {
			commit(TOGGLE_TRANSLATION_API_CALLING, true);
			const response = await this.$axios.get("translations", {
				params: data,
			});
			if (response?.status === 200) {
				commit(TOGGLE_TRANSLATION_API_CALLING, false);
				commit(SET_TRANSLATIONS, response?.data?.data);
				commit(SET_TRANSLATED_LANGUAGE, response?.data?.language);
			}
			return response;
		} catch (error) {
			commit(TOGGLE_TRANSLATION_API_CALLING, false);
			return error;
		}
	},

	async fetchBaseTranslations({ commit }, data) {
		try {
			commit(TOGGLE_BASE_TRANSLATION_API_CALLING, true);
			const response = await this.$axios.get("translations", {
				params: data,
			});
			if (response?.status === 200) {
				commit(TOGGLE_BASE_TRANSLATION_API_CALLING, false);
				commit(SET_BASE_TRANSLATIONS, response?.data?.data);
				commit(SET_BASE_LANGUAGE, response?.data?.language);
			}
			return response;
		} catch (error) {
			commit(TOGGLE_BASE_TRANSLATION_API_CALLING, false);
			return error;
		}
	},

	async fetchPhrases({ commit }, data) {
		try {
			const response = await this.$axios.get("phrases", {
				params: data,
			});
			if (response?.status === 200) {
				commit(SET_PHRASES, response?.data);
			}
			return response;
		} catch (error) {
			return error;
		}
	},
	insertNewItem({ commit }, item) {
		commit(INSERT_ITEM, item);
	},
	deleteItem({ commit }, item) {
		commit(REMOVE_ITEM, item);
	},
};
