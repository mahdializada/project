<template>
  <div>
    <CTitle :text="`inventory`" />
    <MultiInputItem
      :items="form.inventroy.$each.$iter"
      @addMore="addMore"
      @removeItem="removeItem"
      :title="'inventroy'"
    >
      <template v-slot:content="{ item, index }">
        <div class="w-full">
          <CTextField
            :title="$tr('label_required', $tr('sku'))"
            placeholder="Supplier Trading Name"
            type="textfield"
            class="me-md-2"
            rounded
            v-model="item.sku.$model"
            @blur="item.sku.$touch()"
            :rules="validateRule(item.sku, $root, $tr('sku'))"
            :invalid="item.sku.$invalid"
          />
        </div>
        <div class="h-full d-flex" style="height: fit-content !important">
          <div class="w-full">
            <CTextField
              :title="$tr('label_required', $tr('quantity'))"
              placeholder="Supplier Trading Name"
              type="number"
              class="me-md-2"
              rounded
              v-model="item.quantity.$model"
              @blur="item.quantity.$touch()"
              :rules="validateRule(item.quantity, $root, $tr('quantity'))"
              :invalid="item.quantity.$invalid"
            />
          </div>
          <div class="w-full">
            <CTextField
              :title="$tr('label_required', $tr('price_per_unit'))"
              :placeholder="$tr('price_per_unit')"
              type="number"
              class="me-md-2"
              rounded
              v-model="item.price_per_unit.$model"
              @blur="item.price_per_unit.$touch()"
              :rules="
                validateRule(item.price_per_unit, $root, $tr('price_per_unit'))
              "
              :invalid="item.price_per_unit.$invalid"
              prepend-inner-icon="mdi-pound"
            />
          </div>
        </div>

        <div class="d-flex">
          <InputCard :px="'0'" :py="'0'">
            <div v-for="(attr, i) in item.attributes.$each.$iter" :key="i">
              <div class="d-flex flex-column flex-md-row">
                <div style="width: 45%">
                  <CSelect
                    v-model="attr.attribute_id.$model"
                    @blur="attr.attribute_id.$touch()"
                    :rules="
                      validateRule(
                        attr.attribute_id, // validate objec
                        $root, // context
                        $tr('attribute') // lable for feedback
                      )
                    "
                    :invalid="attr.attribute_id.$invalid"
                    :title="$tr('label_required', $tr('attribute'))"
                    :placeholder="$tr('attribute')"
                    prepend-inner-icon="mdi-chart-bar-stacked"
                    :items="attrNames"
                    :loading="attributeNameLoading"
                    item-text="name"
                    item-value="id"
                    @change="selectData($event,i,index)"
                  />
                </div>
                <div style="width: 45%">
                  <CSelectMulti
                    title="Choose attributes"
                    placeholder="Attribute"
                    :items="attrValues[index]"
                    v-model="attr.value.$model"
                    item-text="value"
                    item-value="value"
                    multi
                    prepend-inner-icon="mdi-bookmark"
                    @blur="attr.value.$touch()"
                    :rules="
                      validateRule(
                        attr.value, // validate objec
                        $root, // context
                        $tr('value') // lable for feedback
                      )
                    "
                    :invalid="attr.value.$invalid"
                  />
                </div>
                <div style="width: 10%">
                  <v-icon
                    :class="`${
                      i == item.$model.attributes.length - 1
                        ? 'primary'
                        : 'red--text'
                    } rounded-circle mt-4`"
                    color="white"
                    style="
                      left: 30px !important;
                      top: 30px !important;
                      width: 37px;
                      height: 40px;
                    "
                    @click="
                      clicked(i, index, item.$model.attributes.length, attr)
                    "
                    >{{
                      i == item.$model.attributes.length - 1
                        ? "mdi-plus"
                        : "mdi-close"
                    }}
                  </v-icon>
                </div>
              </div>
            </div>
          </InputCard>
        </div>
      </template>
    </MultiInputItem>
  </div>
</template>

<script>
import MultiInputItem from "../../new_form_components/cat_product_selection/MultiInputItem.vue";
import GlobalRules from "~/helpers/vuelidate";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CSelect from "../../new_form_components/Inputs/CSelect.vue";
import CSelectMulti from "../../new_form_components/Inputs/CSelectMulti.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";

export default {
  components: { MultiInputItem, CTextField, CSelect, CSelectMulti, InputCard, CTitle },
  props: { form: Object },
  data() {
    return {
      validateRule: GlobalRules.validate,
      attributeLoading: false,
      attributeNameLoading: false,
      attrNames: [],
      attrValues: [],
      selectValueModel: [],
      selectNameModel: [],
      selectedItems: [],
    };
  },
  methods: {
    async validate() {
      this.form.inventroy.$touch();
      return !this.form.inventroy.$invalid;
    },
    async loaded() {
      this.fetchAttributes();
    },
    addMore() {
      this.form.inventroy.$model.push({
        sku: "",
        quantity: "",
        price_per_unit: "",
        attributes: [
          {
            attribute_id: "",
            value: [],
          },
        ],
      });
    },
    removeItem(item) {
      if (this.form.inventroy.$model.length > 1)
        this.form.inventroy.$model.splice(item, 1);
    },
    async fetchAttributes() {
      this.selectNameModel = [];
      this.selectValueModel = [];
      this.attributeNameLoading = true;
      try {
        const { data } = await this.$axios.get(
          "single-sales/attribute-ssp?key=all"
        );
        console.log(data.data);
        this.attrNames = data.data;
      } catch (error) {}
      this.attributeNameLoading = false;
    },
    clicked(i, index, length, item) {
      if (i == length - 1) {
        this.addOneMore(index);
      } else {
        this.removeItem1(item, index);
      }
    },
    addOneMore(index) {
      this.form.inventroy.$model[index].attributes.push({
        attribute_id: "",
        value: [],
      });
    },
    removeItem1(item, index) {
      this.form.inventroy.$model[index].attributes.splice(item, 1);
    },
    selectData(id,ind,index) {
      const val = this.attrNames.find((i)=>i.id == id);
      if (val) {
        const values = JSON.parse(val.values);
        this.attrValues[index] = values.map((i) => {
          return {
            value: i,
          };
        });
      }
    },
  },
};
</script>

<style scoped>
.attributes-class .v-timeline-item__divider {
  display: none !important;
}
.attributes-class .v-timeline::before {
  display: none;
}

.attributes-class .v-timeline--dense .v-timeline-item__body {
  max-width: 100%;
}
</style>
