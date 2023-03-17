<template>
	<div
		v-if="no_data"
		class="items-card Scroll pa-1 my-3 bg-custom-gray d-flex flex-wrap align-center justify-center"
		:style="`height:${height}`"
	>
		{{ $tr("no_data") }}
	</div>
	<div
		v-else-if="loading"
		class="items-card Scroll pa-1 my-3 bg-custom-gray d-flex flex-wrap justify-center"
		:style="`height:${height}`"
	>
		<ItemCard v-for="i in 21" :key="i" :selected="false" loading />
	</div>
	<div
		v-else
		class="items-card Scroll pa-1 my-3 bg-custom-gray"
		:style="`height:${height}`"
	>
		<div class="w-full d-flex flex-wrap">
			<template v-for="(item, i) in items">
				<ItemCard
					:key="i"
					:item="item"
					v-if="item.type !== 'user'"
					:selected="
						multi
							? value
								? value.includes(item[itemValue])
								: false
							: value == item[itemValue]
					"
					@click="handleClick(item)"
					:nameLogo="nameLogo"
					:hasLogo="hasLogo"
					:hasImg="hasImg"
					:customIcon="customIcon"
					@dblclick="$emit('category_doubleClicked', item)"
				/>
				<UserCard
					:key="i + 1"
					:item="item"
					v-else-if="item.type == 'user'"
					:selected="
						multi
							? value
								? value.includes(item[itemValue])
								: false
							: value == item[itemValue]
					"
					@click="handleClick(item)"
					@dblclick="$emit('category_doubleClicked', item)"
				/>
			</template>
		</div>
	</div>
</template>
<script>
import ItemCard from "../components/ItemCard.vue";
import UserCard from "../components/UserCard.vue";

export default {
	props: {
		value: null,
		multi: Boolean,
		items: Array,
		loading: Boolean,
		no_data: Boolean,
		hasLogo: Boolean,
		hasImg: Boolean,
		hasFavorite: Boolean,
		nameLogo: String,
		height: {
			type: String,
			default: "382px",
		},
		itemValue: {
			type: String,
			default: "id",
		},
		customIcon: {
			type: String,
			default: "",
		},
	},
	components: {
		ItemCard,
		UserCard,
	},
	data() {
		return {
			selected: [],
		};
	},
	methods: {
		handleClick(item) {
			if (item.type == "category" || item.type == "sub_category") {
				this.$emit("category_clicked", item);
			} else {
				if (this.multi) {
					if (this.value ? this.value.includes(item[this.itemValue]) : false) {
						this.$emit(
							"input",
							this.value.filter((id) => id != item[this.itemValue])
						);
					} else {
						this.$emit("input", [...this.value, item[this.itemValue]]);
					}
				} else {
					if (this.value == item[this.itemValue]) {
						this.$emit("input", "");
					} else {
						this.$emit("input", item[this.itemValue]);
					}
				}
			}
		},
	},
};
</script>
<style scoped>
.items-card {
	border-radius: 12px;
	overflow: auto;
}
</style>
