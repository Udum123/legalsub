/**
 * Import block dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';

import { __ } from '@wordpress/i18n';

import { 	
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import {
	Disabled,
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	
	const { 
		style,
		location,		
		category,
		custom_fields,
		price
	} = attributes;
	
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Search form settings' ) }>
					<PanelRow>
						<SelectControl
							label={ __( 'Select template' ) }
							value={ style }
							options={ [
								{ label: __( 'Vertical' ), value: 'vertical' },
								{ label: __( 'Inline' ), value: 'inline' }
							] }
							onChange={ ( value ) => setAttributes( { style: value } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ __( 'Search by location' ) }
							checked={ location }
							onChange={ () => setAttributes( { location: ! location } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ __( 'Search by category' ) }
							checked={ category }
							onChange={ () => setAttributes( { category: ! category } ) }
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label={ __( 'Search by custom fields' ) }
							checked={ custom_fields }
							onChange={ () => setAttributes( { custom_fields: ! custom_fields } ) }
						/>
					</PanelRow>
					
					<PanelRow>
						<ToggleControl
							label={ __( 'Search by price' ) }
							checked={ price }
							onChange={ () => setAttributes( { price: ! price } ) }
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>

			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						block="acadp/search-form"
						attributes={ attributes }
					/>
				</Disabled>	
			</div>
		</>
	);
}
