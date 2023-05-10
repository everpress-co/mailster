/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelRow,
	PanelBody,
	CheckboxControl,
	Button,
	RangeControl,
	SelectControl,
	__experimentalNumberControl as NumberControl,
	FlexItem,
	Flex,
	Spinner,
	__experimentalItemGroup as ItemGroup,
	Dropdown,
	DropdownMenu,
	MenuGroup,
	MenuItem,
	DateTimePicker,
	ToggleControl,
	BaseControl,
	__experimentalHeading as Heading,
} from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';

import { useEffect, useState } from '@wordpress/element';
import { dateI18n, gmdateI18n } from '@wordpress/date';

import * as Icons from '@wordpress/icons';
import { useSelect, useDispatch, select, subscribe } from '@wordpress/data';

import { PluginPostStatusInfo } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */

import { DATE_FORMAT, IS_12_HOUR, DATE_TIME_FORMAT } from '../delay/constants';
import { FormToggle } from '@wordpress/components';
import { Tip } from '@wordpress/components';

const UPGRADE_NOTICE = __(
	'You have reached the limit of 3 workflows! Your workflow is saved but disabled. Please upgrade your plan to use more workflows.',
	'mailster'
);

export default function PublishInfo(props) {
	const [meta, setMeta] = useEntityProp(
		'postType',
		'mailster-workflow',
		'meta'
	);
	const { createNotice, removeNotice } = useDispatch('core/notices');
	const { editPost } = useDispatch('core/editor');

	const { isAutosavingPost, isSavingPost, isCleanNewPost } =
		useSelect('core/editor');

	//changes
	const status = useSelect((select) => {
		return select('core/editor').getEditedPostAttribute('status');
	}, []);

	//don't change the initialStatus
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
			setDraftNotice(UPGRADE_NOTICE);
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

		const l = window.location;

		createNotice('warning', draftNotice, {
			id: 'upgrade-notice',
			isDismissible: true,
			actions: [
				{
					label: __('Upgrade your license', 'mailster'),
					variant: 'primary',
					onClick: () =>
						window.open(
							l.origin +
								l.pathname.replace('post.php', 'edit.php') +
								'?page=mailster-pricing'
						),
				},
			],
		});
	}, [draftNotice]);
	return (
		<>
			<PluginPostStatusInfo>
				<PanelRow className="edit-post-post-schedule">
					<span>{__('Status', 'mailster')}</span>
					<Button
						onClick={() => {
							setIntalStatus(!isActive ? 'publish' : 'private');
							editPost({
								status: !isActive ? 'publish' : 'private',
							});
						}}
						variant="secondary"
						isPressed={isActive}
					>
						{isActive
							? __('Workflow is Active', 'mailster')
							: __('Workflow is Inactive', 'mailster')}
					</Button>
				</PanelRow>
			</PluginPostStatusInfo>
		</>
	);
}
