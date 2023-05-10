/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	Panel,
	PanelRow,
	PanelBody,
	CheckboxControl,
	TextControl,
	RangeControl,
	SelectControl,
	__experimentalNumberControl as NumberControl,
	FlexItem,
	Flex,
	Spinner,
	__experimentalItemGroup as ItemGroup,
	DropdownMenu,
	MenuGroup,
	MenuItem,
	Button,
	Modal,
	Tip,
	ExternalLink,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import { useSelect, select, dispatch } from '@wordpress/data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

import CampaignSelector from './CampaignSelector';
import { lowerFirst } from 'lodash';

export default function EmailInspectorControls(props) {
	const { attributes, setAttributes, campaignObj } = props;
	const {
		campaign,
		subject = '',
		preheader = '',
		from = '',
		from_name = '',
	} = attributes;

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<CampaignSelector {...props} />
				</PanelBody>
			</Panel>
			<Panel>
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
