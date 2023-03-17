<template>
	<div class="connection__container">
		<CTitle :text="`choose_connection_type`" />
		<div class="mb-3 d-flex">
			<ItemCard
				v-for="(item, index) in items"
				:key="index"
				:item="item"
				:selected="item.type == form.attribute_section.$model"
				class="custom__item__card"
				@click="handleClick(item)"
			/>
		</div>
	</div>
</template>
<script>
import ItemCard from '../../new_form_components/components/ItemCard.vue';
import CTitle from '../../new_form_components/Inputs/CTitle.vue';
import GlobalRules from "~/helpers/vuelidate";

export default {
    props: {
        form: Object,
    },
    data() {
        return {
            validateRule: GlobalRules.validate,
            items: [
                {
                    type: "value_select",
                    name: this.$tr("value_select"),
                },
                {
                    type: "value_input",
                    name: this.$tr("value_input"),
                },
            ],
        };
    },
    methods: {
        handleClick(item) {
            this.form.attribute_section.$model = item.type;
        },
        validate() {
            this.form.attribute_section.$touch();
            return !this.form.attribute_section.$invalid;
        },
    },
    components: { CTitle, ItemCard }
};
</script>

<style scoped lang="scss">
.connection__container {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 100%;
}
.custom__item__card {
	width: 220px;
	height: 160px;
}
.custom__item__card .item-name {
	font-size: 15px !important;
	font-weight: 600 !important;
}
</style>
