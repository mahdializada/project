<template>
  <div>
    <v-dialog v-model="inventoryModel" width="1100" persistent>
        <v-card>
            <v-card elevation="24" style="margin-bottom: 10px">
					<v-card-title style="color: #777" class="px-2 pt-2 pb-2">
						<div class="dialog-title d-flex align-center">
							{{ $tr("inventory_details") }}
							<div
								class="remarks-number ms-1"
								:style="`background: ${$vuetify.theme.defaults.light.primary}`"
							></div>
						</div>
						<v-spacer></v-spacer>
                        <CloseBtn 
                            class="close-dialog-btn"
							@click="closeDialog"
                        />
						
                        
					</v-card-title>
				</v-card>
                <v-card-text 
                    style="box-shadow: none;overflow-y: scroll;overflow-x: hidden;
						height: 500px;
					"
                >
                    <template>
                        <v-expansion-panels   v-model="panel" variant="accordion"
                            :readonly="readonly"
                            multiple
                             class="mt-2"
                        >
                            <v-expansion-panel
                                v-for="(item, index) in inventories"
                                :key="index"
                                @click="(index)=>{
                                    panel = [index]
                                }"
                            >
                                <v-expansion-panel-header class="pa-1 px-2">
                                    <template v-slot:actions>
                                        <v-icon color="primary"
                                            >mdi-chevron-down-circle-outline
                                        </v-icon>
                                    </template>
                                    <div class="d-flex align-center justify-space-between">
                                        <div class="d-flex align-center">
                                            <v-avatar color="primary" size="32">
                                                <v-icon color="white" size="20">
                                                    mdi-warehouse
                                                </v-icon>
                                            </v-avatar>
                                            <p
                                                class="mb-0 ps-2 text-capitalize"
                                                style=" 
                                                    font-size: 16px;
                                                    font-weight: 600;
                                                    opacity: 0.7;
                                                "
                                                >
                                                {{ $tr("inventory") }}
                                            </p>
                                        </div>
                                        
                                    </div>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content class="px-1">
                                    <div class="d-flex">
                                        <div style="width:45%">
                                            <p
                                            class="mb-1"
                                            style="opacity: 0.5;
                                                font-size: 17px;
                                                font-weight: 500;  
                                            "
                                            >
                                                {{ $tr("general_info") }}
                                            </p>
                                            <div
                                                class="w-100  align mb-2 d-flex item-center align-center rounded px-2"
                                                :style="`min-height: 50px; background:#f9f9f9 `"
                                            >
                                                <div
                                                    class="d-flex align-center"
                                                    style="
                                                        font-size: 16px; 
                                                        font-weight: 500;
                                                        width: 100%; 
                                                    "
                                                    >
                                                    <div style="width:50%">
                                                        {{ $tr("sku") }}
                                                    </div>
                                                    <div style="width:50%">
                                                        {{ $tr(item.sku) }}

                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="w-100  align mb-2 d-flex item-center align-center rounded px-2"
                                                :style="`min-height: 50px; background:#f9f9f9 `"
                                            >
                                                <div
                                                    class="d-flex align-center"
                                                    style="
                                                        font-size: 16px;
                                                        font-weight: 500;
                                                        width: 100%;  
                                                    "
                                                    >
                                                    <div style="width:50%">
                                                        {{ $tr("quantity") }}
                                                    </div>
                                                    <div style="width:50%">
                                                        {{ $tr(item.quantity) }}

                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="w-100  align mb-2 d-flex item-center align-center rounded px-2"
                                                :style="`min-height: 50px; background:#f9f9f9  `"
                                            >
                                                <div
                                                    class="d-flex align-center"
                                                    style="
                                                        font-size: 16px; 
                                                        font-weight: 500;
                                                        width: 100%;
                                                    "
                                                    >
                                                    <div style="width:50%">
                                                        {{ $tr("price_per_unit") }}
                                                    </div>
                                                    <div style="width:50%">
                                                        {{ $tr(item.price_per_unit) }}

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="width:55%;" >
                                            <div class="d-flex" style="padding-left:10px">
                                                <div style="width:40%">
                                                    <p
                                                    class="mb-1"
                                                    style="opacity: 0.5;
                                                        font-size: 17px;
                                                        font-weight: 500;padding-left:10px; 
                                                    "
                                                    >
                                                        {{ $tr("atrribute_names") }}
                                                    </p>
                                                    <div style="border-left:1px solid lightgray;">
                                                        <div v-for="(attr, i) in item.product_attribute"
                                                                    :key="i" 
                                                            class="w-100  align mb-2 d-flex item-center align-center rounded px-2"
                                                            :style="`min-height: 50px; background:#f9f9f9;margin-left:13px;`"
                                                        >
                                                            <div  
                                                                class="d-flex align-center"
                                                                style=" 
                                                                    font-size: 16px;   
                                                                    font-weight: 500;  
                                                                    width: 100%; 
                                                                    
                                                                "
                                                                >
                                                                
                                                            
                                                                    {{ $tr(attr.attributes.name) }}


                                                                <!-- attribute name:{{  attr.attributes.name }} -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="width:60%; " >
                                                    <p
                                                    class="mb-1"
                                                    style="opacity: 0.5;
                                                        font-size: 17px;
                                                        font-weight: 500;   
                                                    "
                                                    >
                                                        {{ $tr("atrribute_values") }}
                                                    </p>
                                                    <div v-for="(attr, i) in item.product_attribute"
                                                        :key="i"
                                                        class="w-100  align mb-2 d-flex item-center align-center rounded px-2"
                                                        :style="`min-height: 50px; background:#f9f9f9`"
                                                    >
                                                        <div
                                                            class="d-flex align-center"
                                                            style="
                                                                font-size: 16px; 
                                                                font-weight: 500;
                                                                width: 100%;   
                                                            "
                                                            >
                                                        
                                                                <span
                                                                    class="px-2 mr-1 font-weight-black"
                                                                    style="
                                                                        font-size: 1rem;  
                                                                        background-color: white;      
                                                                        border-radius: 20px;    
                                                                        border: 1px solid #e0e0e0;   
                                                                        padding-top:4px;
                                                                        padding-bottom:4px;    
                                                                    "
                                                                    v-for="(attrVal, i) in attr.attribute_value"
                                                                        :key="i"
                                                                >
                                                                    {{ $tr(attrVal) }}
                                                                </span>
                                                        </div>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </template>
                </v-card-text>
        </v-card>
    </v-dialog>
  </div>
</template>

<script>
import CloseBtn from '../../design/Dialog/CloseBtn.vue';

export default {
    components: { CloseBtn },

    data() {
        return {
            panel: [0],
            readonly: false,
            inventoryModel: false,
            items:[],
            inventories: [],
            product_attribute:[],
            inventory_properties: [
                { name: "sku", type: "string" },
                { name: "quantity", type: "string" },
                { name: "price_per_unit", type: "string" },
            ],
            attribute_properties:[
                { name: "attribute", type: "string" },
            ]
        };
    },
    methods: {
        viewModel(item) {
            console.log(item);
            this.inventoryModel = true;
            this.items=item;
            this.inventories=item.inventory_ssp;
        },
        closeDialog(){
            this.inventoryModel=false;
            this.panel= [0];
        },
    },
}
</script>

<style>

</style>