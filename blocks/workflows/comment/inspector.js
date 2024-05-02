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
	TextareaControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function CommentInspectorControls({
	attributes,
	setAttributes,
}) {
	const { comment = '' } = attributes;

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<h3>{__('This is the help message of this step.', 'mailster')}</h3>
					<PanelRow>
						<TextareaControl
							label={__('Comment', 'mailster')}
							help={__(
								'Add a comment to help you understand your workflow',
								'mailster'
							)}
							rows={8}
							value={comment.replace(/<br>/g, '\n')}
							onChange={(val) =>
								setAttributes({ comment: val ? val : undefined })
							}
						/>
					</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
