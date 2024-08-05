/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { sprintf, __, _n } from '@wordpress/i18n';

import { useEffect, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { CardBody, CardFooter, CardMedia } from '@wordpress/components';

/**
 * Internal dependencies
 */

import Step from '../inspector/Step';
import InspectorControls from './inspector';
import { searchBlocks } from '../../util';

const ExampleEmail = () => {
	return (
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 92.66 111.33">
			<g fill="#d6d6d6">
				<path d="M77.7 81.68H15.43c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM15.43 10.49h34.92c.44 0 .67-.22.67-.67s-.22-.67-.67-.67H15.43c-.44 0-.67.22-.67.67s.22.67.67.67zM77.7 77.66H15.43c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM76.52 14.82H16.6c-.92 0-1.84.73-1.84 1.7v35.02c0 .97.61 1.46 1.84 1.46h59.92c1.23 0 1.84-.49 1.84-1.46V16.28c0-.97-.61-1.46-1.84-1.46zM37.11 57.58H15.43c-.33 0-.67.33-.67.78v16.01c0 .44.22.67.67.67h21.68c.44 0 .67-.22.67-.67V58.25c0-.44-.22-.67-.67-.67zM77.7 57.58H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM77.7 61.6H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM77.7 65.61H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM77.7 69.63H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM77.7 73.65H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM37.11 88.5H15.43c-.33 0-.67.33-.67.78v16.01c0 .44.22.67.67.67h21.68c.44 0 .67-.22.67-.67V89.17c0-.44-.22-.67-.67-.67zM77.7 88.5H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM77.7 92.51H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM77.7 96.53H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM77.7 100.54H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67zM77.7 104.56H42.12c-.44 0-.67.22-.67.67s.22.67.67.67H77.7c.44 0 .67-.22.67-.67s-.22-.67-.67-.67z"></path>
			</g>
		</svg>
	);
};

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id, campaign, subject, preheader, comment, isExample, name } =
		attributes;

	const allEmails = searchBlocks('mailster-workflow/email');

	const currentEmailIndex = allEmails.findIndex((block) => {
		return block.clientId === clientId;
	});

	const [preview_url, setPreviewUrl] = useState(false);
	const [campaignObj, setCampaignObj] = useState();

	const { invalidateResolutionForStore } = useSelect('mailster/automation');
	const { invalidateResolutionForStoreSelector } = useDispatch(
		'mailster/automation'
	);

	const allCampaigns = useSelect((select) =>
		select('mailster/automation').getCampaigns()
	);
	const stats = useSelect(
		(select) => select('mailster/automation').getCampaignStats(campaign),
		[campaign]
	);

	useEffect(() => {
		setPreviewUrl(getPreviewUrl(campaign));
		if (campaignObj) {
			setAttributes({
				name: campaignObj.title,
			});
		}
	}, [campaign, campaignObj]);

	useEffect(() => {
		if (!allCampaigns) return;
		if (!campaign) return;
		const campaignObj = allCampaigns.filter((camp) => camp.ID == campaign);
		if (campaignObj.length) {
			setCampaignObj(campaignObj[0]);
		}
	}, [campaign, allCampaigns]);

	useEffect(() => {
		if (!name)
			setAttributes({
				name: sprintf(__('Email #%s', 'mailster'), currentEmailIndex + 1),
			});
	}, [allEmails]);

	// reset cache if one of the attributes changes
	useEffect(() => {
		invalidateResolutionForStoreSelector('getEmails');
	}, [campaign, name, id]);

	const getPreviewUrl = (campaign) => {
		if (!campaign) return false;
		const url = new URL(
			location.origin + location.pathname.replace('post-new.php', 'post.php')
		);
		const params = url.searchParams;
		params.set('post', campaign);
		params.set('action', 'preview_newsletter');
		const replace = {
			subject: subject || campaignObj?.subject,
			preheader: preheader || campaignObj?.preheader,
		};
		for (var key in replace) {
			if (replace[key]) params.append('replace[' + key + ']', replace[key]);
		}
		params.set('_cache', +new Date());

		return url.toString();
	};

	const displaySubject = subject || campaignObj?.subject;
	const preview = preview_url ? (
		<iframe src={preview_url} loading="lazy" tabIndex={-1} />
	) : (
		<ExampleEmail />
	);

	return (
		<Step
			{...props}
			isIncomplete={!campaign && !isExample}
			inspectorControls={
				<InspectorControls {...props} campaignObj={campaignObj} />
			}
		>
			<CardBody>
				<div className="mailster-step-info">
					{__('Send Email', 'mailster')}
					{campaign && (
						<span className="mailster-campaign-id"> #{campaign}</span>
					)}
				</div>
				<div className="mailster-step-label">{name}</div>
			</CardBody>
			<CardMedia>
				<div className="email-preview">
					{displaySubject && (
						<div className="email-preview-subject">{displaySubject}</div>
					)}
					{preview}
				</div>
			</CardMedia>
			<CardFooter>
				<div className="email-stats">
					<div className="email-stats-sent">
						<div className="email-stats-label">{__('Sent', 'mailster')}</div>
						<div className="email-stats-value">{stats?.sent || '-'}</div>
					</div>
					<div className="email-stats-opens">
						<div className="email-stats-label">{__('Opened', 'mailster')}</div>
						<div className="email-stats-value">{stats?.opens || '-'}</div>
					</div>
					<div className="email-stats-clicks">
						<div className="email-stats-label">{__('Clicked', 'mailster')}</div>
						<div className="email-stats-value">{stats?.clicks || '-'}</div>
					</div>
					<div className="email-stats-unsubs">
						<div className="email-stats-label">{__('Unsubs', 'mailster')}</div>
						<div className="email-stats-value">{stats?.unsubs || '-'}</div>
					</div>
				</div>
			</CardFooter>
		</Step>
	);
}
