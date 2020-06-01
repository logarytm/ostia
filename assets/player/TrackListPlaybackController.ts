import { PlaybackController, PlaybackEmitter, PlaybackStatus } from './PlaybackTypes';
import PlaybackDriver from './PlaybackDriver';
import { Track } from '../tracks/TrackTypes';
import { generateUrl, Route } from '../common/Routing';
import { DisposableLike } from 'event-kit';
import $ from 'jquery';

export default class TrackListPlaybackController implements PlaybackController {
    private currentTrack: Track | null;
    private subscription: DisposableLike;

    public constructor(
        private readonly emitter: PlaybackEmitter,
        private readonly driver: PlaybackDriver,
        private readonly tracks: Track[],
    ) {
        this.subscription = emitter.on('trackEnd', () => {
            if (this.currentTrack !== null) {
                const nextTrackIndex = this.currentTrack.index + 1;
                if (nextTrackIndex < tracks.length) {
                    this.play(tracks[nextTrackIndex]);
                } else {
                    this.changeCurrentTrack(null);
                }
            }
        });
    }

    public play(track: Track) {
        // TODO: extract this request and possibly cache results
        $.ajax({
            url: generateUrl(Route.AJAX_TRACKS_STREAM, { id: track.id }),
            type: 'get',
            dataType: 'json',
            success: (data) => {
                this.changeCurrentTrack(track);
                this.driver.play(data.preferred);
            },
        });
    }

    public resume(): Promise<boolean> {
        this.driver.resume();

        return Promise.resolve(this.driver.isLoaded());
    }

    public pause(): Promise<boolean> {
        this.driver.pause();

        return Promise.resolve(this.driver.isLoaded());
    }

    public next(): Promise<boolean> {
        return Promise.resolve(false);
    }

    public previous(): Promise<boolean> {
        return Promise.resolve(false);
    }

    public reset(): void {
        this.changeCurrentTrack(null);
    }

    private changeCurrentTrack(newTrack: Track | null) {
        const shouldPublish = newTrack != this.currentTrack;
        this.currentTrack = newTrack;
        if (shouldPublish) {
            this.publishTrackChange();
        }
    }

    private publishTrackChange(track: Track = this.currentTrack): void {
        this.emitter.emit('trackChange', track);
    }
}
