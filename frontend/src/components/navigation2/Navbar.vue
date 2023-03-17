<template>
	<div
		:class="`mx-auto px-2 pt-1 navbar ${small ? 'navbar-small' : ''}`"
		width="300"
		v-click-outside="clickOutside"
	>
		<v-list dense dark class="custom-list">
			<v-list-item-group v-model="selectedMenu" class="position-static">
				<template v-for="navItem1 in menu">
					<MenuItem
						v-if="!navItem1.items"
						:item="navItem1"
						:key="navItem1.key"
					/>
					<ManuItemWithchildren
						v-else-if="
							isChildInScope(navItem1.items ? navItem1.items : []) ||
							navItem1.showForAll
						"
						:key="navItem1.key + 'else'"
						:item="navItem1"
						:firstLevel="true"
						:small="small"
						ref="ManuItemWithchildren"
					>
						<v-list dense dark class="custom-list">
							<template v-for="navItem2 in navItem1.items">
								<MenuItem
									v-if="!navItem2.items"
									:item="navItem2"
									:key="navItem2.key"
								/>
								<ManuItemWithchildren
									v-else-if="
										isChildInScope(navItem2.items ? navItem2.items : []) ||
										navItem2.showForAll
									"
									:key="navItem2.key + 'else'"
									:item="navItem2"
									:small="small"
								>
									<v-list dense dark class="custom-list">
										<template v-for="navItem3 in navItem2.items">
											<MenuItem
												v-if="!navItem3.items"
												:item="navItem3"
												:key="navItem3.key"
											/>
										</template>
									</v-list>
								</ManuItemWithchildren>
							</template>
						</v-list>
					</ManuItemWithchildren>
				</template>
			</v-list-item-group>
		</v-list>
	</div>
</template>
<script>
import MenuItem from "./MenuItem.vue";
import ManuItemWithchildren from "./ManuItemWithchildren.vue";
import Menu from "../../configs/navigation";
export default {
	data: () => ({
		menu: Menu.menu,
		selectedMenu: [],
	}),
	props: {
		small: Boolean,
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

		clickOutside() {
			for (let i = 0; i < this.$refs.ManuItemWithchildren.length; i++) {
				let el = this.$refs.ManuItemWithchildren[i];
				if (
					el.$el.classList.contains("v-list-group--active") &&
					el.$el.classList.contains("small")
				) {
					let header = el.$el.querySelector(".v-list-group__header");
					header.click();
				}
			}
		},
	},
	components: { MenuItem, ManuItemWithchildren },
};
</script>
<style>
.navbar {
	overflow-y: auto;
	height: 85%;
}
.navbar .custom-list {
	background: transparent !important;
}
.navbar .v-list-item {
	overflow: hidden;
	border-radius: 8px !important;
}
.navbar .v-list-item.v-list-item--active {
	background: rgba(255, 255, 255, 0.2);
	color: white;
	border-radius: 6px;
	overflow: hidden;
}
.navbar.navbar-small .left-border {
	max-height: 400px;
	overflow-y: auto;
}
.navbar .left-border {
	position: relative;
	/* background-color: #104982; */
	border-radius: 6px;
	margin: 6px 0;
	padding-left: 32px;
	padding-right: 8px;
	transition: opacity 0.3s;
}
.v-application--is-rtl .navbar .left-border {
	padding-left: 8px;
	padding-right: 32px;
}
.navbar .left-border::before {
	display: block;
	content: "";
	width: 1px;
	background: rgba(255, 255, 255, 0.2);
	position: absolute;
	left: 20px;
	top: 10px;
	bottom: 16px;
}
.v-application--is-rtl .navbar .left-border::before {
	right: 20px;
	left: unset;
}
.navbar .v-list-group__items {
	margin-bottom: 0;
}
.v-application--is-rtl .left-border.small-navbar {
	margin-left: unset;
	margin-right: 74px;
}
.left-border.small-navbar {
	min-width: 200px;
	position: absolute;
	z-index: 1000;
	padding: 0 8px !important;
	margin-left: 74px;
	transform: translateY(-40%);
	margin-top: -26px;
	border-radius: 8px;
}
.left-border.small-navbar::before {
	display: none;
}
.left-border .v-list-item {
	padding: 0 10px;
}
.navbar-small .v-list-group.first-level > .v-list-group__items {
	display: block !important;
}
.v-list-group > .v-list-group__items > .left-border.small-navbar {
	opacity: 0;
	left: -1000px;
}
.v-list-group.v-list-group--active
	> .v-list-group__items
	> .left-border.small-navbar {
	opacity: 1;
	left: unset;
}
.navbar .v-list-item__icon {
	align-items: center;
	margin-left: -4px !important;
}
.v-application--is-rtl .navbar .v-list-item__icon {
	margin-left: unset !important;
	margin-right: -4px !important;
}
.navbar .v-list-item__icon svg {
	width: 18px;
	height: 18px;
	fill: currentColor;
}
.v-list-item__icon.v-list-group__header__append-icon {
	margin-left: 8px !important;
}
.v-application--is-rtl .v-list-item__icon.v-list-group__header__append-icon {
	margin-left: unset !important;
	margin-right: 8px !important;
}
.small
	.first-level
	> .v-list-item
	> .v-list-item__icon.v-list-group__header__append-icon {
	display: none !important;
}

.navbar::-webkit-scrollbar,
.navbar ::-webkit-scrollbar {
	width: 10px;
	height: 10px;
}

/* Track */
.navbar::-webkit-scrollbar-track,
.navbar ::-webkit-scrollbar-track {
	background: transparent;
}

/* Handle */
.navbar::-webkit-scrollbar-thumb,
.navbar ::-webkit-scrollbar-thumb {
	background: rgba(255, 255, 255, 0.3);
	border-radius: 5px;
	position: absolute;
}

/* Handle on hover */
.navbar::-webkit-scrollbar-thumb:hover,
.navbar ::-webkit-scrollbar-thumb:hover {
	background: rgba(255, 255, 255, 0.3);
}
.v-list-group:has(a.v-item--active.v-list-item--active.v-list-item.v-list-item--link.theme--dark)
	> .v-list-group__heade
	r {
	background: rgba(255, 255, 255, 0.2);
}
.v-list-group--active.first-level
	> .v-list-group__header
	> .v-list-group__header__append-icon
	.v-icon {
	transform: rotate(-135deg);
}
</style>
