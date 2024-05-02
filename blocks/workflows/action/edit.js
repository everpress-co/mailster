/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';

import { CardBody, Icon } from '@wordpress/components';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

import InspectorControls from './inspector.js';
import Step from '../inspector/Step.js';
import { getAction, getInfo } from './functions.js';

export default function Edit(props) {
	const { attributes } = props;
	const { action } = attributes;

	const actionObj = getAction(action);

	const label = actionObj?.label || <></>;
	const info = getInfo(attributes);
	const icon = actionObj?.icon;

	return (
		<Step
			{...props}
			isIncomplete={!action}
			inspectorControls={<InspectorControls {...props} />}
		>
			<CardBody size="small">
				<div className="mailster-step-label">
					<Icon icon={Icons[icon]} />
					{label}
				</div>
				{info && (
					<div
						className="mailster-step-info"
						dangerouslySetInnerHTML={{ __html: info }}
					/>
				)}
			</CardBody>
		</Step>
	);
}
