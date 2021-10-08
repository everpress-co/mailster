/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

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
	PlainText,
} from "@wordpress/block-editor";
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
} from "@wordpress/components";
import { Fragment, useState } from "@wordpress/element";

import { more } from "@wordpress/icons";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

const MyCheckboxControl = () => {
	const [isChecked, setChecked] = useState(true);
	return (
		<CheckboxControl
			label="Is author"
			help="Is the user a author or not?"
			checked={isChecked}
			onChange={setChecked}
		/>
	);
};

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	return (
		<Fragment>
			<div {...useBlockProps()}>
				<RichText
					tagName="label"
					value={attributes.label}
					onChange={(val) => setAttributes({ label: val })}
					placeholder={isSelected && __("Enter Label", "mailster")}
				/>
				<TextControl value="" disabled="{true}" />
				{isSelected &&
					__("Mailster – hello from the editor! input", "mailster")}
			</div>
			<InspectorControls>
				<Panel>
					<PanelBody title="My Block Settings" initialOpen={true}>
						<PanelRow>
							<MyCheckboxControl />
						</PanelRow>
						<PanelRow>
							<TextControl
								label="Label"
								value={attributes.label}
								onChange={(val) =>
									setAttributes({ label: val })
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</Fragment>
	);
}
