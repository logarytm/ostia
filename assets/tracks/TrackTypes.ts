import Duration from '../common/Duration';

export class Track {
    public constructor(
        public readonly id: string,
        public readonly index: number,
        public readonly title: string,
        public readonly duration: Duration,
    ) {
    }

    public equals(track: Track): boolean {
        return track !== null && this.id === track.id;
    }
};
