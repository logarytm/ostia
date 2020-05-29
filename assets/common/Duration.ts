export default class Duration {
    private constructor(private readonly seconds: number) {
    }

    public static fromSeconds(seconds: number): Duration {
        return new Duration(seconds);
    }

    public toString(): string {
        const minutes = Math.floor(this.seconds / 60);
        const seconds = Math.floor(this.seconds % 60);

        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}
