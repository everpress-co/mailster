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
	const { comment, content, metadata } = attributes;

	if (metadata?.name) return metadata.name;
	return comment || content;
}
