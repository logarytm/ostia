import { Loaded, PlaybackController, PlaybackEmitter } from './PlaybackTypes';
import PlaybackDriver from './PlaybackDriver';
import { Track } from '../tracks/TrackTypes';
import { generateUrl, Route } from '../common/Routing';
import { DisposableLike } from 'event-kit';
import Duration from '../common/Duration';
import fetchWithStatusCheck from '../common/fetchWithStatusCheck';

export default class TrackListPlaybackController implements PlaybackController {
    private currentTrack: Track | null;
    private subscription: DisposableLike;

    public constructor(
        private readonly emitter: PlaybackEmitter,
        private readonly driver: PlaybackDriver,
        private readonly tracks: Track[],
    ) {
        this.subscription = emitter.on('trackEnd', this.handleTrackEnd.bind(this));
    }

    public async handleTrackEnd(): Promise<void> {
        if (this.currentTrack === null) {
            return;
        }

        const nextTrackIndex = this.currentTrack.index + 1;
        const hasNextTrack = nextTrackIndex < this.tracks.length;

        if (hasNextTrack) {
            await this.play(this.tracks[nextTrackIndex]);
        } else {
            this.changeCurrentTrack(null);
        }
    }

    public async play(track: Track): Promise<boolean> {
        // TODO: extract this request and possibly cache results
        const response = await fetchWithStatusCheck(generateUrl(Route.AJAX_TRACKS_STREAM, { id: track.id }), {
            method: 'GET',
            headers: new Headers({
                'Accept': 'application/json',
            }),
        });

        const streamInfo = await response.json();

        this.changeCurrentTrack(track);
        this.driver.play(streamInfo.preferred);

        return true;
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

    public seek(newPosition: Duration): Promise<boolean> {
        const status = this.driver.status;

        if (!(status instanceof Loaded)) {
            return Promise.resolve(false);
        }

        if (newPosition.isWithinTotalDuration(status.totalDuration)) {
            this.driver.seek(newPosition);
        }

        return Promise.resolve(true);
    }

    public getTracks(): Track[] {
        return this.tracks;
    }

    public getEmitter(): PlaybackEmitter {
        return this.emitter;
    }

    private changeCurrentTrack(newTrack: Track | null) {
        const shouldPublish = newTrack !== this.currentTrack;
        this.currentTrack = newTrack;

        if (shouldPublish) {
            this.publishTrackChange();
        }
    }

    private publishTrackChange(track: Track = this.currentTrack): void {
        this.emitter.emit('trackChange', track);
    }
}
