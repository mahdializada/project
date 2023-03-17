<template>
	<div
		:class="`item-card background cursor-pointer ${selected ? 'selected' : ''}`"
		@click="$emit('click')"
		@dblclick="$emit('dblclick')"
	>
		<div class="item-icon d-flex align-center justify-center">
			<v-avatar color="var(--gray)" size="46">
				<v-avatar size="42">
					<img src="https://cdn.vuetifyjs.com/images/john.jpg" alt="John" />
				</v-avatar>
			</v-avatar>
		</div>
		<div class="item-name pa-1 d-flex align-center justify-center text-center">
			{{ item.firstname }} {{ item.lastname }}
		</div>
		<div class="item-btn">
			<div class="item-btn-icon">
				<v-icon size="14" dark>mdi-check-bold</v-icon>
			</div>
			{{ selected ? $tr("assigned") : $tr("assign") }}
		</div>
	</div>
</template>
<script>
export default {
	props: {
		selected: Boolean,
		item: Object,
		loading: Boolean,
	},
};
</script>
<style scoped>
.item-card {
	position: relative;
	width: 110px;
	height: 142px;
	margin: 6px !important;
	border-radius: 8px;
	border: 1px solid transparent;
	transition: all 0.4s;
}
.item-card:hover {
	box-shadow: 0 0 20px rgba(0, 0, 0, 0.16);
}
.item-icon {
	height: 68px;
}
.item-name {
	height: 30px;
	font-size: 12px;
}
.item-card.selected {
	border: 1px solid #2e7be680 !important;
	box-shadow: 0 0 10px #2e7be633;
}

.item-btn {
	display: flex;
	align-items: center;
	justify-content: center;
	text-align: center;
	margin: 8px auto;
	height: 24px;
	width: 90px;
	font-size: 10px;
	font-weight: 500;
	border-radius: 12px;
	border: 1px solid var(--v-primary-base);
	color: var(--v-primary-base);
	position: relative;
	transition: all 0.3s;
}
.selected .item-btn {
	color: #fff;
	background: var(--v-primary-base);
	padding-left: 20px;
}
.item-btn-icon {
	height: 18px;
	width: 18px;
	display: none;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	background: rgba(255, 255, 255, 0.3);
	position: absolute;
	top: 2px;
	left: 2px;
	transition: all 0.3s;
}
.selected .item-btn-icon {
	display: flex;
}
.v-application--is-rtl .selected .item-btn {
	padding-left: unset;
	padding-right: 20px;
}
.v-application--is-rtl .item-btn-icon {
	left: unset;
	right: 2px;
}
</style>
