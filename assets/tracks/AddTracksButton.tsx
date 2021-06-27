import React from 'react';
import { generateUrl, Route } from '../common/Routing';
import Icon, { Icons } from '../common/Icons';

const AddTracksButton: React.FC = () => {
    function handleClick(): void {

    }

    return (
        <a className="button" href={generateUrl(Route.TRACKS_UPLOAD)}>
            <span className="button-icon">
                <Icon icon={Icons.ADD}/>
            </span>
            Add tracks
        </a>
    );
};

export default AddTracksButton;
