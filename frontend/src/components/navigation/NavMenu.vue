<template>
	<div>
		<!-- menu level 1 -->
		<template v-for="(level1Item, level1Index) in menu">
			<nav-menu-item
				v-if="
					isChildInScope(level1Item.items ? level1Item.items : []) ||
					level1Item.showForAll
				"
				:key="level1Index"
				:menu-item="level1Item"
				class="custom-list-item-wrapper"
			>
				<template v-if="level1Item.items">
					<!-- menu level 2 -->
					<template v-for="(level2Item, level2Index) in level1Item.items">
						<nav-menu-item
							v-if="$isInScope(level2Item.scope)"
							:key="level2Index"
							:menu-item="level2Item"
							subgroup
							small
						>
							<template v-if="level2Item.items">
								<!-- menu level 3 -->
								<template v-for="(level3Item, level3Index) in level2Item.items">
									<nav-menu-item
										v-if="$isInScope(level3Item.scope)"
										:key="level3Index"
										:menu-item="level3Item"
										small
									/>
								</template>
							</template>
						</nav-menu-item>
					</template>
				</template>
			</nav-menu-item>
		</template>
	</div>
</template>

<script>
import NavMenuItem from "./NavMenuItem";

/*
|---------------------------------------------------------------------
| Navigation Menu Component
|---------------------------------------------------------------------
|
| Multi-layer navigation menu
|
| menu: [{ text: 'Menu Levels',
|    items: [
|      { text: 'Menu Levels 2.1' },
|      { text: 'Menu Levels 2.2',
|        items: [
|          { text: 'Menu Levels 3.1' },
|          { text: 'Menu Levels 3.2' }
|        ]
|      }
|    ]
|  }]
|
*/
export default {
	components: {
		NavMenuItem,
	},
	props: {
		menu: {
			type: Array,
			default: () => [],
		},
	},
	methods: {
		isChildInScope(children) {
			for (let child of children) {
				if (this.$isInScope(child.scope)) {
					return this.$isInScope(child.scope);
				}
			}
			return false;
		},
	},
};
</script>
<style scoped></style>
