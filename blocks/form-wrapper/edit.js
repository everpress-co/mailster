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
	InnerBlocks,
	useBlockProps,
	InspectorControls,
	RichText,
	PlainText,
	VisuallyHidden,
	BlockControls,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalGetSpacingClassesAndStyles as useSpacingProps,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	ResizableBox,
	Snackbar,
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';

import { brush } from '@wordpress/icons';

import { select, dispatch, subscribe } from '@wordpress/data';
import {
	Fragment,
	Component,
	useState,
	useEffect,
	useMemo,
} from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import Styling from './Styling';
import Messages from './Messages';
import Background from './Background';
import BlockRecovery from './BlockRecovery';

import Css from './Css';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit(props) {
	const { attributes, setAttributes, toggleSelection, isSelected, clientId } =
		props;
	const { style, background, inputs, messages } = attributes;
	let { css } = attributes;
	let backgroundStyles = '';

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	const [displayMessages, setDisplayMessages] = useState(false);
	const [inputStyles, setinputStyles] = useState(meta.input_styles);
	const [showClasses, setShowClasses] = useState(false);

	const borderProps = useBorderProps(attributes);
	const colorProps = useColorProps(attributes);
	const spacingProps = useSpacingProps(attributes);

	const className = ['mailster-form', 'mailster-form-' + clientId];

	showClasses && className.push('show-classes');

	const styleSuccessMessage = {
		color: messages.success,
		backgroundColor: messages.successBackground,
	};
	const styleErrorMessage = {
		color: messages.error,
		backgroundColor: messages.errorBackground,
	};

	function setStyle(prop, data) {
		var newStyle = { ...style };
		newStyle[prop] = data;
		setAttributes({ style: newStyle });
	}
	function setBackground(prop, data) {
		var newBackground = { ...background };
		newBackground[prop] = data;
		setAttributes({ background: newBackground });
	}
	function setMessages(prop, data) {
		var newMessages = { ...messages };
		newMessages[prop] = data;
		setAttributes({ messages: newMessages });
	}
	function setCss(css) {
		setAttributes({ css: css });
	}

	function prefixCss(css, className) {
		if (!css) return css;

		var classLen = className.length,
			char,
			nextChar,
			isAt,
			isIn,
			rules = css;

		// removes comments
		rules = rules.replace(/\/\*(?:(?!\*\/)[\s\S])*\*\/|[\r\n\t]+/g, '');

		// makes sure nextChar will not target a space
		rules = rules.replace(/}(\s*)@/g, '}@');
		rules = rules.replace(/}(\s*)}/g, '}}');

		for (var i = 0; i < rules.length - 2; i++) {
			char = rules[i];
			nextChar = rules[i + 1];

			if (char === '@' && nextChar !== 'f') isAt = true;
			if (!isAt && char === '{') isIn = true;
			if (isIn && char === '}') isIn = false;

			if (
				!isIn &&
				nextChar !== '@' &&
				nextChar !== '}' &&
				(char === '}' ||
					char === ',' ||
					((char === '{' || char === ';') && isAt))
			) {
				rules =
					rules.slice(0, i + 1) +
					className +
					' ' +
					rules.slice(i + 1);
				i += classLen;
				isAt = false;
			}
		}

		// prefix the first select if it is not `@media` and if it is not yet prefixed
		if (rules.indexOf(className) !== 0 && rules.indexOf('@') !== 0) {
			rules = className + ' ' + rules;
		}

		//make sure the root element is not prefixed
		rules = rules.replaceAll(
			className + ' .mailster-form',
			className + '.mailster-form'
		);

		return rules;
	}

	css += '';

	const mediaPosition = ({ x, y }) => {
		return `${Math.round(x * 100)}% ${Math.round(y * 100)}%`;
	};

	if (background.image) {
		backgroundStyles += '.mailster-form::before{';
		backgroundStyles += "content:'';";
		backgroundStyles += 'background-image: url(' + background.image + ');';
		if (background.fixed)
			backgroundStyles += 'background-attachment:fixed;';
		if (background.repeat) backgroundStyles += 'background-repeat:repeat;';
		backgroundStyles += 'background-size:' + background.size + ';';
		backgroundStyles +=
			'background-position:' + mediaPosition(background.position) + ';';
		backgroundStyles += 'opacity:' + background.opacity + '%;';
		backgroundStyles += '}';
	}

	const getInputStyles = () => {
		const iframe = document.getElementById('inputStylesIframe');

		if (!iframe) return;

		const doc = iframe.contentWindow.document,
			//get a couple of possible DOM elements
			el =
				doc.getElementsByClassName('entry-content')[0] ||
				doc.getElementById('page') ||
				doc.getElementById('site-content') ||
				doc.getElementById('content') ||
				doc.getElementsByTagName('body')[0],
			properties = [
				'color',
				'padding',
				'border',
				'font',
				'border-radius',
				'background',
				'box-shadow',
				'line-height',
				'appearance',
				'outline',
				'text-transform',
				'letter-spacing',
			],
			selectors = {
				'input[type="text"]': [],
				'input[type="email"]': [],
				'input[type="date"]': [],
				'input[type="checkbox"]': ['width', 'height'],
				'input[type="radio"]': ['width', 'height'],
				select: [],
				'label.mailster-label': [],
			};

		wp.element.render(
			<form className="mailster-form">
				<label className="mailster-label">This is my Label</label>
				<select className="input">
					<option>This is a select</option>
				</select>
				<input type="checkbox" />
				<input type="radio" />
				<input type="text" className="input" />
				<input type="email" className="input" />
				<input type="date" className="input " />
			</form>,
			el
		);

		const styles = Object.keys(selectors)
			.map((selector, i) => {
				const style = getStyles(
					doc.querySelector('.mailster-form ' + selector),
					[...properties, ...selectors[selector]]
				);
				return '.mailster-form ' + selector + '{' + style + '}' + '\n';
			})
			.join('');

		if (styles != inputStyles) {
			setMeta({ input_styles: styles });
			setinputStyles(styles);
			dispatch('core/notices').createNotice(
				'success',
				__('Input field styles have been updated.', 'mailster'),
				{
					type: 'snackbar',
					isDismissible: true,
				}
			);
		}
	};

	const convertRestArgsIntoStylesArr = ([...args]) => {
		return args.slice(1);
	};
	const getStyles = function () {
		const args = [...arguments];
		const [element] = args;
		let s = '';

		if (!element) return s;

		const stylesProps =
			[...args][1] instanceof Array
				? args[1]
				: convertRestArgsIntoStylesArr(args);

		const styles = window.getComputedStyle(element);
		stylesProps.reduce((acc, v) => {
			const x = styles.getPropertyValue(v);
			if (x) s += v + ': ' + x + ';';
		}, {});

		return s;
	};

	const prefixedCss = useMemo(() => {
		return prefixCss(css, '.mailster-form-' + clientId);
	}, [css]);

	useEffect(() => {
		const all = select('core/block-editor').getBlocks(clientId);
		const exists = all.filter((block) => {
			return block.name == 'mailster/gdpr';
		});

		if (exists.length && !meta.gdpr) {
			dispatch('core/block-editor').removeBlock(exists[0].clientId);
		} else if (!exists.length && meta.gdpr) {
			const block = wp.blocks.createBlock('mailster/gdpr', {
				content: 'I agree to the privacy policy and terms.',
			});
			dispatch('core/block-editor').insertBlock(
				block,
				all.length,
				clientId
			);
		}
	}, [meta.gdpr]);

	return (
		<>
			<div
				{...useBlockProps({
					className: className.join(' '),
				})}
				style={{
					...borderProps.style,
					...colorProps.style,
					...spacingProps.style,
				}}
				data-class=".mailster-form"
			>
				{prefixedCss && (
					<style id="mailster-custom-styles">{prefixedCss}</style>
				)}
				{inputStyles && (
					<style id="mailster-inline-styles">{inputStyles}</style>
				)}
				{backgroundStyles && (
					<style id="mailster-bg-styles">{backgroundStyles}</style>
				)}
				{displayMessages && (
					<div className="mailster-form-info">
						<div
							className="mailster-form-info-success"
							style={styleSuccessMessage}
						>
							{__('This is a success message', 'mailster')}
						</div>
						<div
							className="mailster-form-info-error"
							style={styleErrorMessage}
						>
							{__(
								'Following fields are missing or incorrect. This is an error message',
								'mailster'
							)}
						</div>
					</div>
				)}
				<BlockRecovery {...props} />
				<InnerBlocks />
				{!isSelected && (
					<ToolbarGroup className="style-form">
						<ToolbarButton
							icon={brush}
							label="Style"
							onClick={() => {
								dispatch('core/block-editor').selectBlock(
									clientId
								);
							}}
						>
							{__('Style this form', 'mailster')}
						</ToolbarButton>
					</ToolbarGroup>
				)}
			</div>
			<iframe
				src="../"
				id="inputStylesIframe"
				onLoad={getInputStyles}
				sandbox="allow-scripts allow-same-origin"
				hidden
			></iframe>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={brush}
						label="Style"
						onClick={() => {
							dispatch('core/block-editor').selectBlock(clientId);
						}}
					/>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<Messages
					{...props}
					setMessages={setMessages}
					displayMessages={displayMessages}
					setDisplayMessages={setDisplayMessages}
				/>
				<Background {...props} setBackground={setBackground} />
				<Css
					{...props}
					setCss={setCss}
					setShowClasses={setShowClasses}
				/>
			</InspectorControls>
		</>
	);
}
