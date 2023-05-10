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
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { Card, CardBody, Icon } from '@wordpress/components';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

import ActionInspectorControls from './inspector.js';
import QueueBadge from '../inspector/QueueBadge.js';
import Comment from '../inspector/Comment.js';
import StepId from '../inspector/StepId.js';
import { formatLists, formatTags, formatField } from '../../util/index.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id, action, comment } = attributes;
	const className = [];

	const allActions = useSelect((select) =>
		select('mailster/automation').getActions()
	);
	className.push('mailster-step-' + id);
	!action && className.push('mailster-step-incomplete');

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	const getAction = (id) => {
		if (!allActions) {
			return null;
		}
		const action = allActions.filter((action) => {
			return action.id == id;
		});
		return action.length ? action[0] : null;
	};

	const getInfo = () => {
		const { id, action, comment, field, value, lists, tags } = attributes;

		var info = '';

		switch (action) {
			case 'add_list':
			case 'remove_list':
				info = formatLists(lists);
				break;
			case 'add_tag':
			case 'remove_tag':
				info = formatTags(tags);
				break;
			case 'update_field':
				info = formatField(field, value);
				break;

			default:
				break;
		}

		if (!info) {
			info =
				'<i>' +
				(actionObj?.info || __('Set up an action', 'mailster')) +
				'</i>';
		}

		return info;
	};

	const actionObj = getAction(action);

	const label = actionObj?.label || <></>;
	const info = getInfo();
	const icon = actionObj?.icon;

	return (
		<>
			<ActionInspectorControls {...props} />
			<div {...blockProps}>
				<Card className="mailster-step" title={info}>
					<QueueBadge {...props} />
					<Comment {...props} />
					<CardBody size="small">
						<div className="mailster-step-label">
							<Icon icon={Icons[icon]} />
							{label}
						</div>
						{info && (
							<div
								className="mailster-step-info"
								dangerouslySetInnerHTML={{ __html: info }}
							/>
						)}
					</CardBody>
				</Card>
				<div className="end-stop canvas-handle"></div>
			</div>
			<StepId {...props} />
		</>
	);
}
