/**
 * SliderPress -- Slideshow block edit component.
 *
 * @package SliderPress
 */

import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
    InnerBlocks,
} from '@wordpress/block-editor';
import {
    PanelBody,
    SelectControl,
    ToggleControl,
    RangeControl,
} from '@wordpress/components';

import './editor.scss';

/**
 * Locks InnerBlocks to sliderpress/slide only and provides
 * a labelled "Add Slide" button as the block appender.
 */
const ALLOWED_BLOCKS = ['sliderpress/slide'];

const SLIDESHOW_TEMPLATE = [
    ['sliderpress/slide'],
];

/**
 * @param {Object}   props
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 */
export default function Edit({ attributes, setAttributes }) {
    const {
        transition,
        autoplay,
        autoplayInterval,
        showArrows,
        showDots,
        aspectRatio,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'sp-slideshow',
    });

    /**
     * Inspector controls
     *
     * Slideshow-level settings: transition, aspect ratio, arrows, dots, autoplay.
     */
    const inspectorControls = (
        <InspectorControls>
            <PanelBody title={__('Slideshow Settings', 'sliderpress')} initialOpen={true}>

                <SelectControl
                    label={__('Transition', 'sliderpress')}
                    value={transition}
                    options={[
                        { label: __('Fade', 'sliderpress'), value: 'fade' },
                        { label: __('Slide', 'sliderpress'), value: 'slide' },
                    ]}
                    onChange={(val) => setAttributes({ transition: val })}
                />

                <SelectControl
                    label={__('Aspect ratio', 'sliderpress')}
                    value={aspectRatio}
                    options={[
                        { label: '16:9', value: '16:9' },
                        { label: '4:3', value: '4:3' },
                        { label: '21:9', value: '21:9' },
                        { label: '1:1', value: '1:1' },
                    ]}
                    onChange={(val) => setAttributes({ aspectRatio: val })}
                />

                <ToggleControl
                    label={__('Show arrows', 'sliderpress')}
                    checked={showArrows}
                    onChange={(val) => setAttributes({ showArrows: val })}
                />

                <ToggleControl
                    label={__('Show dots', 'sliderpress')}
                    checked={showDots}
                    onChange={(val) => setAttributes({ showDots: val })}
                />

                <ToggleControl
                    label={__('Autoplay', 'sliderpress')}
                    checked={autoplay}
                    onChange={(val) => setAttributes({ autoplay: val })}
                />

                {autoplay && (
                    <RangeControl
                        label={__('Interval (seconds)', 'sliderpress')}
                        value={autoplayInterval}
                        min={1}
                        max={30}
                        onChange={(val) => setAttributes({ autoplayInterval: val })}
                    />
                )}

            </PanelBody>
        </InspectorControls>
    );

    return (
        <>
            {inspectorControls}
            <div {...blockProps}>
                <InnerBlocks
                    allowedBlocks={ALLOWED_BLOCKS}
                    template={SLIDESHOW_TEMPLATE}
                    renderAppender={InnerBlocks.ButtonBlockAppender}
                />
            </div>
        </>
    );
}