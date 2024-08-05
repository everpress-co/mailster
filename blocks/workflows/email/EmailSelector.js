/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { sprintf, __ } from '@wordpress/i18n';

import {
	Button,
	PanelRow,
	Modal,
	TreeSelect,
	ToolbarButton,
	ButtonGroup,
} from '@wordpress/components';

import { useSelect, dispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { BlockControls } from '@wordpress/block-editor';
import { clearData } from '../../util';

/**
 * Internal dependencies
 */

export default function CampaignSelector(props) {
	const { attributes, setAttributes, campaignObj } = props;

	const { campaign } = attributes;

	const allCampaigns = useSelect((select) => {
		return select('mailster/automation').getCampaigns();
	}, []);
	const workflow_id = useSelect((select) => {
		return select('core/editor').getCurrentPostId();
	}, []);
	const [editIframe, setEditIframe] = useState(false);

	useEffect(() => {
		window.mailster_receiver_post_id = (post_id) => {
			setAttributes({ campaign: undefined });
			clearData('getCampaigns', 'mailster/automation');
			setTimeout(() => {
				setAttributes({
					campaign: post_id ? parseInt(post_id, 10) : undefined,
				});
			}, 100);
			setEditIframe(false);
		};
		return () => {
			window.mailster_receiver_post_id = false;
		};
	}, []);

	const editCampaign = () => {
		const nonce = campaignObj?.nonce;
		const edit_url = campaignObj?.edit_url;
		const url = new URL(edit_url);
		const params = url.searchParams;
		params.set('post', campaign);
		params.set('action', 'edit');
		params.set('workflow', workflow_id);
		params.set('nonce', nonce);

		setEditIframe(url.toString());
	};

	const newCampaign = () => {
		const url = new URL(
			location.origin + location.pathname.replace('post.php', 'post-new.php')
		);
		const params = url.searchParams;
		params.set('post_type', 'newsletter');
		params.set('workflow', workflow_id);
		setEditIframe(url.toString());
	};

	return (
		<>
			<BlockControls group="inline">
				<ToolbarButton
					icon={'edit'}
					disabled={!campaign || !campaignObj}
					title={__('Edit Email', 'mailster')}
					onClick={editCampaign}
				/>
				<ToolbarButton
					icon={'welcome-add-page'}
					title={__('New Email', 'mailster')}
					onClick={newCampaign}
				/>
			</BlockControls>
			{allCampaigns.length > 0 && (
				<PanelRow>
					<TreeSelect
						label={__('Email', 'mailster')}
						help={__(
							'Select an email you like to send in this step',
							'mailster'
						)}
						noOptionLabel={__('Select a email', 'mailster')}
						value={campaign}
						onChange={(val) => {
							setAttributes({ campaign: val ? parseInt(val, 10) : undefined });
						}}
						tree={allCampaigns.map((campaign, i) => {
							return {
								name: sprintf('[#%d] %s', campaign.ID, campaign.title),
								id: campaign.ID,
							};
						})}
					></TreeSelect>
				</PanelRow>
			)}
			<PanelRow>
				<ButtonGroup>
					<Button
						variant="secondary"
						onClick={editCampaign}
						disabled={!campaign || !campaignObj}
					>
						{__('Edit Email', 'mailster')}
					</Button>{' '}
					<Button variant="secondary" onClick={newCampaign}>
						{__('New Email', 'mailster')}
					</Button>
				</ButtonGroup>

				{editIframe && (
					<Modal
						title={__('Email for this step', 'mailster')}
						className="mailster-edit-campaign-modal"
						onRequestClose={() => setEditIframe(false)}
						shouldCloseOnClickOutside={false}
						shouldCloseOnEsc={false}
						isFullScreen={false}
						__experimentalHideHeader={false}
					>
						<iframe src={editIframe} id="modal-iframe"></iframe>
					</Modal>
				)}
			</PanelRow>
		</>
	);
}
