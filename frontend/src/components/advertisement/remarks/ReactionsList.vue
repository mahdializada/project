<template>
	<div class="reaction-list d-flex align-center">
		<div
			:class="`single-reaction ${reaction == react ? 'selected' : ''}`"
			v-for="(reaction, i) in reactions"
			:key="i"
			@click="
				() => {
					$emit('setReact', reaction);
				}
			"
		>
			<ReactionIcons width="22" :emoji="reaction" />
		</div>
	</div>
</template>
<script>
import ReactionIcons from "./ReactionIcons.vue";
export default {
	props: {
		react: String | null,
	},
	data() {
		return {
			reactions: ["like", "wow", "natural", "sad", "love"],
		};
	},
	components: { ReactionIcons },
};
</script>
<style scoped>
.reaction-list {
	padding: 4px;
	height: 34px;
	position: absolute;
	top: -40px;
	background: var(--v-background-base);
	border-radius: 18px;
	left: 0;
	transform: translateX(-37%);
	box-shadow: 0 5px 20px rgb(0 0 0 / 20%);
}
.single-reaction {
	cursor: pointer;
	height: 30px;
	width: 30px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: all 0.3s;
}
.single-reaction:hover,
.single-reaction.selected {
	background: #efefef;
}
</style>
