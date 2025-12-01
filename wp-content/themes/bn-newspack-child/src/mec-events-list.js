/**
 * MEC Events List Block Registration
 */
import { registerBlockType } from '@wordpress/blocks';
import Edit from '../blocks/mec-events-list/edit';

registerBlockType('bn/mec-events-list', {
    edit: Edit,
});

