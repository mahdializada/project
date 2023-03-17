export const UPDATE_ITEM = 'UPDATE_ITEM';
export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const REMOVE_ITEM = 'REMOVE_ITEM';
export const INSERT_ITEM = 'INSERT_ITEM';
export const SET_ITEMS  = 'SET_ITEMS';
export const SET_ITEMS_TOTAL = 'SET_ITEMS_TOTAL';

export default {
    [SET_ITEMS](state, items){
        state.items = items;
    },

    [REMOVE_ITEM](state, ids) {
        state.systems = state.systems.filter((ele) => ele?.id !== ids);
    },

    [INSERT_ITEM](state, newItem) {
        state.items?.unshift(newItem)
        state.itemsTotal = state.itemsTotal + 1
    },
    
    [UPDATE_ITEM](state, data) {
        state.items = state.items?.map((item) => {
            if (item?.id === updatedItem?.id) {
              return updatedItem
            }
            return item
        })
    },
    
    [TOGGLE_API_CALLING](state, value) {
        state.isApiCalling = value
    }, 

    [SET_ITEMS_TOTAL](state, total) {
        state.itemsTotal = total
    },

}