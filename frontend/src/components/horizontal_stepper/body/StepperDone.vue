<template>
	<div class="w-full h-full d-flex flex-column align-center justify-center">
		<lottie-player
			src="/assets/done_animation.json"
			background="transparent"
			speed="1"
			loop
			autoplay
			style="width: 450px; height: 450px"
		>
		</lottie-player>
		<div class="friqiBase--text" style="font-size: 30px; font-weight: 600">
			{{ $tr("successfully_inserted") }}
		</div>
	</div>
</template>
<script>
export default {};
</script>
