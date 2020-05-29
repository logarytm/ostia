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

function onPlayRequest(track: Track): void {
    $.ajax({
        url: generateUrl(Route.AJAX_TRACKS_STREAM, { id: track.id }),
        type: 'get',
        dataType: 'json',
        success: (data) => {
            state.currentTrack = track;
            playAudioFile(data.preferred);
        },
    });
}

declare var __tracks: TrackData[];

const tracks: Track[] = state.tracks = __tracks.map((trackData, index) => new Track(
    trackData.id,
    index,
    trackData.title,
    Duration.fromSeconds(trackData.duration.totalSeconds),
));

render(
    <TrackListView tracks={tracks} onPlayRequest={onPlayRequest}/>,
    document.querySelector('#track-list-holder'),
);
