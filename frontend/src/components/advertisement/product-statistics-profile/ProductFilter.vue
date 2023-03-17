<template>
  <div v-if="isFilter">
    <v-card style="height: 100%; width: 100%; background-color: white" outlined>
      <v-card-title
        class="pt-1 pl-1 pr-1 pb-0 d-flex flex-column"
        style="background-color: #eeeeee; height: 100%"
      >
        <div class="d-flex" style="width: 100%">
          <div class="d-flex">
            <v-badge dot style="margin-top: 5px" :value="true">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="19.444"
                height="17.77"
                viewBox="0 0 19.444 17.77"
              >
                <path
                  id="Path_2156"
                  data-name="Path 2156"
                  d="M-6343.477-4389.457a2.534,2.534,0,0,1-1.428-2.814,2.509,2.509,0,0,1,2.333-1.974,2.4,2.4,0,0,1,2.376,1.4.417.417,0,0,0,.451.262c.745-.011,1.489-.006,2.234,0a.826.826,0,0,1,.923.833c0,.518-.335.817-.923.83h-1.1c-.118,0-.236.009-.354,0a1.364,1.364,0,0,0-1.545.718,2.133,2.133,0,0,1-1.888.974A2.672,2.672,0,0,1-6343.477-4389.457Zm-10.545-1.462c-.626,0-.966-.3-.966-.832s.362-.832.967-.832q3.218,0,6.439,0a.83.83,0,0,1,.944.815c.01.534-.33.843-.947.846-1.086,0-2.172,0-3.259,0h-3.179Zm2.663-5.561a.446.446,0,0,0-.476-.291c-.76.015-1.518.012-2.276,0a.836.836,0,0,1-.845-.583.793.793,0,0,1,.314-.933,1.366,1.366,0,0,1,.591-.167c.376-.026.758-.007,1.135-.007.106,0,.211-.008.315,0a1.366,1.366,0,0,0,1.525-.685,2.33,2.33,0,0,1,2.9-.793,2.5,2.5,0,0,1,1.53,2.649,2.507,2.507,0,0,1-2.076,2.157,2.2,2.2,0,0,1-.426.042A2.525,2.525,0,0,1-6351.359-4396.48Zm7.3-.29a.857.857,0,0,1-.857-.618.808.808,0,0,1,.392-.94,1.5,1.5,0,0,1,.6-.127c1.06-.012,2.119-.005,3.179-.005s2.146-.006,3.221,0a.836.836,0,0,1,.93.952c-.046.458-.411.738-.99.74q-2.139,0-4.278,0c-.246,0-.489,0-.733,0h-.6C-6343.48-4396.762-6343.77-4396.764-6344.059-4396.77Zm2.453-6.71a2.551,2.551,0,0,1,2.53-2.519,2.506,2.506,0,0,1,2.489,2.5,2.471,2.471,0,0,1-2.492,2.522h-.009A2.512,2.512,0,0,1-6341.605-4403.48Zm-12.489.837a.82.82,0,0,1-.893-.828.8.8,0,0,1,.881-.833c1.662-.006,3.323,0,4.983,0s3.323,0,4.985,0a.8.8,0,0,1,.829.588.764.764,0,0,1-.312.9,1.025,1.025,0,0,1-.544.167q-3.147.01-6.3.009Z"
                  transform="translate(6355.529 4406.499)"
                  fill="#555"
                  stroke="rgba(0,0,0,0)"
                  stroke-width="1"
                />
              </svg>
            </v-badge>
            <h5 class="ml-1" style="font-size: medium; color: black">Filter</h5>
          </div>
          <div class="d-flex ml-auto">
            <h5 style="font-size: medium; color: #2979ff">Reset</h5>
            <v-icon class="ml-1" style="font-size: large; color: #757575"
              >mdi-close</v-icon
            >
          </div>
        </div>
        <div class="d-flex pa-2 flex-wrap" style="width: 100%">
          <span
            v-for="(item, index) in selectedItems"
            :key="index"
            style="
              border: 1px solid #bdbdbd;
              border-radius: 5px;
              padding: 0px 2px;
              color: #757575;
              font-size: small;
            "
          >
            {{ item.name }}
            <v-icon
              @click="removeItems(item)"
              style="font-size: medium"
              color="#757575"
              >mdi-close</v-icon
            >
          </span>
        </div>
      </v-card-title>
      <v-divider light class="mt-3"></v-divider>
      <v-card-text>
        <v-row justify="center">
          <v-expansion-panels accordion light>
            <v-expansion-panel>
              <v-expansion-panel-header
                >General Inforamtion</v-expansion-panel-header
              >
              <v-expansion-panel-content>
                <h6 style="font-size: small">Content Type</h6>
                <br />
                <div class="d-flex flex-wrap" style="width: 100%">
                  <SelectItem
                    v-for="item in ContentType"
                    :key="item.id"
                    :item="item"
                    :selected="item.id == Content_Type"
                    :disable="!item.active"
                    @click="onSelected(item, 'Content_Type')"
                    :isSmall="true"
                  />
                </div>
                <v-divider light class="mt-3"></v-divider>
                <h6 style="font-size: small" class="mt-2">Content Language</h6>
                <div class="d-flex flex-wrap mt-1" style="width: 100%">
                  <SelectItem
                    v-for="item in contentLanguage"
                    :key="item.id"
                    :item="item"
                    :selected="item.id == Content_Language"
                    :disable="!item.active"
                    @click="onSelected(item, 'Content_Language')"
                    :isSmall="true"
                  />
                </div>
                <v-divider light class="mt-3"></v-divider>
                <h6 style="font-size: small" class="mt-2">Content Size</h6>
                <div class="d-flex flex-wrap mt-1" style="width: 100%">
                  <SelectItem
                    v-for="item in ContentSize"
                    :key="item.id"
                    :item="item"
                    :icon="true"
                    :selected="item.id == Content_Size"
                    :disable="!item.active"
                    @click="onSelected(item, 'Content_Size')"
                    :isSmall="true"
                    :hasDetails="true"
                  />
                </div>
              </v-expansion-panel-content>
            </v-expansion-panel>
            <v-expansion-panel>
              <v-expansion-panel-header>{{
                $tr("marketing_information")
              }}</v-expansion-panel-header>
              <v-expansion-panel-content>
                <div>
                  <CRadioWIthBody
                    :px="'px-0'"
                    :py="'py-0'"
                    :items="season1"
                    :text="$tr('is_the_content_for_the_specific_reason?')"
                    @onChange="(v) => (season = v)"
                  />
                </div>
                <v-divider light></v-divider>
                <div class="mt-2">
                  <InputCard
                    :title="
                      this.$tr('content_risks_of_violating_advertising_policy')
                    "
                    :px="'px-0'"
                    :py="'py-0'"
                    :hasSearch="false"
                    :hasPagination="false"
                    class="sales__type__container"
                  >
                    <div class="d-flex align-center">
                      <SelectItem
                        v-model="risk_palicy"
                        v-for="item in riskPalicy"
                        :key="item.id"
                        :item="item"
                        :selected="item.id == risk_palicy"
                        :disable="!item.active"
                        @click="onSelected(item, 'risk_palicy')"
                        :isSmall="true"
                      >
                      </SelectItem>
                    </div>
                    <v-divider light class="mt-2"></v-divider>
                    <div class="">
                      <CRadioWIthBody
                        :px="'px-0'"
                        :py="'py-0'"
                        :items="Main_or_Customer"
                        :text="
                          $tr(
                            'is_there_an_oreder_of_the_custormer_to_do_someting?'
                          )
                        "
                        @onChange="(v) => (main_or_customer = v)"
                      />
                    </div>
                  </InputCard>
                </div>
                <div class="mt-3">
                  <InputCard
                    :title="this.$tr('information_offering')"
                    :hasSearch="false"
                    :hasPagination="false"
                    :px="'px-0'"
                    :py="'py-0'"
                  >
                    <div class="d-flex align-center">
                      <SelectItem
                        v-model="info_offer"
                        v-for="item in Info_Offer"
                        :key="item.id"
                        :item="item"
                        :selected="item.id == info_offer"
                        :disable="!item.active"
                        @click="onSelected(item, 'info_offer')"
                        :isSmall="true"
                      >
                      </SelectItem>
                    </div>
                  </InputCard>
                </div>
              </v-expansion-panel-content>
            </v-expansion-panel>
            <v-expansion-panel>
              <v-expansion-panel-header
                >Design Information</v-expansion-panel-header
              >
              <v-expansion-panel-content>
                <div class="w-full d-flex">
                  <NoCheckboxItemContainer
                    v-model="content_direction"
                    :items="contentDirections"
                    :bgWhite="true"
                    :customIcon="'puzzle'"
                    :customWidth2="true"
                    :multi="true"
                    height="320px"
                    @click="onSelected(item, 'content_direction')"
                  ></NoCheckboxItemContainer>
                </div>
                <div>
                  <InputCard
                    :title="this.$tr('voice_over')"
                    :hasSearch="false"
                    :hasPagination="false"
                    :px="'px-0'"
                    :py="'py-0'"
                  >
                    <div class="d-flex align-center flex-wrap">
                      <SelectItem
                        v-model="voice_over"
                        v-for="item in voiceOver"
                        :key="item.id"
                        :item="item"
                        :selected="item.id == voice_over"
                        :disable="!item.active"
                        @click="onSelected(item, 'voice_over')"
                        :isSmall="true"
                      >
                      </SelectItem>
                    </div>
                  </InputCard>
                </div>
                <div>
                  <InputCard
                    :title="this.$tr('music')"
                    :hasSearch="false"
                    :hasPagination="false"
                    :px="'px-0'"
                    :py="'py-0'"
                  >
                    <div class="d-flex align-center flex-wrap">
                      <SelectItem
                        v-model="music"
                        v-for="item in musics"
                        :key="item.id"
                        :item="item"
                        :selected="item.id == music"
                        :disable="!item.active"
                        @click="onSelected(item, 'music')"
                        :isSmall="true"
                      >
                      </SelectItem>
                    </div>
                  </InputCard>
                </div>
                <div>
                  <InputCard
                    :title="this.$tr('shooting')"
                    :hasSearch="false"
                    :hasPagination="false"
                    :px="'px-0'"
                    :py="'py-0'"
                  >
                    <div class="d-flex align-center flex-wrap">
                      <SelectItem
                        v-model="shooting"
                        v-for="item in shootings"
                        :key="item.id"
                        :item="item"
                        :selected="item.id == shooting"
                        :disable="!item.active"
                        @click="onSelected(item, 'shooting')"
                        :isSmall="true"
                      >
                      </SelectItem>
                    </div>
                  </InputCard>
                </div>
                <div>
                  <InputCard
                    :title="this.$tr('people')"
                    :hasSearch="false"
                    :hasPagination="false"
                    :px="'px-0'"
                    :py="'py-0'"
                  >
                    <div class="d-flex align-center flex-wrap">
                      <SelectItem
                        v-model="people"
                        v-for="item in peoples"
                        :key="item.id"
                        :item="item"
                        :selected="item.id == people"
                        :disable="!item.active"
                        @click="onSelected(item, 'people')"
                        :isSmall="true"
                      >
                      </SelectItem>
                    </div>
                  </InputCard>
                </div>
                <div>
                  <InputCard
                    :title="this.$tr('graphics')"
                    :hasSearch="false"
                    :hasPagination="false"
                    :px="'px-0'"
                    :py="'py-0'"
                  >
                    <div class="d-flex align-center flex-wrap">
                      <SelectItem
                        v-model="graphic"
                        v-for="item in graphics"
                        :key="item.id"
                        :item="item"
                        :selected="item.id == graphic"
                        :disable="!item.active"
                        @click="onSelected(item, 'graphic')"
                        :isSmall="true"
                      >
                      </SelectItem>
                    </div>
                  </InputCard>
                </div>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
export default {};
</script>

<style>
</style>