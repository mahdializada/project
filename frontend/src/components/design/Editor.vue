<template>
	<div>
		<p class="mb-1 custom-input-title" v-if="label">{{ $tr(label) }}</p>
		<client-only>
			<vue-editor
				v-bind="$attrs"
				v-on="$listeners"
				:class="`${focus ? 'focus' : ''} ${rules.length > 0 ? 'hasError' : ''}`"
				@focus="toggleFocus"
				@blur="toggleFocus"
				@input="onChange"
				:placeholder="!hidePlaceholder ? $tr('write_something') : ''"
				:editorOptions="editorOptions"
				:editorToolbar="editorToolbar"
			>
			</vue-editor>
		</client-only>
		<div v-if="showDetails" class="v-text-field__details mt-1">
			<div class="v-messages theme--light error--text" role="alert">
				<div class="v-messages__wrapper d-flex justify-space-between">
					<div class="v-messages__message">
						{{ rules[0] }}
					</div>
					<div class="v-messages__message" v-if="counter">
						{{ counter }}
					</div>
				</div>
			</div>
		</div>
		<style>
			.ql-picker.ql-color-picker svg {
				height: 18px !important;
				width: 18px !important;
			}
		</style>
	</div>
</template>
<script>
export default {
	props: {
		rules: {
			type: Array,
			default: () => [],
		},
		showDetails: {
			type: Boolean,
			default: true,
		},
		label: {
			type: String,
		},
		hidePlaceholder: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			focus: false,
			content: "<p>Some initial content</p>",
			editorOptions: {},
			counter: null,

			editorToolbar: [
				[
					{
						header: [false, 1, 2, 3, 4, 5, 6],
					},
				],
				["bold", "italic", "underline", "strike"], // toggled buttons
				[
					{
						align: "",
					},
					{
						align: "center",
					},
					{
						align: "right",
					},
					{
						align: "justify",
					},
				],
				[],
				[
					{
						list: "ordered",
					},
					{
						list: "bullet",
					},
					{
						list: "check",
					},
				],
				[
					{
						indent: "-1",
					},
					{
						indent: "+1",
					},
				], // outdent/indent
				[
					{
						color: [],
					},
					{
						background: [],
					},
				], // dropdown with defaults from theme
				["link"],
				[], // remove formatting button
			],
		};
	},

	methods: {
		onChange(value) {
			if (value) {
				let div = document.createElement("div");
				div.innerHTML = value;
				let actaulText = div.innerText;
				this.counter = actaulText.trim().length;
				return;
			}
			this.counter = null;
		},
		toggleFocus() {
			this.focus = !this.focus;
		},
	},
};
</script>
<style lang="scss">
.ql-container {
	font-family: $body-font-family;
}
</style>
<style>
.ql-container.ql-snow {
	border: 2px solid var(--input-border-color) !important;
	border-radius: 0 0 4px 4px !important;
}

.ql-container .ql-editor {
	max-height: 200px !important;
}

.ql-toolbar.ql-snow {
	border: 2px solid var(--input-border-color) !important;
	border-radius: 4px 4px 0 0 !important;
	border-bottom: 0 !important;
	padding: 8px 0 !important;
}

.focus .ql-container.ql-snow {
	border-color: var(--v-primary-base) !important;
}

.hasError .ql-container.ql-snow {
	border-color: var(--v-error-base) !important;
}
.ql-snow .ql-toolbar button svg,
.quillWrapper .ql-snow.ql-toolbar button svg {
	height: 18px !important;
	width: 18px !important;
}
.ql-picker.ql-color-picker svg {
	height: 18px !important;
	width: 18px !important;
}
.quillWrapper .ql-snow.ql-toolbar .ql-formats {
	margin-bottom: 0 !important;
}
.ql-picker:not(.ql-background) {
	top: 0 !important;
}
</style>
