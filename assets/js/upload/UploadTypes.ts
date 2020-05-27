import { Emitter } from 'event-kit';

export enum UploadedFileStatus {
    PENDING = 'pending',
    STARTED = 'uploading',
    SUCCESS = 'success',
    ERROR = 'error',
}

export class UploadedFile {
    public status: UploadedFileStatus;
    public bytesUploaded: number;

    public constructor(
        public readonly formData: FormData,
        public readonly id: string,
        public readonly name: string,
        public readonly sizeBytes: number,
    ) {
        this.status = UploadedFileStatus.PENDING;
        this.bytesUploaded = 0;
    }

    public get percentage(): number {
        return Math.round(100 * this.bytesUploaded / this.sizeBytes );
    }
}

export type UploadProgress = {
    file: UploadedFile;
};

export type UploadEmissions = {
    enqueue: UploadedFile[];
    progress: UploadProgress;
};

export type UploadEmitter = Emitter<UploadEmissions, UploadEmissions>;

