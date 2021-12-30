/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import apiFetch from '@wordpress/api-fetch';
import {
	Panel,
	PanelBody,
	Placeholder,
	Spinner,
	Flex,
	FlexItem,
	FlexBlock,
	Toolbar,
	ToolbarGroup,
	ToolbarItem,
	ToolbarButton,
} from '@wordpress/components';
import { Icons, email, screenoptions } from '@wordpress/components';
import {
	useSelect,
	select,
	useDispatch,
	dispatch,
	subscribe,
} from '@wordpress/data';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import { Fragment, useState, Component, useEffect } from '@wordpress/element';
import { check, edit, tablet, mobile, update } from '@wordpress/icons';

import {
	Button,
	ButtonGroup,
	DropdownMenu,
	SelectControl,
} from '@wordpress/components';

function MailsterFormSelector(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { id } = attributes;

	const forms = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'newsletter_form');
	});

	const isLoading = useSelect((select) => {
		return select('core/data').isResolving('core', 'getEntityRecords', [
			'postType',
			'newsletter_form',
		]);
	});

	if (isLoading) return <Spinner />;

	return (
		<>
			{forms && (
				<SelectControl
					value={id}
					onChange={(val) => setAttributes({ id: parseInt(val, 10) })}
				>
					<option value={0}>Choose</option>
					{forms.map((form) => {
						return (
							<option key={form.id} value={form.id}>
								{form.title.rendered}
							</option>
						);
					})}
				</SelectControl>
			)}
		</>
	);
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
	const { id, height } = attributes;

	const [displayForm, setDisplayForm] = useState(true);

	const EmptyResponsePlaceholder = () => {
		return (
			<Flex
				justify="center"
				style={{
					backgroundColor: '#fafafa',
					minHeight: '200px',
				}}
			>
				<Spinner />
			</Flex>
		);
	};

	const reloadForm = () => {
		setDisplayForm(false);
		setTimeout(() => {
			setDisplayForm(true);
		}, 0);
	};

	const editForm = () => {
		window.open('post.php?post=' + id + '&action=edit', 'edit_form_' + id);
	};

	return (
		<>
			<div {...useBlockProps()}>
				{parseInt(id) > 0 && (
					<>
						<div className="mailster-block-form-editor-wrap">
							<Flex
								className="update-form-button"
								justify="space-evenly"
							>
								<strong className="align-center">
									{__(
										'Please click on the edit button in the toolbar to edit this form.',
										'mailster'
									)}
								</strong>
							</Flex>
							<BlockControls>
								<Toolbar>
									<Button
										label={__('Reload Form', 'mailster')}
										icon={update}
										onClick={reloadForm}
									/>
									<Button
										label={__('Edit Form', 'mailster')}
										icon={edit}
										onClick={editForm}
									/>
								</Toolbar>
							</BlockControls>

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
					</>
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
		</>
	);
}
