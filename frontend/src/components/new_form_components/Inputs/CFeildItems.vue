<template>
	<div class="items d-flex flex-column flex-md-row">
		<div class="w-full pe-md-1">
			<InputItem
				v-for="(item, i) in getOdd(items)"
				:key="i"
				:label="item"
				:icon="icon"
				@removeItem="$emit('removeItem', 2 * i)"
			/>
		</div>

		<div class="w-full ps-md-1">
			<InputItem
				v-for="(item, i) in getEven(items)"
				:key="i"
				:label="item"
				:icon="icon"
				@removeItem="$emit('removeItem', 2 * i + 1)"
			/>
		</div>
	</div>
</template>
<script>
import InputItem from "../components/InputItem.vue";
export default {
	props: {
		items: Array,
		icon: String,
	},
	methods: {
		getOdd(arr) {
			if (arr?.length > 0) {
				return arr.filter((item, i) => i % 2 == 0);
			}
		},
		getEven(arr) {
			if (arr?.length > 0) {
				return arr.filter((item, i) => i % 2 == 1);
			}
		},
	},
	components: { InputItem },
};
</script>
