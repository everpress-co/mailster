/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n, sprintf } from '@wordpress/i18n';

import {
	PanelRow,
	PanelBody,
	Panel,
	Button,
	Modal,
	Spinner,
	ExternalLink,
	Tooltip,
	Tip,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

import { InspectorControls } from '@wordpress/block-editor';
import { useState, useEffect } from '@wordpress/element';
import { dateI18n, humanTimeDiff } from '@wordpress/date';

/**
 * Internal dependencies
 */

import { TIME_FORMAT, DATE_FORMAT } from '../trigger/constants';
import { clearData, useSteps } from '../../util';

export default function QueueBadge(props) {
	const { attributes, setAttributes, isSelected, clientId, name } = props;
	const { id, trigger } = attributes;

	const allQueue = useSelect((select) => {
		return select('mailster/automation').getQueue();
	}, []);
	const post_id = useSelect((select) => {
		return select('core/editor').getCurrentPostId();
	}, []);

	const [queued, setQueued] = useState(0);
	const [modalOpen, setModalOpen] = useState(false);
	const [data, setData] = useState(false);

	const title = sprintf(
		_n('%s subscriber queued', '%s subscribers queued', queued, 'mailster'),
		queued
	);

	useEffect(() => {
		if (!allQueue) return;
		setQueued(allQueue.filter((item) => item.step === id).length);
	}, [allQueue]);

	useEffect(() => {
		if (!modalOpen || !id || !post_id) return;

		apiFetch({
			path: '/mailster/v1/automations/queue/' + post_id + '/' + id,
		}).then((response) => {
			setData(response);
		});
	}, [modalOpen, post_id, id]);

	return (
		<>
			{queued > 0 && (
				<span
					className="mailster-step-queued"
					title={title}
					onClick={() => setModalOpen(true)}
				>
					{queued}
				</span>
			)}
			{modalOpen && (
				<Modal title={title} onRequestClose={() => setModalOpen(false)}>
					<div className="mailster-queue-table">
						{!data && (
							<Panel>
								<PanelBody>
									<PanelRow>
										<Spinner />
									</PanelRow>
								</PanelBody>
							</Panel>
						)}
						{data && <Table data={data} setData={setData} {...props} />}
					</div>
					<Tip>
						{__(
							'This table shows you the subscribers which are currently queued in this step. You can finish, trash or move them to the next step with the action buttons.',
							'mailster'
						)}
					</Tip>
				</Modal>
			)}
			<InspectorControls>
				{queued > 0 && (
					<Panel>
						<PanelBody title={__('Queue', 'mailster')} initialOpen={false}>
							<PanelRow>
								<Button onClick={() => setModalOpen(true)} variant="secondary">
									{title}
								</Button>
							</PanelRow>
						</PanelBody>
					</Panel>
				)}
			</InspectorControls>
		</>
	);
}

const Table = (props) => {
	const { attributes, data, setData } = props;
	const { id } = attributes;

	const [disabled, setDisabled] = useState(false);

	const post_id = useSelect((select) => {
		return select('core/editor').getCurrentPostId();
	}, []);

	const doItem = (item, index, method, args) => {
		const path =
			'/mailster/v1/automations/queue/' + post_id + '/' + id + '/' + item.ID;

		setDisabled(true);
		apiFetch({
			path: path,
			method: method,
			data: args,
		}).then((response) => {
			response && setData(data.filter((item, i) => i !== index));
			setDisabled(false);
			clearData('getQueue', 'mailster/automation');
		});
	};

	const deleteItem = (item, index) => {
		const t = sprintf(
			__('Do you really like to remove %s from the queue?', 'mailster'),
			item.email
		);
		if (!confirm(t)) return;

		doItem(item, index, 'DELETE');
	};

	const forwardItem = (item, index) => {
		const t = sprintf(
			__('Do you really like to forward %s to the next step?', 'mailster'),
			item.email
		);
		if (!confirm(t)) return;

		doItem(item, index, 'POST', { forward: true });
	};

	const finishItem = (item, index) => {
		const t = sprintf(
			__('Do you really like to finish the journey for %s?', 'mailster'),
			item.email
		);
		if (!confirm(t)) return;

		doItem(item, index, 'POST', { finish: true });
	};

	const rows = data.map((item, index) => {
		const t = item.timestamp * 1000;
		return (
			<tr key={index}>
				<td>
					<ExternalLink
						href={
							'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' +
							item.subscriber_id
						}
						target="abc"
					>
						{item.email}
					</ExternalLink>
				</td>
				<td>{item.trigger}</td>
				<td>
					{t && (
						<Tooltip
							text={dateI18n(DATE_FORMAT, t) + ' @ ' + dateI18n(TIME_FORMAT, t)}
						>
							<div>{humanTimeDiff(t)}</div>
						</Tooltip>
					)}
				</td>
				<td>
					<Button
						icon="flag"
						disabled={disabled}
						label={__('Finish Journey', 'mailster')}
						onClick={() => finishItem(item, index)}
					/>
					<Button
						icon="controls-skipforward"
						disabled={disabled}
						label={__('Forward to next step', 'mailster')}
						onClick={() => forwardItem(item, index)}
					/>
					<Button
						icon="trash"
						disabled={disabled}
						isDestructive
						label={__('Remove item', 'mailster')}
						onClick={() => deleteItem(item, index)}
					/>
				</td>
			</tr>
		);
	});

	return (
		<table className="wp-list-table widefat striped">
			<thead>
				<tr>
					<th>{__('Subscriber', 'mailster')}</th>
					<th>{__('Trigger', 'mailster')}</th>
					<th>{__('Continues', 'mailster')}</th>
					<th style={{ width: '150px' }}>{__('Actions', 'mailster')}</th>
				</tr>
			</thead>
			<tbody>{rows}</tbody>
		</table>
	);
};
