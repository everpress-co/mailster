/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { useSelect, useDispatch, select, subscribe } from '@wordpress/data';
import { Icon, check, cancelCircleFilled } from '@wordpress/icons';

/**
 * Internal dependencies
 */

const UPGRADE_NOTICE = __(
	'You have reached the limit of %d workflows! Your workflow is saved but disabled. Please upgrade your plan to use more workflows.',
	'mailster'
);

const MAX_WORKFLOWS = 3;

export default function ActiveStatus(props) {
	const { createNotice, removeNotice } = useDispatch('core/notices');
	const { editPost } = useDispatch('core/editor');

	const { isAutosavingPost, isSavingPost, isCleanNewPost } =
		useSelect('core/editor');

	// get current status
	const status = useSelect((select) => {
		return select('core/editor').getEditedPostAttribute('status');
	}, []);

	// don't change the initialStatus
	const [initialStatus, setIntalStatus] = useState(
		select('core/editor').getEditedPostAttribute('status')
	);
	const [draftNotice, setDraftNotice] = useState();

	const isActive = status === 'publish';

	const unsubscribe = subscribe(() => {
		if (isAutosavingPost()) {
			return;
		}
		if (!isSavingPost()) {
			return;
		}
		const status = select('core/editor').getEditedPostAttribute('status');
		if (status == 'private' && initialStatus != status) {
			setDraftNotice(sprintf(UPGRADE_NOTICE, MAX_WORKFLOWS));
		} else {
			setDraftNotice(false);
		}
		//setIntalStatus('unknown');
		unsubscribe();
	});

	useEffect(() => {
		if (!draftNotice) {
			removeNotice('upgrade-notice');
			return;
		}

		const action = {
			label: __('Upgrade your license', 'mailster'),
			variant: 'primary',
			onClick: () =>
				window.open(
					window.location.origin +
						window.location.pathname.replace('post.php', 'edit.php') +
						'?page=mailster-pricing'
				),
		};

		createNotice('warning', draftNotice, {
			id: 'upgrade-notice',
			isDismissible: true,
			actions: [action],
		});
	}, [draftNotice]);

	return (
		<Button
			onClick={() => {
				setIntalStatus(!isActive ? 'publish' : 'private');
				editPost({
					status: !isActive ? 'publish' : 'private',
				});
			}}
			icon={isActive ? check : cancelCircleFilled}
			variant="secondary"
			isDestructive={!isActive}
		>
			{isActive
				? __('Workflow is Active', 'mailster')
				: __('Workflow is Inactive', 'mailster')}
		</Button>
	);
}
