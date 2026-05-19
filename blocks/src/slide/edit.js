/**
 * SliderPress -- Slide child block edit component.
 *
 * @package SliderPress
 */

import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    MediaUpload,
    MediaUploadCheck,
} from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

import './editor.scss';

/**
 * @param {Object}   props
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 */
export default function Edit({ attributes, setAttributes }) {
    const { imageId, imageUrl, altText } = attributes;

    const blockProps = useBlockProps({
        className: 'sp-slide',
    });

    /**
     * Image management
     *
     * Sets or clears the slide image from the Media Library.
     */
    function onSelectImage(media) {
        setAttributes({
            imageId: media.id,
            imageUrl: media.url,
            altText: media.alt || '',
        });
    }

    function onRemoveImage() {
        setAttributes({
            imageId: 0,
            imageUrl: '',
            altText: '',
        });
    }

    return (
        <div {...blockProps}>
            <div className="sp-slide__media">
                {imageUrl ? (
                    <img
                        src={imageUrl}
                        alt={altText}
                        className="sp-slide__img"
                    />
                ) : (
                    <div className="sp-slide__placeholder">
                        <span
                            className="dashicons dashicons-format-image"
                            aria-hidden="true"
                        />
                        <span className="sp-slide__placeholder-label">
                            {__('No image selected', 'sliderpress')}
                        </span>
                    </div>
                )}
            </div>

            <div className="sp-slide__controls">
                <MediaUploadCheck>
                    <MediaUpload
                        onSelect={onSelectImage}
                        allowedTypes={['image']}
                        value={imageId || null}
                        render={({ open }) => (
                            <Button variant="secondary" onClick={open}>
                                {imageUrl
                                    ? __('Change image', 'sliderpress')
                                    : __('Select image', 'sliderpress')
                                }
                            </Button>
                        )}
                    />
                </MediaUploadCheck>

                {imageUrl && (
                    <Button variant="tertiary" onClick={onRemoveImage}>
                        {__('Remove image', 'sliderpress')}
                    </Button>
                )}
            </div>
        </div>
    );
}