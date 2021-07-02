import React from 'react';

import './css/trackList.scss';
import { Empty, PlaybackController, PlaybackStatus } from './player/PlaybackTypes';
import TrackListView from './tracks/TrackListView';
import { Track } from './tracks/TrackTypes';
import TrackListActions from './tracks/TrackListActions';
import AddTracksButton from './tracks/AddTracksButton';
import useDisposableEffect from './common/useDisposableEffect';
import { DisposablePool } from './common/DisposablePool';

export default function TrackList({ controller }: { controller: PlaybackController }) {
    const [currentTrack, setCurrentTrack] = React.useState<Track | null>(null);
    const [tracks] = React.useState<Track[]>(controller.getTracks());
    const [status, setStatus] = React.useState<PlaybackStatus>(new Empty());

    useDisposableEffect(() => {
        const trackChangeSubscription = controller.getEmitter().on('trackChange', (track: Track) => {
            setCurrentTrack(track);
        });

        const statusSubscription = controller.getEmitter().on('status', (status: PlaybackStatus) => {
            setStatus(status);
        });

        return new DisposablePool([
            trackChangeSubscription,
            statusSubscription,
        ]);
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
