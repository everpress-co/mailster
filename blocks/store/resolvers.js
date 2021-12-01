/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */

import * as actions from './actions';

export function* getLists() {
	const lists = yield actions.getLists('/mailster/v1/lists/');
	return actions.setLists(lists);
}
