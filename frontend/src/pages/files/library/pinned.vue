<template>
	<div>
		<h1>I am the parent view</h1>
		<nav>
			<ul>
				<li>
					<NuxtLink :to="`/files/personal/files/kdkdk`">
						link : {{ $route.params.pathMatch }}
					</NuxtLink>
				</li>
			</ul>
		</nav>
		<NuxtChild :test="test" />
	</div>
</template>

<script>
export default {
	validate({ params }) {
		if (params.pathMatch == undefined || params.pathMatch == "") {
			return false;
		} else {
			return true;
		}
	},
	data() {
		return {
			test: Math.random() * 100000,
		};
	},
	created() {
		this.test = Math.random() * 100000;
	},
};
</script>
