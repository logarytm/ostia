import { Emitter } from 'event-kit';

export type QueuedFile = {
    formData: FormData;
    id: string;
    name: string;
    size: number;
    uploaded: number;
    percent: number;
    success: boolean;
};

export type UploadEmissions = {
    enqueue: QueuedFile[];
};

export type UploadEmitter = Emitter<UploadEmissions, UploadEmissions>;
