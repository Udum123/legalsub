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
		view: {
			type: 'string',
			default: acadp_blocks.listings.view
		},		
		location: {
			type: 'number',
			default: acadp_blocks.listings.location
		},	
		category: {
			type: 'number',
			default: acadp_blocks.listings.category
		},	
		filterby: {
			type: 'string',
			default: acadp_blocks.listings.filterby
		},
		orderby: {
			type: 'string',
			default: acadp_blocks.listings.orderby
		},
		order: {
			type: 'string',
			default: acadp_blocks.listings.order
		},
		columns: {
			type: 'number',
			default: acadp_blocks.listings.columns
		},
		listings_per_page: {
			type: 'number',
			default: acadp_blocks.listings.listings_per_page
		},
		featured: {
			type: 'boolean',
			default: acadp_blocks.listings.featured
		},		
		header: {
			type: 'boolean',
			default: acadp_blocks.listings.header
		},
		pagination: {
			type: 'boolean',
			default: acadp_blocks.listings.pagination
		},
	},
	
	edit: Edit
} );
