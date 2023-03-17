<template>
	<div
		class="d-flex flex-column justify-center align-center stepper-sidebar-bottom pa-3"
	>
		<div
			class="row ma-0 mb-1 w-full d-flex justify-space-between align-center px-1"
		>
			<div class="btn">Esc</div>
			<div class="text">{{ $tr("cancel") }}</div>
			<div class="btn">Enter</div>
			<div class="text">{{ $tr("confirm") }}</div>
		</div>
		<div class="row ma-0 w-full d-flex justify-space-between align-center px-1">
			<div class="btn">
				Tab
				<svg
					xmlns="http://www.w3.org/2000/svg"
					height="10px"
					fill="currentColor"
					viewBox="0 0 320 512"
					class="ms-1"
					:style="`transform: rotate(${$vuetify.rtl ? '270deg' : '90deg'})`"
				>
					<path
						d="M310.6 182.6c-12.51 12.51-32.76 12.49-45.25 0L192 109.3V480c0 17.69-14.31 32-32 32s-32-14.31-32-32V109.3L54.63 182.6c-12.5 12.5-32.75 12.5-45.25 0s-12.5-32.75 0-45.25l128-128c12.5-12.5 32.75-12.5 45.25 0l128 128C323.1 149.9 323.1 170.1 310.6 182.6z"
					/>
				</svg>
			</div>
			<div class="text">{{ $tr("or") }}</div>
			<div class="btn icon">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					height="10px"
					fill="currentColor"
					viewBox="0 0 320 512"
				>
					<path
						d="M310.6 182.6c-12.51 12.51-32.76 12.49-45.25 0L192 109.3V480c0 17.69-14.31 32-32 32s-32-14.31-32-32V109.3L54.63 182.6c-12.5 12.5-32.75 12.5-45.25 0s-12.5-32.75 0-45.25l128-128c12.5-12.5 32.75-12.5 45.25 0l128 128C323.1 149.9 323.1 170.1 310.6 182.6z"
					/>
				</svg>
			</div>
			<div class="btn icon">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					height="10px"
					fill="currentColor"
					viewBox="0 0 320 512"
					style="transform: scaleY(-1)"
				>
					<path
						d="M310.6 182.6c-12.51 12.51-32.76 12.49-45.25 0L192 109.3V480c0 17.69-14.31 32-32 32s-32-14.31-32-32V109.3L54.63 182.6c-12.5 12.5-32.75 12.5-45.25 0s-12.5-32.75 0-45.25l128-128c12.5-12.5 32.75-12.5 45.25 0l128 128C323.1 149.9 323.1 170.1 310.6 182.6z"
					/>
				</svg>
			</div>
			<div class="text">{{ $tr("to_navigate") }}</div>
		</div>
	</div>
</template>
<script>
export default {};
</script>
<style scoped>
.stepper-sidebar-bottom {
	height: 120px;
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
}
.stepper-sidebar-bottom .btn {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 24px;
	padding: 2px 10px;
	font-size: 11px;
	border-radius: 4px;
	border: 1px solid var(--gray);
	color: #ccc;
}
.stepper-sidebar-bottom .btn.icon {
	padding: 2px 8px;
}
.stepper-sidebar-bottom .text {
	font-size: 12px;
	color: #777;
}
</style>
