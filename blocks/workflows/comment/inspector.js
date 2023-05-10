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
	__experimentalItemGroup as ItemGroup,
	TextareaControl,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import * as Icons from '@wordpress/icons';

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
