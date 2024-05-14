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
	TextControl,
	Tip,
} from '@wordpress/components';

/**
 * Internal dependencies
 */

import CampaignSelector from './CampaignSelector';
import { HelpBeacon } from '../../util';

export default function EmailInspectorControls(props) {
	const { attributes, setAttributes, campaignObj } = props;
	const {
		campaign,
		subject = '',
		preheader = '',
		from = '',
		from_name = '',
		name = '',
	} = attributes;

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<HelpBeacon id="64623ab58783627a4ed4c5ec" align="right" />
					<CampaignSelector {...props} />
				</PanelBody>
			</Panel>
			<Panel>
				<PanelBody>
					<PanelRow>
						<TextControl
							label={__('Name', 'mailster')}
							help={__('Set a name of this campaign.', 'mailster')}
							value={name}
							onChange={(val) => setAttributes({ name: val ? val : undefined })}
						/>
					</PanelRow>
				</PanelBody>
				{campaign && (
					<PanelBody>
						<PanelRow>
							<TextControl
								label={__('Subject', 'mailster')}
								help={__(
									'Overwrite the subject line of this campaign.',
									'mailster'
								)}
								placeholder={campaignObj?.subject}
								value={subject}
								onChange={(val) =>
									setAttributes({ subject: val ? val : undefined })
								}
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={__('Preheader', 'mailster')}
								help={__(
									'Overwrite the preheader of this campaign.',
									'mailster'
								)}
								placeholder={campaignObj?.preheader}
								value={preheader}
								onChange={(val) =>
									setAttributes({ preheader: val ? val : undefined })
								}
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={__('From Email Address', 'mailster')}
								help={__(
									'Overwrite the email address of this campaign.',
									'mailster'
								)}
								placeholder={campaignObj?.from}
								value={from}
								onChange={(val) =>
									setAttributes({ from: val ? val : undefined })
								}
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={__('From Name', 'mailster')}
								help={__(
									'Overwrite the from name of this campaign.',
									'mailster'
								)}
								placeholder={campaignObj?.from_name}
								value={from_name}
								onChange={(val) =>
									setAttributes({ from_name: val ? val : undefined })
								}
							/>
						</PanelRow>
					</PanelBody>
				)}
			</Panel>
			<Panel>
				{campaign && (
					<PanelBody>
						<PanelRow>
							<Tip>
								{__(
									'Emails get queued in this step and sent by your current cron process.',
									'mailster'
								)}
							</Tip>
						</PanelRow>
					</PanelBody>
				)}
			</Panel>
		</InspectorControls>
	);
}
