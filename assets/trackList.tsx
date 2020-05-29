import './css/trackList.css';
import { generateUrl, Route } from './common/Routing';
import PlaybackDriver from './playback/PlaybackDriver';
import { Emitter } from 'event-kit';
import { PlaybackEmissions } from './playback/PlaybackStatus';

import $ from 'jquery';

const emitter = new Emitter<PlaybackEmissions, PlaybackEmissions>();
const driver = new PlaybackDriver(emitter);

function playAudioFile(uri: string) {
    driver.play(uri);
}

document.querySelector('.track-list').addEventListener('dblclick', (e) => {
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
