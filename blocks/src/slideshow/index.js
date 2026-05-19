/**
 * SliderPress -- Slideshow block registration.
 *
 * @package SliderPress
 */

import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

import './style.scss';

import Edit from './edit';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: Edit,

    /**
     * The slideshow is a dynamic block (PHP renders the front end), but
     * save() must return InnerBlocks.Content so WordPress serialises the
     * child slide blocks into post content. Without this, inner blocks
     * are lost on editor reload.
     */
    save() {
        return (
            <div {...useBlockProps.save()}>
                <InnerBlocks.Content />
            </div>
        );
    },
});