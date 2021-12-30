/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
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
	PanelColorSettings,
	Warning,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	Card,
	CardBody,
	CardMedia,
	Button,
	RangeControl,
	FocalPointPicker,
	SelectControl,
	Modal,
	Notice,
	Popover,
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { select, dispatch, subscribe } from '@wordpress/data';

import { more } from '@wordpress/icons';
import { getBlockType, createBlock, rawHandler } from '@wordpress/blocks';
import apiFetch from '@wordpress/api-fetch';

import { PluginPostStatusInfo } from '@wordpress/edit-post';

const SAMPLEFORM = (
	<form className="mailster-block-form">
		<label className="mailster-label">This is my Label</label>
		<select className="input">
			<option>This is a select</option>
		</select>
		<input type="checkbox" />
		<input type="radio" />
		<input type="text" className="input" />
		<input type="email" className="input" />
		<input type="date" className="input" />
		<input type="submit" className="wp-block-button__link" />
	</form>
);

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
			doc.getElementsByClassName('wp-site-blocks')[0] ||
			doc.getElementsByTagName('body')[0],
		properties = [
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
			'input[type="submit"]': ['border', 'outline', 'color'],
			select: [],
			'label.mailster-label': [],
		};

	wp.element.render(SAMPLEFORM, el);

	return Object.keys(selectors)
		.map((selector, i) => {
			const style = getStyles(
				doc.querySelector('.mailster-block-form ' + selector),
				[...properties, ...selectors[selector]]
			);
			return '.mailster-block-form ' + selector + '{' + style + '}';
		})
		.join('');
};

export default function InlineStyles(props) {
	const { meta, setMeta } = props;
	const [inputStyles, setinputStyles] = useState(
		window.mailster_inline_styles
	);

	const [render, setRender] = useState(true);

	const updateStyles = () => {
		const styles = getInputStyles();
		if (styles != inputStyles) {
			apiFetch({
				path: '/wp/v2/settings',
				method: 'POST',
				data: { mailster_inline_styles: styles },
			}).then((settings) => {
				dispatch('core/notices').createNotice(
					'success',
					__('Input field styles have been updated.', 'mailster'),
					{
						type: 'snackbar',
						isDismissible: true,
					}
				);
			});
			setinputStyles(styles);
			window.mailster_inline_styles = styles;
		}
		setRender(false);
	};

	return (
		<>
			{inputStyles && (
				<style className="mailster-inline-styles">{inputStyles}</style>
			)}
			{render && (
				<PluginPostStatusInfo className="my-plugin-post-status-info">
					<iframe
						src="../"
						id="inputStylesIframe"
						style={{
							width: screen.width,
							zIndex: -1,
							position: 'absolute',
							top: 0,
							visibility: 'hidden',
						}}
						onLoad={updateStyles}
						sandbox="allow-scripts allow-same-origin"
					></iframe>
				</PluginPostStatusInfo>
			)}
		</>
	);
}
