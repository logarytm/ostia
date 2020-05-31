import Duration from '../common/Duration';
import { Emitter } from 'event-kit';

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
};

export type PlaybackEmitter = Emitter<PlaybackEmissions, PlaybackEmissions>;
