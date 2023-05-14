export function setTriggers(triggers) {
	return {
		type: 'SET_TRIGGERS',
		triggers,
	};
}
export function getTriggers(path) {
	return {
		type: 'GET_TRIGGERS',
		path,
	};
}
export function setNumbers(numbers) {
	return {
		type: 'SET_NUMBERS',
		numbers,
	};
}
export function getNumbers(path) {
	return {
		type: 'GET_NUMBERS',
		path,
	};
}
export function clearNumbers() {
	return {
		type: 'CLEAR_NUMBERS',
	};
}
export function setActions(actions) {
	return {
		type: 'SET_ACTIONS',
		actions,
	};
}
export function getActions(path) {
	return {
		type: 'GET_ACTIONS',
		path,
	};
}
export function setLists(lists) {
	return {
		type: 'SET_LISTS',
		lists,
	};
}
export function getLists(path) {
	return {
		type: 'GET_LISTS',
		path,
	};
}
export function setTags(tags) {
	return {
		type: 'SET_TAGS',
		tags,
	};
}
export function getTags(path) {
	return {
		type: 'GET_TAGS',
		path,
	};
}
export function setFields(fields) {
	return {
		type: 'SET_FIELDS',
		fields,
	};
}
export function getFields(path) {
	return {
		type: 'GET_FIELDS',
		path,
	};
}
export function setCampaigns(campaigns) {
	return {
		type: 'SET_CAMPAIGNS',
		campaigns,
	};
}
export function getCampaigns(path) {
	return {
		type: 'GET_CAMPAIGNS',
		path,
	};
}
export function setCampaignStats(stats, campaign) {
	return {
		type: 'SET_CAMPAIGN_STATS',
		campaign,
		stats,
	};
}
export function getCampaignStats(path, campaign) {
	return {
		type: 'GET_CAMPAIGN_STATS',
		campaign,
		path,
	};
}
export function setForms(forms) {
	return {
		type: 'SET_FORMS',
		forms,
	};
}
export function getForms(path) {
	return {
		type: 'GET_FORMS',
		path,
	};
}
export function setEmails(emails) {
	return {
		type: 'SET_EMAILS',
		emails,
	};
}
export function getEmails(path) {
	return {
		type: 'GET_EMAILS',
		path,
	};
}
