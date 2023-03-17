export default (context, inject) => {
	const TalkhAlertNA = (
		text,
		icon,
		acceptCallBack = ()=>{},
		acceptBtnText = "yes",
		descText = ' ',
		cancelCallBack = ()=>{},
	) => {
		context.store.commit('talkh_alert/changeAlertModel', true);
		context.store.commit('talkh_alert/changeAcceptbBtnTxt',acceptBtnText);
		context.store.commit('talkh_alert/changeAlertIcon', icon);
		context.store.commit('talkh_alert/changeAlertText',text);
		context.store.commit('talkh_alert/changeDescText',descText);
		context.store.commit('talkh_alert/cancelCallback', cancelCallBack);
		context.store.commit('talkh_alert/acceptCallback', acceptCallBack);
	};
	inject("TalkhAlertNA", TalkhAlertNA);
	// For Nuxt <= 2.12, also add 👇
	context.$TalkhAlertNA = TalkhAlertNA;
};
