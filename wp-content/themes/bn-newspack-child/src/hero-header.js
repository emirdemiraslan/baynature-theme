/**
 * Hero Header Block Registration
 */
import { registerBlockType } from '@wordpress/blocks';
import Edit from '../blocks/hero-header/edit';

registerBlockType('bn/hero-header', {
    edit: Edit,
});

