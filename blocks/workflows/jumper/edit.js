/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import { CardBody, Icon, Spinner } from '@wordpress/components';

/**
 * Internal dependencies
 */
import InspectorControls from './inspector.js';
import StepIcon from './Icon.js';
import Step from '../inspector/Step.js';
import { useWindow } from '../../util/index.js';

export default function Edit(props) {
	const { attributes, isSelected } = props;
	const { conditions, step } = attributes;

	const label = sprintf(__('Jump to #%s', 'mailster'), step || '…');

	const window = useWindow();

	useEffect(() => {
		if (!step || !window) return;
		const block = window.document.querySelector('.mailster-step-' + step);

		if (!block) return;

		isSelected
			? block.classList.add('is-jumper')
			: block.classList.remove('is-jumper');
	}, [window, step, isSelected]);

	return (
		<Step
			{...props}
			isIncomplete={!step}
			inspectorControls={<InspectorControls {...props} />}
		>
			<CardBody size="small">
				<div className="mailster-step-label">
					<Icon icon={StepIcon} />
					{label}
				</div>
				{conditions && (
					<div className="mailster-step-info">
						{__('only if', 'mailster')}
						<ServerSideRender
							block="mailster-workflow/conditions"
							attributes={{
								...attributes,
								...{ render: true, plain: true },
							}}
							EmptyResponsePlaceholder={() => <Spinner />}
						/>
						{__('otherwise continue', 'mailster')}
					</div>
				)}
			</CardBody>
		</Step>
	);
}
