import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { heading, message, buttonText, buttonUrl } = attributes;
    const blockProps = useBlockProps({ className: 'bn-paywall-cta-block' });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('CTA Settings', 'bn-newspack-child')}>
                    <TextControl
                        label={__('Heading', 'bn-newspack-child')}
                        value={heading}
                        onChange={(val) => setAttributes({ heading: val })}
                    />
                    <TextareaControl
                        label={__('Message', 'bn-newspack-child')}
                        value={message}
                        onChange={(val) => setAttributes({ message: val })}
                    />
                    <TextControl
                        label={__('Button Text', 'bn-newspack-child')}
                        value={buttonText}
                        onChange={(val) => setAttributes({ buttonText: val })}
                    />
                    <TextControl
                        label={__('Button URL', 'bn-newspack-child')}
                        value={buttonUrl}
                        onChange={(val) => setAttributes({ buttonUrl: val })}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <div className="bn-paywall-cta-inner">
                    <h3 className="bn-paywall-cta-heading">{heading}</h3>
                    <p className="bn-paywall-cta-message">{message}</p>
                    <a href={buttonUrl} className="bn-paywall-cta-button">{buttonText}</a>
                </div>
            </div>
        </>
    );
}

