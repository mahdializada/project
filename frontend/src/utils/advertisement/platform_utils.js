const parseCampaigns = (items) => {
  let parsedItems = [];
  for (let index = 0; index < items.length; index++) {
    const element = items[index];
    if (element.platform_name == "tiktok") {
      let campaigns = element.campaigns.map((item) => ({
        id: item.campaign_id,
        name: item.campaign_name,
        status: item.status,
        objective: item.objective,
        delivery_status: item.effective_status,
        updated_at: item.modify_time,
        start_time: item.create_time,
      }));
      parsedItems = [...parsedItems, ...campaigns];
    } else if (element.platform_name == "google ads") {
      let campaigns = element.campaigns.map(({ campaign }) => ({
        id: campaign.id,
        name: campaign.name,
        status: campaign.status,
        objective: null,
        delivery_status: campaign.effective_status,
        updated_at: campaign.endDate,
        start_time: campaign.startDate,
      }));
      parsedItems = [...parsedItems, ...campaigns];
    } else if (element.platform_name == "snapchat") {
      let campaigns = element.campaigns.map((campaign) => ({
        id: campaign.id,
        name: campaign.name,
        status: campaign.status,
        objective: campaign.objective,
        delivery_status: campaign.effective_status,
        updated_at: campaign.updated_at,
        start_time: campaign.start_time,
      }));
      parsedItems = [...parsedItems, ...campaigns];
    } else if (element.platform_name == "facebook") {
      let campaigns = element.campaigns.map(({ campaign }) => ({
        id: campaign.id,
        name: campaign.name,
        status: campaign.status,
        objective: campaign.objective,
        delivery_status: campaign.effective_status,
        updated_at: campaign.updated_time,
        start_time: campaign.start_time,
      }));
      parsedItems = [...parsedItems, ...campaigns];
    }
  }
  parsedItems = parsedItems.filter(
    (value, index, self) => index === self.findIndex((t) => t.id === value.id)
  );
  return parsedItems;
};

export const parseTabItems = (tab, items) => {
  switch (tab) {
    case "campaign":
      return parseCampaigns(items);
      break;
    default:
      return items;
  }
};
