import React, { useEffect } from 'react';

import './css/trackList.scss';
import { Empty, PlaybackController, PlaybackStatus } from './player/PlaybackTypes';
import TrackListView from './tracks/TrackListView';
import { Track } from './tracks/TrackTypes';
import TrackListActions from './tracks/TrackListActions';
import AddTracksButton from './tracks/AddTracksButton';

export default function TrackList({ controller }: { controller: PlaybackController }) {
    const [currentTrack, setCurrentTrack] = React.useState<Track | null>(null);
    const [tracks] = React.useState<Track[]>(controller.getTracks());
    const [status, setStatus] = React.useState<PlaybackStatus>(new Empty());

    useEffect(() => {
        const trackChangeSubscription = controller.getEmitter().on('trackChange', (track: Track) => {
            setCurrentTrack(track);
        });

        const statusSubscription = controller.getEmitter().on('status', (status: PlaybackStatus) => {
            setStatus(status);
        });

        // TODO: introduce a helper to merge subscriptions
        return () => {
            trackChangeSubscription.dispose();
            statusSubscription.dispose();
        };
    });

    function play(track: Track): void {
        controller.play(track);
    }

    return (
        <>
            <TrackListActions status={status}/>
            {tracks.length === 0
                ? <>
                    <p className="welcome-text">Welcome to Ostia! You don’t have any tracks yet.</p>
                    <p><AddTracksButton/></p>
                </>
                : <p>
                    <AddTracksButton/>
                </p>}
            <TrackListView currentTrack={currentTrack} tracks={tracks} status={status} onPlayRequest={play}/>
        </>
    );
};
