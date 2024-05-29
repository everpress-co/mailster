/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	Spinner,
	Button,
	ButtonGroup,
	SelectControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';

import { edit, plus, update } from '@wordpress/icons';

/**
 * Internal dependencies
 */

export default function FormSelector(props) {
	const { attributes, setAttributes, isSelected, selectForm, formId } = props;

	const query = { status: 'publish,future,draft,pending,private' };

	const forms = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'mailster-form', query);
	});

	const createNewForm = () => {
		window.open('post-new.php?post_type=mailster-form', 'new_form');
	};
	const editForm = () => {
		window.open(
			'post.php?post=' + formId + '&action=edit',
			'edit_form_' + formId
		);
	};
	const isLoading = useSelect((select) => {
		return select('core/data').isResolving('core', 'getEntityRecords', [
			'postType',
			'mailster-form',
			query,
		]);
	});

	if (isLoading)
		return (
			<p>
				<Spinner />
			</p>
		);

	if (forms && forms.length === 0) {
		return (
			<p>
				{__(
					'You currently have no forms. Please create a new form.',
					'mailster'
				)}
			</p>
		);
	}

	return (
		forms && (
			<>
				<SelectControl
					value={formId}
					onChange={(val) => selectForm(val ? parseInt(val, 10) : undefined)}
				>
					<option value="">{__('Choose a form', 'mailster')}</option>
					{forms.map((form) => (
						<option key={form.id} value={form.id}>
							{sprintf('#%d - %s', form.id, form.title.rendered)}
						</option>
					))}
				</SelectControl>
				<ButtonGroup>
					{formId && (
						<Button
							variant="secondary"
							icon={edit}
							onClick={editForm}
							text={__('Edit form', 'mailster')}
						/>
					)}{' '}
					<Button
						variant="secondary"
						icon={plus}
						onClick={createNewForm}
						text={__('Create new form', 'mailster')}
					/>
				</ButtonGroup>
			</>
		)
	);
}
