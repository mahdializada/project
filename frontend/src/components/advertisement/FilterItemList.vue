<template>
	<v-list dense class="pa-0">
		<v-list-group v-model="isActive" sub-group class="filter_list_group">
			<template v-slot:activator>
				<v-list-item-content>
					<v-list-item-title
						class="text-capitalize"
						v-text="$tr(itemTitle)"
					></v-list-item-title>
				</v-list-item-content>
			</template>
			<template v-slot:prependIcon>
				<svg
					v-if="isActive"
					xmlns="http://www.w3.org/2000/svg"
					id="Group_1271"
					data-name="Group 1271"
					width="14"
					height="14"
					viewBox="0 0 14 14"
				>
					<g
						id="Rectangle_404"
						data-name="Rectangle 404"
						fill="#fff"
						stroke="#707070"
						stroke-width="1"
					>
						<rect width="14" height="14" rx="2" stroke="none" />
						<rect x="0.5" y="0.5" width="13" height="13" rx="1.5" fill="none" />
					</g>
					<path
						id="Path_503"
						data-name="Path 503"
						d="M351.064,406h5.762"
						transform="translate(-346.945 -399)"
						fill="none"
						stroke="#707070"
						stroke-linecap="round"
						stroke-width="1"
					/>
				</svg>
				<svg
					v-else
					xmlns="http://www.w3.org/2000/svg"
					id="Group_1203"
					data-name="Group 1203"
					width="14"
					height="14"
					viewBox="0 0 14 14"
				>
					<g
						id="Rectangle_404"
						data-name="Rectangle 404"
						fill="#fff"
						stroke="#707070"
						stroke-width="1"
					>
						<rect width="14" height="14" rx="2" stroke="none" />
						<rect x="0.5" y="0.5" width="13" height="13" rx="1.5" fill="none" />
					</g>
					<path
						id="Path_503"
						data-name="Path 503"
						d="M351.064,406h5.762"
						transform="translate(-346.945 -399)"
						fill="none"
						stroke="#707070"
						stroke-linecap="round"
						stroke-width="1"
					/>
					<path
						id="Path_504"
						data-name="Path 504"
						d="M0,0H5.762"
						transform="translate(7 9.881) rotate(-90)"
						fill="none"
						stroke="#707070"
						stroke-linecap="round"
						stroke-width="1"
					/>
				</svg>
			</template>

			<v-list dense>
				<v-list-group
					v-for="item in items"
					:key="item.title"
					v-model="item.active"
					no-action
					class="ms-4"
				>
					<template v-slot:activator>
						<v-list-item-content>
							<v-list-item-title
								class="text-capitalize"
								v-text="$tr(item.title)"
							></v-list-item-title>
						</v-list-item-content>
						<v-list-item-icon
							v-if="item.showClear"
							@click.stop="() => $emit('clearItem', item.title)"
						>
							<v-btn small icon color="primary">
								<v-icon small>mdi-close</v-icon>
							</v-btn>
						</v-list-item-icon>
					</template>
					<div>
						<slot :name="item.title" :item="item" />
					</div>
				</v-list-group>
			</v-list>
		</v-list-group>
	</v-list>
</template>

<script>
export default {
	props: ["itemTitle", "items"],
	data() {
		return {
			isActive: false,
		};
	},
};
</script>
<style>
.filter_list_group .v-list-item__icon {
	display: flex;
	align-items: center;
	justify-content: center;
}
</style>
