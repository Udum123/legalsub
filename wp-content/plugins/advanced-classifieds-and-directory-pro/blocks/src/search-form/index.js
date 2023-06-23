/**
 * Import block dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';
import metadata from './block.json';

/**
 * Register the block.
 */
registerBlockType( metadata.name, {
	attributes: {
		style: {
			type: 'string',
			default: acadp_blocks.search_form.style
		},
		location: {
			type: 'boolean',
			default: acadp_blocks.search_form.location
		},
		category: {
			type: 'boolean',
			default: acadp_blocks.search_form.category
		},
		custom_fields: {
			type: 'boolean',
			default: acadp_blocks.search_form.custom_fields
		},
		price: {
			type: 'boolean',
			default: acadp_blocks.search_form.price
		},
	},
	
	edit: Edit
} );
