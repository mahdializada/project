export const state = () => ({
	icon :'',
	textDialog: "",
	show: false,
	acceptbBtnTxt: "",
	descText:"",
	cancelCallback: () => {},
	acceptCallback: () => {},
});
export const getters = {
	getShow: (state) => {
		return state.show;
	},
};
export const mutations = {
	changeAlertModel(state, value) {
		state.show = value;
	},

	changeAlertIcon(state, value) {
		state.icon = value;
	},

	changeAlertText(state, value) {
		state.textDialog = value;
	},

	changeAcceptbBtnTxt(state, value) {
		state.acceptbBtnTxt = value;
	},

	cancelCallback(state, callback) {
		state.cancelCallback = () => {
			callback();
		};
	},
	changeDescText (state , vlaue){
		state.descText = vlaue;
	},

	acceptCallback(state, callback) {
		state.acceptCallback = () => {
			callback();
		};
	},
};

export default {
	namespace: true,
	state,
	getters,
	mutations,
};
