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
	__experimentalSpacer as Spacer,
	Flex,
	Tip,
	Modal,
} from '@wordpress/components';

import { useState, useEffect } from '@wordpress/element';
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
	const codesnippet2 = sprintf(
		"mailster_trigger( '%s', array( $subscriber_id1, $subscriber_id2 ) );",
		hook
	);
	const codesnippet3 = sprintf("mailster_trigger( '%s' );", hook);

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

					{isVisible && (
						<Modal
							title={__(
								'Trigger this workflow by executing some code.',
								'mailster'
							)}
						>
							<Card>
								<CardBody>
									<BaseControl
										label={__('Trigger for a single subscriber', 'mailster')}
									>
										<Flex>
											<pre>
												<code>{codesnippet}</code>
											</pre>
											<ClipboardButton
												isSmall
												variant="secondary"
												text={codesnippet}
												onCopy={() => setHasCopied(codesnippet)}
												onFinishCopy={() => setHasCopied(false)}
											>
												{hasCopied == codesnippet
													? __('Copied!', 'mailster')
													: __('Copy Snippet', 'mailster')}
											</ClipboardButton>
										</Flex>
									</BaseControl>

									<BaseControl
										label={__('Trigger for multiple subscribers', 'mailster')}
									>
										<Flex>
											<pre>
												<code>{codesnippet2}</code>
											</pre>
											<ClipboardButton
												isSmall
												variant="secondary"
												text={codesnippet2}
												onCopy={() => setHasCopied(codesnippet2)}
												onFinishCopy={() => setHasCopied(false)}
											>
												{hasCopied == codesnippet2
													? __('Copied!', 'mailster')
													: __('Copy Snippet', 'mailster')}
											</ClipboardButton>
										</Flex>
									</BaseControl>
									<BaseControl
										label={__('Trigger for all subscribers', 'mailster')}
									>
										<Flex>
											<pre>
												<code>{codesnippet3}</code>
											</pre>
											<ClipboardButton
												isSmall
												variant="secondary"
												text={codesnippet3}
												onCopy={() => setHasCopied(codesnippet3)}
												onFinishCopy={() => setHasCopied(false)}
											>
												{hasCopied == codesnippet3
													? __('Copied!', 'mailster')
													: __('Copy Snippet', 'mailster')}
											</ClipboardButton>
										</Flex>
									</BaseControl>
								</CardBody>
							</Card>
							<Spacer paddingY={1}></Spacer>
							<Tip>
								<p>
									{__(
										'You can use it in your theme or in a plugin. Make sure you define the subscriber ID to trigger this workflow for the right subscriber.',
										'mailster'
									)}
								</p>
								<p>
									{__(
										'Use conditions to further segment your selection.',
										'mailster'
									)}
								</p>
							</Tip>
						</Modal>
					)}
				</Flex>
			)}
		</BaseControl>
	);
}
