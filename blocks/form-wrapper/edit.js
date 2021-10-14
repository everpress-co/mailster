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

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import Styling from './Styling';
import Background from './Background';

const getBlockList = () => select('core/block-editor').getBlocks();
let blockList = getBlockList();
subscribe(() => {
	const newBlockList = getBlockList();

	//no more than one root block
	if (newBlockList.length > 1) {
		alert('You cannot insert an additional block here!');
		dispatch('core/block-editor').resetBlocks(blockList);

		//not remove the root block
	} else if (
		false &&
		newBlockList.length < blockList.length &&
		blockList.some((block) => block.name === 'mailster/form-wrapper') &&
		newBlockList.every((block) => block.name !== 'mailster/form-wrapper')
	) {
		alert('This Block cannot get removed!');
		dispatch('core/block-editor').resetBlocks(blockList);

		//do not add form inside form
	} else if (
		newBlockList.length &&
		newBlockList[0].innerBlocks.some(
			(block) => block.name === 'mailster/form-wrapper'
		)
	) {
		alert('You cannot insert an additional form here!');
		dispatch('core/block-editor').resetBlocks(blockList);
	}

	blockList = newBlockList;
});

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
	const { style, background } = attributes;

	const styleSheets = {
		width: style.width,
		minHeight: style.height,
		paddingTop: style.padding.top,
		paddingLeft: style.padding.left,
		paddingRight: style.padding.right,
		paddingBottom: style.padding.bottom,
		//background: style.style.color.gradient,
	};
	const styleBackground = {
		opacity: background.opacity,
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

	return (
		<>
			<div {...useBlockProps()} style={styleSheets}>
				<div className="faux-bg" style={styleBackground} />
				<InnerBlocks />
				{!isSelected && (
					<Button icon={brush} className="style-form">
						Style this form
					</Button>
				)}
			</div>

			<InspectorControls>
				<Styling {...props} setStyle={setStyle} />
				<Background {...props} setBackground={setBackground} />
			</InspectorControls>
		</>
	);
}
