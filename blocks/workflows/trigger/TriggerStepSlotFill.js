/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { createSlotFill } from '@wordpress/components';

/**
 * Internal dependencies
 */

const TriggerStepSlotFill = (props) => {
	const { clientId, children, attributes } = props;
	const { trigger } = attributes;

	const id = 'mailster/trigger/step/' + clientId;

	const { Fill, Slot } = createSlotFill(id);

	let Triggerer = () => {
		return <Fill name={id}>{children}</Fill>;
	};

	Triggerer.Slot = Slot;

	return <Triggerer.Slot fillProps={{ ...props, trigger: trigger }} />;
};

export default TriggerStepSlotFill;
