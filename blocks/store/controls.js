import apiFetch from '@wordpress/api-fetch';

export function GET_LISTS(action) {
	console.warn('FETCH');
	return apiFetch({ path: action.path });
}
