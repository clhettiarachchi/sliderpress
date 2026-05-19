/**
 * SliderPress -- Slide child block save function.
 *
 * The slide block uses a static save (not a render_callback) because its
 * content is simple and deterministic. The parent slideshow block reads
 * inner block content via get_the_block_template_html() in render.php.
 *
 * @package SliderPress
 */

import { useBlockProps } from '@wordpress/block-editor';

/**
 * @param {Object} props
 * @param {Object} props.attributes Block attributes.
 */
export default function save({ attributes }) {
    const { imageUrl, altText } = attributes;

    const blockProps = useBlockProps.save({
        className: 'sp-slide',
    });

    if (!imageUrl) {
        return <div {...blockProps}></div>;
    }

    return (
        <div {...blockProps}>
            <img
                src={imageUrl}
                alt={altText}
                loading="lazy"
                decoding="async"
            />
        </div>
    );
}