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
							{isExample && <img src="https://dummy.mailster.co/268x322.jpg" />}
						</div>
					</CardMedia>

					<CardFooter>
						<div className="email-stats">
							<div className="email-stats-sent">
								<div className="email-stats-label">
									{__('Sent', 'mailster')}
								</div>
								<div className="email-stats-value">{stats?.sent_total}</div>
							</div>
							<div className="email-stats-opens">
								<div className="email-stats-label">
									{__('Opened', 'mailster')}
								</div>
								<div className="email-stats-value">{stats?.opens_total}</div>
							</div>
							<div className="email-stats-clicks">
								<div className="email-stats-label">
									{__('Clicked', 'mailster')}
								</div>
								<div className="email-stats-value">{stats?.clicks_total}</div>
							</div>
							<div className="email-stats-unsubs">
								<div className="email-stats-label">
									{__('Unsubs', 'mailster')}
								</div>
								<div className="email-stats-value">{stats?.unsubs}</div>
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
