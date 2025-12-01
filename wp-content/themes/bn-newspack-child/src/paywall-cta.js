/**
 * Paywall CTA Block Registration
 */
import { registerBlockType } from '@wordpress/blocks';
import Edit from '../blocks/paywall-cta/edit';

registerBlockType('bn/paywall-cta', {
    edit: Edit,
});

