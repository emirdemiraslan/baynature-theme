/**
 * MEC Events List Block Editor
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl, SelectControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';

export default function Edit({ attributes, setAttributes }) {
    const {
        heading,
        numberOfEvents,
        showDate,
        showExcerpt,
        categoryFilter,
        showPastEvents,
        orderBy
    } = attributes;

    // Fetch MEC categories for the dropdown
    const categories = useSelect((select) => {
        const { getEntityRecords } = select('core');
        const categoriesData = getEntityRecords('taxonomy', 'mec_category', {
            per_page: -1,
            orderby: 'name',
            order: 'asc'
        });
        
        if (!categoriesData) {
            return [];
        }
        
        return categoriesData.map((cat) => ({
            label: cat.name,
            value: cat.id.toString()
        }));
    }, []);

    // Prepare category options for SelectControl
    const categoryOptions = [
        { label: __('All Categories', 'bn-newspack-child'), value: '' },
        ...categories
    ];

    const blockProps = useBlockProps({
        className: 'bn-mec-events-list-editor'
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Event Settings', 'bn-newspack-child')} initialOpen={true}>
                    <TextControl
                        label={__('Block Heading', 'bn-newspack-child')}
                        value={heading}
                        onChange={(value) => setAttributes({ heading: value })}
                        placeholder={__('Add an optional heading...', 'bn-newspack-child')}
                    />

                    <RangeControl
                        label={__('Number of Events', 'bn-newspack-child')}
                        value={numberOfEvents}
                        onChange={(value) => setAttributes({ numberOfEvents: value })}
                        min={1}
                        max={20}
                        help={__('Maximum number of events to display', 'bn-newspack-child')}
                    />
                    
                    <SelectControl
                        label={__('Filter by Category', 'bn-newspack-child')}
                        value={categoryFilter}
                        options={categoryOptions}
                        onChange={(value) => setAttributes({ categoryFilter: value })}
                        help={__('Show events from a specific category', 'bn-newspack-child')}
                    />
                    
                    <SelectControl
                        label={__('Sort Order', 'bn-newspack-child')}
                        value={orderBy}
                        options={[
                            { label: __('Date (Ascending)', 'bn-newspack-child'), value: 'date_asc' },
                            { label: __('Date (Descending)', 'bn-newspack-child'), value: 'date_desc' }
                        ]}
                        onChange={(value) => setAttributes({ orderBy: value })}
                        help={__('Order events by date', 'bn-newspack-child')}
                    />
                </PanelBody>
                
                <PanelBody title={__('Display Options', 'bn-newspack-child')} initialOpen={true}>
                    <ToggleControl
                        label={__('Show Event Dates', 'bn-newspack-child')}
                        checked={showDate}
                        onChange={(value) => setAttributes({ showDate: value })}
                        help={__('Display event start date below title', 'bn-newspack-child')}
                    />
                    
                    <ToggleControl
                        label={__('Show Excerpts', 'bn-newspack-child')}
                        checked={showExcerpt}
                        onChange={(value) => setAttributes({ showExcerpt: value })}
                        help={__('Display event excerpt/description', 'bn-newspack-child')}
                    />
                    
                    <ToggleControl
                        label={__('Show Past Events', 'bn-newspack-child')}
                        checked={showPastEvents}
                        onChange={(value) => setAttributes({ showPastEvents: value })}
                        help={
                            showPastEvents
                                ? __('Currently showing past events only', 'bn-newspack-child')
                                : __('Currently showing upcoming events only', 'bn-newspack-child')
                        }
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <ServerSideRender
                    block="bn/mec-events-list"
                    attributes={attributes}
                />
            </div>
        </>
    );
}

