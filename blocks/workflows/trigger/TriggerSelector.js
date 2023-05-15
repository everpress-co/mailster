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
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */

import icon from './Icon';

import ListSelector from '../inspector/ListSelector';
import FormSelector from '../inspector/FormSelector';
import TagSelector from '../inspector/TagSelector';

import PageSelector from './PageSelector';
import LinkSelector from './LinkSelector';
import DateSelector from './DateSelector';
import FieldSelector from './FieldSelector';
import HookSelector from './HookSelector';
import PublishSelector from './PublishSelector';
import { HelpBeacon } from '../../util';

export default function Selector(props) {
	const { attributes, setAttributes } = props;
	const { trigger, hook = '', lists = [], forms = [] } = attributes;

	const [meta, setMeta] = useEntityProp(
		'postType',
		'mailster-workflow',
		'meta'
	);
	const [title, setTitle] = useEntityProp(
		'postType',
		'mailster-workflow',
		'title'
	);

	const allTriggers = useSelect((select) =>
		select('mailster/automation').getTriggers()
	);

	const index = meta.trigger.indexOf(trigger);

	function setTrigger(trigger) {
		var newTrigger = [...meta.trigger];
		if (index !== -1) {
			newTrigger[index] = trigger.id;
		} else {
			newTrigger.push(trigger.id);
		}
		setMeta({ trigger: newTrigger });

		setAttributes({ trigger: trigger.id });

		if (!title) {
			setTitle(trigger.label || '');
		}
	}

	const getTrigger = (id) => {
		if (!allTriggers) {
			return null;
		}
		const trigger = allTriggers.filter((trigger) => {
			return trigger.id == id;
		});
		return trigger.length ? trigger[0] : null;
	};

	const filterTrigger =
		allTriggers &&
		allTriggers.filter((t) => {
			return !meta.trigger.includes(t.id) || t.id === trigger;
		});

	const label =
		getTrigger(trigger)?.label ||
		__('Define a trigger to start this workflow.', 'mailster');
	const info =
		getTrigger(trigger)?.info ||
		__('Define a trigger to start this workflow.', 'mailster');
	const t_icon = getTrigger(trigger)?.icon;

	const TriggerButtons = ({ onClose }) => {
		return filterTrigger.map((t, i) => {
			return (
				<MenuGroup key={i}>
					<MenuItem
						icon={Icons[t.icon]}
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
				{!filterTrigger && <Spinner />}
				{trigger && (
					<BaseControl help={info}>
						<HelpBeacon id="63fb5f4e0b394c459d8a5c1e" align="right" />
						<DropdownMenu icon={Icons[t_icon] || icon} text={label}>
							{(props) => <TriggerButtons {...props} />}
						</DropdownMenu>
					</BaseControl>
				)}
				{!trigger && (
					<BaseControl>
						<HelpBeacon id="63fb5f4e0b394c459d8a5c1e" align="right" />
						<h2>{label}</h2>
						<div className="components-dropdown-menu__menu">
							<TriggerButtons onClose={() => {}} />
						</div>
					</BaseControl>
				)}
			</PanelRow>
			{trigger == 'list_add' && (
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
			{trigger == 'link_click' && <LinkSelector {...props} />}
			{trigger == 'hook' && <HookSelector {...props} />}
			{trigger == 'tag_added' && (
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
		</>
	);
}
