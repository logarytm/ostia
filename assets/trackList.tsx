import React from 'react';

import './css/trackList.css';
import { generateUrl, Route } from './common/Routing';
import PlaybackDriver from './player/PlaybackDriver';
import { Emitter } from 'event-kit';
import { Empty, Loaded, PlaybackEmissions, PlaybackStatus } from './player/PlaybackStatus';

import $ from 'jquery';
import Duration, { DurationData } from './common/Duration';
import { render } from 'react-dom';
import TrackListView from './tracks/TrackListView';
import { Track } from './tracks/TrackTypes';
import Player from './player/Player';

const emitter = new Emitter<PlaybackEmissions, PlaybackEmissions>();
const driver = new PlaybackDriver(emitter);

function playAudioFile(uri: string) {
    driver.play(uri);
}

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

function TrackListPage() {
    const [currentTrack, setCurrentTrack] = React.useState<Track | null>(null);
    const [tracks, setTracks] = React.useState<Track[]>(tracksFromServer);
    const [status, setStatus] = React.useState<PlaybackStatus>(new Empty());

    emitter.on('status', (status: PlaybackStatus) => {
        setStatus(status);

        if (status instanceof Loaded && status.ended && currentTrack !== null) {
            const nextTrackIndex = currentTrack.index + 1;
            if (nextTrackIndex < tracks.length) {
                onPlayRequest(tracks[nextTrackIndex]);
            } else {
                setCurrentTrack(null);
            }
        }
    });

    function onPlayRequest(track: Track): void {
        $.ajax({
            url: generateUrl(Route.AJAX_TRACKS_STREAM, { id: track.id }),
            type: 'get',
            dataType: 'json',
            success: (data) => {
                setCurrentTrack(track);
                playAudioFile(data.preferred);
            },
        });
    }

    return (
        <>
            <TrackListView currentTrack={currentTrack} tracks={tracks} status={status} onPlayRequest={onPlayRequest}/>
            <Player driver={driver} emitter={emitter} tracks={tracks}/>
        </>
    );
}

render(
    <TrackListPage/>,
    document.querySelector('#track-list-holder'),
);
