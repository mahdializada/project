export const SET_ITEM = 'SET_ITEM'
export const UPDATE_ITEM = 'UPDATE_ITEM'
export const UPDATE_SUPPLIER = 'UPDATE_SUPPLIER'
export const SET_EXTRA_DATA = "SET_EXTRA_DATA";
export const INSERT_ITEM = 'INSERT_ITEM'
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING"
export default {
    [SET_ITEM](state,item){
        state.productList = item.data;
        state.total = item.total;
    },
    // toggle api
    [TOGGLE_API_CALLING](state, value) {
      state.isApiCalling = value;
    },
  
   // insert new item to the store
   [INSERT_ITEM](state, newItem) {
    for (let index = 0; index < newItem.length; index++) {
      state.productList?.unshift(newItem[index]);
      state.total += 1;
    }
    },
   [UPDATE_ITEM](state, item) {
    state.productList = state.productList.map((i)=>{
      if(i.id == item.id){
        return item;
      }else{
        return i;
      }
    })
    },
    [SET_EXTRA_DATA](state, extraData) {
      extraData.allTotal = extraData.withSupplierTotal+extraData.withoutSupplierTotal;
      state.extraData = extraData;
    },
   [UPDATE_SUPPLIER](state, item) {
    for (let j = 0; j < item.length; j++) {
      state.productList = state.productList.map((i)=>{
        if(i.id == item[j].id){
          return item[j];
        }else{
          return i;
        }
      })
    }
    },
};