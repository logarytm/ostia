import React from 'react';
import { Track } from './TrackTypes';
import { generateUrl, Route } from '../common/Routing';
import { Play } from 'react-feather';

type TrackListViewProps = {
    tracks: Track[];
    currentTrack: Track;
    onPlayRequest: (track: Track) => void;
};

const TrackListView: React.FC<TrackListViewProps> = ({ currentTrack, tracks, onPlayRequest }) => {
    function handleDoubleClick(e: React.MouseEvent<HTMLDivElement>): void {
        const trackId: string = (e.target as HTMLDivElement).getAttribute('data-id');
        const track = tracks.find((track) => track.id === trackId);

        onPlayRequest(track);
    }

    return (
        <div className="track-list">
            <div className="track-list-items">
                {tracks.map((track, index) => (
                    <div className={'track-list-item ' + (track.equals(currentTrack) ? 'track-list-item-playing' : '')}
                         onDoubleClick={handleDoubleClick} key={track.id}
                         data-id={track.id}>
                        <div className="track-list-item-status">
                            {track.equals(currentTrack)
                                ? (
                                    <div className="track-list-item-status-icon">
                                        <Play stroke="mediumslateblue"/>
                                    </div>
                                )
                                : (
                                    <div className="track-list-item-number">
                                        {index + 1}.
                                    </div>
                                )}
                        </div>
                        <div className="track-list-item-info">
                            <div className="track-list-item-title">
                                {track.title}
                            </div>
                            <div className="track-list-item-duration">
                                {track.duration.toString()}
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default TrackListView;
