import { SET_TRANSLATED_LANGUAGE, SET_TRANSLATIONS } from "./mutations";

export default {
  async fetchTranslations(app, data) {
    if (process.client) {
      let translations = JSON.parse(localStorage.getItem("translations"));
      // if translation exists in local storage
      if (translations?.language != null) {
        let res = false;
        // if local storage language id is equal to requested language
        if (translations.language.id == data.language_id) {
          res = await app.dispatch("isUptoDate", {
            language_id: translations?.language.id,
            updated_at: translations?.language.updated_at,
          });
          // if language is up to date
          if (res) {
            app.commit(SET_TRANSLATIONS, translations.translations);
            app.commit(SET_TRANSLATED_LANGUAGE, translations.language);
          }
          // else if language is not up to date
          else {
            await app.dispatch("fetchTranslationDb", {
              language_id: translations.language.id,
            });
          }
        }
        // if localstorage language is not equal to users language
        else {
          await app.dispatch("fetchTranslationDb", data);
        }
      }
      // if language is not in local storage
      else {
        await app.dispatch("fetchTranslationDb", data);
      }
    }
  },

  async isUptoDate(app, data) {
    try {
      const response = await this.$axios.get("translations/is_uptodate", {
        params: data,
      });
      if (response?.data?.result) {
        return true;
      }
      return false;
    } catch (error) {
      return false;
    }
  },

  async fetchTranslationDb(app, data) {
    try {
      const response = await this.$axios.get("translations/for_system", {
        params: data,
      });
      if (response?.status === 200) {
        app.commit(SET_TRANSLATIONS, response?.data?.translations);
        app.commit(SET_TRANSLATED_LANGUAGE, response?.data?.language);
        localStorage.setItem("translations", JSON.stringify(response?.data));
      }
      return response;
    } catch (error) {
      return error;
    }
  },
};
