import Duration from '../common/Duration';
import { Emitter } from 'event-kit';
import { Track } from '../tracks/TrackTypes';

export class Loaded {
    public constructor(
        public readonly paused: boolean,
        public readonly ended: boolean,
        public readonly uri: string | null,
        public readonly position: Duration,
        public readonly totalDuration: Duration,
    ) {
    }
}

export class Empty {
}

export type PlaybackStatus = Loaded | Empty;

export type PlaybackEmissions = {
    status: PlaybackStatus;
    trackEnd: PlaybackStatus;
    trackChange: Track | null;
    trackListChange: Track[];
};

export type PlaybackEmitter = Emitter<PlaybackEmissions, PlaybackEmissions>;

export type PlaybackController = {
    play(track: Track): Promise<boolean>;
    resume(): Promise<boolean>;
    pause(): Promise<boolean>;
    previous(): Promise<boolean>;
    next(): Promise<boolean>;
    seek(position: Duration): Promise<boolean>;
    replaceTracks(track: Track[]): Promise<boolean>;
    getTracks(): Track[];
    getEmitter(): PlaybackEmitter;
};
