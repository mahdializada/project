<template>
	<div class="files-container d-flex background">
		<div
			:class="`custom-transition library-companies lib-border-right ${
				$vuetify.breakpoint.width < 700
					? hideCompanies
						? 'custom-w-0'
						: 'min-w-full'
					: ''
			}`"
		>
			<LibraryCompanies @toggleCompanies="toggleCompanies" @companyChanged="fetchSelectedCompanyFiles" />
		</div>
		<div
			:class="`custom-transition children  ${
				$vuetify.breakpoint.width < 700
					? !hideCompanies
						? 'custom-w-0'
						: 'min-w-full'
					: 'w-full'
			}`"
		>
			<div
				:class="$vuetify.breakpoint.width < 700 ? 'custom-min-w-screen' : ''"
			>
				<LibraryTopTitle
					:hideCompanies="$vuetify.breakpoint.width < 700 && hideCompanies"
					@toggleCompanies="toggleCompanies"					
				/>
				
				<div class="pa-2">
					<NuxtChild
						ref="nuxtchild"
						:key="$route.fullPath"
						:currentPage="history.current_page"
						:loading="fetchingItems"
						:isCompanySelected="isCompanySelected"
					/>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import LibraryCompanies from "../../../components/files/library/LibraryCompanies.vue";
import LibraryTopTitle from "../../../components/files/library/LibraryTopTitle.vue"
export default {
		
	data() {
		return {
			hideCompanies: true,
			fetchingItems: false,
			isCompanySelected: false,
			selectedCompanyId: null,
			history: {
				current_page: {
					sortBy: 'name',
					files: [],
					folders: [],
					breadcrumb: [
						{
							to: '/files/system/drive',
							exact: false,
							text: 'drive',
							parent: true,
						},
					],
				},
			},
		};
	},
	watch: {
		'$route.params.slug'() {
			const { slug } = this.$route.params;
			if (slug) {
				if (slug in this.history) {
					const prevPage = this.history[slug];
					this.history.current_page = prevPage;
				} else {
					this.fetchChildItems(slug);
				}
			}
		},

	},
	methods: {
		toggleCompanies() {
			this.hideCompanies = !this.hideCompanies;
		},

		async fetchChildItems(slug) {
			try {
				this.fetchingItems = true;
				const response = await this.$axios.get(
					`files/systems?company_id=${this.selectedCompanyId}&sub_system_id=${slug}`
				);
				if (response.data.not_found) {
					this.history.current_page = {
						...this.history.current_page,
						folders: [],
						files: [],
					};
					this.fetchingItems = false;
					return;
				}
				const items = response.data.files;
				const breadcrumb = response.data.breadcrumb;
				const parent = response.data.parent;
				const folders = [];
				const files = [];
				for (let index = 0; index < items.length; index++) {
					const item = items[index];
					item.type == 'folder' ? folders.push(item) : files.push(item);				
				}

				this.history.current_page = {
					...this.history.current_page,
					folders,
					files,
					breadcrumb,
					parent,
					sortBy: 'name',
				};
				let currentPage = this.history.current_page;
				this.history[slug] = currentPage;
			} catch (_) {
				this.history.current_page = {
					...this.history.current_page,
					folders: [],
					files: [],
					breadcrumb: [
						{
							to: '/files/application/drive',
							exact: false,
							text: 'drive',
						},
					],
				};
			}
			this.fetchingItems = false;
		},


	 	async fetchSelectedCompanyFiles(id){
			this.selectedCompanyId = id;
			this.isCompanySelected = true;
			try {
				this.fetchingItems = true;
				const response = await this.$axios.get('files/systems?company_id=' + id);
				const items = response.data.files;
				const folders = [];
				const files = [];
				for (let index = 0; index < items.length; index++) {
					const item = items[index];
					item.type == 'folder' ? folders.push(item) : files.push(item);				
				}

				this.history.current_page = {
					...this.history.current_page,
					sortBy: 'name',
					folders,
					files,
					parent: null,
					breadcrumb: [
						{
							to: '/files/system/drive',
							exact: false,
							text: 'drive',
							parent: true,
						},
					],
				};

				this.history.parent = this.history.current_page;
			} catch (_) {}
			this.fetchingItems = false;
		},		
	},
	components: {
		LibraryTopTitle,
		LibraryCompanies,
	},
};
</script>
<style>
.library-companies {
	min-width: 240px;
	width: 240px;
}
.custom-transition {
	transition: all 0.4s;
}
.file-top-bar-title {
	font-size: 18px;
	font-weight: 600 !important;
}
.lib-border-right {
	border-right: 1px solid #ddd;
}

.lib-top-bar {
	height: 54px;
}
.custom-w-0 {
	min-width: 0 !important;
	width: 0 !important;
	overflow: hidden;
}
.custom-min-w-screen {
	min-width: 90vw;
}
</style>
