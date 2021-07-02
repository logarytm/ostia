import { DisposableLike } from 'event-kit';

export class DisposablePool implements DisposableLike {
    public constructor(private readonly disposables: DisposableLike[]) {
    }

    public dispose(): void {
        for (const disposable of this.disposables) {
            disposable.dispose();
        }
    }
}