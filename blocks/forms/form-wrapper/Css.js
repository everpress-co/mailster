/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	useBlockProps,
	InspectorControls,
	RichText,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
	ColorPaletteControl,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextareaControl,
	Card,
	CardBody,
	CardMedia,
	Button,
	RangeControl,
	FocalPointPicker,
	SelectControl,
	ComboboxControl,
	TabPanel,
	Modal,
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { useDebounce } from '@wordpress/compose';

import { more, external } from '@wordpress/icons';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
const Wrapper = ({ children, isCSSModal, setCSSModal }) => {
	return isCSSModal ? (
		<Modal
			title="Please select a form to start with"
			className="css-modal"
			onRequestClose={() => setCSSModal(false)}
			style={{
				width: '80vw',
				marginBottom: '5%',
			}}
		>
			{children}
		</Modal>
	) : (
		children
	);
};
export default function Css(props) {
	const { attributes, setAttributes, isSelected } = props;

	const { css } = attributes;

	const tabs = {
		general: __('General', 'mailster'),
		tablet: __('Tablet', 'mailster'),
		mobile: __('Mobile', 'mailster'),
	};

	let codeEditor;

	function setCss(name, data) {
		var newCss = { ...css };
		newCss[name] = data;
		setAttributes({ css: newCss });
	}

	const setCssDebounce = useDebounce(setCss, 500);

	const initCodeMirror = (isOpened, name) => {
		const placeholder = '/* Style for ' + tabs[name] + ' /*';
		if (!isOpened || !wp.CodeMirror) return;

		setTimeout(() => {
			if (!document || document.querySelector('.CodeMirror')) return;

			const settings = {
				...wp.codeEditor.defaultSettings.codemirror,
				...{
					autofocus: true,
					placeholder: placeholder,
				},
			};

			codeEditor = wp.CodeMirror.fromTextArea(
				document.getElementById('custom-css-textarea'),
				settings
			).on('change', function (editor) {
				setCssDebounce(name, editor.getValue());
			});
		}, 0);

		return;
	};

	const addSelector = (selector) => {
		if (!selector) return;

		const editor = document
				.querySelector('.custom-css-tabs')
				.querySelector('.CodeMirror').CodeMirror,
			placeholder = '/*' + __('Your CSS Rules', 'mailster') + '*/',
			value =
				editor.getValue() +
				selector +
				'{\n    ' +
				placeholder +
				'\n}\n',
			lines = value.match(/(\n)/g).length;

		editor.setValue(value);
		editor.focus();
		editor.setSelection(
			{ line: lines - 2, ch: 4 },
			{ line: lines - 2, ch: placeholder.length + 4 }
		);
	};

	const [isCSSModal, setCSSModal] = useState(false);

	useEffect(() => {
		initCodeMirror(true, 'general');
	}, [isCSSModal]);

	return (
		<PanelBody
			name="css"
			title="Custom CSS"
			initialOpen={false}
			onToggle={(isOpen) => {
				initCodeMirror(isOpen, 'general');
			}}
		>
			<PanelRow>
				<Button
					onClick={() => setCSSModal(true)}
					variant="link"
					iconPosition="right"
					isSmall={true}
					disabled={isCSSModal}
					icon={external}
				>
					Open in Modal Window
				</Button>
			</PanelRow>
			<Wrapper isCSSModal={isCSSModal} setCSSModal={setCSSModal}>
				<PanelRow>
					<TabPanel
						className="custom-css-tabs"
						activeClass="is-active"
						orientation="horizontal"
						initialTabName="general"
						onSelect={(tabName) => {
							initCodeMirror(true, tabName);
						}}
						tabs={Object.keys(tabs).map((tab) => {
							return {
								name: tab,
								title: tabs[tab],
							};
						})}
					>
						{(tab) => (
							<>
								<TextareaControl
									id="custom-css-textarea"
									help="Enter your custom CSS here. Every declaration will get prefixed to work only for this specific form."
									value={css[tab.name]}
									onChange={(value) =>
										wp.CodeMirror &&
										setCssDebounce(tab.name, name)
									}
								/>
							</>
						)}
					</TabPanel>
				</PanelRow>
				<PanelRow>
					<SelectControl
						label={__('Choose a Selector:', 'mailster')}
						help="Enter your custom CSS here. Every declaration will get prefixed to work only for this specific form."
						onChange={addSelector}
					>
						<option value="">{__('Choose', 'mailster')}</option>
						<option value=".mailster-block-form">
							{__('Form selector', 'mailster')}
						</option>
						<option value=".mailster-wrapper">
							{__('Field wrapper', 'mailster')}
						</option>
						<option value=".mailster-wrapper .input">
							{__('Input fields', 'mailster')}
						</option>
						<option value=".mailster-wrapper label.mailster-label">
							{__('Labels', 'mailster')}
						</option>
						<optgroup
							label={__('Custom Field Wrapper divs', 'mailster')}
						>
							{[].map((el) => {
								return (
									<option
										value={' .mailster-' + key + '-wrapper'}
									>
										{field}
									</option>
								);
							})}
						</optgroup>
						<optgroup label={__('Custom Field Inputs', 'mailster')}>
							{[].map((el) => {
								return (
									<option
										value={
											' .mailster-' +
											key +
											'-wrapper input.input'
										}
									>
										{field}
									</option>
								);
							})}
						</optgroup>
						<optgroup label={__('Other', 'mailster')}>
							<option value=".mailster-wrapper-required label.mailster-label::after">
								{__('Required Asterisk', 'mailster')}
							</option>
							<option value=".mailster-submit-wrapper .wp-block-button__link">
								{__('Submit Button', 'mailster')}
							</option>
						</optgroup>
					</SelectControl>
				</PanelRow>
			</Wrapper>
		</PanelBody>
	);
}
