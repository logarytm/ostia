import React from 'react';
import feather from 'feather-icons';

export enum Icons {
    NEXT = 'arrow-right',
    FINISH = 'check',
    DURATION = 'clock',
    PAUSE = 'pause',
    PLAY = 'play',
    ADD = 'plus',
    TRACK_PREVIOUS = 'skip-back',
    TRACK_NEXT = 'skip-forward',
    PENDING = 'clock',
    SAVING = 'clock',
    SUCCESS = 'check',
    ERROR = 'x',
}

const Icon: React.FC<{ icon: Icons }> = ({ icon }) => {
    return (
        <span className="icon" dangerouslySetInnerHTML={{ __html: feather.icons[icon.toString()].toSvg() }}/>
    );
};

export default Icon;
