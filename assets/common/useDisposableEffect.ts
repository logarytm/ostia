import { DisposableLike } from 'event-kit';
import { DependencyList, useEffect } from 'react';

export default function useDisposableEffect(effect: () => DisposableLike, deps?: DependencyList) {
    useEffect(() => {
        const disposable = effect();

        return disposable.dispose.bind(disposable);
    });
}