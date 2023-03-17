export default {
    getItems: (state)=>{
        return state.productList;
    },
    getTotal: (state)=>{
        return state.total;
    },
    getExtraData: (state)=>{
        return state.extraData;
    },
}