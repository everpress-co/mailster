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
	FlexBlock,
	ToggleControl,
	BaseControl,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */
import ListSelector from '../inspector/ListSelector';
import TagSelector from '../inspector/TagSelector';
import FieldSelector from '../inspector/FieldSelector';
import WebHookSelector from '../inspector/WebHookSelector';
import { HelpBeacon } from '../../util';

export default function ActionInspectorControls(props) {
	const { attributes, setAttributes } = props;
	const { action, doubleoptin = false, comment = '' } = attributes;

	const allActions = useSelect((select) =>
		select('mailster/automation').getActions()
	);

	const setAction = (action) => {
		setAttributes({ action: action.id });
	};

	const getAction = (id) => {
		if (!allActions) {
			return null;
		}
		const action = allActions.filter((action) => {
			return action.id == id;
		});
		return action.length ? action[0] : null;
	};

	const actionObj = getAction(action);

	const label =
		actionObj?.label || __('Define an action for this step.', 'mailster');
	const info =
		actionObj?.info ||
		__('Define a trigger to start this workflow.', 'mailster');
	const icon = actionObj?.icon;

	const ActionButtons = ({ onClose = () => {} }) => {
		return (
			allActions &&
			allActions.map((a, i) => {
				return (
					<MenuGroup key={i} isSelected={a.id === action}>
						<MenuItem
							icon={Icons[a.icon]}
							iconPosition={'left'}
							info={a.info}
							isSelected={a.id === action}
							onClick={() => {
								setAction(a);
								onClose();
							}}
						>
							{a.label}
						</MenuItem>
					</MenuGroup>
				);
			})
		);
	};

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<HelpBeacon id="646237eb17da4d6b8d6ef0b7" align="right" />
					<PanelRow>
						<ItemGroup>
							{!allActions && <Spinner />}
							{action && (
								<BaseControl help={info}>
									<DropdownMenu icon={Icons[icon]} label={info} text={label}>
										{({ onClose }) => <ActionButtons onClose={onClose} />}
									</DropdownMenu>
								</BaseControl>
							)}
							{!action && (
								<BaseControl>
									<h2>{label}</h2>
									<div className="components-dropdown-menu__menu">
										<ActionButtons />
									</div>
								</BaseControl>
							)}
						</ItemGroup>
					</PanelRow>
				</PanelBody>
				{action && (
					<PanelBody>
						<PanelRow>
							{action == 'update_field' && (
								<FieldSelector
									{...props}
									label={__('Remove subscribers from these lists', 'mailster')}
									help={__(
										'Select all lists which get removed from the subscriber in this step.',
										'mailster'
									)}
								/>
							)}
							{action == 'add_list' && (
								<BaseControl>
									<ListSelector
										{...props}
										label={__('Add subscribers to these lists', 'mailster')}
										help={__(
											'Select all lists users get subscribed to in this step.',
											'mailster'
										)}
									/>
									<ToggleControl
										onChange={(val) =>
											setAttributes({
												doubleoptin: val ? true : undefined,
											})
										}
										checked={doubleoptin}
										label={__(
											'Users must confirm their subscription.',
											'mailster'
										)}
										help={__(
											'Enable this option only if you like users to confirm their new list.',
											'mailster'
										)}
									/>
								</BaseControl>
							)}
							{action == 'remove_list' && (
								<ListSelector
									{...props}
									label={__('Remove subscribers from these lists', 'mailster')}
									help={__(
										'Select all lists which get removed from the subscriber in this step.',
										'mailster'
									)}
								/>
							)}
							{action == 'add_tag' && (
								<TagSelector
									{...props}
									label={__('Add these tags to the subscriber', 'mailster')}
									help={__(
										'Select all tags which get removed from the subscriber in this step.',
										'mailster'
									)}
								/>
							)}
							{action == 'remove_tag' && (
								<TagSelector
									{...props}
									label={__(
										'Remove these tags form the subscriber',
										'mailster'
									)}
									help={__(
										'Select all tags which get removed from the subscriber in this step.',
										'mailster'
									)}
								/>
							)}
							{action == 'webhook' && <WebHookSelector {...props} />}
						</PanelRow>
					</PanelBody>
				)}
			</Panel>
		</InspectorControls>
	);
}
