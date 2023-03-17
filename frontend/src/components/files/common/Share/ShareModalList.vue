<template>
	<v-list class="surface" dense v-if="items.length > 0">
		<v-list-item-group v-model="selected" v-bind="$attrs" v-on="$listeners" multiple active-class="">
			<v-list-item v-for="(item, i) in items" :key="i" :value="item.id">
				<template v-slot:default="{ active }">
					<v-list-item-action class="">
						<v-checkbox :input-value="active" @change="(event) => checkboxChanged(event, item)"></v-checkbox>
					</v-list-item-action>
					<div class="w-full d-flex cursor-pointer" @click.stop="clicked(item)">
						<v-list-item-avatar size="30" color="blue-grey darken-3" class="ms-3 mx-2">
							<v-img
								v-if="!noImage"
								width="30"
								:alt="`${item[titleProperty]} avatar`"
								:src="item[imageProperty]"
							></v-img>
							<span v-else class="white--text text-h6 font-weight-medium pa-1">
								{{ item[titleProperty] ? item[titleProperty][0].toUpperCase() : "" }}
							</span>
						</v-list-item-avatar>

						<v-list-item-content>
							<v-list-item-title>
								<span
									:title="`${type == 'user' ? `${item.firstname} ${item.lastname}` : item[titleProperty]} ${
										type == 'department' || type == 'user' ? getNames(item.companies) : ''
									}`"
								>
									{{ type == "user" ? `${item.firstname} ${item.lastname}` : item[titleProperty] }}
									{{ !hideMeta && (type == "department" || type == "user") ? getNames(item.companies) : "" }}
									{{ type == "team" || type == "role" ? getNames(item.departments) : "" }}
								</span>
							</v-list-item-title>
						</v-list-item-content>
						<v-list-item-icon class="primary--text" v-if="showChevron">
							<v-icon color="primary" size="28"> mdi-chevron-right </v-icon>
						</v-list-item-icon>
					</div>
				</template>
			</v-list-item>
		</v-list-item-group>
	</v-list>
	<div v-else class="w-full d-flex align-center justify-center" style="height: 220px">
		<div class="text-center">
			<div class="mb-1 text--disabled">
				<svg fill="currentColor" width="80" viewBox="0 0 700 400">
					<g>
						<path
							d="m431.67 349.24c27.812-32.148 36.875-76.465 23.91-116.95-12.961-40.488-46.078-71.297-87.395-81.309-41.312-10.012-84.863 2.2188-114.92 32.277-30.059 30.059-42.289 73.609-32.277 114.92 10.012 41.316 40.82 74.434 81.309 87.395 40.484 12.965 84.801 3.9023 116.95-23.91l50.75 50.75 12.191-12.191zm-17.965-5.832h-0.003906c-19.598 19.578-46.168 30.574-73.871 30.566-27.703-0.011718-54.27-11.02-73.855-30.613-19.586-19.59-30.59-46.16-30.59-73.863s11.004-54.273 30.59-73.863c19.586-19.594 46.152-30.602 73.855-30.613 27.703-0.007813 54.273 10.988 73.871 30.566 19.559 19.652 30.52 46.262 30.473 73.988-0.042969 27.723-11.086 54.297-30.707 73.887z"
						/>
						<path
							d="m376.13 220.79-36.574 36.574-36.574-36.574-12.25 12.191 36.633 36.574-36.633 36.574 12.25 12.191 36.574-36.574 36.574 36.574 12.191-12.191-36.574-36.574 36.574-36.574z"
						/>
					</g>
				</svg>
			</div>
			<div style="max-width: 250px">
				<p class="ma-0 text--secondary" style="font-size: 14px">
					{{ $tr("no_items_found") }}
				</p>
			</div>
		</div>
	</div>
</template>
<script>
export default {
	props: {
		items: {
			type: Array,
		},
		imageProperty: {
			type: String,
			default: "image",
		},
		titleProperty: {
			type: String,
			default: "title",
		},
		showChevron: {
			type: Boolean,
			default: false,
		},
		hideMeta: {
			type: Boolean,
			default: false,
		},
		noImage: {
			type: Boolean,
			default: false,
		},
		type: {
			type: String,
		},
		value: {
			type: Array,
		},
	},
	methods: {
		clicked(id) {
			this.$emit("itemClick", id);
		},
		getNames(companies) {
			if (companies) {
				let str = "";
				let length = companies.length;
				for (let i = 0; i < length; i++) {
					str += companies[i].name + (i !== length - 1 ? ", " : "");
				}
				return "(" + str + ")";
			}
			return;
		},
		checkboxChanged(item, value) {
			this.$emit("checkboxChanged", item, value, this.type + "_selected_users");
		},
	},
	computed: {
		selected: {
			get() {
				return this.value;
			},
			set(value) {
				this.$emit("input", value);
			},
		},
	},
};
</script>
