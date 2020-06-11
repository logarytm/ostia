import React, { useEffect } from 'react';

import './css/trackList.css';
import PlaybackDriver from './player/PlaybackDriver';
import { Emitter } from 'event-kit';
import { Empty, PlaybackEmissions, PlaybackStatus } from './player/PlaybackTypes';
import Duration, { DurationData } from './common/Duration';
import { render } from 'react-dom';
import TrackListView from './tracks/TrackListView';
import { Track } from './tracks/TrackTypes';
import PlayerControls from './player/PlayerControls';
import TrackListPlaybackController from './player/TrackListPlaybackController';
import TrackListActions from './tracks/TrackListActions';

const emitter = new Emitter<PlaybackEmissions, PlaybackEmissions>();
const driver = new PlaybackDriver(emitter);

type TrackData = {
    id: string;
    title: string;
    duration: DurationData;
}

declare var __tracks: TrackData[];

const tracksFromServer: Track[] = __tracks.map((trackData, index) => new Track(
    trackData.id,
    index,
    trackData.title,
    Duration.fromSeconds(trackData.duration.totalSeconds),
));

const controller = new TrackListPlaybackController(emitter, driver, tracksFromServer);

const TrackListPage: React.FC = () => {
    const [currentTrack, setCurrentTrack] = React.useState<Track | null>(null);
    const [tracks] = React.useState<Track[]>(tracksFromServer);
    const [status, setStatus] = React.useState<PlaybackStatus>(new Empty());

    useEffect(() => {
        const trackChangeSubscription = emitter.on('trackChange', (track: Track) => {
            setCurrentTrack(track);
        });

        const statusSubscription = emitter.on('status', (status: PlaybackStatus) => {
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
            <TrackListView currentTrack={currentTrack} tracks={tracks} status={status} onPlayRequest={play}/>
            <PlayerControls controller={controller} emitter={emitter} tracks={tracks} currentTrack={currentTrack}/>
        </>
    );
};

render(
    <TrackListPage/>,
    document.querySelector('#track-list-holder'),
);
