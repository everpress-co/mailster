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
	CheckboxControl,
	TreeSelect,
	ButtonGroup,
	IconButton,
	ToolbarItem,
	ToolbarButton,
	ExternalLink,
	Tooltip,
	Tip,
} from '@wordpress/components';

import { select, useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

import { BlockControls, InspectorControls } from '@wordpress/block-editor';
import { useState, useEffect } from '@wordpress/element';
import { dateI18n, gmdateI18n, humanTimeDiff } from '@wordpress/date';

/**
 * Internal dependencies
 */

import { TIME_FORMAT, DATE_FORMAT } from '../trigger/constants';

export default function QueueBadge(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id } = attributes;

	const allNumbers = useSelect((select) => {
		return select('mailster/automation').getNumbers();
	}, []);

	const [modalOpen, setModalOpen] = useState(false);
	const [data, setData] = useState(false);

	const queued = allNumbers ? allNumbers['steps'][id]?.count : null;

	const title =
		queued &&
		sprintf(
			_n('%s subscriber queued', '%s subscribers queued', queued, 'mailster'),
			queued
		);

	useEffect(() => {
		if (!modalOpen || !id) return;

		apiFetch({
			path: '/mailster/v1/automations/queue/' + id,
		}).then((response) => {
			setData(response);
		});
	}, [modalOpen]);

	return (
		<>
			{queued && (
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
				{queued && (
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

	const rows = data.map((item, index) => {
		const deleteItem = () => {
			if (
				!confirm(
					sprintf(
						__('Do you really like to remove %s from the queue?', 'mailster'),
						item.email
					)
				)
			)
				return;
			setDisabled(true);
			apiFetch({
				path: '/mailster/v1/automations/queue/' + id + '/' + item.ID,
				method: 'DELETE',
			}).then((response) => {
				response && setData(data.filter((item, i) => i !== index));
				setDisabled(false);
			});
		};
		const forwardItem = () => {
			if (
				!confirm(
					sprintf(
						__(
							'Do you really like to forward %s to the next step?',
							'mailster'
						),
						item.email
					)
				)
			)
				return;
			setDisabled(true);

			apiFetch({
				path: '/mailster/v1/automations/queue/' + id + '/' + item.ID,
				method: 'POST',
				data: { forward: true },
			}).then((response) => {
				response && setData(data.filter((item, i) => i !== index));
				setDisabled(false);
			});
		};
		const finishItem = () => {
			if (
				!confirm(
					sprintf(
						__('Do you really like to finish the journey for %s?', 'mailster'),
						item.email
					)
				)
			)
				return;
			setDisabled(true);

			apiFetch({
				path: '/mailster/v1/automations/queue/' + id + '/' + item.ID,
				method: 'POST',
				data: { finish: true },
			}).then((response) => {
				response && setData(data.filter((item, i) => i !== index));
				setDisabled(false);
			});
		};
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
					<Tooltip
						text={dateI18n(DATE_FORMAT, t) + ' @ ' + dateI18n(TIME_FORMAT, t)}
					>
						<div>{humanTimeDiff(t)}</div>
					</Tooltip>
				</td>
				<td>
					<IconButton
						icon="flag"
						disabled={disabled}
						label={__('Finish Journey', 'mailster')}
						onClick={finishItem}
					/>
					<IconButton
						icon="controls-skipforward"
						disabled={disabled}
						label={__('Forward to next step', 'mailster')}
						onClick={forwardItem}
					/>
					<IconButton
						icon="trash"
						disabled={disabled}
						isDestructive
						label={__('Remove item', 'mailster')}
						onClick={deleteItem}
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
