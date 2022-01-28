/**
 * External dependencies
 */

const { kebabCase } = lodash;
import classnames from 'classnames';

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

import { useUpdateEffect } from '../../util';
import BlockRecovery from './BlockRecovery';

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
		className + ' .mailster-block-form{',
		className + '.mailster-block-form{'
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
	const { css, style, background, inputs } = attributes;

	const [siteUrl] = useEntityProp('root', 'site', 'url');

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	const borderProps = useBorderProps(attributes);
	const colorProps = useColorProps(attributes);
	const spacingProps = useSpacingProps(attributes);

	const formClasses = classnames(
		'mailster-block-form',
		'mailster-block-form-' + clientId,
		colorProps.className,
		borderProps.className
	);

	const formStyle = {
		...borderProps.style,
		...colorProps.style,
		...spacingProps.style,
		...{
			//color: attributes.color,
			//backgroundColor: attributes.backgroundColor,
			fontSize: attributes.fontSize,
			borderRadius: attributes.borderRadius,
		},
	};

	const cleanedFormStyle = Object.fromEntries(
		Object.entries(formStyle).filter(([_, v]) => v != null)
	);

	const mediaPosition = ({ x, y }) => {
		return `${Math.round(x * 200) - 50}% ${Math.round(y * 100)}%`;
	};

	const backgroundStyles = useMemo(() => {
		let s = '';
		if (background.image) {
			s +=
				'.wp-block-mailster-form-wrapper.mailster-block-form-' +
				clientId +
				'::before{';
			s += "content:'';";
			s += 'background-image: url(' + background.image + ');';
			if (background.fixed) s += 'background-attachment:fixed;';
			if (background.repeat) s += 'background-repeat:repeat;';
			s +=
				'background-size:' +
				(isNaN(background.size)
					? background.size
					: background.size + '%') +
				';';
			if (background.position)
				s +=
					'background-position:' +
					mediaPosition(background.position) +
					';';
			s += 'opacity:' + background.opacity + '%;';
			if (attributes.borderRadius) {
				s += 'border-radius:' + attributes.borderRadius + ';';
			}
			s += '}';
		}
		return s;
	}, [background, attributes.borderRadius]);

	const inputStyle = useMemo(() => {
		let s = '';
		style &&
			Object.entries(style).map(([k, v]) => {
				if (!v) return;
				s +=
					'.wp-block-mailster-form-wrapper.mailster-block-form-' +
					clientId;

				switch (k) {
					case 'labelColor':
						s += ' .mailster-label{';
						s += 'color:' + v + ';';
						break;
					default:
						s += ' .input{';
						s += kebabCase(k) + ':' + v + ';';
				}

				s += '}';
			});
		return s;
	}, [style]);

	const prefixedCss = useMemo(() => {
		return Object.keys(css).map((name, b) => {
			return prefixCss(
				css[name],
				'.editor-styles-wrapper div.wp-block-mailster-form-wrapper.mailster-block-form-' +
					clientId,
				name
			);
		});
	}, [css]);

	useEffect(() => {
		if (!siteUrl) return;
		const actionUrl = siteUrl + '/wp-json/mailster/v1/subscribe';

		if (actionUrl != attributes.action)
			setAttributes({ action: actionUrl });
	}, [siteUrl]);

	//enter the root wrapper or replace it with a new one
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

	// add message block if missing
	useEffect(() => {
		const all = select('core/block-editor').getBlocks(clientId);
		const messagesBlock = all.find((block) => {
			return block.name == 'mailster/messages';
		});

		if (!messagesBlock) {
			const block = wp.blocks.createBlock('mailster/messages');
			const first = all.find((block) => {
				return /mailster\//.test(block.name);
				return block.name == 'mailster/field-submit';
			});
			const pos = first
				? select('core/block-editor').getBlockIndex(
						first.clientId,
						clientId
				  )
				: 0;

			dispatch('core/block-editor').insertBlock(block, pos, clientId);
			// clear any selected block
			dispatch('core/block-editor').clearSelectedBlock();
			// select "Form" in side panel
			dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
		}
	}, []);

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

	return (
		<>
			<div
				{...useBlockProps({
					className: formClasses,
				})}
				hidden // overwritten via CSS
				style={cleanedFormStyle}
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
				<div className="mailster-block-form-inner">
					<InnerBlocks renderAppender={null} />
				</div>
			</div>
			<div
				className="mailster-editor-info"
				hidden // overwritten via CSS
			>
				{__(
					'Forms may look different in the editor. Please check the final result on your website.',
					'mailster'
				)}
			</div>
			<BlockRecovery {...props} />
		</>
	);
}
