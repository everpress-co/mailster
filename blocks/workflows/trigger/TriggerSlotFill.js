/**
 * External dependencies
 */

/**
 * XWordPress dependencies
 */

import { createSlotFill } from '@wordpress/components';

/**
 * Internal dependencies
 */

const { Fill, Slot } = createSlotFill('mailster/trigger/selector');

const TriggerSlotFill = ({ children }) => {
	return <Fill name="mailster/trigger/selector">{children}</Fill>;
};

TriggerSlotFill.Slot = Slot;

export default TriggerSlotFill;
