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

const { pick, kebabCase } = lodash;
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

import { select, dispatch, subscribe, useDispatch } from '@wordpress/data';
import {
	Fragment,
	Component,
	useState,
	useEffect,
	useMemo,
} from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';
import apiFetch from '@wordpress/api-fetch';

import { store as editPostStore } from '@wordpress/edit-post';

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

// const getCurrentPostAttribute = select('core/editor').getCurrentPostAttribute;
// const getBlocks = select('core/editor').getBlocks;

// let blockList = getBlocks();

// subscribe(() => {
// 	const newBlockList = getBlocks();

// 	console.warn(newBlockList);

// 	// get the current postFormat
// 	const meta = getCurrentPostAttribute('meta');
// 	if (meta) {
// 		console.warn(meta);
// 	}

// 	// only do something if postFormat has changed.
// 	// if (postFormat !== newPostFormat) {
// 	// 	// Do whatever you want after postFormat has changed
// 	// 	if (newPostFormat == 'gallery') {
// 	// 		$('#blockAudio, #blockVideo, #blockGallery').hide();
// 	// 		$('#blockGallery').fadeIn();
// 	// 	}
// 	// }

// 	// // update the postFormat variable.
// 	// postFormat = newPostFormat;
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
	let backgroundStyles = '';
	let inputStyle = '';

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	const styleForm = () => {
		dispatch('core/block-editor').clearSelectedBlock(clientId);
		dispatch('core/block-editor').selectBlock(clientId);
	};

	const [displayMessages, setDisplayMessages] = useState(false);

	const borderProps = useBorderProps(attributes);
	const colorProps = useColorProps(attributes);
	const spacingProps = useSpacingProps(attributes);

	let className = ['mailster-form', 'mailster-form-' + clientId];

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
	function setCss(name, data) {
		var newCss = { ...css };
		newCss[name] = data;
		setAttributes({ css: newCss });
	}

	function prefixCss(css, className, type) {
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

		if ('tablet' == type) {
			rules = '@media only screen and (max-width: 800px) {' + rules + '}';
		} else if ('mobile' == type) {
			rules = '@media only screen and (max-width: 400px) {' + rules + '}';
		}

		return rules;
	}

	const mediaPosition = ({ x, y }) => {
		return `${Math.round(x * 100)}% ${Math.round(y * 100)}%`;
	};

	if (background.image) {
		backgroundStyles +=
			'.wp-block-mailster-form-wrapper.mailster-form-' +
			clientId +
			'::before{';
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

	const filteredInputStyles = Object.fromEntries(
		Object.entries(style).filter(([k, v]) => v)
	);

	Object.entries(filteredInputStyles).map(([k, v]) => {
		inputStyle +=
			'.wp-block-mailster-form-wrapper.mailster-form-' + clientId;

		switch (k) {
			case 'labelColor':
				inputStyle += ' .mailster-label{';
				inputStyle += 'color:' + v + ';';
				break;
			default:
				inputStyle += ' .input{';
				inputStyle += kebabCase(k) + ':' + v + ';';
		}

		inputStyle += '}';
	});

	const prefixedCss = useMemo(() => {
		return Object.keys(css).map((name, b) => {
			return prefixCss(
				css[name],
				'.wp-block-mailster-form-wrapper.mailster-form-' + clientId,
				name
			);
		});
	}, [css]);

	useEffect(() => {
		const all = select('core/block-editor').getBlocks(),
			count = all.length;

		if (count > 1) {
			const inserted = select('core/block-editor').getBlock(clientId);
			const current = all.find(
				(block) =>
					block.name == 'mailster/form-wrapper' &&
					block.clientId != clientId
			);
			if (
				confirm(
					'This will replace your current form with the selected one. Continue?'
				)
			) {
				dispatch('core/block-editor').removeBlock(current.clientId);
			} else {
				dispatch('core/block-editor').removeBlock(inserted.clientId);
			}
		}
	}, []);

	useEffect(() => {
		const all = select('core/block-editor').getBlocks(clientId);
		const exists = all.filter((block) => {
			return block.name == 'mailster/gdpr';
		});

		if (exists.length && !meta.gdpr) {
			dispatch('core/block-editor').removeBlock(exists[0].clientId);
			dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
		} else if (!exists.length && meta.gdpr) {
			const block = wp.blocks.createBlock('mailster/gdpr');
			const submit = all.filter((block) => {
				return block.name == 'mailster/field-submit';
			});
			const pos = submit.length
				? select('core/block-editor').getBlockIndex(
						submit[0].clientId,
						clientId
				  )
				: all.length;

			dispatch('core/block-editor').insertBlock(block, pos, clientId);
		}
	}, [meta.gdpr]);

	useEffect(() => {
		const all = select('core/block-editor').getBlocks(clientId);
		const exists = all.filter((block) => {
			return block.name == 'mailster/lists';
		});

		if (exists.length && !meta.userschoice) {
			dispatch('core/block-editor').removeBlock(exists[0].clientId);
			dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
		} else if (!exists.length && meta.userschoice) {
			const block = wp.blocks.createBlock('mailster/lists');
			const submit = all.filter((block) => {
				return block.name == 'mailster/field-submit';
			});
			const pos = submit.length
				? select('core/block-editor').getBlockIndex(
						submit[0].clientId,
						clientId
				  )
				: all.length;

			dispatch('core/block-editor').insertBlock(block, pos, clientId);
		}
	}, [meta.userschoice]);

	useEffect(() => {
		if (
			!select('core/block-editor').getBlocks().length ||
			document.getElementById('style-this-form')
		) {
			return;
		}
		const el = document.getElementsByClassName(
			'edit-post-header__toolbar'
		)[0];
		const div = document.createElement('div');
		div.classList.add('edit-post-header__settings');
		el.parentNode.insertBefore(div, el.nextSibling);

		wp.element.render(
			<Button
				id="style-this-form"
				icon={brush}
				label="Style"
				variant="tertiary"
				onClick={styleForm}
			>
				{__('Style this form', 'mailster')}
			</Button>,
			div
		);
	}, []);

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
			>
				{window.mailster_inline_styles && (
					<style className="mailster-custom-styles">
						{window.mailster_inline_styles}
					</style>
				)}
				{prefixedCss && (
					<style className="mailster-custom-styles">
						{prefixedCss}
					</style>
				)}
				{backgroundStyles && (
					<style className="mailster-bg-styles">
						{backgroundStyles}
					</style>
				)}{' '}
				{inputStyle && (
					<style className="mailster-inline-styles">
						{inputStyle}
					</style>
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
			</div>
			<InspectorControls>
				<Styling {...props} />
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
