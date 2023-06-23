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
	edit: Edit
} );
