/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';

import {
	Spinner,
	Button,
	ButtonGroup,
	TreeSelect,
	BaseControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */

export default function FormSelector(props) {
	const { selectForm, formId, label, help } = props;

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
			<>
				<p>
					{__(
						'You currently have no forms. Please create a new form.',
						'mailster'
					)}
				</p>
				<Button
					variant="primary"
					onClick={createNewForm}
					text={__('Create new form', 'mailster')}
				/>
			</>
		);
	}

	return (
		forms && (
			<BaseControl>
				<TreeSelect
					selectedId={formId}
					label={label}
					help={help}
					noOptionLabel={__('Choose a form', 'mailster')}
					onChange={(val) => selectForm(val ? parseInt(val, 10) : undefined)}
					tree={forms.map((form) => ({
						name: sprintf('#%d - %s', form.id, form.title.rendered),
						id: form.id,
					}))}
				/>

				<ButtonGroup>
					{formId && (
						<Button
							variant="tertiary"
							onClick={editForm}
							text={__('Edit form', 'mailster')}
						/>
					)}{' '}
					<Button
						variant="primary"
						onClick={createNewForm}
						text={__('Create new form', 'mailster')}
					/>
				</ButtonGroup>
			</BaseControl>
		)
	);
}
