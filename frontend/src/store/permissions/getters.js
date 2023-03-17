export default {

  items: (state) => [...state.items],
  itemsTotal: (state) => state.itemsTotal,

  // search in items
  filterItems: (state) => (searchQuery) => {
    if (searchQuery && searchQuery.toString().trim()) {
      return state.items.filter((item) => item?.name?.toLowerCase().includes(searchQuery?.toLowerCase()))
    }
    return [...state.items]
  },

  // search in deleted items
  filterDeletedItems: (state) => (searchQuery) => {
    if (searchQuery && searchQuery.toString().trim()) {
      return state.deletedItems.filter((item) => item?.name?.toLowerCase().includes(searchQuery?.toLowerCase()))
    }
    return [...state.deletedItems]
  },

  //  Deleted getters
  deletedItems: (state) => state.deletedItems,
  deletedItemsTotal: (state) => state.deletedItemsTotal,

  isApiCalling: (state) => state.isApiCalling,

}
