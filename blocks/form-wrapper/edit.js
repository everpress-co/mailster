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
import Inputs from './Inputs';
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
	const { style, background, inputs, css, messages } = attributes;

	const [displayMessages, setDisplayMessages] = useState(false);

	const styleSheets = {
		width: style.width,
		minHeight: style.height,
		paddingTop: style.padding.top,
		paddingLeft: style.padding.left,
		paddingRight: style.padding.right,
		paddingBottom: style.padding.bottom,
		background: attributes.backgroundColor
			? null
			: style.color.background || style.color.gradient || null,
		color: attributes.textColor ? null : style.color.text || null,
	};

	const styleBackground = {
		opacity: background.opacity + '%',
		backgroundAttachment: background.fixed ? 'fixed' : null,
		backgroundRepeat: background.repeat ? 'repeat' : 'no-repeat',
		backgroundSize: background.size,
		backgroundImage: background.image
			? 'url(' + background.image + ')'
			: '',
		backgroundPosition:
			background.position.x * 100 +
			'%  ' +
			background.position.y * 100 +
			'%',
	};

	const styleSuccessMessage = {
		backgroundColor: messages.success,
	};
	const styleErrorMessage = {
		backgroundColor: messages.error,
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
	function setInputs(prop, data) {
		var newInputs = { ...inputs };
		newInputs[prop] = data;
		setAttributes({ inputs: newInputs });
		select('core/editor')
			.getBlocksByClientId(clientId)[0]
			.innerBlocks.forEach(function (block) {
				if (block.name != 'mailster/input') return;
				dispatch('core/editor').updateBlockAttributes(block.clientId, {
					style: newInputs,
				});
			});
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

	const prefixedCss = useMemo(() => {
		return prefixCss(css, '.mailster-form-' + clientId);
	}, [css]);

	return (
		<>
			<div
				{...useBlockProps({
					className: 'mailster-form mailster-form-' + clientId,
				})}
				style={styleSheets}
			>
				{prefixedCss && <style scoped>{prefixedCss}</style>}
				<div className="mailster-faux-bg" style={styleBackground} />
				{displayMessages && (
					<div className="mailster-form-info">
						<div
							className="mailster-form-info-success"
							style={styleSuccessMessage}
						>
							message
						</div>
						<div
							className=" mailster-form-info-error"
							style={styleErrorMessage}
						>
							Errormessage
						</div>
					</div>
				)}
				<InnerBlocks test="test" />
				{!isSelected && (
					<Button icon={brush} className="style-form">
						Style this form
					</Button>
				)}
			</div>

			<InspectorControls>
				<Styling {...props} setStyle={setStyle} />
				<Messages
					{...props}
					setMessages={setMessages}
					displayMessages={displayMessages}
					setDisplayMessages={setDisplayMessages}
				/>
				<Background {...props} setBackground={setBackground} />
				<Inputs {...props} setInputs={setInputs} />
				<Css {...props} setCss={setCss} />
			</InspectorControls>
		</>
	);
}
