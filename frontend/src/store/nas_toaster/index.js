export const state = () => ({
	arrayOfTosater: [],
});
export const getters = {

};
export const mutations = {
	toasterNew(state, value) {
		state.arrayOfTosater = [value, ...state.arrayOfTosater];
	},
	arrayOfTosaterSet(state, value) {
		state.arrayOfTosater = value;
	},
	setInterval(state ,{item , value}){
		state.arrayOfTosater = state.arrayOfTosater.map(row=>{
			if(row.id == item.id){
				row.interval = value;
				return row
			}
			return row
		})
	},
	setValue(state, { item, value }) {
		if (value == 0) {
			state.arrayOfTosater = state.arrayOfTosater.map((row) => {
				if (row.id == item.id) {
					row.value = value;
					return row;
				}
				return row;
			});
		} else {
			state.arrayOfTosater = state.arrayOfTosater.map((row) => {
				if (row.id == item.id) {
					row.value += value;
					return row;
				}
				return row;
			});
		}
	},
};

export default {
	namespace: true,
	state,
	getters,
	mutations,
};
