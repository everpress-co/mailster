import apiFetch from '@wordpress/api-fetch';

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
