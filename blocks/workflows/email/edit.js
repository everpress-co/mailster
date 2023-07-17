/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { sprintf, __, _n } from '@wordpress/i18n';

import { useBlockProps, RichText } from '@wordpress/block-editor';

import { useEffect, useMemo, useState, useRef } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';
import { dispatch, useSelect } from '@wordpress/data';
import { Card, CardBody, CardFooter, CardMedia } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

import QueueBadge from '../inspector/QueueBadge';
import Comment from '../inspector/Comment';
import StepId from '../inspector/StepId';
import EmailInspectorControls from './inspector';
import { set } from 'lodash';

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
	const className = [];

	const ref = useRef();

	const allEmails = useSelect((select) =>
		select('mailster/automation').getEmails()
	);

	const [preview_url, setPreviewUrl] = useState(false);

	const { getCampaignStats, getCampaigns, hasFinishedResolution } = useSelect(
		'mailster/automation'
	);
	const allCampaigns = useSelect((select) =>
		select('mailster/automation').getCampaigns()
	);
	const stats = useSelect(
		(select) => select('mailster/automation').getCampaignStats(campaign),
		[campaign]
	);

	const getCampaign = (id) => {
		const a = allCampaigns.filter((camp) => camp.ID == campaign);
		return a.length ? a[0] : null;
	};

	const campaignObj = getCampaign(campaign);

	useEffect(() => {
		setPreviewUrl(getPreviewUrl(campaign));
		if (campaignObj) {
			setAttributes({
				name: campaignObj.title,
			});
		}
	}, [campaign, campaignObj]);

	useEffect(() => {
		if (!name)
			setAttributes({
				name: sprintf(__('Email #%s', 'mailster'), allEmails.length + 1),
			});
	}, [allEmails]);

	//reset cache if one of the attributes changes
	useEffect(() => {
		dispatch('mailster/automation').invalidateResolutionForStoreSelector(
			'getEmails'
		);
	}, [campaign, name, id]);

	!campaign && !isExample && className.push('mailster-step-incomplete');

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

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

	return (
		<>
			<EmailInspectorControls {...props} campaignObj={campaignObj} />
			<div {...blockProps}>
				<Card className="mailster-step mailster-email-ref" ref={ref}>
					<Comment {...props} />
					<QueueBadge {...props} />
					<CardBody>
						<div className="mailster-step-info">
							{__('Send Email', 'mailster')}
						</div>
						<div className="mailster-step-label">{name}</div>
					</CardBody>
					<CardMedia>
						<div className="email-preview">
							{displaySubject && (
								<div className="email-preview-subject">{displaySubject}</div>
							)}
							{preview_url && <iframe src={preview_url} loading="lazy" />}
							{!preview_url && <ExampleEmail />}
						</div>
					</CardMedia>

					<CardFooter>
						<div className="email-stats">
							<div className="email-stats-sent">
								<div className="email-stats-label">
									{__('Sent', 'mailster')}
								</div>
								<div className="email-stats-value">
									{stats?.sent_total || '-'}
								</div>
							</div>
							<div className="email-stats-opens">
								<div className="email-stats-label">
									{__('Opened', 'mailster')}
								</div>
								<div className="email-stats-value">
									{stats?.opens_total || '-'}
								</div>
							</div>
							<div className="email-stats-clicks">
								<div className="email-stats-label">
									{__('Clicked', 'mailster')}
								</div>
								<div className="email-stats-value">
									{stats?.clicks_total || '-'}
								</div>
							</div>
							<div className="email-stats-unsubs">
								<div className="email-stats-label">
									{__('Unsubs', 'mailster')}
								</div>
								<div className="email-stats-value">{stats?.unsubs || '-'}</div>
							</div>
						</div>
					</CardFooter>
				</Card>
				<div className="end-stop canvas-handle"></div>
			</div>
			<StepId {...props} />
		</>
	);
}