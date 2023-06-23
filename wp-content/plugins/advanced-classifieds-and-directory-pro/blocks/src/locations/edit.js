/**
 * Import block dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';

import { useBlockProps } from '@wordpress/block-editor';

import { Disabled } from '@wordpress/components';

/**
 * Describes the structure of the block in the context of the editor.
 * This represents what the editor will render when the block is used.
 *
 * @return {WPElement} Element to render.
 */
export default function Edit() {
	return (
		<>
			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender block="acadp/locations" />
				</Disabled>	
			</div>					
		</>
	);
}
