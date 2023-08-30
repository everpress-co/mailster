/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelRow, PanelBody } from '@wordpress/components';

import { useEffect } from '@wordpress/element';
import { useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { whenEditorIsReady } from '../../util';

export default function StepId(props) {
	const { attributes, setAttributes, clientId, isSelected } = props;
	const { id } = attributes;

	useEffect(() => {
		whenEditorIsReady().then((w) => {
			if (!id || w.document.querySelectorAll('.mailster-step-' + id).length > 1)
				setAttributes({ id: clientId.substring(30) });
		});
	}, []);

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
		<InspectorControls>
			<Panel>
				<PanelBody>
					<PanelRow>{sprintf('Step ID : %s', id)}</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
