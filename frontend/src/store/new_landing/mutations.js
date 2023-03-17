export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_CATEGORIES = "SET_CATEGORIES";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const REMOVE_CATEGORY = "REMOVE_CATEGORY";
export const INSERT_CATEGORY = "INSERT_CATEGORY";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const INSERT_ITEM = "INSERT_ITEM";
export const SET_SUB_CATEGORIES = "SET_SUB_CATEGORIES";
export const SET_ITEMS = "SET_ITEMS";
export const REMOVE_SUBCATEGORY = "REMOVE_SUBCATEGORY";
export const INSERT_SUBCATEGORY = "INSERT_SUBCATEGORY";
export const SET_ALL_CATEGORIES = 'SET_ALL_CATEGORIES';

export default {
  [SET_ALL_CATEGORIES](state, allCategories){
    state.allCategories = allCategories;
  },

  [SET_SUB_CATEGORIES](state, subCategories){
    state.subCategories = subCategories;
  },
  [SET_ITEMS](state, items) {
    state.items = items;
  },
  // toggle api
  [TOGGLE_API_CALLING](state, value) {
    state.isApiCalling = value;
  },

 

  // set categories
  [SET_CATEGORIES](state, data) {
    state.categories = data;
  },

  // set the extra data of items in to store
  [SET_EXTRA_DATA](state, extraData) {
    state.extraData = extraData;
  },

  // Remove a category from  Categories
  [REMOVE_CATEGORY](state, code) {
    state.categories = state.categories.filter((ele) => ele?.code !== code);
  },

  // Add a category from  Categories
  [INSERT_CATEGORY](state, data) {
    state.categories?.unshift(data);
  },

  // Remove an item from  items list
  [REMOVE_ITEM](state, code) {
    state.items = state.items.filter((ele) => ele?.code !== code);
  },

  // Add an item from  items list
  [INSERT_ITEM](state, data) {
    state.items?.unshift(data);
  },

  // Remove a SubCategory from  SubCategories
  [REMOVE_SUBCATEGORY](state, code) {
    state.subCategories = state.subCategories.filter((ele) => ele?.code !== code);
  },

  // Add a SubCategory from  SubCategories
  [INSERT_SUBCATEGORY](state, data) {
    state.subCategories?.unshift(data);
  },
};
