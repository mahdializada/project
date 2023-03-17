import tableIcons from "~/configs/menus/advertisementsTableIcons";
const utils = (appContext) => {
  return {
    fileExtension:
      "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    excelIcon: `<svg viewBox="0 0 25.61 32.001" >
        <path
            id="Path_1136"
            data-name="Path 1136"
            d="M576.4-4716c-.424,0-.849-.007-1.272-.006-3.92,0-7.84.008-11.761,0a4.088,4.088,0,0,1-4.315-3.444,4.538,4.538,0,0,1-.051-.845q0-5.638,0-11.276,0-5.667,0-11.335a4.056,4.056,0,0,1,3.069-4.007,2.833,2.833,0,0,1,.715-.079c4.062,0,8.124-.013,12.185.016a1.535,1.535,0,0,1,.989.416q3.649,3.764,7.235,7.59a1.636,1.636,0,0,1,.4,1.018q.036,8.879.012,17.762a4.029,4.029,0,0,1-3.935,4.158c-.68.034-1.362.043-2.044.043C577.222-4715.994,576.812-4716,576.4-4716Zm2.64-1.489a2.833,2.833,0,0,0,3.072-3.046c-.057-5.436-.019-10.874-.02-16.31,0-.214-.019-.429-.032-.695-1.439,0-2.831.013-4.22-.008a4.013,4.013,0,0,1-1.185-.186,3.265,3.265,0,0,1-2.217-3.271c-.016-1.492,0-2.982,0-4.545H573.8q-5.214,0-10.428,0a2.625,2.625,0,0,0-2.878,2.847q-.016,11.156,0,22.312a2.636,2.636,0,0,0,2.912,2.875q2.227,0,4.452-.007,2.093-.005,4.187-.006C574.374-4717.53,576.708-4717.521,579.042-4717.49Zm-3.072-26.541c0,1.009-.008,2.017,0,3.025a1.8,1.8,0,0,0,1.934,1.981c.907.023,1.814.007,2.722,0a1.938,1.938,0,0,0,.373-.089c-1.673-1.757-3.291-3.458-4.988-5.241C575.983-4744.149,575.97-4744.09,575.97-4744.03Zm-3.36,22.417a1.632,1.632,0,0,1-.653-.512,1.269,1.269,0,0,1-.255-.723h.944a.644.644,0,0,0,.268.468.974.974,0,0,0,.612.187,1.03,1.03,0,0,0,.6-.148.448.448,0,0,0,.212-.38.39.39,0,0,0-.236-.368,4.422,4.422,0,0,0-.748-.263,7.219,7.219,0,0,1-.808-.265,1.417,1.417,0,0,1-.541-.392,1.031,1.031,0,0,1-.228-.7,1.083,1.083,0,0,1,.208-.645,1.383,1.383,0,0,1,.6-.46,2.225,2.225,0,0,1,.891-.168,1.842,1.842,0,0,1,1.213.38,1.367,1.367,0,0,1,.492,1.037h-.913a.65.65,0,0,0-.239-.473.9.9,0,0,0-.584-.175.95.95,0,0,0-.553.136.422.422,0,0,0-.192.36.391.391,0,0,0,.129.3.909.909,0,0,0,.311.187c.123.045.3.1.544.173a5.773,5.773,0,0,1,.789.26,1.456,1.456,0,0,1,.531.388,1.03,1.03,0,0,1,.232.679,1.132,1.132,0,0,1-.208.672,1.379,1.379,0,0,1-.587.465,2.2,2.2,0,0,1-.892.168A2.222,2.222,0,0,1,572.61-4721.613Zm-8.748,0a.569.569,0,0,1-.168-.417.567.567,0,0,1,.168-.416.565.565,0,0,1,.416-.168.555.555,0,0,1,.407.168.567.567,0,0,1,.168.416.569.569,0,0,1-.168.417.555.555,0,0,1-.407.168A.565.565,0,0,1,563.862-4721.608Zm14.632.112-.952-1.5-.9,1.5h-.952l1.425-2.168-1.425-2.241h1.033l.952,1.5.9-1.5h.952l-1.424,2.177,1.424,2.232Zm-8.665,0v-5.921h.913v5.921Zm-1.712,0-.952-1.5-.9,1.5h-.952l1.424-2.168-1.424-2.241h1.032l.952,1.5.9-1.5h.952l-1.424,2.177,1.424,2.232Z"
            transform="translate(-558.501 4747.496)"
            fill="#2e7be6"
            stroke="rgba(0,0,0,0)"
            stroke-width="1"
        />
    </svg>`,
    error_headers: [
      {title: appContext.$tr("row_or_line"), value: "row"},
      {title: appContext.$tr("column"), value: "column"},
      {title: appContext.$tr("descriptions"), value: "descriptions"},
    ],
    campaign_headers: [
      { text: appContext.$tr("Campaign ID"), value: "campaign_id" },
      { text: appContext.$tr("Name"), value: "name" },
      { text: appContext.$tr("Objective Type"), value: "objective_type" },
      { text: appContext.$tr("Budget Mode"), value: "budget_mode" },
      { text: appContext.$tr("Budget"), value: "budget" },
      { text: appContext.$tr("Campaign Type"), value: "campaign_type" },
      { text: appContext.$tr("Delivery Status"), value: "delivery_status" },
      { text: appContext.$tr("Objective"), value: "objective" },
      { text: appContext.$tr("Start Time"), value: "start_time" },
      { text: appContext.$tr("End Time"), value: "end_time" },
      { text: appContext.$tr("Server Created At"), value: "server_created_at" },
      { text: appContext.$tr("Server Updated At"), value: "server_updated_at" },
    ],
    ad_set_headers: [
      { text: appContext.$tr("Ad Set ID"), value: "ad_set_id" },
      { text: appContext.$tr("Name"), value: "name" },
      { text: appContext.$tr("Campaign ID"), value: "campaign_id" },
      { text: appContext.$tr("Delivery Status"), value: "delivery_status" },
      { text: appContext.$tr("Daily Budget"), value: "daily_budget" },
      { text: appContext.$tr("Currency"), value: "currency" },
      { text: appContext.$tr("Bid"), value: "bid" },
      { text: appContext.$tr("Optimization Goal"), value: "optimization_goal" },
      { text: appContext.$tr("Pixel ID"), value: "pixel_id" },
      { text: appContext.$tr("Start Time"), value: "start_time" },
      { text: appContext.$tr("End Time"), value: "end_time" },
    ],
    ad_headers: [
      { text: appContext.$tr("Ad Name"), value: "ad_name" },
      { text: appContext.$tr("Ad ID"), value: "ad_id" },
      { text: appContext.$tr("Ad Set ID"), value: "ad_set_id" },
      { text: appContext.$tr("Campaign ID"), value: "campaign_id" },
      { text: appContext.$tr("Product Code"), value: "product_code" },
      { text: appContext.$tr("Delivery Status"), value: "delivery_status" },
      { text: appContext.$tr("Result"), value: "result" },
      { text: appContext.$tr("Impressions"), value: "impressions" },
      {
        text: appContext.$tr("Viewable Impressions"),
        value: "viewable_impressions",
      },
      {
        text: appContext.$tr("Two Second Video Views"),
        value: "two_second_video_views",
      },
      {
        text: appContext.$tr("Six Second Video Views"),
        value: "six_second_video_views",
      },
      {
        text: appContext.$tr("Average Screen Time"),
        value: "average_screen_time",
      },
      { text: appContext.$tr("Video Views"), value: "video_views" },
      { text: appContext.$tr("View Completion"), value: "view_completion" },
      { text: appContext.$tr("Spend"), value: "spend" },
      { text: appContext.$tr("Clicks"), value: "clicks" },
      { text: appContext.$tr("Total Page Views"), value: "total_page_views" },
      { text: appContext.$tr("Story Opens"), value: "story_opens" },
      { text: appContext.$tr("Currency"), value: "currency" },
      { text: appContext.$tr("Reach"), value: "reach" },
      {
        text: appContext.$tr("Cost Per Fifteen Sec View"),
        value: "cost_per_fifteen_sec_view",
      },
      { text: appContext.$tr("Frequency"), value: "frequency" },
      { text: appContext.$tr("Optimization Goal"), value: "optimization_goal" },
      { text: appContext.$tr("Start Time"), value: "start_time" },
      { text: appContext.$tr("End Time"), value: "end_time" },
      { text: appContext.$tr("Server Created At"), value: "server_created_at" },
      { text: appContext.$tr("Server Updated At"), value: "server_updated_at" },
    ],
    tabItems: [
      {
        text: "campaign",
        name: "Campaign",
        icon: tableIcons.compaign,
        isSelected: 1,
        key: "campaign",
        selectedItems: [],
        count: 0,
      },
      {
        text: "ad_set",
        name: "Ad_Set",
        icon: tableIcons.ad_set,
        isSelected: 1,
        key: "ad_set",
        selectedItems: [],
        count: 0,
      },
      {
        text: "ad",
        name: "Ad",
        icon: tableIcons.ad,
        isSelected: 1,
        key: "ad",
        selectedItems: [],
        count: 0,
      },
    ],
  };
};

export default utils;
