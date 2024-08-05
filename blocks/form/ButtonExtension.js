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
	RangeControl,
	SelectControl,
	__experimentalBoxControl as BoxControl,
	__experimentalToolsPanelItem as ToolsPanelItem,
	__experimentalToolsPanel as ToolsPanel,
} from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */

import FormSelector from './FormSelector';

const BlockEdit = (props) => {
	const { attributes, setAttributes, clientId } = props;
	const { id, identifier, width, padding, animation } = attributes.mailster;

	const setOption = (options) => {
		setAttributes({ mailster: { ...attributes.mailster, ...options } });
	};

	useEffect(() => {
		setOption({
			identifier: !id ? undefined : identifier ?? clientId.substring(30),
		});
		if (!id) resetAll();
	}, [id]);

	const resetAll = () => {
		setAttributes({ mailster: {} });
	};

	return (
		<InspectorControls>
			<ToolsPanel
				label={__('Mailster Form', 'mailster')}
				resetAll={resetAll}
				shouldRenderPlaceholderItems={true}
			>
				<ToolsPanelItem
					hasValue={() => !!id}
					label={__('Form', 'mailster')}
					isShownByDefault
					onDeselect={() => setOption({ id: undefined })}
				>
					<p>
						{__(
							'Open a Newsletter popup if someone clicks the button.',
							'mailster'
						)}
					</p>
					<FormSelector
						{...props}
						selectForm={(val) => setOption({ id: val ? val : undefined })}
						label={__('Select a Form', 'mailster')}
						help={__(
							'Select the form you like to open as a popup if someone clicks on the button.',
							'mailster'
						)}
						formId={id}
					/>
				</ToolsPanelItem>
				<ToolsPanelItem
					hasValue={() => !!width}
					label={__('Width')}
					onDeselect={() => setOption({ width: undefined })}
				>
					<RangeControl
						className="widefat"
						label={__('Form Width', 'mailster')}
						help={__('Set the with of your form in %', 'mailster')}
						value={width}
						allowReset={true}
						onChange={(val) => setOption({ width: val })}
						min={10}
						max={100}
					/>
				</ToolsPanelItem>
				<ToolsPanelItem
					hasValue={() => !!padding}
					label={__('Padding')}
					onDeselect={() => setOption({ padding: undefined })}
				>
					<BoxControl
						label={__('Padding')}
						onChange={(val) => setOption({ padding: val })}
						values={padding}
						allowReset={false}
					/>
				</ToolsPanelItem>
				<ToolsPanelItem
					hasValue={() => !!animation}
					label={__('Animation')}
					onDeselect={() => setOption({ animation: undefined })}
				>
					<SelectControl
						label={__('Animation', 'mailster')}
						value={animation}
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
						<option value="heartbeat">{__('Heart Beat', 'mailster')}</option>
						<option value="tada">{__('Tada', 'mailster')}</option>
						<option value="wobble">{__('Wobble', 'mailster')}</option>
					</SelectControl>
				</ToolsPanelItem>
			</ToolsPanel>
		</InspectorControls>
	);
};

const generateClassName = (attributes) => {
	const { identifier } = attributes.mailster;
	let string = '';
	if (identifier) {
		string += `has-mailster-form has-mailster-form-${identifier}`;
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
