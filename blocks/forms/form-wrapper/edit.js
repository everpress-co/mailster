/**
 * External dependencies
 */

const { kebabCase } = lodash;

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

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
	//__experimentalGetSpacingClassesAndStyles as useSpacingProps,
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

import {
	useSelect,
	select,
	useDispatch,
	dispatch,
	subscribe,
} from '@wordpress/data';
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
 * Internal dependencies
 */

import './editor.scss';

import Styles from './Styles';
import Messages from './Messages';
import Background from './Background';
import BlockRecovery from './BlockRecovery';
import Css from './Css';

const prefixCss = (css, className, type) => {
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
				rules.slice(0, i + 1) + className + ' ' + rules.slice(i + 1);
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
		className + ' .mailster-block-form',
		className + '.mailster-block-form'
	);

	if ('tablet' == type) {
		rules = '@media only screen and (max-width: 800px) {' + rules + '}';
	} else if ('mobile' == type) {
		rules = '@media only screen and (max-width: 400px) {' + rules + '}';
	}

	return rules;
};

export default function Edit(props) {
	const { attributes, setAttributes, toggleSelection, isSelected, clientId } =
		props;
	const { style, background, inputs, messages } = attributes;
	let { css } = attributes;
	let backgroundStyles = '';
	let inputStyle = '';

	const [siteUrl] = useEntityProp('root', 'site', 'url');

	useEffect(() => {
		if (!siteUrl) return;
		const actionUrl = siteUrl + '/wp-json/mailster/v1/subscribe';

		if (actionUrl != attributes.action)
			setAttributes({ action: actionUrl });
	}, [siteUrl]);

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
	//const spacingProps = useSpacingProps(attributes);

	let className = ['mailster-block-form', 'mailster-block-form-' + clientId];

	const styleSuccessMessage = {
		color: messages.success,
		backgroundColor: messages.successBackground,
	};
	const styleErrorMessage = {
		color: messages.error,
		backgroundColor: messages.errorBackground,
	};

	function setMessages(prop, data) {
		var newMessages = { ...messages };
		newMessages[prop] = data;
		setAttributes({ messages: newMessages });
	}

	const mediaPosition = ({ x, y }) => {
		return `${Math.round(x * 100)}% ${Math.round(y * 100)}%`;
	};

	if (background.image) {
		backgroundStyles +=
			'.wp-block-mailster-form-wrapper.mailster-block-form-' +
			clientId +
			'::before{';
		backgroundStyles += "content:'';";
		backgroundStyles += 'background-image: url(' + background.image + ');';
		if (background.fixed)
			backgroundStyles += 'background-attachment:fixed;';
		if (background.repeat) backgroundStyles += 'background-repeat:repeat;';
		backgroundStyles += 'background-size:' + background.size + ';';
		if (background.position)
			backgroundStyles +=
				'background-position:' +
				mediaPosition(background.position) +
				';';
		backgroundStyles += 'opacity:' + background.opacity + '%;';
		if (attributes.borderRadius) {
			backgroundStyles +=
				'border-radius:' + attributes.borderRadius + ';';
		}
		backgroundStyles += '}';
	}

	Object.entries(style).map(([k, v]) => {
		if (!v) return;
		inputStyle +=
			'.wp-block-mailster-form-wrapper.mailster-block-form-' + clientId;

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
				'.wp-block-mailster-form-wrapper.mailster-block-form-' +
					clientId,
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
		const gdprBlock = all.find((block) => {
			return block.name == 'mailster/gdpr';
		});

		if (gdprBlock && !meta.gdpr) {
			dispatch('core/block-editor').removeBlock(gdprBlock.clientId);
			dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
		} else if (!gdprBlock && meta.gdpr) {
			const block = wp.blocks.createBlock('mailster/gdpr');
			const submit = all.find((block) => {
				return block.name == 'mailster/field-submit';
			});
			const pos = submit
				? select('core/block-editor').getBlockIndex(
						submit.clientId,
						clientId
				  )
				: all.length;

			dispatch('core/block-editor').insertBlock(block, pos, clientId);
		}
	}, [meta.gdpr]);

	useEffect(() => {
		const all = select('core/block-editor').getBlocks(clientId);
		const listBlock = all.find((block) => {
			return block.name == 'mailster/lists';
		});

		if (listBlock && !meta.userschoice) {
			dispatch('core/block-editor').removeBlock(listBlock.clientId);
			dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
		} else if (!listBlock && meta.userschoice) {
			const block = wp.blocks.createBlock('mailster/lists');
			const submit = all.find((block) => {
				return block.name == 'mailster/field-submit';
			});
			const pos = submit
				? select('core/block-editor').getBlockIndex(
						submit.clientId,
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
					//...spacingProps.style,
					...{
						color: attributes.color,
						backgroundColor: attributes.backgroundColor,
						fontSize: attributes.fontSize,
						padding: attributes.padding / 10 + 'rem',
						borderRadius: attributes.borderRadius,
					},
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
				)}
				{inputStyle && (
					<style className="mailster-inline-styles">
						{inputStyle}
					</style>
				)}
				{displayMessages && (
					<div className="mailster-block-form-info">
						<div
							className="mailster-block-form-info-success"
							style={styleSuccessMessage}
						>
							{__('This is a success message', 'mailster')}
						</div>
						<div
							className="mailster-block-form-info-error"
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
				<Styles {...props} meta={meta} setMeta={setMeta} />
				<Messages
					{...props}
					setMessages={setMessages}
					displayMessages={displayMessages}
					setDisplayMessages={setDisplayMessages}
				/>
				<Background {...props} />
				<Css {...props} />
			</InspectorControls>
		</>
	);
}
