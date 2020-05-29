import { Empty, Loaded, PlaybackEmitter, PlaybackStatus } from './PlaybackStatus';
import Duration from '../common/Duration';

// TODO: Probably needs to support multiple URIs for cross-browser compatibility.
export default class PlaybackDriver {
    private audioElement: HTMLAudioElement | null = null;

    public constructor(public readonly emitter: PlaybackEmitter) {
    }

    public get status(): PlaybackStatus {
        return this.audioElement !== null
            ? new Loaded(
                this.audioElement.paused,
                this.audioElement.src,
                // The `||` accounts for NaN returned when the media has not yet been loaded.
                Duration.fromSeconds(this.audioElement.currentTime || 0),
                Duration.fromSeconds(this.audioElement.duration || 0),
            )
            : new Empty();
    }

    public play(uri: string): void {
        this.getOrCreateAudioElement(uri).play();
        this.publishUpdate();
    }

    public pause(): void {
        this.audioElement?.pause();
        this.publishUpdate();
    }

    private getOrCreateAudioElement(uri: string): HTMLAudioElement {
        if (this.audioElement === null) {
            this.audioElement = document.createElement('audio');
            document.body.appendChild(this.audioElement);

            this.audioElement.addEventListener('timeupdate', () => {
                this.publishUpdate();
            }, false);
        }

        // Writing `src` always stops playback. Therefore, write it only when the URI actually changes.
        if (this.audioElement.src !== uri) {
            this.audioElement.src = uri;
        }

        return this.audioElement;
    }

    private publishUpdate(): void {
        this.emitter.emit('status', this.status);
    }
}
