<template>
	<div class="pagination">
		<div class="page-item pagination-prev cursor-pointer" @click="prev">
			<v-icon color="primary" size="26">mdi-menu-up</v-icon>
		</div>
		<div v-if="pages <= 7">
			<div
				v-for="i in pages"
				:key="i"
				:class="`page-item pagination-page ${value == i ? 'active' : ''}`"
				@click="changeTo(i)"
			>
				{{ i }}
			</div>
		</div>
		<div v-else>
			<div
				v-if="value > 2"
				class="page-item pagination-page"
				@click="changeTo(1)"
			>
				1
			</div>
			<div v-if="value > 2" class="page-item pagination-page">●</div>
			<div v-if="value >= pages - 1" class="page-item pagination-page">●</div>
			<div v-if="value >= pages - 1" class="page-item pagination-page">●</div>
			<div
				v-if="value == pages"
				class="page-item pagination-page"
				@click="changeTo(value - 2)"
			>
				{{ value - 2 }}
			</div>
			<div
				v-if="value > 1"
				class="page-item pagination-page"
				@click="changeTo(value - 1)"
			>
				{{ value - 1 }}
			</div>
			<div class="page-item pagination-page active" @click="changeTo(page)">
				{{ value }}
			</div>
			<div
				v-if="value < pages"
				class="page-item pagination-page"
				@click="changeTo(value + 1)"
			>
				{{ value + 1 }}
			</div>
			<div
				v-if="value < 2"
				class="page-item pagination-page"
				@click="changeTo(value + 2)"
			>
				{{ value + 2 }}
			</div>
			<div v-if="value < pages - 1" class="page-item pagination-page">●</div>
			<div v-if="value <= 2" class="page-item pagination-page">●</div>
			<div v-if="value <= 2" class="page-item pagination-page">●</div>
			<div
				v-if="value < pages - 1"
				class="page-item pagination-page"
				@click="changeTo(pages)"
			>
				{{ pages }}
			</div>
		</div>
		<!-- <div class="pagination-page active">100</div> -->
		<div class="page-item pagination-next cursor-pointer" @click="next">
			<v-icon color="primary" size="26">mdi-menu-down</v-icon>
		</div>
	</div>
</template>
<script>
export default {
	props: {
		pages: Number,
		value: Number,
	},

	methods: {
		prev() {
			if (this.value > 1) {
				this.$emit("input", this.value - 1);
				// this.page--;
			}
		},
		next() {
			if (this.value < this.pages) {
				this.$emit("input", this.value + 1);
				// this.page++;
			}
		},
		changeTo(page) {
			this.$emit("input", page);
			// this.page = page;
		},
	},
};
</script>
<style scoped>
.pagination {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	right: 3px;
}
.page-item {
	text-align: center;
	height: 18px;
	border-radius: 9px;
	display: flex;
	justify-content: center;
	align-items: center;
	margin: 4px 0;
	font-size: 12px;
	color: #aaa;
	cursor: pointer;
}
.pagination-page.active {
	color: var(--v-background-base);
	background: var(--v-primary-base);
}
</style>
