import {
  INSERT_DELETED_ITEM,
  INSERT_ITEM, REMOVE_DELETED_ITEM,
  REMOVE_ITEM,
  SET_DELETED_ITEMS, SET_DELETED_ITEMS_TOTAL,
  SET_ITEMS,
  SET_ITEMS_TOTAL,
  TOGGLE_API_CALLING,
  UPDATE_ITEM
} from "./mutations";

export default {

  async fetchItems({commit}, baseUrl) {
    try {
      const response = await this.$axios.get(baseUrl)
      if (response?.status === 200) {
        commit(SET_ITEMS, response?.data?.data)
        commit(SET_ITEMS_TOTAL, response?.data?.total)
      }
      return response
    } catch (error) {
      return error
    }
  },


  async paginateItems({commit}, options) {
    try {
      commit(TOGGLE_API_CALLING, true)
      const response = await this.$axios.get(options?.baseUrl, {
        params: options,
      })
      if (response?.status === 200) {
        commit(SET_ITEMS, response?.data?.data)
        commit(SET_ITEMS_TOTAL, response?.data?.total)
      }
      commit(TOGGLE_API_CALLING, false)
      return response
    } catch (error) {
      commit(TOGGLE_API_CALLING, false)
      return error
    }

  },

  insertNewItem({commit}, item) {
    commit(INSERT_ITEM, item)
  },

  removeItem({commit}, itemId) {
    commit(REMOVE_ITEM, itemId)

  },

  updateItem({commit}, updatedItem) {
    commit(UPDATE_ITEM, updatedItem)
  },


  // ==========>   Deleted Section   <========= //

  async fetchDeletedItems({commit}, baseUrl) {
    try {
      const response = await this.$axios.get(baseUrl)
      if (response?.status === 200) {
        commit(SET_DELETED_ITEMS, response?.data?.data)
        commit(SET_DELETED_ITEMS_TOTAL, response?.data?.total)
      }
      return response
    } catch (error) {
      return error
    }
  },


  async paginateDeletedItems({commit}, options) {
    try {
      commit(TOGGLE_API_CALLING, true)
      const response = await this.$axios.get(options?.baseUrl, {
        params: options,
      })
      if (response?.status === 200) {
        commit(SET_DELETED_ITEMS, response?.data?.data)
        commit(SET_DELETED_ITEMS_TOTAL, response?.data?.total)
      }
      commit(TOGGLE_API_CALLING, false)
      return response
    } catch (error) {
      commit(TOGGLE_API_CALLING, false)
      return error
    }
  },

  insertDeletedItem({commit}, newItem) {
    commit(INSERT_DELETED_ITEM, newItem)
  },


  removeDeletedItem({commit}, itemId) {
    commit(REMOVE_DELETED_ITEM, itemId)
  },

}
