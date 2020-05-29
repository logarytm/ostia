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

function playAudioFile(uri: string) {
    driver.play(uri);
}

document.querySelector('#track-list-holder').addEventListener('dblclick', (e) => {
    if (e.target instanceof HTMLElement && e.target.matches('.track-list-item')) {
        const trackId: string = e.target.getAttribute('data-id');

        $.ajax({
            url: generateUrl(Route.AJAX_TRACKS_STREAM, { id: trackId }),
            type: 'get',
            dataType: 'json',
            success: (data) => {
                console.log(data);
                playAudioFile(data.preferred);
            },
        });
    }
});

type TrackData = {
    id: string;
    title: string;
    duration: DurationData;
}

declare var __tracks: TrackData[];

const tracks: Track[] = __tracks.map((trackData, index) => ({
    id: trackData.id,
    order: index,
    title: trackData.title,
    duration: Duration.fromSeconds(trackData.duration.totalSeconds),
}));

render(<TrackListView tracks={tracks}/>, document.querySelector('#track-list-holder'));
