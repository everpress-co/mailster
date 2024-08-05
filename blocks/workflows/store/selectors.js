export function getTriggers(state) {
	const { triggers } = state;
	return triggers;
}
export function getNumbers(state) {
	const { numbers } = state;
	return numbers;
}
export function getQueue(state) {
	const { queue } = state;
	return queue;
}
export function getActions(state) {
	const { actions } = state;
	return actions;
}
export function getLists(state) {
	const { lists } = state;
	return lists;
}
export function getTags(state) {
	const { tags } = state;
	return tags;
}
export function getFields(state) {
	const { fields } = state;
	return fields;
}
export function getCampaigns(state) {
	const { campaigns } = state;
	return campaigns;
}
export function getCampaignStats(state, campaign) {
	const { stats } = state;
	return stats[campaign] || [];
}
export function getForms(state) {
	const { forms } = state;
	return forms;
}
export function getEmails(state) {
	const { emails } = state;
	return emails;
}
