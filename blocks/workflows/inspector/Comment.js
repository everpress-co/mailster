/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelRow,
	PanelBody,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function Comment(props) {
	const { attributes, setAttributes, name } = props;
	const { comment = '' } = attributes;

	// no comments on triggers
	if ('mailster-workflow/trigger' === name) return null;

	return (
		<>
			{comment && <div className="mailster-step-comment">{comment}</div>}
			<InspectorControls>
				<Panel>
					<PanelBody>
						<PanelRow>
							<TextControl
								label={__('Comment', 'mailster')}
								help={__(
									'Add a comment to help you understand your workflow.',
									'mailster'
								)}
								value={comment}
								onChange={(val) =>
									setAttributes({ comment: val ? val : undefined })
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</>
	);
}
