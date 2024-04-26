/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';

import { __ } from '@wordpress/i18n';

import { useSelect } from '@wordpress/data';
import { CardBody, CardFooter, Tooltip, Spinner } from '@wordpress/components';
import { dateI18n, gmdateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */

import InspectorControls from './inspector';
import Step from '../inspector/Step';
import {
	formatLists,
	formatForms,
	formatTags,
	formatPages,
	formatField,
	formatOffset,
	formatLinks,
} from '../../util';

import { TIME_FORMAT, DATE_FORMAT } from './constants';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { trigger, conditions, repeat } = attributes;

	const allTriggers = useSelect((select) =>
		select('mailster/automation').getTriggers()
	);

	const getTrigger = (id) => {
		if (!allTriggers) {
			return null;
		}
		const t1 = allTriggers.filter((t) => {
			return t.id == id;
		});
		return t1.length ? t1[0] : null;
	};

	const getInfo = () => {
		const { lists, forms, tags, pages, links, hook, field, date, offset } =
			attributes;
		if (!trigger) return __('Set up a trigger', 'mailster');

		switch (trigger) {
			case 'list_add':
				return formatLists(lists);
			case 'form_conversion':
				return formatForms(forms);
			case 'tag_added':
				return formatTags(tags);
			case 'updated_field':
				return formatField(field, false);
			case 'page_visit':
				return formatPages(pages);
			case 'link_click':
				return formatLinks(links);
			case 'hook':
				return (
					hook &&
					'<strong class="mailster-step-badge code">' + hook + '</strong>'
				);
			case 'date':
				if (field) {
					return (
						formatField(
							field,
							dateI18n(TIME_FORMAT, date),
							__('On subscribers %s field at %s', 'mailster')
						) + formatOffset(offset)
					);
				}
				return sprintf(
					__('On %s at %s', 'mailster'),
					'<strong class="mailster-step-badge">' +
						dateI18n(DATE_FORMAT, date) +
						'</strong>',
					'<strong class="mailster-step-badge">' +
						dateI18n(TIME_FORMAT, date) +
						'</strong>'
				);
			case 'anniversary':
				if (field) {
					return (
						formatField(
							field,
							dateI18n(TIME_FORMAT, date),
							__('Yearly based on the subscribers %s field at %s', 'mailster')
						) + formatOffset(offset)
					);
				}
				return sprintf(
					__('Yearly on the %s at %s', 'mailster'),
					'<strong class="mailster-step-badge">' +
						dateI18n('F j', date) +
						'</strong>',
					'<strong class="mailster-step-badge">' +
						dateI18n(TIME_FORMAT, date) +
						'</strong>'
				);

			default:
				return trigger;
		}
		return '';
	};

	const label = getTrigger(trigger)?.info || <Spinner />;
	const info = getInfo();

	return (
		<Step
			{...props}
			hasStepId={false}
			isIncomplete={!trigger}
			inspectorControls={<InspectorControls {...props} />}
			blockAttributes={{ 'data-or': __('or', 'mailster') }}
		>
			<CardBody>
				{repeat != 1 && (
					<Tooltip
						text={
							repeat == -1
								? __('repeat forever', 'mailster')
								: sprintf(__('repeat %d times', 'mailster'), repeat)
						}
					>
						<div className="mailster-trigger-repeats">
							{repeat == -1 ? '∞' : sprintf('%d ×', repeat)}
						</div>
					</Tooltip>
				)}
				{trigger && <div className="mailster-step-label">{label}</div>}
				<div
					className="mailster-step-info"
					dangerouslySetInnerHTML={{ __html: info }}
				/>
			</CardBody>
			{trigger && conditions && (
				<CardBody>
					<div className="mailster-step-info conditions">
						<strong>{__('only when', 'mailster')}</strong>
						<ServerSideRender
							block="mailster-workflow/conditions"
							attributes={{
								...attributes,
								...{ render: true, plain: true },
							}}
							EmptyResponsePlaceholder={() => <Spinner />}
						/>
					</div>
				</CardBody>
			)}
			{trigger && false && (
				<CardFooter>
					<div className="mailster-step-info">{trigger}</div>
				</CardFooter>
			)}
		</Step>
	);
}
