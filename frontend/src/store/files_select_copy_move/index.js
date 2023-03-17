/* ================================> States <================================*/

var newFolderUploaded = "";
var selected = {};
export const state = () => ({
	newFolderUploaded,
	selected,
});

export const actions = {};

/* ================================>Mutations <================================*/

export const mutations = {
	// context menu
	addOrRemoveToSelected(
		state,
		{ item: { id, name, type, favorites, path }, selected }
	) {
		let temp = { ...state.selected };
		if (selected) {
			temp[id] = { id, name, type, favorites, path };
		} else {
			delete temp[id];
		}
		state.selected = temp;
	},
	changeSelected(
		state,
		{ folders, files, direction, container_width, layout }
	) {
		let items = [...folders, ...files];
		let index = items.findIndex(
			(item) => item.id == Object.values(state.selected)[0].id
		);
		let x = {};
		if (index != -1) {
			if (layout == "grid") {
				let step = Math.floor(container_width / 296);
				switch (direction) {
					case "up":
						if (index - step >= 0) {
							delete state.selected[items[index].id];
							x[items[index - step].id] = items[index - step];
							state.selected = x;
						} else {
							x[items[0].id] = items[0];
							state.selected = x;
						}
						break;
					case "down":
						if (index + step < items.length) {
							delete state.selected[items[index].id];
							x[items[index + step].id] = items[index + step];
							state.selected = x;
						} else {
							x[items[items.length - 1].id] = items[items.length - 1];
							state.selected = x;
						}
						break;
					case "right":
						if (index + 1 < items.length) {
							delete state.selected[items[index].id];
							x[items[index + 1].id] = items[index + 1];
							state.selected = x;
						}
						break;
					case "left":
						if (index - 1 >= 0) {
							delete state.selected[items[index].id];
							x[items[index - 1].id] = items[index - 1];
							state.selected = x;
						}
						break;
				}
			} else if (layout == "list") {
				switch (direction) {
					case "up":
						if (index - 1 >= 0) {
							delete state.selected[items[index].id];
							x[items[index - 1].id] = items[index - 1];
							state.selected = x;
						}
						break;
					case "down":
						if (index + 1 < items.length) {
							delete state.selected[items[index].id];
							x[items[index + 1].id] = items[index + 1];
							state.selected = x;
						}
						break;
				}
			}
		}
	},

	emptySelected(state) {
		let temp = {};
		state.selected = temp;
	},

	selectAllVuex(state, { files, folders }) {
		let items = [...files, ...folders];
		let temp = {};

		// for (let i = 0; i < items.length; i++) {
		let i = 0;
		let arrLenght = items.length;
		while (i < arrLenght) {
			temp[items[i].id] = {
				id: items[i].id,
				name: items[i].name,
				type: items[i].type,
				favorites: items[i].favorites,
				path: items[i].path,
			};
			i++;
		}
		// }
		state.selected = temp;
	},
};

export default {
	namespace: true,
	state,
	mutations,
};
