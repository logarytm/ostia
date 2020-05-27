import { Emitter } from 'event-kit';

export enum UploadedFileStatus {
    PENDING = 'pending',
    STARTED = 'uploading',
    SUCCESS = 'success',
    ERROR = 'error',
}

export class UploadedFile {
    private statusBackingField: UploadedFileStatus;
    public bytesUploaded: number;
    public uuid: string | null;

    public constructor(
        public readonly formData: FormData,
        public readonly id: string,
        public readonly name: string,
        public readonly sizeBytes: number,
    ) {
        this.statusBackingField = UploadedFileStatus.PENDING;
        this.bytesUploaded = 0;
    }

    public get percentage(): number {
        return Math.round(100 * this.bytesUploaded / this.sizeBytes);
    }

    public get status(): UploadedFileStatus {
        return this.statusBackingField;
    }

    public started(): void {
        this.statusBackingField = UploadedFileStatus.STARTED;
    }

    public error(): void {
        this.statusBackingField = UploadedFileStatus.ERROR;
    }

    public success(): void {
        this.statusBackingField = UploadedFileStatus.SUCCESS;
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

