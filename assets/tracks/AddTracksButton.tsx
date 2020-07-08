import React from 'react';
import { generateUrl, Route } from '../common/Routing';
import { Plus } from 'react-feather';

const AddTracksButton: React.FC = () => {
    return (
        <a className="button" href={generateUrl(Route.TRACKS_UPLOAD)}>
            <span className="button-icon">
            <Plus/>
            </span>
            Add tracks
        </a>
    );
};

export default AddTracksButton;
