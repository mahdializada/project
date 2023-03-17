import {
    SET_ITEM,
    UPDATE_ITEM,
    INSERT_ITEM,
    SET_EXTRA_DATA,
    UPDATE_SUPPLIER,
    TOGGLE_API_CALLING,
} from './mutations';
export default{
    async fetchItems({commit},data){
        try {
            commit(TOGGLE_API_CALLING,true);
            const response = await this.$axios.get(`product_list`,{
                params: data,
            });
            if(response.status == 200){
                commit(TOGGLE_API_CALLING,false);
                commit(SET_ITEM,response.data.data);
                commit(SET_EXTRA_DATA,response.data.extraData);
            }
            commit(TOGGLE_API_CALLING,false);
            return response;
        } catch (error) {
            commit(TOGGLE_API_CALLING,false);
            return error;
        }
    },
    insertNewItem({ commit }, item) {
        commit(INSERT_ITEM, item);
      },
      updateItem({ commit }, item) {
        commit(UPDATE_ITEM, item);
      },
      updateProductSupplier({ commit }, item) {
        commit(UPDATE_SUPPLIER, item);
      },
}