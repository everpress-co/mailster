/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { registerBlockExtension } from '@10up/block-components';

import { InspectorControls } from '@wordpress/block-editor';
import {
	BaseControl,
	Panel,
	PanelBody,
	PanelRow,
	RangeControl,
	SelectControl,
	__experimentalBoxControl as BoxControl,
	__experimentalItemGroup as ItemGroup,
} from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */

import FormSelector from './FormSelector';

const BlockEdit = (props) => {
	const { attributes, setAttributes, clientId } = props;
	const { mailster } = attributes;

	const setOption = (options) => {
		setAttributes({ mailster: { ...attributes.mailster, ...options } });
	};

	useEffect(() => {
		setOption({
			identifier: !mailster.id
				? undefined
				: mailster.identifier ?? clientId.substring(30),
		});
	}, [mailster.id]);

	return (
		<InspectorControls>
			<Panel>
				<PanelBody title={__('Mailster Form', 'mailster')}>
					<PanelRow>
						<p>
							{__(
								'Open a Mailster form if someone clicks on this button.',
								'mailster'
							)}
						</p>
					</PanelRow>
					<FormSelector
						{...props}
						selectForm={(val) => setOption({ id: val ? val : undefined })}
						formId={mailster.id}
					/>
					{mailster.id && (
						<>
							<PanelRow>
								<RangeControl
									className="widefat"
									label={__('Form Width', 'mailster')}
									help={__('Set the with of your form in %', 'mailster')}
									value={mailster.width}
									allowReset={true}
									onChange={(val) => setOption({ width: val })}
									min={10}
									max={100}
								/>
							</PanelRow>
							<PanelRow>
								<BoxControl
									label={__('Form Padding', 'mailster')}
									values={mailster.padding}
									onChange={(val) => setOption({ padding: val })}
								/>
							</PanelRow>
							<PanelRow>
								<ItemGroup isBordered={false} size="small" className="widefat">
									<SelectControl
										label={__('Animation', 'mailster')}
										value={mailster.animation}
										help={__(
											'Define how the popup should appear on the screen.',
											'mailster'
										)}
										onChange={(val) => setOption({ animation: val })}
									>
										<option value="">{__('None', 'mailster')}</option>
										<option value="fadein">{__('FadeIn', 'mailster')}</option>
										<option value="shake">{__('Shake', 'mailster')}</option>
										<option value="swing">{__('Swing', 'mailster')}</option>
										<option value="heartbeat">
											{__('Heart Beat', 'mailster')}
										</option>
										<option value="tada">{__('Tada', 'mailster')}</option>
										<option value="wobble">{__('Wobble', 'mailster')}</option>
									</SelectControl>
								</ItemGroup>
							</PanelRow>
						</>
					)}
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
};

const generateClassName = (attributes) => {
	const { mailster } = attributes;
	let string = '';
	if (mailster.identifier) {
		string += `has-mailster-form has-mailster-form-${mailster.identifier}`;
	}
	return string;
};

const Attributes = {
	mailster: {
		type: 'object',
		default: {
			id: undefined,
			identifier: undefined,
			width: undefined,
			padding: undefined,
			animation: undefined,
		},
	},
};

registerBlockExtension(['core/button'], {
	extensionName: 'mailster-form-button',
	attributes: Attributes,
	classNameGenerator: generateClassName,
	inlineStyleGenerator: () => null,
	Edit: BlockEdit,
});
