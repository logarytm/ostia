import React, { ReactNode } from 'react';
import { Track } from './TrackTypes';
import { Loaded, PlaybackStatus } from '../player/PlaybackTypes';
import Icon, { Icons } from '../common/Icons';

type TrackListViewProps = {
    tracks: Track[];
    currentTrack: Track;
    status: PlaybackStatus;
    onPlayRequest: (track: Track) => void;
};

const TrackListView: React.FC<TrackListViewProps> = ({ currentTrack, tracks, status, onPlayRequest }) => {
    function handleDoubleClick(e: React.MouseEvent<HTMLDivElement>): void {
        const trackId: string = (e.target as HTMLDivElement).getAttribute('data-id');
        const track = tracks.find((track) => track.id === trackId);

        onPlayRequest(track);
    }

    if (tracks.length === 0) {
        return <></>;
    }

    function renderPlayingIcon(track: Track): ReactNode {
        if (track.equals(currentTrack)) {
            return status instanceof Loaded && status.paused
                ? (
                    <div className="track-list-item-status-icon">
                        <Icon icon={Icons.PAUSE}/>
                    </div>
                )
                : (
                    <div className="track-list-item-status-icon">
                        <Icon icon={Icons.PLAY}/>
                    </div>
                );
        }

        return <></>;
    }

    function renderItem(track: Track, index: number): ReactNode {
        return (
            <div className={'track-list-item ' + (track.equals(currentTrack) ? 'track-list-item-playing' : '')}
                 onDoubleClick={handleDoubleClick} key={track.id}
                 data-id={track.id}>
                <div className="track-list-item-status">
                    <button type="button" className="track-list-item-play-button sr-only focusable"
                            onClick={() => onPlayRequest(track)}>
                        Play this song
                    </button>
                    <div className="track-list-item-number">
                        {index + 1}
                    </div>
                </div>
                <div className="track-list-item-info">
                    <div className="track-list-item-title">
                        {track.title}
                    </div>
                    <div className="track-list-item-duration">
                        <span className="track-list-item-duration-icon">
                            <Icon icon={Icons.DURATION}/>
                        </span>
                        {track.duration.toString()}
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="track-list">
            <div className="track-list-items">
                {tracks.map((track, index) => renderItem(track, index))}
            </div>
        </div>
    );
};

export default TrackListView;
