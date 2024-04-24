/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelRow, PanelBody } from '@wordpress/components';

import { useEffect, useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { whenEditorIsReady } from '../../util';

export default function StepId(props) {
	const { attributes, setAttributes, clientId, isSelected } = props;
	const { id } = attributes;

	// TODO run this code only once and not on every render
	useEffect(() => {
		whenEditorIsReady().then((w) => {
			if (!id || w.document.querySelectorAll('.mailster-step-' + id).length > 1)
				setAttributes({ id: clientId.substring(30) });
		});
	});

	useEffect(() => {
		if (!isSelected || !id) return;
		history.replaceState(undefined, undefined, '#step-' + id);

		return () => {
			history.pushState(
				'',
				document.title,
				location.pathname + location.search
			);
		};
	}, [isSelected]);

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
						<PanelRow>{sprintf(__('Step ID : %s', 'mailster'), id)}</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</>
	);
}
