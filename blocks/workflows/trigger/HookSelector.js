/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import {
	Button,
	TextControl,
	BaseControl,
	ClipboardButton,
	Card,
	CardBody,
	CardHeader,
	CardFooter,
	__experimentalConfirmDialog as ConfirmDialog,
	Flex,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';
import { useState, useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */

export default function Selector(props) {
	const { attributes, setAttributes } = props;
	const { hook = '' } = attributes;

	const [isVisible, setIsVisible] = useState(false);
	const [hasCopied, setHasCopied] = useState(false);
	const toggleVisible = () => {
		setIsVisible((state) => !state);
	};

	const codesnippet = sprintf(
		"mailster_trigger( '%s', $subscriber_id );",
		hook
	);

	const setHook = (value) => {
		value = value.toLowerCase();
		value = value.replace(' ', '_');
		value = value.replace(/[^a-z0-9_-]/, '');

		setAttributes({ hook: value ? value : undefined });
	};

	return (
		<BaseControl
			label={__('Hook', 'mailster')}
			help={__('Define your hook you like to trigger', 'mailster')}
		>
			<TextControl value={hook} onChange={(val) => setHook(val)} />
			{hook && (
				<Flex direction="row" justify="flex-end">
					<Button variant="link" onClick={toggleVisible}>
						{__('How to implement?', 'mailster')}
					</Button>

					<ConfirmDialog
						isOpen={isVisible}
						onConfirm={toggleVisible}
						onCancel={toggleVisible}
					>
						<h3>
							{__('Trigger this workflow by executing some code.', 'mailster')}
						</h3>
						<BaseControl
							help={__(
								'You can use it in your theme or in a plugin. Make sure you define the subscriber ID to trigger this workflow for the right subscriber.',
								'mailster'
							)}
						>
							<pre>{codesnippet}</pre>
							<ClipboardButton
								variant="link"
								text={codesnippet}
								onCopy={() => setHasCopied(true)}
								onFinishCopy={() => setHasCopied(false)}
							>
								{hasCopied ? 'Copied!' : 'Copy Text'}
							</ClipboardButton>
						</BaseControl>
					</ConfirmDialog>
				</Flex>
			)}
		</BaseControl>
	);
}
