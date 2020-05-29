import React from 'react';
import { Track } from './TrackTypes';

type TrackListViewProps = {
    tracks: Track[];
};

const TrackListView: React.FC<TrackListViewProps> = ({ tracks }) => {
    return (
        <div className="track-list">
            <div className="track-list-items">
                {tracks.map((track) => (
                    <div className="track-list-item" key={track.id} data-id={track.id}>
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
