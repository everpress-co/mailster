/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

export default function Label(attributes, { context }) {
	const { comment, content } = attributes;
	return comment || content;
}
