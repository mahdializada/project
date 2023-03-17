import Vue from "vue";

Vue.prototype.$animate = function (node, animationName, callback) {
	node.classList.add("animate__animated", `animate__${animationName}`);

	function handleAnimationEnd() {
		node.classList.remove("animate__animated", `animate__${animationName}`);
		node.removeEventListener("animationend", handleAnimationEnd);

		// eslint-disable-next-line callback-return
		if (typeof callback === "function") callback();
	}

	node.addEventListener("animationend", handleAnimationEnd);
};
