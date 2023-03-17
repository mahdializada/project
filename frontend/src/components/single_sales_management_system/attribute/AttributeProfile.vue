<template>
  <v-card height="500px">
  
    <div width="100%">
      <div class="d-flex align-center pa-2">
        <div class="title ms-3 font-weight-bold " :class="
          this.$vuetify.breakpoint.sm || this.$vuetify.breakpoint.xs
            ? ''
            : 'text-h5'
        ">
          {{ $tr("profile") }}
        </div>
        <v-spacer></v-spacer>
        <CloseBtn @click="closeAttributeDialoge" />
      </div>
    </div>
   
     <v-card-title>
      
        <v-row text-align="center">
          <v-col cols="2"></v-col>
          <v-col cols="8">
            <v-chip
        
            >
              <v-avatar
                class="accent"
                left
                v-text="attribute.name.slice(0, 1).toUpperCase()"
              >
            
            </v-avatar>
         
            </v-chip>
          </v-col>
          <v-col cols="2"></v-col>
        </v-row>
     
    </v-card-title>
  
</v-card>
</template> 

<script>
import CloseBtn from "../../design/Dialog/CloseBtn";
export default {
  components: { 
    CloseBtn,
   },
   props: {
    attribute: {
      type: Object,
      required: true,
    },
  },

  
  methods: {
    closeAttributeDialoge() {
      this.$emit('closeAttributeDialoge');
    }
  }
}
</script>

<style>

</style>