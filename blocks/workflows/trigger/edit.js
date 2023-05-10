/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';

import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText } from '@wordpress/block-editor';

import { useEffect } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import {
	Card,
	CardBody,
	CardFooter,
	CardHeader,
	Spinner,
} from '@wordpress/components';
import { dateI18n, gmdateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */

import TriggerInspectorControls from './inspector';
import QueueBadge from '../inspector/QueueBadge';
import {
	formatLists,
	formatForms,
	formatTags,
	formatPages,
	formatField,
	formatLinks,
} from '../../util';

import {
	DELAY_OPTIONS,
	IS_12_HOUR,
	TIME_FORMAT,
	DATE_FORMAT,
} from './constants';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { trigger, conditions } = attributes;
	const className = [];

	const [meta, setMeta] = useEntityProp(
		'postType',
		'mailster-workflow',
		'meta'
	);

	const allTriggers = useSelect((select) =>
		select('mailster/automation').getTriggers()
	);

	//make sure the trigger is in the workflows meta
	useEffect(() => {
		if (trigger && !meta.trigger.includes(trigger)) {
			var newTrigger = [...meta.trigger];
			newTrigger.push(trigger);
			setMeta({ trigger: newTrigger });
		}
	}, []);

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
		const { lists, forms, tags, pages, links, hook, field, date } = attributes;
		if (!trigger) return __('Set up a trigger', 'mailster');

		switch (trigger) {
			case 'list_add':
				return formatLists(lists);
				break;
			case 'form_conversion':
				return formatForms(forms);
				break;
			case 'tag_added':
				return formatTags(tags);
				break;
			case 'update_field':
				return formatField(field, value);
				break;
			case 'page_visit':
				return formatPages(pages);
				break;
			case 'link_click':
				return formatLinks(links);
				break;
			case 'hook':
				return (
					hook &&
					'<strong class="mailster-step-badge code">' + hook + '</strong>'
				);
				break;
			case 'date':
				if (field) {
					return formatField(
						field,
						dateI18n(TIME_FORMAT, date),
						__('On subscribers %s field at %s', 'mailster')
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
					return formatField(
						field,
						dateI18n(TIME_FORMAT, date),
						__('Yearly based on the subscribers %s field at %s', 'mailster')
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
				break;

			default:
				return trigger;
				break;
		}
		return '';
	};

	const label = getTrigger(trigger)?.info || <Spinner />;
	const info = getInfo();

	!trigger && className.push('mailster-step-incomplete');

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	return (
		<>
			<TriggerInspectorControls {...props} />
			<div {...blockProps} data-or={__('or', 'mailster')}>
				<Card className="mailster-step">
					<QueueBadge {...props} />
					<CardBody>
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
				</Card>
			</div>
		</>
	);
}
