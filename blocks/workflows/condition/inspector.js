/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelRow, PanelBody } from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

export default function ConditionInspectorControls(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;

	const fulfilled = name === 'mailster-workflow/condition-yes';

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<PanelRow>
						{fulfilled
							? __(
									'These steps run if the condition is fullfilled.',
									'mailster'
							  )
							: __(
									'These steps run if the condition is not fullfilled.',
									'mailster'
							  )}
					</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
