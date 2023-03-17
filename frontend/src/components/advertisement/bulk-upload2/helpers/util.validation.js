const utilValidation = (param) => {
  return {
    campaign_form: parseCampaignForm(param),
    ad_set_form: parseAdsetForm(param),
    ad_form: parseAdForm(param),
  };
};

const parseAdsetForm = (param) => {
  return {
    ad_set_id: param,
    name: param,
    campaign_id: param,
    delivery_status: param,
    daily_budget: param,
    currency: param,
    bid: param,
    optimization_goal: param,
    pixel_id: param,
    start_time: param,
    end_time: param,
  };
};

const parseAdForm = (param) => {
  return {
    ad_name: param,
    ad_id: param,
    ad_set_id: param,
    campaign_id: param,
    product_code: param,
    delivery_status: param,
    result: param,
    impressions: param,
    viewable_impressions: param,
    two_second_video_views: param,
    six_second_video_views: param,
    average_screen_time: param,
    video_views: param,
    view_completion: param,
    spend: param,
    clicks: param,
    total_page_views: param,
    story_opens: param,
    currency: param,
    reach: param,
    cost_per_fifteen_sec_view: param,
    frequency: param,
    optimization_goal: param,
    start_time: param,
    end_time: param,
    server_created_at: param,
    server_updated_at: param,
  };
};

const parseCampaignForm = (param) => {
  return {
    campaign_id: param,
    name: param,
    objective_type: param,
    budget_mode: param,
    budget: param,
    campaign_type: param,
    delivery_status: param,
    objective: param,
    end_time: param,
    start_time: param,
    server_created_at: param,
    server_updated_at: param,
  };
};

export default utilValidation;
