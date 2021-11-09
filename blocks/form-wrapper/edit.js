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
	__experimentalUseBorderProps as useBorderProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalGetSpacingClassesAndStyles as useSpacingProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, ResizableBox } from '@wordpress/components';

import { brush } from '@wordpress/icons';

import { select, dispatch, subscribe } from '@wordpress/data';
import {
	Fragment,
	Component,
	useState,
	useEffect,
	useMemo,
} from '@wordpress/element';

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
import Css from './Css';

// const getBlockList = () => select('core/block-editor').getBlocks();
// let blockList = getBlockList();
// subscribe(() => {
// 	const newBlockList = getBlockList();

// 	return;

// 	//no more than one root block
// 	if (newBlockList.length > 1) {
// 		alert('You cannot insert an additional block here!');
// 		dispatch('core/block-editor').resetBlocks(blockList);

// 		//not remove the root block
// 	} else if (
// 		false &&
// 		newBlockList.length < blockList.length &&
// 		blockList.some((block) => block.name === 'mailster/form-wrapper') &&
// 		newBlockList.every((block) => block.name !== 'mailster/form-wrapper')
// 	) {
// 		alert('This Block cannot get removed!');
// 		dispatch('core/block-editor').resetBlocks(blockList);

// 		//do not add form inside form
// 	} else if (
// 		newBlockList.length &&
// 		newBlockList[0].innerBlocks.some(
// 			(block) => block.name === 'mailster/form-wrapper'
// 		)
// 	) {
// 		alert('You cannot insert an additional form here!');
// 		dispatch('core/block-editor').resetBlocks(blockList);
// 	}

// 	blockList = newBlockList;
// });

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

	const [displayMessages, setDisplayMessages] = useState(false);
	const [inputStyles, setinputStyles] = useState('');

	const borderProps = useBorderProps(attributes);
	const colorProps = useColorProps(attributes);
	const spacingProps = useSpacingProps(attributes);

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

	if (background.image && inputStyles) {
		css += '.mailster-form::before{';
		css += "content:'';";
		css += 'background-image: url(' + background.image + ');';
		if (background.fixed) css += 'background-attachment:fixed;';
		if (background.repeat) css += 'background-repeat:repeat;';
		css += 'background-size:' + background.size + ';';
		css +=
			'background-position:' + mediaPosition(background.position) + ';';
		css += 'opacity:' + background.opacity + '%;';
		css += '}';
	}

	const getInputStyles = () => {
		setinputStyles(
			getStyles(
				document
					.getElementById('inputStylesIframe')
					.contentWindow.document.querySelector(
						'.mailster-form .input'
					),
				'color',
				'padding',
				'height',
				'border',
				'border-radius',
				'background',
				'box-shadow',
				'line-height',
				'appearance',
				'-webkit-appearance',
				'outline'
			)
		);
	};

	const convertRestArgsIntoStylesArr = ([...args]) => {
		return args.slice(1);
	};
	const getStyles = function () {
		const args = [...arguments];
		const [element] = args;
		let s = '';

		let stylesProps =
			[...args][1] instanceof Array
				? args[1]
				: convertRestArgsIntoStylesArr(args);

		const styles = window.getComputedStyle(element);
		stylesProps.reduce((acc, v) => {
			const x = styles.getPropertyValue(v);
			if (x) {
				s += v + ': ' + x + ';';
			}
		}, {});

		return s;
	};

	if (inputStyles) {
		css += '.mailster-wrapper .input{' + inputStyles + '}';
	}

	const prefixedCss = useMemo(() => {
		return prefixCss(css, '.mailster-form-' + clientId);
	}, [css]);

	return (
		<>
			{!inputStyles && (
				<iframe
					src="/sample-page/"
					id="inputStylesIframe"
					onLoad={getInputStyles}
				></iframe>
			)}
			<div
				{...useBlockProps({
					className: 'mailster-form mailster-form-' + clientId,
				})}
				style={{
					...borderProps.style,
					...colorProps.style,
					...spacingProps.style,
				}}
			>
				{prefixedCss && <style>{prefixedCss}</style>}
				{displayMessages && (
					<div className="mailster-form-info">
						<div
							className="mailster-form-info-success"
							style={styleSuccessMessage}
						>
							{__('This is a success message', 'mailster')}
						</div>
						<div
							className=" mailster-form-info-error"
							style={styleErrorMessage}
						>
							{__(
								'Following fields are missing or incorrect. This is an error message',
								'mailster'
							)}
						</div>
					</div>
				)}
				<InnerBlocks />
				{!isSelected && (
					<Button icon={brush} className="style-form" isPrimary>
						{__('Style this form', 'mailster')}
					</Button>
				)}
			</div>

			<InspectorControls>
				<Messages
					{...props}
					setMessages={setMessages}
					displayMessages={displayMessages}
					setDisplayMessages={setDisplayMessages}
				/>
				<Background {...props} setBackground={setBackground} />
				<Css {...props} setCss={setCss} />
			</InspectorControls>
		</>
	);
}
