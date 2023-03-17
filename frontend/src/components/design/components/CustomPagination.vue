<template>
	<div class="pagination-wrapper d-flex" v-if="Math.floor(pageCount) > 1">
		<div :class="`first first-visible`" @click="first" v-if="current != 1">
			<v-icon x-small>fa-chevron-{{ $vuetify.rtl ? "right" : "left" }}</v-icon>
			<v-icon x-small>fa-chevron-{{ $vuetify.rtl ? "right" : "left" }}</v-icon>
		</div>
		<div class="prev" @click="prev" v-if="current != 1">
			<v-icon x-small>fa-chevron-{{ $vuetify.rtl ? "right" : "left" }}</v-icon>
		</div>
		<template v-for="index in Math.floor(pageCount / 2)">
			<div
				:class="`number ${index == current ? 'active' : ''} ${
					(index < maxShow / 2 && index == current - 1) ||
					index == current ||
					(index == current - 1 && current < Math.floor(pageCount / 2)) ||
					index == current + 1 ||
					(index < maxShow / 2 + 2 && current == 1) ||
					(current > Math.floor(pageCount / 2) && index <= maxShow / 2 - 1) ||
					(current == Math.floor(pageCount / 2) - 1 && index == current - 1) ||
					(current == Math.floor(pageCount / 2) && index == current - 1) ||
					(current == Math.floor(pageCount / 2) && index == current - 2)
						? 'd-flex'
						: 'd-none'
				}`"
				:key="index"
				@click="currentTo(index)"
			>
				<span>{{ index }}</span>
			</div>
		</template>
		<span class="dots px-1 pt-1" v-if="pageCount > maxShow">
			<span>....</span>
		</span>
		<template v-for="index in Math.ceil(pageCount / 2)">
			<div
				:class="`number ${
					index + Math.floor(pageCount / 2) == current ? 'active' : ''
				} ${
					(index + Math.ceil(pageCount / 2) > pageCount - (maxShow / 2 - 1) &&
						index + Math.floor(pageCount / 2) == current + 1) ||
					index + Math.floor(pageCount / 2) == current ||
					index + Math.floor(pageCount / 2) == current - 1 ||
					(index + Math.floor(pageCount / 2) == current + 1 &&
						current > Math.floor(pageCount / 2)) ||
					(index + Math.floor(pageCount / 2) > pageCount - (maxShow / 2 + 1) &&
						current == pageCount) ||
					(current <= Math.floor(pageCount / 2) &&
						index + Math.floor(pageCount / 2) >
							pageCount - (maxShow / 2 - 1)) ||
					(pageCount % 2 != 0 &&
						current == Math.ceil(pageCount / 2) &&
						index + Math.ceil(pageCount / 2) == current + 3) ||
					(current == Math.ceil(pageCount / 2) + 1 &&
						index + Math.ceil(pageCount / 2) == current + 2) ||
					(pageCount % 2 == 0 &&
						current == Math.ceil(pageCount / 2) - 1 &&
						index + Math.ceil(pageCount / 2) == current + 1)
						? 'd-flex'
						: 'd-none'
				}
        `"
				:key="index + pageCount + 2"
				@click="currentTo(index + Math.floor(pageCount / 2))"
			>
				<span>{{ index + Math.floor(pageCount / 2) }}</span>
			</div>
		</template>

		<div class="next" @click="next" v-if="current != pageCount">
			<v-icon x-small>fa-chevron-{{ $vuetify.rtl ? "left" : "right" }}</v-icon>
		</div>
		<div class="last last-visible" @click="last" v-if="current != pageCount">
			<v-icon x-small>fa-chevron-{{ $vuetify.rtl ? "left" : "right" }}</v-icon>
			<v-icon x-small>fa-chevron-{{ $vuetify.rtl ? "left" : "right" }}</v-icon>
		</div>
	</div>
	<div class="pagination-wrapper d-flex pagination-1num" v-else>
		<div :class="`number active first-visible last-visible`">1</div>
	</div>
</template>
<script>
export default {
	props: {
		pageCount: {
			type: Number,
			required: true,
		},
		currentIndex: {
			type: Number,
			default: 1,
		},
	},
	watch: {
		currentIndex: function (index) {
			this.current = index;
		},
	},
	data() {
		return {
			maxShow: 8,
			current: 1,
		};
	},

	created() {
		this.$root.$on("resetPageNumber", () => {
			if (this.current != 1) this.current = 1;
		});
	},
	methods: {
		prev() {
			if (this.current > 1) {
				this.current = this.current - 1;
				this.$emit("paginate", this.current);
			}
		},
		next() {
			if (this.current < this.pageCount) {
				this.current = this.current + 1;
				this.$emit("paginate", this.current);
			}
		},
		first() {
			this.current = 1;
			this.$emit("paginate", this.current);
		},
		last() {
			this.current = this.pageCount;
			this.$emit("paginate", this.current);
		},
		async currentTo(i) {
			this.current = i;
			await this.$emit("paginate", this.current);
		},
	},
};
</script>
<style scoped>
.pagination-wrapper > div {
	height: 36px;
	min-width: 36px;
	border: 1px solid #ccc;
	text-align: center;
	display: flex;
	justify-content: center;
	align-items: center;
	/* font-size: 16px; */
	transition: all 0.3s;
	cursor: pointer;
	font-weight: 500;
}
.pagination-wrapper > div:hover,
.pagination-wrapper > div.active {
	background: var(--v-primary-base);
	border-color: var(--v-primary-base);
}
.pagination-wrapper > div:hover,
.pagination-wrapper > div:hover .v-icon,
.pagination-wrapper > div.active {
	color: white !important;
}
.pagination-wrapper > div:first-child {
	border-radius: 4px 0 0 4px;
}
.pagination-wrapper > div:last-child {
	border-radius: 0 4px 4px 0;
}
.pagination-1num.pagination-wrapper > div {
	border-radius: 4px 4px 4px 4px;
}
.v-application--is-rtl .pagination-wrapper > div:last-child {
	border-radius: 4px 0 0 4px;
}
.v-application--is-rtl .pagination-wrapper > div:first-child {
	border-radius: 0 4px 4px 0;
}
.pagination-wrapper .dots {
	font-size: 20px;
	letter-spacing: 2px;
}
</style>
