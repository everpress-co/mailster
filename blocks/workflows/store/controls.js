import apiFetch from '@wordpress/api-fetch';
import { searchBlocks } from '../../util';

export function GET_TRIGGERS(action) {
	return apiFetch({ path: action.path });
}
export function GET_NUMBERS(action) {
	return apiFetch({ path: action.path });
}
export function GET_ACTIONS(action) {
	return apiFetch({ path: action.path });
}
export function GET_LISTS(action) {
	return apiFetch({ path: action.path });
}
export function GET_TAGS(action) {
	return apiFetch({ path: action.path });
}
export function GET_FIELDS(action) {
	return apiFetch({ path: action.path });
}
export function GET_CAMPAIGNS(action) {
	return apiFetch({ path: action.path });
}
export function GET_CAMPAIGN_STATS(action) {
	return apiFetch({ path: action.path });
}
export function GET_FORMS(action) {
	return apiFetch({ path: action.path });
}
export function GET_EMAILS(action) {
	const emails2 = searchBlocks('mailster-workflow/email').map((step) => {
		return {
			name: step.attributes.name,
			campaign: step.attributes.campaign,
			id: step.attributes.id,
		};
	});
	var arrResult = [];
	emails2.forEach(function (value) {
		arrResult[value.id] = value;
	});

	const emails = searchBlocks('mailster-workflow/email').map((step) => {
		return {
			name: step.attributes.name,
			campaign: step.attributes.campaign,
			id: step.attributes.id,
		};
	});

	console.log(emails);
	return emails;
}
