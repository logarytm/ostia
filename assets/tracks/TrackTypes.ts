import Duration from '../common/Duration';

export class Track {
    public constructor(
        public readonly id: string,
        public readonly order: number,
        public readonly title: string,
        public readonly duration: Duration,
    ) {
    }

    public equals(track: Track): boolean {
        return this.id === track.id;
    }
};
