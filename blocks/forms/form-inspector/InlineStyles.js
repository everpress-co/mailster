/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { useState } from '@wordpress/element';
import { dispatch, useSelect } from '@wordpress/data';

import apiFetch from '@wordpress/api-fetch';
import { useEntityProp } from '@wordpress/core-data';

import { PluginPostStatusInfo } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */

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
const getInlineStyles = () => {
	const iframe = document.getElementById('inlineStylesIframe');

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

	const [inlineStyles, setInlineStyles] = useEntityProp(
		'root',
		'site',
		'mailster_inline_styles'
	);
	const [render, setRender] = useState(true);

	const posts = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'post', {
			per_page: 1,
		});
	});

	if (!posts || posts.length < 0) {
		return null;
	}

	const updateStyles = () => {
		const styles = getInlineStyles();
		if (styles != inlineStyles) {
			setInlineStyles(styles);
			apiFetch({
				path: '/wp/v2/settings',
				method: 'POST',
				data: { mailster_inline_styles: styles },
				//data: { mailster_inline_styles: '' },
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
		}
		setRender(false);
	};

	return (
		<>
			{render && (
				<PluginPostStatusInfo className="mailster-inline-styles-handler">
					<iframe
						src={posts[0].link}
						id="inlineStylesIframe"
						style={{
							width: screen.width,
							_height: 0,
							__zIndex: -1,
							_position: 'absolute',
							_top: 0,
							_visibility: 'hidden',
						}}
						onLoad={updateStyles}
						sandbox="allow-scripts allow-same-origin"
					></iframe>
				</PluginPostStatusInfo>
			)}
		</>
	);
}
