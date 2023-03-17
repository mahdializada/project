<template>
  <v-card elevation="0" rounded class="">
    <AdReasons
      :showReasonDialog="showReasonDialog"
      @onSubmit="changeAdvertisementStatus($event)"
      @closeDialog="showReasonDialog = false"
      tabName="ad"
      ref="reasonRef"
    />

    <LabelForm
      ref="labelRef"
      subsystem_name="advertisement"
      :model_name.sync="model_name"
      :selected_item.sync="selected_item"
    />

    <div
      class="mb-1 ps-2 py-auto d-flex align-center justify-space-between"
      style="height: 40px; background-color: #f7f8fc"
      v-if="card_title != ''"
    >
      <span class="custom-title"> {{ $tr(card_title) }}</span>
      <v-btn
        x-small
        v-if="show_label_button"
        color="primary"
        class="me-1"
        @click="$refs.labelRef.toggleDialog()"
        fab
      >
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </div>
    <v-data-table
      :show-select="showSelect"
      v-show="!loading_api && !paginate_loading && items.length > 0"
      calculate-widths
      dense
      :headers="headers"
      :items="items"
      hide-default-footer
      class="graph-table elevation-0"
      v-if="card_title != 'remark'"
      hide-default-header
      id="graph-table-scroll"
      :height="height"
      v-model="selectedItem"
      item-key="id"
    >
      <template v-slot:header="{ on, props }">
        <tr
          :class="
            card_title == ''
              ? 'graph-custom-header-wrapper'
              : 'header-wrapper-divider'
          "
        >
          <th v-for="(head, i) in props.headers" :key="i">
            <v-checkbox
              v-if="i == 0 && showSelect"
              v-model="selectAll"
              class="ma-0 px-2"
              style="height: 30px"
              @change="on['toggle-select-all'](selectAll)"
              hide-details
            >
            </v-checkbox>

            <div
              :class="`px-2 custom-header item ${
                sortBy == head.value ? 'sorted' : ''
              } `"
              v-else
              @click="head.sortable !== 0 ? on.sort(head.value) : () => {}"
            >
              {{ head.text }}

              <v-icon
                class="ps-1"
                style=""
                x-small
                v-if="head.sortable !== false"
                >fa-sort</v-icon
              >
            </div>
          </th>
        </tr>
      </template>

      <template v-slot:[`item.status_creator`]="{ item }">
        <v-tooltip bottom max-width="500">
          <template v-slot:activator="{ on, attrs }">
            <v-avatar size="26" v-bind="attrs" v-on="on">
              <v-img :src="item.data[0].creator.image"></v-img>
            </v-avatar>
          </template>
          <div>
            {{ item.data[0].creator.firstname }}
            {{ item.data[0].creator.lastname }}
          </div>
        </v-tooltip>
      </template>
      <template v-slot:[`item.changed_by`]="{ item }">
        <v-tooltip bottom max-width="500">
          <template v-slot:activator="{ on, attrs }">
            <div class="text-center">
              <v-avatar size="26" v-bind="attrs" v-on="on">
                <v-img
                  :src="item?.changed_by?.image"
                  lazy-src="https://picsum.photos/id/11/10/6"
                ></v-img>
              </v-avatar>
            </div>
          </template>
          <div>
            {{ item?.changed_by?.firstname }}
            {{ item?.changed_by?.lastname }}
          </div>
        </v-tooltip>
      </template>
      <template v-slot:[`item.status_date`]="{ item }">
        <v-tooltip bottom max-width="500">
          <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on" style="width: 30px">
              {{ localeHumanReadableTime3(item.data[0].created_at) }}
            </span>
          </template>
          <span>
            {{ localeHumanReadableTime(item.data[0].created_at) }}
          </span>
        </v-tooltip>
      </template>
      <template v-slot:[`item.status_status`]="{ item }">
        <v-switch
          inset
          dense
          readonly
          :input-value="item.data[0].status == 'active'"
          hide-details
          class="mt-0"
        >
        </v-switch>
      </template>

      <template v-slot:[`item.label`]="{ item }">
        <v-menu
          open-on-hover
          right
          offset-x
          v-if="item.data.length > 0"
          nudge-right="15px"
        >
          <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on" class="">
              <v-icon
                size="20"
                class="me-1"
                color="primary"
                v-if="item.data[0].status == 'active'"
              >
                mdi-plus-box</v-icon
              >
              <v-icon size="20" class="me-1" color="error" v-else
                >mdi-minus-box</v-icon
              >
              <template v-for="(data, i) in item.data">
                <v-avatar
                  v-if="i < 3"
                  :key="i"
                  size="22"
                  color="white"
                  class="ml-n1"
                >
                  <v-avatar
                    size="19"
                    :color="data.label.color"
                    class="white--text text-uppercase"
                    >{{ data.label.label.charAt(0) }}</v-avatar
                  >
                </v-avatar>
              </template>
              <span v-if="item.data.length > 3">
                +{{ item.data.length - 3 }}
              </span>
            </span>
          </template>
          <v-list>
            <v-list-item v-for="(data, index) in item.data" :key="index" dense>
              <v-list-item-icon>
                <v-avatar
                  size="22"
                  :color="data.label.color"
                  class="white--text text-center text-uppercase"
                  >{{ data.label.label.charAt(0) }}</v-avatar
                >
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="custom-card-title-2">{{
                  data.label.label
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.label_date`]="{ item }">
        <v-tooltip bottom max-width="500">
          <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on">
              {{ localeHumanReadableTime3(item.data[0].created_at) }}
            </span>
          </template>
          <span v-for="(data, index) in item.data" :key="index" class="d-block">
            {{ localeHumanReadableTime(data.created_at) }}
          </span>
        </v-tooltip>
      </template>
      <template v-slot:[`item.label_creator`]="{ item }">
        <v-tooltip bottom max-width="500">
          <template v-slot:activator="{ on, attrs }">
            <v-avatar size="26" v-bind="attrs" v-on="on">
              <v-img :src="item.data[0].creator.image"></v-img>
            </v-avatar>
          </template>
          <div>
            {{ item.data[0].creator.firstname }}
            {{ item.data[0].creator.lastname }}
          </div>
        </v-tooltip>
      </template>
      <template v-slot:[`item.status_reason`]="{ item }">
        <v-tooltip bottom max-width="500">
          <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on" class="primary--text">Reasons</span>
          </template>
          <div class="">
            <div
              class="d-flex align-start"
              v-for="(item, j) in item.data"
              :key="j"
            >
              <v-icon left color="white">mdi-circle-outline</v-icon>
              <p>
                {{ item.reasons.title }}
              </p>
            </div>
          </div>
        </v-tooltip>
      </template>

      <template v-slot:[`item.daily_budget`]="{ item }">
        <div class="d-flex justify-center">
          <div
            class="
              rounded-pill
              d-flex
              align-center
              justify-space-around
              orange
              lighten-5
            "
            style="min-width: 100px; max-width: 100px; height: 27px"
          >
            <v-avatar
              class="d-flex justify-center align-center orange"
              size="20"
            >
              <SvgIcon
                :icon="'budget-icon'"
                style="height: 80%; width: 80%"
                :fill="'#fff'"
              >
              </SvgIcon>
            </v-avatar>
            <span class="orange--text">
              {{ item.daily_budget || item.value + " USD" }}
            </span>
          </div>
        </div>
      </template>

      <template v-slot:[`item.bid`]="{ item }">
        <div class="d-flex justify-center">
          <div
            class="
              rounded-pill
              d-flex
              align-center
              justify-space-around
              purple
              lighten-5
            "
            style="min-width: 100px; max-width: 100px; height: 27px"
          >
            <v-avatar
              class="d-flex justify-center align-center purple"
              size="22"
            >
              <SvgIcon
                :icon="'budget-icon'"
                style="height: 80%; width: 80%"
                :fill="'#fff'"
              >
              </SvgIcon>
            </v-avatar>
            <span class="purple--text">
              {{ item.bid || item.value + " USD" }}
            </span>
          </div>
        </div>
      </template>
      <template v-slot:[`item.bid_data_date`]="{ item }">
        <v-tooltip bottom max-width="500">
          <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on">
              {{ localeHumanReadableTime3(item.created_at) }}
            </span>
          </template>
          <span>
            {{ localeHumanReadableTime(item.created_at) }}
          </span>
        </v-tooltip>
      </template>
      <template v-slot:[`item.connection.generated_link`]="{ item }">
        <div class="link">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <span
                @click.stop.prevent="
                  clickchild($event, item.connection.generated_link)
                "
                v-bind="attrs"
                v-on="on"
                style="
                  white-space: nowrap;
                  padding-top: 1.5px;
                  padding-bottom: 1.5px;
                "
                class="white--text px-5 rounded-pill primary"
              >
                {{ $tr("link") }}
              </span>
            </template>
            <div v-if="item.connection.generated_link">
              <div v-if="item.connection.generated_link.length > 0">
                <p class="pb-0 mb-0">
                  {{ item.connection.generated_link }}
                </p>
              </div>
              <div v-else>
                <p class="pb-0 mb-0">{{ $tr("no_item", $tr("link")) }}</p>
              </div>
            </div>
          </v-tooltip>
        </div>
      </template>
      <template v-slot:[`item.connection.landing_page_link`]="{ item }">
        <div class="link">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <span
                @click.stop.prevent="
                  clickchild($event, item.connection.landing_page_link)
                "
                v-bind="attrs"
                v-on="on"
                style="
                  white-space: nowrap;
                  padding-top: 1.5px;
                  padding-bottom: 1.5px;
                "
                class="white--text px-5 rounded-pill info"
              >
                {{ $tr("landing_page_link") }}
              </span>
            </template>
            <div v-if="item.connection.landing_page_link">
              <p class="pb-0 mb-0">
                {{ item.connection.landing_page_link }}
              </p>
            </div>

            <div v-else>
              <p class="pb-0 mb-0">{{ $tr("no_item", $tr("link")) }}</p>
            </div>
          </v-tooltip>
        </div>
      </template>

      <template v-slot:[`item.iso2`]="{ item }">
        <FlagIcon
          v-if="item.iso2"
          :flag="item.iso2.toLowerCase()"
          :round="true"
        />
      </template>

      <template v-slot:[`item.remarks_count`]="{ item }">
        <div
          class="
            rounded-pill
            d-flex
            align-center
            justify-space-around
            pink
            lighten-5
          "
          style="min-width: 45px; max-width: 80px; height: 80%"
        >
          <v-avatar
            class="d-flex justify-center align-center"
            size="21"
            style="background-color: rgba(255, 0, 112)"
          >
            <span style="height: 80%; width: 80%; padding-top: 1px">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="11.294"
                height="11.277"
                viewBox="0 0 11.294 11.277"
              >
                <path
                  id="remark-icon"
                  d="M176.188,248.219c.924,0,1.849-.006,2.773,0a2.806,2.806,0,0,1,2.8,2.227,3.317,3.317,0,0,1,.071.707c.007,1.224,0,2.447,0,3.671a2.823,2.823,0,0,1-2.959,2.947c-1.126,0-.888-.122-1.58.78-.128.168-.253.338-.38.506a.816.816,0,0,1-1.439.007c-.234-.308-.472-.614-.7-.929a.846.846,0,0,0-.737-.373,6.334,6.334,0,0,1-1.489-.125,2.746,2.746,0,0,1-2-2.675c-.014-1.312-.018-2.623,0-3.935a2.815,2.815,0,0,1,2.862-2.807C174.339,248.211,175.264,248.219,176.188,248.219Zm-.01,3.8c.923,0,1.847,0,2.77,0,.279,0,.445-.133.47-.358.035-.3-.161-.485-.538-.485q-2.691,0-5.382,0c-.355,0-.543.155-.54.434s.181.409.528.409Q174.833,252.024,176.178,252.022Zm-1.133,2.831c.546,0,1.091,0,1.637,0a.451.451,0,0,0,.5-.434.428.428,0,0,0-.486-.408q-1.637,0-3.273,0a.578.578,0,0,0-.295.083.377.377,0,0,0-.146.467.432.432,0,0,0,.455.29C173.971,254.855,174.508,254.853,175.045,254.853Z"
                  transform="translate(-170.54 -248.216)"
                  fill="#fff"
                />
              </svg>
            </span>
          </v-avatar>
          <span style="color: rgba(255, 0, 112)">
            {{ item.remarks_count }}
          </span>
        </div>
      </template>

      <!-- <template v-slot:[`item.generated_link_date2`]="{ item }">
                    {{ localeHumanReadableTime2(item.connection_date.created_at) }}
               </template> -->
      <!-- <template v-slot:[`item.created_at`]="{ item }">
                    {{ localeHumanReadableTime(item.created_at) }}
               </template> -->
      <!-- 
               <template v-slot:[`item.updated_at`]="{ item }">
                    {{ localeHumanReadableTime(item.updated_at) }}
               </template> -->

      <!-- end history -->
      <!-- budget status -->

      <!-- end history -->

      <template v-slot:[`item.payment_method`]="{ item }">
        {{ getPaymentMethod(item) }}
      </template>

      <template v-slot:[`item.total_balance`]="{ item }">
        {{ item.balances_sum_balance }}
      </template>
      <template v-slot:[`item.delivery_status`]="{ item }">
        <v-menu open-on-hover right offset-x nudge-right="15px">
          <template v-slot:activator="{ on, attrs }">
            <div
              v-bind="attrs"
              v-on="on"
              class="
                rounded-pill
                d-flex
                align-center
                justify-space-around
                primary
                lighten-5
              "
              style="min-width: 45px; max-width: 80px; height: 80%"
            >
              <v-avatar class="d-flex justify-center align-center" size="22">
                <SvgIcon
                  :icon="'delivery-icon'"
                  style="height: 80%; width: 80%"
                >
                </SvgIcon>
              </v-avatar>
              <span style="color: #115598">
                {{ item.delivery_status.length }}
              </span>
            </div>
          </template>
          <v-list v-if="item.delivery_status">
            <v-list-item v-if="item.delivery_status.length < 1" dense>
              <v-list-item-icon>
                <v-icon large>mdi-circle-small</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="custom-card-title-2">
                  {{ $tr("not_available") }}</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              v-else
              v-for="(status, index) in item.delivery_status"
              :key="index"
              dense
            >
              <v-list-item-icon>
                <v-icon large>mdi-circle-small</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="custom-card-title-2">{{
                  status
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.ad_account_pixels`]="{ item }">
        <v-menu open-on-hover right offset-x nudge-right="15px">
          <template v-slot:activator="{ on, attrs }">
            <div v-bind="attrs" v-on="on">Account Pixels</div>
          </template>
          <v-list v-if="item.ad_account_pixels">
            <v-list-item v-if="item.ad_account_pixels.length < 1" dense>
              <v-list-item-icon>
                <v-icon large>mdi-circle-small</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="custom-card-title-2">
                  {{ $tr("not_available") }}</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              v-else
              v-for="(pixel_item, index) in item.ad_account_pixels"
              :key="index"
              dense
            >
              <v-list-item-icon>
                <v-icon large>mdi-circle-small</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="custom-card-title-2">{{
                  pixel_item.pixel_id
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.pcode_landing_urls`]="{ item }">
        <v-menu right offset-x nudge-right="15px">
          <template v-slot:activator="{ on, attrs }">
            <!-- <div v-bind="attrs" v-on="on">Product Landing Page Links</div> -->
            <v-btn
              rounded
              color="primary"
              dark
              v-bind="attrs"
              v-on="on"
              height="28px "
            >
              product landing page urls
            </v-btn>
          </template>
          <v-list v-if="item.pcode_landing_urls">
            <v-list-item
              v-if="item.pcode_landing_urls.split(',').length < 1"
              dense
            >
              <v-list-item-icon>
                <v-icon large>mdi-circle-small</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="custom-card-title-2">
                  {{ $tr("not_available") }}</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              v-else
              v-for="(link_item, index) in item.pcode_landing_urls.split(',')"
              :key="index"
              dense
            >
              <v-list-item-icon>
                <v-icon large>mdi-circle-small</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title
                  class="custom-card-title-2"
                  @click="copy_url(link_item)"
                  >{{ link_item }}</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.avg_bids`]="{ item }">
        {{ item.campaign_adsets_avg_bid }}
      </template>
      <!-- <template v-slot:[`item.generated_link_date`]="{ item }">
                    {{ localeHumanReadableTime(item.connection_date.created_at) }}
               </template> -->

      <template v-slot:[`item.first_generated_date`]="{ item }">
        {{ localeHumanReadableTime(item.first_generated_date) }}
      </template>

      <template v-slot:[`item.first_generated_date2`]="{ item }">
        {{ localeHumanReadableTime2(item.first_generated_date) }}
      </template>

      <template v-slot:[`item.index`]="{ index }">
        <v-avatar color="primary" size="22" class="white--text">
          {{ index + 1 }}
        </v-avatar>
      </template>
      <template v-slot:[`item.country`]="{ item }">
        <div class="d-flex">
          <FlagIcon
            v-if="item.country.iso2"
            :flag="item.country.iso2.toLowerCase()"
            :round="true"
          />
          <span style="padding: 4px">{{ item.country.name }}</span>
        </div>
      </template>
      <template v-slot:[`item.company`]="{ item }">
        <div class="d-flex">
          <span style="white-space: nowrap" class="mb-1">
            <v-img
              class="rounded-circle"
              width="25"
              height="25"
              lazy-src="https://picsum.photos/id/11/10/6"
              :src="item.logo"
            ></v-img>
          </span>
          <span style="padding: 4px">{{ item.company.name }}</span>
        </div>
      </template>

      <template v-slot:[`item.bank_details`]="{ item }">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <span
              v-bind="attrs"
              v-on="on"
              style="
                white-space: nowrap;
                padding-top: 1.5px;
                padding-bottom: 1.5px;
              "
              class="px-5"
            >
              {{ $tr("bank_details") }}
            </span>
          </template>
          <div class="white">
            <div v-if="item.bank_account.length > 0">
              <p class="pb-0 mb-0">
                card_number: {{ item.bank_account[0].card_number }}
              </p>
              <p class="pb-0 mb-0">owner: {{ item.bank_account[0].owner }}</p>
            </div>
          </div>
        </v-tooltip>
      </template>
      <template v-slot:[`item.logo-avatar`]="{ item }">
        <v-avatar :size="30" color="blue-grey darken-3">
          <span class="white--text text-h6 pa-1">
            {{ item.ad_name[0].toUpperCase() }}
          </span>
        </v-avatar>
      </template>
      <template v-slot:[`item.platform.logo`]="{ item }">
        <span style="white-space: nowrap" class="mb-1">
          <v-img
            class="rounded-circle"
            width="30"
            height="30"
            lazy-src="https://picsum.photos/id/11/10/6"
            :src="item.platform.logo"
          ></v-img>
        </span>
      </template>

      <template v-slot:[`item.platform.platform_name`]="{ item }">
        <span
          v-if="item.platform && item.platform.platform_name"
          class="text-capitalize"
        >
          {{ item.platform.platform_name }}
        </span>
      </template>
      <template v-slot:[`item.status`]="{ item }">
        <v-switch
          :input-value="item.status.toLowerCase() == 'active'"
          inset
          :color="primaryColor"
          @click="changeStatus(item)"
          hide-details
          readonly
          class="mt-0"
        >
        </v-switch>
      </template>
    </v-data-table>
    <div v-show="loading_api || paginate_loading">
      <div v-for="i in 5" :key="i" class="d-flex w-full">
        <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
        <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
        <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
        <v-skeleton-loader type="list-item" class="rounded-0 w-full" />
      </div>
    </div>

    <v-pagination
      v-if="show_pagination && items.length > 0"
      v-model="page"
      :length="last_page"
      :total-visible="7"
    ></v-pagination>
    <div
      v-if="!loading_api && !paginate_loading && items.length == 0"
      class="text-center d-flex align-cener justify-center mx-auto"
      :style="`height:${height}px`"
    >
      <NoLabel
        class="align-self-center"
        v-show="card_title == 'label'"
        width="140"
        height="130"
      />
      <NoStatus
        class="align-self-center"
        v-show="card_title == 'status'"
        width="140"
        height="130"
      />

      <NoAds
        class="align-self-center"
        v-show="card_title == 'related_ads'"
        width="140"
        height="130"
      />
      <NoBid
        class="align-self-center"
        v-show="
          card_title == 'bid_&_budget_info' ||
          card_title == 'bid_history' ||
          card_title == 'budget_history'
        "
        width="140"
        height="130"
      />
    </div>
  </v-card>
</template>
<script>
import moment from "moment-timezone";
import SvgIcon from "../../design/components/SvgIcon.vue";
import AdReasons from "../AdReasons.vue";
import LabelForm from "../label/LabelForm.vue";
import NoRemark from "../remarks/NoRemark.vue";
import SingleRemark from "../remarks/SingleRemark.vue";
import NoLabel from "../label/NoLabel.vue";
import NoStatus from "../budget/NoStatus.vue";
import NoAds from "./NoAds.vue";
import NoBid from "./NoBid.vue";

export default {
  components: {
    NoRemark,
    SingleRemark,
    SvgIcon,
    AdReasons,
    LabelForm,
    NoLabel,
    NoStatus,
    NoAds,
    NoBid,
  },
  props: {
    show_label_button: {
      type: Boolean,
      default: false,
    },
    showSelect: {
      type: Boolean,
      default: false,
    },
    card_title: {
      type: String,
      default: "",
    },
    last_page: {
      type: Number,
      default: 0,
    },
    height: {
      type: String,
      default: "300",
    },
    show_pagination: {
      type: Boolean,
      default: false,
    },
    loading_api: {
      type: Boolean,
      require: true,
    },
    headers: {
      type: Array,
      default: () => {
        return [
          { text: "Calories", value: "calories" },
          { text: "Fat ", value: "fat" },
          { text: "Carbs ", value: "carbs" },
          { text: "Protein ", value: "protein" },
          { text: "Iron ", value: "iron" },
        ];
      },
    },
    items: {
      type: Array,
      default: () => {
        return [];
      },
    },
    model_name: {
      type: String,
      default: "",
    },
    selected_item: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      selectedItem: [],
      selectAll: false,
      showReasonDialog: false,
      statusChangeItem: [],
      page: 1,
      sortBy: "",

      item_data: [
        {
          calories: 159,
          fat: 6.0,
          carbs: 24,
          protein: 4.0,
          iron: "1%",
        },
        {
          name: "Ice cream sandwich",
          calories: 237,
          fat: 9.0,
          carbs: 37,
          protein: 4.3,
          iron: "1%",
        },
      ],
      paginate_loading: false,
      force_reset: false,
    };
  },
  watch: {
    page: function () {
      if (this.force_reset) {
        this.force_reset = false;
        return;
      }
      this.togglePaginateLoading();
      console.log("log pagination", this.page);
      this.$emit("onPaginate", {
        page: this.page,
        card_title: this.card_title,
      });
    },
    selectedItem: function () {
      if (this.force_reset) {
        this.force_reset = false;
        return;
      }
      let data = this.selectedItem.map((row) => row.ad_id);
      this.$emit("filterByAd", data);
    },
  },
  computed: {
    primaryColor() {
      return this.$vuetify.theme.themes.light.primary == "#2E7BE6"
        ? "success"
        : "primary";
    },
  },
  methods: {
    isEnabled(slot) {
      return this.enabled === slot;
    },
    resetDatatable() {
      this.force_reset = true;
      this.selectedItem = [];
      this.page = 1;
      this.selectAll = false;
    },

    togglePaginateLoading() {
      this.paginate_loading = !this.paginate_loading;
    },
    localeHumanReadableTime3(date) {
      return moment(date).locale(this.$vuetify.lang.current).format("MMM Do");
    },
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },

    localeHumanReadableTime2(date) {
      return moment(date).fromNow();
    },
    getPaymentMethod(item) {
      let method = "";
      if (item.connection !== undefined) {
        switch (item.connection.platform.platform_name) {
          case "facebook":
            method = "Credit Card";
            break;
          case "tiktok":
            method = "Debit Card";
            break;
          case "snapchat":
            method = "Credit Card";
            break;
          default:
            method = "unkown";
            break;
        }
      }
      return method;
    },
    async copy_url(item) {
      await navigator.clipboard.writeText(item);
      this.$toasterNA("green", this.$tr("successfully_copied"));
    },
    changeStatus(item) {
      console.log("log item", item);
      this.status_loading = true;
      this.statusChangeItem = item;
      const newStatus = item.status == "ACTIVE" ? "inactive" : "active";
      this.$refs.reasonRef.fetchAllReasons(null, newStatus);
      this.showReasonDialog = true;
    },

    async changeAdvertisementStatus(reasons) {
      try {
        const id = this.statusChangeItem.id;
        this.updateStatusItemId = id;
        const body = {
          section: "ad",
          item_id: id,
          status: this.statusChangeItem.status,
          reason_ids: reasons,
        };
        const url = `/advertisement/change-status`;
        const { data } = await this.$axios.put(url, body);
        if (data.status == "success") {
          const currentStatus = this.statusChangeItem.status;
          const newStatus = currentStatus == "ACTIVE" ? "INACTIVE" : "ACTIVE";
          if (this.updateStatusItemId == this.statusChangeItem.id) {
            this.statusChangeItem.status = newStatus;
          }
          const message = this.$tr(
            "record_item_status_changed",
            this.$tr(data.type)
          );
          this.$toasterNA("green", this.$tr(message));
          this.showReasonDialog = false;
        } else {
          this.$toasterNA("red", this.$tr(data.status));
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
      this.updateStatusItemId = null;
    },
  },
};
</script>
<style scoped>
.custom-title {
  font-size: 16px !important;
  font-weight: 500 !important;
  text-transform: uppercase;
  color: #777 !important;
}

.custom-chip {
  padding: 1px 5px;
  border-radius: 20px;
  border: solid 1.5px rgb(212, 212, 212);
  cursor: pointer;
}

.custom-chip:hover {
}

.label-color {
  display: inline-block;
  width: 21px;
  height: 21px;

  border-radius: 50px;
}
</style>
<style>
.custom-header {
  font-size: 12px;
  cursor: pointer;
  padding: 5px 2px;
}

.graph-custom-header-wrapper {
  padding: 10px !important;
  background: white;
  position: sticky;
  top: 0px;
  z-index: 5;
  box-shadow: 0 6px 6px -6px rgba(0, 0, 0, 0.3);
}

.header-wrapper-divider {
  padding: 10px !important;
  background: white;
  position: sticky;
  top: 0px;
  z-index: 5;
  border-bottom: 3px solid #777 !important;
  margin-bottom: 20px !important;
}

.v-data-table .custom-header .item,
.v-data-table .custom-header .item .v-icon {
  color: rgba(0, 0, 0, 0.55) !important;
}

.theme--dark.v-data-table .custom-header .item,
.theme--dark.v-data-table .custom-header .item .v-icon {
  color: rgba(255, 255, 255, 0.6) !important;
}

.v-data-table .custom-header .item.sorted,
.v-data-table .custom-header .item.sorted .v-icon {
  color: rgba(0, 0, 0, 1) !important;
}

.theme--dark.v-data-table .custom-header .item.sorted,
.theme--dark.v-data-table .custom-header .item.sorted .v-icon {
  color: rgba(255, 255, 255, 1) !important;
}
</style>

<style>
.graph-table
  .v-data-table--fixed-height
  .v-data-table__wrapper::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}

.graph-table .v-data-table__wrapper::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}

.graph-table .v-data-table__wrapper::-webkit-scrollbar {
  width: 8px !important;
  background-color: var(--v-surface-base) !important;
}

.graph-table .v-data-table__wrapper::-webkit-scrollbar:hover {
  cursor: alias !important;
}

.graph-table .v-data-table__wrapper::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

.graph-table .v-data-table__wrapper::-webkit-scrollbar-thumb {
  border-radius: 8px !important;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-background-darken2) !important;
}
</style>
