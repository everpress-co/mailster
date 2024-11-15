/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';

import { __, sprintf } from '@wordpress/i18n';

import { CardBody, CardFooter, Tooltip, Spinner } from '@wordpress/components';

/**
 * Internal dependencies
 */

import InspectorControls from './inspector';
import Step from '../inspector/Step';
import { getTrigger, getInfo } from './functions';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { trigger, conditions, repeat } = attributes;

	const triggerObj = getTrigger(trigger);

	const label =
		triggerObj === false ? (
			<div className="mailster-step-info">
				{sprintf(__('Trigger %s not found!', 'mailster'), '"' + trigger + '"')}
			</div>
		) : (
			triggerObj?.label || <Spinner />
		);

	const info = getInfo(props);

	return (
		<Step
			{...props}
			isIncomplete={!trigger || triggerObj === false}
			inspectorControls={<InspectorControls {...props} />}
			blockAttributes={{ 'data-or': __('or', 'mailster') }}
		>
			<CardBody>
				{repeat != 1 && (
					<Tooltip
						text={
							repeat == -1
								? __('repeat forever', 'mailster')
								: sprintf(__('repeat %d times', 'mailster'), repeat)
						}
					>
						<div className="mailster-trigger-repeats">
							{repeat == -1 ? '∞' : sprintf('%d ×', repeat)}
						</div>
					</Tooltip>
				)}
				{trigger && <div className="mailster-step-label">{label}</div>}
				{info && <div className="mailster-step-info">{info}</div>}
			</CardBody>
			{trigger && conditions && (
				<CardBody>
					<div className="mailster-step-info conditions">
						<strong>{__('only when', 'mailster')}</strong>
						<ServerSideRender
							block="mailster-workflow/conditions"
							attributes={{
								...attributes,
								...{ render: true, plain: true },
							}}
							EmptyResponsePlaceholder={() => <Spinner />}
						/>
					</div>
				</CardBody>
			)}
			{trigger && false && (
				<CardFooter>
					<div className="mailster-step-info">{trigger}</div>
				</CardFooter>
			)}
		</Step>
	);
}
