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

const SAMPLEFORM = (
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
		<input type="submit" />
	</form>
);

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function InlineStyles(props) {
	const { meta, setMeta } = props;
	const [inputStyles, setinputStyles] = useState(
		window.mailster_inline_styles
	);

	const [render, setRender] = useState(true);

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

		const styles = Object.keys(selectors)
			.map((selector, i) => {
				const style = getStyles(
					doc.querySelector('.mailster-form ' + selector),
					[...properties, ...selectors[selector]]
				);
				return '.mailster-form ' + selector + '{' + style + '}';
			})
			.join('');

		if (styles != inputStyles) {
			apiFetch({
				path: '/wp/v2/settings',
				method: 'POST',
				data: { mailster_inline_styles: styles },
			}).then((settings) => {
				inputStyles &&
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

	return (
		<>
			{inputStyles && (
				<style className="mailster-inline-styles">{inputStyles}</style>
			)}
			{render && (
				<div id="mycustomstyles">
					<iframe
						src="../"
						id="inputStylesIframe"
						style={{
							width: screen.width,
							zIndex: -1,
							position: 'absolute',
							left: 0,
							right: 0,
							bottom: 0,
							top: 0,
						}}
						onLoad={getInputStyles}
						sandbox="allow-scripts allow-same-origin"
					></iframe>
				</div>
			)}
		</>
	);
}
