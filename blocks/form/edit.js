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

import { Fragment, useState, Component, useEffect } from '@wordpress/element';

import { Button, DropdownMenu, SelectControl } from '@wordpress/components';

class MailsterFormSelector extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			forms: [{ label: __('Loading Forms', 'mailster'), value: false }],
		};
	}

	componentDidMount() {
		this.updateFormForm();
	}

	updateFormForm = () => {
		apiFetch({ path: '/mailster/v1/forms' }).then((data) => {
			if (data.length)
				data.unshift({
					label: __('Select a Mailster form', 'mailster'),
					value: false,
				});
			this.setState({ forms: data });
		});
	};

	render() {
		return (
			<Fragment>
				{this.state.forms.length > 0 && (
					<SelectControl
						value={this.props.attributes.id}
						options={this.state.forms}
						onChange={(val) =>
							this.props.setAttributes({ id: val })
						}
					/>
				)}
				{this.state.forms.length < 1 && (
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
	const { id } = attributes;

	const [displayForm, setDisplayForm] = useState(true);

	const EmptyResponsePlaceholder = () => {
		return (
			<Placeholder
				icon={screenoptions}
				label={__('Mailster Subscription Form', 'mailster')}
			>
				<p>
					<Spinner />
					{__('Loading your Form', 'mailster')}
				</p>
			</Placeholder>
		);
	};

	const reloadForm = () => {
		setDisplayForm(false);
		setTimeout(() => {
			setDisplayForm(true);
		}, 1);
	};

	return (
		<Fragment>
			<div {...useBlockProps()}>
				{parseInt(id) > 0 && (
					<div className="mailster-form-editor-wrap">
						<div className="update-form-button">
							<Button
								variant="primary"
								href={'post.php?post=' + id + '&action=edit'}
								target={'edit_form_' + id}
								text={__('Update Form', 'mailster')}
							/>{' '}
							<Button
								variant="primary"
								onClick={reloadForm}
								text={__('Reload Form', 'mailster')}
							/>
						</div>
						{displayForm && (
							<ServerSideRender
								block="mailster/form"
								attributes={attributes}
								EmptyResponsePlaceholder={
									EmptyResponsePlaceholder
								}
							/>
						)}
					</div>
				)}
				{!id && (
					<Placeholder
						icon={email}
						label={__('Mailster Subscription Form', 'mailster')}
					>
						<MailsterFormSelector {...props} />

						<div className="placeholder-buttons-wrap">
							<Button
								variant="link"
								href={'post-new.php?post_type=newsletter_form'}
								target={'edit_form_new'}
								text={__('create new form', 'mailster')}
							/>
							<Button
								variant="primary"
								icon={email}
								className="is-primary"
								onClick={reloadForm}
								text={__('Update Forms', 'mailster')}
							/>
						</div>
					</Placeholder>
				)}
			</div>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={__('General Settings', 'mailster')}
						initialOpen={true}
					>
						<MailsterFormSelector {...props} />
					</PanelBody>
				</Panel>
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
