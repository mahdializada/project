<template>
	<div class="w-full h-full d-flex flex-column align-center justify-center">
		<lottie-player
			src="/assets/done_animation.json"
			background="transparent"
			speed="1"
			loop
			autoplay
			style="width: 300px; height: 300px"
		>
		</lottie-player>
		<div class="friqiBase--text" style="font-size: 25px; font-weight: 500">
			{{ $tr("successfully_inserted") }}
		</div>
	</div>
</template>
<script>
export default {};
</script>
