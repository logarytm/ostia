export default class Duration {
    public readonly totalSeconds: number;

    private constructor(totalSeconds: number) {
        this.totalSeconds = Math.ceil(totalSeconds);
    }

    public static fromSeconds(totalSeconds: number): Duration {
        return new Duration(totalSeconds);
    }

    public toString(): string {
        const minutes = Math.floor(this.totalSeconds / 60);
        const seconds = Math.floor(this.totalSeconds % 60);

        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    public percentageOf(totalDuration: Duration): number {
        return 100 * this.totalSeconds / totalDuration.totalSeconds;
    }

    public isWithinTotalDuration(totalDuration: Duration): boolean {
        return this.totalSeconds <= totalDuration.totalSeconds;
    }

    public fraction(fraction: number): Duration {
        return new Duration(fraction * this.totalSeconds);
    }
}

export type DurationData = { totalSeconds: number };
