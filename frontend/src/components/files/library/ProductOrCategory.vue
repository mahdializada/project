<template>
	<div
		class="product-category__container elevation-1"
		:class="{ product_container: $attrs.product }"
	>
		<div class="product-category__top">
			<svg
				v-if="$attrs.product"
				xmlns="http://www.w3.org/2000/svg"
				id="box_1_"
				data-name="box (1)"
				width="40"
				height="40"
				viewBox="0 0 39.287 39.279"
			>
				<path
					style="fill: var(--v-primary-lighten2)"
					id="Path_58"
					fill="currentColor"
					data-name="Path 58"
					d="M31.1.052H8.183A8.183,8.183,0,0,0,0,8.235H0a4.91,4.91,0,0,0,4.91,4.91H34.369A4.861,4.861,0,0,0,39.279,8.6,8.191,8.191,0,0,0,31.453.06Q31.274.052,31.1.052Z"
					transform="translate(0 -0.052)"
				/>
				<path
					style="fill: var(--v-primary-base)"
					id="Path_59"
					data-name="Path 59"
					d="M55.7,213.385H22.971a1.637,1.637,0,0,0-1.637,1.637v13.093a8.193,8.193,0,0,0,8.183,8.183h19.64a8.193,8.193,0,0,0,8.183-8.183V215.022A1.637,1.637,0,0,0,55.7,213.385Zm-11.456,8.183h-9.82a1.637,1.637,0,0,1,0-3.273h9.82a1.637,1.637,0,0,1,0,3.273Z"
					transform="translate(-19.697 -197.019)"
				/>
			</svg>

			<svg
				v-else
				xmlns="http://www.w3.org/2000/svg"
				id="apps-add"
				width="40"
				height="40"
				viewBox="0 0 39.234 39.234"
			>
				<path
					style="fill: var(--v-primary-base)"
					id="Path_60"
					data-name="Path 60"
					d="M6.539,0h4.9a6.539,6.539,0,0,1,6.539,6.539v4.9a6.539,6.539,0,0,1-6.539,6.539h-4.9A6.539,6.539,0,0,1,0,11.443v-4.9A6.539,6.539,0,0,1,6.539,0Z"
				/>
				<path
					style="fill: var(--v-primary-base)"
					data-name="Path 61"
					d="M6.539,277.333h4.9a6.539,6.539,0,0,1,6.539,6.539v4.9a6.539,6.539,0,0,1-6.539,6.539h-4.9A6.539,6.539,0,0,1,0,288.776v-4.9A6.539,6.539,0,0,1,6.539,277.333Z"
					transform="translate(0 -256.081)"
				/>
				<path
					style="fill: var(--v-primary-base)"
					id="Path_62"
					data-name="Path 62"
					d="M283.873,277.333h4.9a6.539,6.539,0,0,1,6.539,6.539v4.9a6.539,6.539,0,0,1-6.539,6.539h-4.9a6.539,6.539,0,0,1-6.539-6.539v-4.9A6.539,6.539,0,0,1,283.873,277.333Z"
					transform="translate(-256.082 -256.081)"
				/>
				<path
					style="fill: var(--v-primary-lighten2)"
					data-name="Path 63"
					d="M278.969,31.142h4.9v4.9a1.635,1.635,0,0,0,3.269,0v-4.9h4.9a1.635,1.635,0,0,0,0-3.269h-4.9v-4.9a1.635,1.635,0,0,0-3.269,0v4.9h-4.9a1.635,1.635,0,0,0,0,3.269Z"
					transform="translate(-256.082 -19.699)"
				/>
			</svg>
		</div>
		<div class="product-category__bottom">
			<div class="name">{{ $attrs.name }}</div>
			<div class="code">{{ $attrs.code }}</div>
		</div>
	</div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
.product-category__container:hover {
	transform: scale(1.05);
	.product-category__bottom {
		.name {
			color: var(--v-primary-base);
		}
		.code {
			font-size: 12px;
		}
	}
}
.product_container {
	.product-category__top {
		background-color: unset !important;
	}
}
.product-category__container {
	width: 140px;
	height: 140px;
	border-radius: 12px;
	margin: 8px;
	cursor: pointer;
	transition: all 0.3s;
	transform: scale(1);

	.product-category__top {
		text-align: center;
		background-color: #71717721;

		padding: 16px;
		border-radius: 12px;
		path#primary-one {
			fill: var(--v-primary-base);
		}
		path#info-one {
			fill: var(--v-info-base) !important;
		}
	}
	.product-category__bottom {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 0px 16px;
		margin-top: 5px;
		.name {
			font-weight: 500;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			width: 150px;
			text-align: center;
			transition: all 0.3s;
			text-transform: capitalize;
		}
		.code {
			color: #aaa;
			text-transform: uppercase;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			width: 150px;
			text-align: center;
			transition: all 0.3s;
			font-size: 12px;
		}
	}
}
</style>
