/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

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
	Button,
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
	Spinner,
	RangeControl,
	SelectControl,
	ToggleControl,
	FocalPointPicker,
	FontSizePicker,
	TabPanel,
	Modal,
	__experimentalBoxControl as BoxControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';
import {
	PanelColorSettings,
	__experimentalColorGradientControl as ColorGradientControl,
} from '@wordpress/block-editor';

import { useDebounce } from '@wordpress/compose';

import {
	Fragment,
	Component,
	useState,
	useEffect,
	useMemo,
} from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { more, external } from '@wordpress/icons';

/**
 * Internal dependencies
 */

function getSelectors() {
	const custom = window.mailster_fields.filter((el) => el.id != 'submit');
	return [
		{
			label: __('General', 'mailster'),
			items: [
				{
					selector: '.mailster-block-form',
					title: __('Form selector', 'mailster'),
				},
				{
					selector: '.mailster-block-form',
					title: __('Form selector', 'mailster'),
				},
				{
					selector: '.mailster-wrapper',
					title: __('Field wrapper', 'mailster'),
				},
				{
					selector: '.mailster-wrapper .input',
					title: __('Input fields', 'mailster'),
				},
				{
					selector: '.mailster-wrapper label.mailster-label',
					title: __('Labels', 'mailster'),
				},
				{
					selector: '.wp-block-mailster-form-outside-wrapper',
					title: __('Outside Wrapper', 'mailster'),
				},
			],
		},
		{
			label: __('Custom Field Wrapper divs', 'mailster'),
			items: custom.map((el) => {
				return {
					selector: '.wp-block-mailster-field-' + el.id,
					title: el.name,
				};
			}),
		},
		{
			label: __('Custom Field Inputs', 'mailster'),
			items: custom.map((el) => {
				return {
					selector: '.wp-block-mailster-field-' + el.id + ' .input',
					title: el.name,
				};
			}),
		},
		{
			label: __('Other', 'mailster'),
			items: [
				{
					selector:
						'.mailster-wrapper-required label.mailster-label::after',
					title: __('Required Asterisk', 'mailster'),
				},
				{
					selector: '.mailster-wrapper .submit-button',
					title: __('Submit Button', 'mailster'),
				},
			],
		},
	];
}

const Wrapper = ({ children, isCSSModal, setCSSModal }) => {
	return isCSSModal ? (
		<Modal
			title={__('Enter your custom CSS', 'mailster')}
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

export const CssPanel = (props) => {
	const { attributes, setAttributes, children } = props;

	if (!attributes) {
		return <Spinner />;
	}

	const { css = {} } = attributes;

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

	const selectors = useMemo(() => {
		return getSelectors();
	}, [window.mailster_fields]);

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
			editorValue = editor.getValue(),
			value =
				editorValue +
				(editorValue ? '\n' : '') +
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
		<>
			<PanelRow>
				<Button
					onClick={() => setCSSModal(true)}
					variant="link"
					iconPosition="right"
					isSmall={true}
					disabled={isCSSModal}
					icon={external}
				>
					{__('Open in Modal Window', 'mailster')}
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
							<TextareaControl
								id="custom-css-textarea"
								help="Enter your custom CSS here. Every declaration will get prefixed to work only for this specific form."
								value={css[tab.name]}
								onChange={(value) =>
									wp.CodeMirror &&
									setCssDebounce(tab.name, name)
								}
							/>
						)}
					</TabPanel>
				</PanelRow>
				<PanelRow>
					<SelectControl
						label={__('Selectors', 'mailster')}
						help="Helps you find the right selector for form elements"
						onChange={addSelector}
					>
						<option value="">
							{__('Choose Selector', 'mailster')}
						</option>
						{selectors.map((group, i) => {
							return (
								<optgroup key={i} label={group.label}>
									{group.items.map((el, j) => {
										return (
											<option key={j} value={el.selector}>
												{el.title}
											</option>
										);
									})}
								</optgroup>
							);
						})}
					</SelectControl>
				</PanelRow>
				{!!children && <>{children}</>}
			</Wrapper>
		</>
	);
};
