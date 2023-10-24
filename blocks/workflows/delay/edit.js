/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { sprintf, __, _n } from '@wordpress/i18n';

import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import * as Icons from '@wordpress/icons';
import { Card, CardBody, CardHeader, Icon } from '@wordpress/components';
import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import DelayInspectorControls from './inspector.js';
import QueueBadge from '../inspector/QueueBadge.js';
import Comment from '../inspector/Comment.js';
import StepId from '../inspector/StepId.js';

import {
	DELAY_OPTIONS,
	WEEK_OPTIONS,
	MONTH_OPTIONS,
	DATE_FORMAT,
	TIME_FORMAT,
	DATE_TIME_FORMAT,
} from './constants.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const {
		id,
		amount,
		unit,
		date = new Date(),
		month = 1,
		timezone,
		weekdays,
	} = attributes;
	const className = [];

	useEffect(() => {
		!amount && setAttributes({ amount: 1 });
		!unit && setAttributes({ unit: 'hours' });
	});

	id && className.push('mailster-step-' + id);

	const isRelative = ['minutes', 'hours', 'days', 'weeks', 'months'].includes(
		unit
	);

	const getLabel = () => {
		if (isRelative) {
			const element = DELAY_OPTIONS.find((item) => item.value === unit);

			return sprintf(
				'%d %s',
				amount,
				(amount > 1 && element.plural) || element.single
			);
		}

		const currDate = new Date(date);

		switch (unit) {
			case 'day':
			case 'week':
			case 'month':
			case 'year':
				return dateI18n(TIME_FORMAT, currDate);
		}

		return dateI18n(TIME_FORMAT, currDate);
	};

	const getInfo = () => {
		if (isRelative) {
			return '';
		}

		const currDate = new Date(date);

		switch (unit) {
			case 'day':
				return '';

			case 'week':
				if (!weekdays || weekdays.length == 7) {
					return __('on ever day in the week.', 'mailster');
				}

				const names = WEEK_OPTIONS.filter((key, index) => {
					return weekdays.includes(index);
				})
					.join(', ')
					.replace(/,([^,]*)$/, ' ' + __('or', 'mailster') + '$1');

				return sprintf(__('on a %s.', 'mailster'), names);

			case 'month':
				return sprintf(__('on the %s.', 'mailster'), MONTH_OPTIONS[month]);

			case 'year':
				return sprintf(
					__('on the %s.', 'mailster'),
					currDate.toLocaleDateString()
				);
		}

		return new Date(date).toString();
	};

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	const info = getInfo();
	const label = isRelative
		? sprintf(__('Wait for %s', 'mailster'), getLabel())
		: sprintf(__('Wait until %s', 'mailster'), getLabel());

	return (
		<>
			<DelayInspectorControls {...props} getLabel={getLabel} />
			<div {...blockProps}>
				<Card className="mailster-step" title={info}>
					<Comment {...props} />
					<QueueBadge {...props} />
					<CardBody size="small">
						<div className="mailster-step-label">
							<Icon icon={Icons.backup} />
							{label}
						</div>
						{info && <div className="mailster-step-info">{info}</div>}
						{!isRelative && timezone && (
							<div className="mailster-step-info">
								<br />
								{__('Respect users timezone', 'mailster')}
							</div>
						)}
					</CardBody>
				</Card>
				<div className="end-stop canvas-handle"></div>
			</div>
			<StepId {...props} />
		</>
	);
}
