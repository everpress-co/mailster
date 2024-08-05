/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelRow, PanelBody } from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function StepId(props) {
	const { attributes } = props;
	const { id } = attributes;

	if (!id) return;

	return (
		<>
			<span
				className="mailster-step-id"
				title={sprintf(__('Step ID : %s', 'mailster'), id)}
			>
				{id}
			</span>
			<InspectorControls>
				<Panel>
					<PanelBody>
						<PanelRow className="code">
							{sprintf(__('Step ID : %s', 'mailster'), id)}
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</>
	);
}
