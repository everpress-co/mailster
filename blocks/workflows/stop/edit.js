/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText } from '@wordpress/block-editor';

import { useEffect } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */

import icon from './Icon';
import StopInspectorControls from './inspector.js';
import Comment from '../inspector/Comment';
import StepId from '../inspector/StepId';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id } = attributes;
	const className = [];

	useEffect(() => {
		if (!id || document.querySelectorAll('.mailster-step-' + id).length > 1)
			setAttributes({ id: clientId.substring(30) });
	});

	id && className.push('mailster-step-' + id);

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	return (
		<>
			<StopInspectorControls {...props} />
			<div {...blockProps}>
				<StepId {...props} />
				<Comment {...props} />
				<div className="mailster-stop">{icon}</div>
			</div>
		</>
	);
}
