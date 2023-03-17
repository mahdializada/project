import state from "./state";

export default {
  items:            (state) => [...state.items],
  itemsTotal:       (state) => state.itemsTotal,
  
  isApiCalling:     (state) => state.isApiCalling,
  actions:          (state) => state.actions,
}
