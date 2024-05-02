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
	__experimentalSpacer as Spacer,
	Flex,
	FlexBlock,
	FlexItem,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { useState, useEffect } from '@wordpress/element';
import { Icon, aspectRatio } from '@wordpress/icons';
/**
 * Internal dependencies
 */

export default function Selector(props) {
	const { attributes, setAttributes, clientId } = props;
	const { hook } = attributes;

	const [isVisible, setIsVisible] = useState(false);
	const [hasCopied, setHasCopied] = useState(false);

	const toggleVisible = () => {
		setIsVisible((state) => !state);
	};

	useEffect(() => {
		if (!hook) setHook('my_custom_hook_' + clientId.substring(30));
	}, []);

	const codesnippet = sprintf(
		"mailster_trigger( '%s', $subscriber_id );",
		hook
	);

	const setHook = (value) => {
		setAttributes({ hook: value ? value : undefined });
	};

	return (
		<BaseControl
			label={__('Hook', 'mailster')}
			help={__('Define your hook you like to trigger', 'mailster')}
		>
			<TextControl value={hook || ''} onChange={(val) => setHook(val)} />
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
							<Spacer paddingY={2}>
								<Flex>
									<pre>{codesnippet}</pre>
									<ClipboardButton
										icon={aspectRatio}
										isSmall
										variant="secondary"
										text={codesnippet}
										onCopy={() => setHasCopied(true)}
										onFinishCopy={() => setHasCopied(false)}
									>
										{hasCopied ? 'Copied!' : 'Copy Text'}
									</ClipboardButton>
								</Flex>
							</Spacer>
						</BaseControl>
					</ConfirmDialog>
				</Flex>
			)}
		</BaseControl>
	);
}
