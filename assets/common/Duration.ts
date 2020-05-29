export default class Duration {
    private constructor(private readonly totalSeconds: number) {
    }

    public static fromSeconds(totalSeconds: number): Duration {
        return new Duration(totalSeconds);
    }

    public toString(): string {
        const minutes = Math.floor(this.totalSeconds / 60);
        const seconds = Math.floor(this.totalSeconds % 60);

        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}

export type DurationData = { totalSeconds: number };
