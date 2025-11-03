import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
} from '@wordpress/block-editor';
import {
    PanelBody,
    SelectControl,
    ToggleControl,
    TextControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit({ attributes, setAttributes }) {
    const { postId, contentPosition, mobileHeight, desktopHeight, showExcerpt, showAuthor, overlay, taxonomy } = attributes;

    const { posts } = useSelect((select) => {
        return {
            posts: select(coreStore).getEntityRecords('postType', 'post', {
                per_page: 20,
                _embed: true,
                status: 'publish',
            }),
        };
    }, []);

    const postOptions = posts
        ? posts.map((post) => ({ label: post.title.rendered, value: post.id }))
        : [];
    postOptions.unshift({ label: __('Latest post (fallback)', 'bn-newspack-child'), value: 0 });

    const blockProps = useBlockProps();

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Hero Settings', 'bn-newspack-child')}>
                    <SelectControl
                        label={__('Select Post', 'bn-newspack-child')}
                        value={postId}
                        options={postOptions}
                        onChange={(val) => setAttributes({ postId: parseInt(val) })}
                    />
                    <SelectControl
                        label={__('Content Position (Desktop)', 'bn-newspack-child')}
                        value={contentPosition}
                        options={[
                            { label: __('Left', 'bn-newspack-child'), value: 'left' },
                            { label: __('Center', 'bn-newspack-child'), value: 'center' },
                            { label: __('Right', 'bn-newspack-child'), value: 'right' },
                        ]}
                        onChange={(val) => setAttributes({ contentPosition: val })}
                    />
                    <TextControl
                        label={__('Mobile Height', 'bn-newspack-child')}
                        value={mobileHeight}
                        onChange={(val) => setAttributes({ mobileHeight: val })}
                    />
                    <TextControl
                        label={__('Desktop Height', 'bn-newspack-child')}
                        value={desktopHeight}
                        onChange={(val) => setAttributes({ desktopHeight: val })}
                    />
                    <ToggleControl
                        label={__('Show Excerpt', 'bn-newspack-child')}
                        checked={showExcerpt}
                        onChange={(val) => setAttributes({ showExcerpt: val })}
                    />
                    <ToggleControl
                        label={__('Show Author', 'bn-newspack-child')}
                        checked={showAuthor}
                        onChange={(val) => setAttributes({ showAuthor: val })}
                    />
                    <TextControl
                        label={__('Overlay Gradient', 'bn-newspack-child')}
                        value={overlay}
                        onChange={(val) => setAttributes({ overlay: val })}
                        help={__('CSS gradient string', 'bn-newspack-child')}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <ServerSideRender block="bn/hero-header" attributes={attributes} />
            </div>
        </>
    );
}

