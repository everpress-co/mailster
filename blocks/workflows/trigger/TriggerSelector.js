/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	PanelRow,
	DropdownMenu,
	MenuGroup,
	MenuItem,
	Spinner,
	BaseControl,
	CheckboxControl,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

import icon from './Icon';

import ListSelector from '../inspector/ListSelector';
import FormSelector from '../inspector/FormSelector';
import TagSelector from '../inspector/TagSelector';

import PageSelector from './PageSelector';
import CampaignSelector from './CampaignSelector';
import LinkSelector from './LinkSelector';
import DateSelector from './DateSelector';
import FieldSelector from './FieldSelector';
import HookSelector from './HookSelector';
import PublishSelector from './PublishSelector';
import TriggerSlotFill from './TriggerSlotFill';
import { HelpBeacon } from '../../util';
import { getTrigger } from './functions';

export default function Selector(props) {
	const { attributes, setAttributes } = props;
	const { trigger, lists = [], forms = [] } = attributes;

	const [title, setTitle] = useEntityProp(
		'postType',
		'mailster-workflow',
		'title'
	);

	const allTriggers = useSelect((select) =>
		select('mailster/automation').getTriggers()
	);

	function setTrigger(trigger) {
		setAttributes({ trigger: trigger.id });

		if (!title) {
			setTitle(trigger.label || '');
		}
	}

	const triggerObj = getTrigger(trigger);

	const label =
		triggerObj === false ? (
			<div className="mailster-step-info">
				{sprintf(__('Trigger %s not found!', 'mailster'), '"' + trigger + '"')}
			</div>
		) : (
			triggerObj?.label || (
				<>{__('Define a trigger to start this workflow.', 'mailster')}</>
			)
		);
	const info =
		triggerObj?.info ||
		__('Define a trigger to start this workflow.', 'mailster');
	const t_icon = triggerObj?.icon;

	const TriggerButtons = ({ onClose }) => {
		if (!allTriggers) {
			return null;
		}

		return allTriggers.map((t, i) => {
			const ico = Icons[t.icon] || t.icon;

			return (
				<MenuGroup key={i}>
					<MenuItem
						icon={ico || icon}
						iconPosition={'left'}
						info={t.disabled ? t.reason : t.info}
						isSelected={t.id === trigger}
						disabled={t.disabled}
						onClick={() => {
							setTrigger(t);
							onClose();
						}}
					>
						{t.label}
					</MenuItem>
				</MenuGroup>
			);
		});
	};

	return (
		<>
			<PanelRow>
				{!allTriggers && <Spinner />}
				{trigger && triggerObj !== false ? (
					<BaseControl help={info}>
						<HelpBeacon id="646232738783627a4ed4c596" align="right" />
						<DropdownMenu icon={Icons[t_icon] || t_icon || icon} text={label}>
							{(props) => <TriggerButtons {...props} />}
						</DropdownMenu>
					</BaseControl>
				) : (
					<BaseControl>
						<HelpBeacon id="646232738783627a4ed4c596" align="right" />
						<h2>{label}</h2>
						<div className="components-dropdown-menu__menu">
							<TriggerButtons onClose={() => {}} />
						</div>
					</BaseControl>
				)}
			</PanelRow>
			{(trigger == 'list_add' || trigger == 'list_removed') && (
				<>
					<ListSelector
						{...props}
						help={__(
							'Select all lists where this workflow should get triggered',
							'mailster'
						)}
					/>
					<CheckboxControl
						checked={lists.includes(-1)}
						aria-label={__('Any List', 'mailster')}
						label={__('Any List', 'mailster')}
						onChange={(add) => setAttributes({ lists: add ? [-1] : [] })}
					/>
				</>
			)}
			{trigger == 'form_conversion' && (
				<>
					<FormSelector
						{...props}
						help={__(
							'Select all forms where this workflow should get triggered',
							'mailster'
						)}
					/>
					<CheckboxControl
						checked={forms.includes(-1)}
						aria-label={__('Any Form', 'mailster')}
						label={__('Any Form', 'mailster')}
						onChange={(add) => setAttributes({ forms: add ? [-1] : [] })}
					/>
				</>
			)}
			{trigger == 'opened_campaign' && <CampaignSelector {...props} />}
			{trigger == 'link_click' && <LinkSelector {...props} />}
			{trigger == 'hook' && <HookSelector {...props} />}
			{(trigger == 'tag_added' || trigger == 'tag_removed') && (
				<TagSelector
					{...props}
					help={__(
						'Select all tags where this workflow should get triggered',
						'mailster'
					)}
				/>
			)}
			{trigger == 'updated_field' && <FieldSelector {...props} />}
			{trigger == 'page_visit' && <PageSelector {...props} />}
			{trigger == 'date' && <DateSelector {...props} />}
			{trigger == 'anniversary' && <DateSelector {...props} isAnniversary />}
			{trigger == 'published_post' && <PublishSelector {...props} />}
			<TriggerSlotFill.Slot
				fillProps={{ ...props, trigger: trigger, allTriggers: allTriggers }}
			/>
		</>
	);
}
