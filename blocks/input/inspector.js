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
	PanelColorSettings,
	RichText,
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	SelectControl,
	RangeControl,
	ColorPalette,
} from '@wordpress/components';

import { Fragment, Component, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

import { more } from '@wordpress/icons';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
class MailsterFieldSelector extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			fields: [{ label: __('Loading Fields', 'mailster'), value: false }],
		};
	}

	componentDidMount() {
		this.updateFormList();
	}

	updateFormList = () => {
		apiFetch({ path: '/mailster/v1/fields' }).then((data) => {
			if (data.length)
				data.unshift({
					label: __('Select a Mailster form', 'mailster'),
					value: false,
				});
			this.setState({
				fields: data.map((field) => {
					return {
						label: field.name,
						value: field.id,
						type: field.type || 'text',
					};
				}),
			});
		});
	};

	render() {
		return (
			<Fragment>
				{this.state.fields.length > 0 && (
					<SelectControl
						value={this.props.attributes.field}
						options={this.state.fields}
						onChange={(val) =>
							this.props.setAttributes({ field: val })
						}
					/>
				)}
				{this.state.fields.length < 1 && (
					<p>
						{__(
							"There's currently no fields available.",
							'mailster'
						)}
					</p>
				)}
			</Fragment>
		);
	}
}

export default function InputFieldInspectorControls(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { label, inline, required, style } = attributes;

	const [width, setWidth] = useState(100);

	function setStyle(prop, data) {
		var newStyle = { ...style };
		newStyle[prop] = data;
		setAttributes({ style: newStyle });
	}
	return (
		<InspectorControls>
			<Panel>
				<PanelBody
					title={__('Field Settings', 'mailster')}
					initialOpen={true}
				>
					<PanelRow>
						<TextControl
							label={__('Label', 'mailster')}
							value={label}
							onChange={(val) => setAttributes({ label: val })}
						/>
					</PanelRow>
					<PanelRow>
						<MailsterFieldSelector {...props} />
					</PanelRow>
					<PanelRow>
						<CheckboxControl
							label={__('Inline Labels', 'mailster')}
							checked={inline}
							onChange={() => setAttributes({ inline: !inline })}
						/>
					</PanelRow>
					<PanelRow>
						<CheckboxControl
							label={__('Required Labels', 'mailster')}
							checked={required}
							onChange={() =>
								setAttributes({ required: !required })
							}
						/>
					</PanelRow>{' '}
					<PanelRow>
						<RangeControl
							label="Width"
							value={style.width}
							onChange={(value) => setStyle('width', value)}
							min={2}
							max={100}
						/>
					</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
