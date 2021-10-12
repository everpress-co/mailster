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
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import apiFetch from '@wordpress/api-fetch';
import { Panel, PanelBody, Placeholder, Spinner } from '@wordpress/components';
import { Icons, email, screenoptions } from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import { Fragment, useState, Component } from '@wordpress/element';

import { Button, DropdownMenu, SelectControl } from '@wordpress/components';

class MailsterFormPlaceholder extends Component {
	constructor() {
		super(...arguments);
	}

	updateFormList = () => {
		apiFetch({ path: '/wp/v2/users/1' }).then((data) => {
			console.warn(data);
		});
	};

	render() {
		const abc = {
			foo: 'bar',
		};
		return (
			<Placeholder
				icon={screenoptions}
				label={__('Mailster Subscription Form', 'mailster')}
			>
				<MailsterFormSelector {...this.props} abc={abc} />

				<div className="placeholder-buttons-wrap">
					<Button
						variant="link"
						href="https://google.com"
						text={__('create new form', 'mailster')}
					/>
					<Button
						variant="primary"
						icon={screenoptions}
						className="is-primary"
						onClick={this.updateFormList.bind(this)}
						text={__('Update lists', 'mailster')}
					/>
				</div>
			</Placeholder>
		);
	}
}

class MailsterFormSelector extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			lists: [{ label: __('Loading Forms', 'mailster'), value: false }],
		};
	}

	componentDidMount() {
		this.updateFormList();
	}

	updateFormList = () => {
		apiFetch({ path: '/mailster/v1/lists' }).then((data) => {
			if (data.length)
				data.unshift({
					label: __('Select a Mailster form', 'mailster'),
					value: false,
				});
			this.setState({ lists: data });
		});
	};

	render() {
		return (
			<Fragment>
				{this.state.lists.length > 0 && (
					<SelectControl
						value={this.props.attributes.id}
						options={this.state.lists}
						onChange={(val) =>
							this.props.setAttributes({ id: val })
						}
					/>
				)}
				{this.state.lists.length < 1 && (
					<p>
						{__("There's currently no form available.", 'mailster')}
					</p>
				)}
			</Fragment>
		);
	}
}
/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	let blockContent;

	if (parseInt(attributes.id) > 0) {
		blockContent = (
			<div className="mailster-form-editor-wrap">
				<div className="update-form-button">
					<Button
						icon={screenoptions}
						href={'post.php?post=' + attributes.id + '&action=edit'}
						target={'edit_form_' + attributes.id}
						text={__('Update Form', 'mailster')}
					/>{' '}
					<Button
						icon={screenoptions}
						href={'post.php?post=' + attributes.id + '&action=edit'}
						target={'edit_form_' + attributes.id}
						text={__('Reload Form', 'mailster')}
					/>
				</div>
				<ServerSideRender
					block="mailster/form"
					attributes={attributes}
				/>
			</div>
		);
	} else {
		blockContent = <MailsterFormPlaceholder {...props} />;
	}
	return (
		<Fragment>
			<div {...useBlockProps()}>{blockContent}</div>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={__('General Settings', 'mailster')}
						initialOpen={true}
					>
						<MailsterFormSelector {...props} />
					</PanelBody>
				</Panel>{' '}
				<Panel>
					<PanelBody
						title={__('Field Settings', 'mailster')}
						initialOpen={true}
					></PanelBody>
				</Panel>
			</InspectorControls>
		</Fragment>
	);
}
