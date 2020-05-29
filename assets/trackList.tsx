import React from 'react';

import './css/trackList.css';
import { generateUrl, Route } from './common/Routing';
import PlaybackDriver from './playback/PlaybackDriver';
import { Emitter } from 'event-kit';
import { PlaybackEmissions } from './playback/PlaybackStatus';

import $ from 'jquery';
import Duration, { DurationData } from './common/Duration';
import { render } from 'react-dom';
import TrackListView from './tracks/TrackListView';
import { Track } from './tracks/TrackTypes';

const emitter = new Emitter<PlaybackEmissions, PlaybackEmissions>();
const driver = new PlaybackDriver(emitter);

const state: { tracks: Track[], currentTrack: Track | null } = { tracks: [], currentTrack: null };

function playAudioFile(uri: string) {
    driver.play(uri);
}

type TrackData = {
    id: string;
    title: string;
    duration: DurationData;
}

declare var __tracks: TrackData[];

const tracksFromServer: Track[] = state.tracks = __tracks.map((trackData, index) => new Track(
    trackData.id,
    index,
    trackData.title,
    Duration.fromSeconds(trackData.duration.totalSeconds),
));

function TrackListPage() {
    const [currentTrack, setCurrentTrack] = React.useState<Track | null>(null);
    const [tracks, setTracks] = React.useState<Track[]>(tracksFromServer);

    function onPlayRequest(track: Track): void {
        console.log(track);
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
        <TrackListView currentTrack={currentTrack} tracks={tracks} onPlayRequest={onPlayRequest}/>
    );
}

render(
    <TrackListPage/>,
    document.querySelector('#track-list-holder'),
);
