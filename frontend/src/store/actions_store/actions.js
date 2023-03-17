import {
    SET_ITEMS,
    SET_ITEMS_TOTAL,
    TOGGLE_API_CALLING,
} from "./mutations";
  
export default {
    async fetchItems({commit}) {
        try {
            const response = await this.$axios.get('actions')
            if (response?.status === 200) {
                commit(SET_ITEMS, response?.data)
                commit(SET_ITEMS_TOTAL, response?.data?.total)
            }
            return response
        } catch (error) {
            return error
        }
    },  
}
  