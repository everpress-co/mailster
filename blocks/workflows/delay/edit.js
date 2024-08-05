/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';

import { useEffect } from '@wordpress/element';
import { CardBody, Icon } from '@wordpress/components';

/**
 * Internal dependencies
 */
import InspectorControls from './inspector.js';
import Step from '../inspector/Step.js';
import StepIcon from './Icon.js';

import { getInfo, getLabel, isRelative } from './functions.js';

export default function Edit(props) {
	const { attributes, setAttributes } = props;
	const { amount, unit, date, month, timezone } = attributes;
	const className = [];

	useEffect(() => {
		!amount && setAttributes({ amount: 1 });
		!unit && setAttributes({ unit: 'hours' });
		!month && setAttributes({ month: 1 });
		!date && setAttributes({ date: new Date() });
	});

	const info = getInfo(attributes);
	const label = getLabel(attributes);

	return (
		<Step
			{...props}
			className={className}
			inspectorControls={<InspectorControls {...props} />}
		>
			<CardBody size="small">
				<div className="mailster-step-label">
					<Icon icon={StepIcon} />
					{label}
				</div>
				{info && <div className="mailster-step-info">{info}</div>}
				{!isRelative() && timezone && (
					<div className="mailster-step-info">
						<br />
						{__('Respect users timezone', 'mailster')}
					</div>
				)}
			</CardBody>
		</Step>
	);
}
