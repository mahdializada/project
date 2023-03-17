<template>
	<div>
		<v-list-item
			v-if="!menuItem.items"
			:input-value="menuItem.value"
			:to="menuItem.link"
			:exact="menuItem.exact"
			:disabled="menuItem.disabled"
			active-class="primary--text"
			link
			:class="`list-item-custom text-uppercase ${
				menuItem.link !== '/' ? 'ms-3' : ''
			} `"
		>
			<v-list-item-icon>
				<v-avatar
					v-if="menuItem.logo"
					:color="menuItem.color"
					:size="30"
					class="rounded"
				>
					<img :src="menuItem.logo" alt="" />
				</v-avatar>

				<v-icon v-else :class="{ 'grey--text': menuItem.disabled }">
					{{ menuItem.icon }}
				</v-icon>
			</v-list-item-icon>
			<v-list-item-content class="pa-0">
				<v-list-item-title
					:style="`${
						menuItem.link !== '/'
							? 'font-size: 0.75rem; font-weight: bold;'
							: ''
					}`"
				>
					{{ menuItem.key ? $tr(menuItem.key) : menuItem.text }}
				</v-list-item-title>
			</v-list-item-content>
		</v-list-item>

		<v-list-group
			v-else-if="!subgroup"
			:value="menuItem.regex ? menuItem.regex.test($route.path) : false"
			:disabled="menuItem.disabled"
			:class="{
				'has-items': true,
			}"
			:to="menuItem.link"
			link
		>
			<template v-slot:activator>
				<v-list-item-icon>
					<v-avatar v-if="menuItem.logo" :color="menuItem.color" :size="25">
						<img :src="menuItem.logo" alt="" />
					</v-avatar>
					<v-icon v-else>{{ menuItem.icon }}</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title
						class="text-uppercase ms-1"
						:style="`${
							menuItem.link !== '/'
								? 'font-size: 0.9rem; font-weight: bold;'
								: ''
						}`"
					>
						{{ menuItem.key ? $tr(menuItem.key) : menuItem.text }}
					</v-list-item-title>
				</v-list-item-content>
			</template>
			<slot></slot>
		</v-list-group>
		<v-list-group
			v-else
			:value="menuItem.regex ? menuItem.regex.test($route.path) : false"
			:disabled="menuItem.disabled"
			:sub-group="subgroup"
			:class="{
				'has-items subMenuStyle': true,
			}"
			:to="menuItem.link"
			link
		>
			<template v-slot:activator>
				<v-list-item-content>
					<v-list-item-title
						class="text-uppercase"
						:style="`${
							menuItem.link !== '/'
								? 'font-size: 0.80rem; font-weight: bold;'
								: ''
						}`"
					>
						{{ menuItem.key ? $tr(menuItem.key) : menuItem.text }}
					</v-list-item-title>
				</v-list-item-content>
				<v-list-item-icon>
					<v-avatar v-if="menuItem.logo" :color="menuItem.color" :size="25">
						<img :src="menuItem.logo" alt="" />
					</v-avatar>
					<v-icon v-else>{{ menuItem.icon }}</v-icon>
				</v-list-item-icon>
			</template>
			<slot></slot>
		</v-list-group>
	</div>
</template>

<script>
export default {
	props: {
		menuItem: {
			type: Object,
			default: () => {},
		},
		subgroup: {
			type: Boolean,
			default: false,
		},
		small: {
			type: Boolean,
			default: false,
		},
	},

	methods: {
		onRouteClicked(system) {
			this.$router.push(
				system.items
					? system.key === "settings"
						? `/systems/${system.subLink}`
						: `/systems/${system.text}`
					: system.link
			);
		},
	},
};
</script>

<style>
/* .v-navigation-drawer__content .v-list-item {
	padding: 0 4px !important;
}
.v-application-is-rtl .has-items .v-list-group__items .list-item-custom {
	padding-right: 5px !important;
	padding-left: 0 !important;
}
.has-items .v-list-group__items .list-item-custom {
	padding-left: 5px !important;
	padding-right: 0 !important;
}
.has-items.subMenuStyle .v-list-group__header {
	display: flex;
	flex-direction: row-reverse;
}
.has-setting .v-icon {
	font-size: 24px !important;
}
.v-list-group__items .list-item-custom {
	min-height: 32px !important;
}
.v-list-group__items .list-item-custom .v-list-item__icon {
	margin: 4px 0 !important;
	margin-right: 5px !important;
}
.text-uppercase {
	text-transform: uppercase;
}
.has-items .v-list-group__header {
	min-height: 32px !important;
	padding-left: 4px !important;
}
.has-items.subMenuStyle {
	margin-left: 24px;
}
.has-items .v-list-group .v-list-group__header .v-list-item__icon {
	margin-left: 0px !important;
	margin-top: 4px !important;
	margin-bottom: 4px !important;
}

.has-items .v-list-item__content {
	padding: 0 !important;
}
.has-items .v-list-group .v-list-group__header .v-list-item__title {
	padding-left: 5px !important;
}
.has-items .v-list-group .v-list-group__items .v-list-item {
	margin-left: 12px !important;
} */
</style>
