/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { useSelect, select, dispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import * as actions from './actions';
import { clearData } from '../../util';

export function* getTriggers() {
	const data = yield actions.getTriggers('/mailster/v1/automations/triggers');
	return actions.setTriggers(data);
}
export function* getNumbers() {
	const post_id = select('core/editor').getCurrentPostId();
	const data = yield actions.getNumbers(
		'/mailster/v1/automations/numbers/' + post_id
	);
	return actions.setNumbers(data);
}
export function* getQueue() {
	const post_id = select('core/editor').getCurrentPostId();
	const data = yield actions.getQueue(
		'/mailster/v1/automations/queue/' + post_id
	);
	return actions.setQueue(data);
}

export function* getActions() {
	const data = yield actions.getActions('/mailster/v1/automations/actions');
	return actions.setActions(data);
}
export function* getLists() {
	const data = yield actions.getLists('/mailster/v1/automations/lists');
	return actions.setLists(data);
}
export function* getTags() {
	const data = yield actions.getTags('/mailster/v1/automations/tags');
	return actions.setTags(data);
}
export function* getFields() {
	const data = yield actions.getFields('/mailster/v1/automations/fields');
	return actions.setFields(data);
}
export function* getCampaigns() {
	const data = yield actions.getCampaigns('/mailster/v1/automations/campaigns');
	return actions.setCampaigns(data);
}
export function* getCampaignStats(campaign) {
	if (!campaign) {
		return [];
	}
	const data = yield actions.getCampaignStats(
		'/mailster/v1/automations/stats/' + campaign,
		campaign
	);
	return actions.setCampaignStats(data, campaign);
}
export function* getForms() {
	const data = yield actions.getForms('/mailster/v1/automations/forms');
	return actions.setForms(data);
}
export function* getEmails() {
	const data = yield actions.getEmails();
	return actions.setEmails(data);
}
