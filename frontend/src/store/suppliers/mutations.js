export const SET_ITEMS = "SET_ITEMS";
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const REMOVE_ITEM = "REMOVE_ITEM";
export const INSERT_ITEM = "INSERT_ITEM";
export const UPDATE_ITEM = "UPDATE_ITEM";
export const SET_SUPPLIER_INSERT_DATA = "SET_SUPPLIER_INSERT_DATA";

export default {
  [REMOVE_ITEM](state, depsId) {
    state.suppliers = state.suppliers.filter((deps) => deps?.id !== depsId);
    state.extraData.total = state.extraData?.total - 1;
  },

  // [INSERT_ITEM](state, data) {

  //   state.insertItem=data;

  // },
  // insert new item to the store
  [INSERT_ITEM](state, {newItem,tabKey}) {
    if(tabKey == 'all' || tabKey == 'pending')
    state.suppliers?.unshift(newItem);
    state.extraData.total+=1;
    state.extraData.allTotal+=1;
    state.extraData.pendingTotal+=1;
  },
  
  [SET_ITEMS](state, data) {
    state.suppliers = data;
  },

  [UPDATE_ITEM](state, updatedItem) {
    state.suppliers = state.suppliers?.map((item) => {
      if (item?.id === updatedItem?.id) {
        return updatedItem;
      }
      return item;
    });
  },

  [SET_EXTRA_DATA](state, extraData) {
    state.extraData = extraData;
  },

  [TOGGLE_API_CALLING](state, value) {
    state.isApiCalling = value;
  },
  [SET_SUPPLIER_INSERT_DATA](state, value) {
    state.supplierInsertData = value;
  },
};
