export const TOGGLE_API_CALLING = "TOGGLE_API_CALLING";
export const SET_ITEMS          = 'SET_ITEMS';
export const SET_ITEMS_TOTAL    = 'SET_ITEMS_TOTAL';

export default {
    [SET_ITEMS](state, items){
        state.items = items;
    },

    [TOGGLE_API_CALLING](state, value) {
        state.isApiCalling = value
    }, 
    [SET_ITEMS_TOTAL](state, total) {
        state.itemsTotal = total
    },
}