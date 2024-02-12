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
	SelectControl,
	Modal,
	Flex,
} from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect, select, dispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */

export default function CampaignSelector(props) {
	const { attributes, setAttributes, campaignObj, isSelected, clientId } =
		props;

	const { campaign } = attributes;

	const allCampaigns = useSelect((select) => {
		return select('mailster/automation').getCampaigns();
	}, []);
	const [editIframe, setEditIframe] = useState(false);

	useEffect(() => {
		window.mailster_receiver_post_id = (post_id) => {
			setAttributes({ campaign: undefined });
			dispatch('mailster/automation').invalidateResolutionForStoreSelector(
				'getCampaigns'
			);
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
		const workflow_id = wp.data.select('core/editor').getCurrentPostId();
		const url = new URL(edit_url);
		const params = url.searchParams;
		params.set('post', campaign);
		params.set('action', 'edit');
		params.set('workflow', workflow_id);
		params.set('nonce', nonce);

		setEditIframe(url.toString());
	};
	const newCampaign = () => {
		const workflow_id = wp.data.select('core/editor').getCurrentPostId();
		const url = new URL(
			window.location.origin +
				window.location.pathname.replace('post.php', 'post-new.php')
		);
		const params = url.searchParams;
		params.set('post_type', 'newsletter');
		params.set('workflow', workflow_id);
		setEditIframe(url.toString());
	};

	return (
		<>
			{allCampaigns.length > 0 && (
				<PanelRow>
					<SelectControl
						label={__('Campaign')}
						help={__('Select a campaign you like to send in this step')}
						value={campaign}
						onChange={(val) => {
							setAttributes({ campaign: val ? parseInt(val, 10) : undefined });
						}}
					>
						<option value="">{__('Select a campaign', 'mailster')}</option>
						{allCampaigns.map((campaign, i) => {
							return (
								<option key={i} value={campaign.ID}>
									{sprintf('[#%d] %s', campaign.ID, campaign.title)}
								</option>
							);
						})}
					</SelectControl>
				</PanelRow>
			)}
			<PanelRow>
				{allCampaigns.length > 0 && (
					<Button
						variant="secondary"
						onClick={editCampaign}
						disabled={!campaign || !campaignObj}
					>
						{__('Edit Campaign', 'mailster')}
					</Button>
				)}
				<Button variant="secondary" onClick={() => newCampaign()}>
					{__('New Campaign', 'mailster')}
				</Button>
				{editIframe && (
					<Modal
						title={__('Campaign for this step', 'mailster')}
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
