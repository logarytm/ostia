import React from 'react';
import { render } from 'react-dom';
import { Emitter } from 'event-kit';

import '../css/upload.css';

import UploadQueue from './upload/UploadQueue';
import UploadForm from './upload/UploadForm';
import { UploadedFile, UploadedFileStatus, UploadEmissions, UploadEmitter, UploadProgress } from './upload/UploadTypes';
import uploadFile from './upload/uploadFile';

type UploadViewState = { queue: UploadedFile[] };

class UploadView extends React.Component<{}, UploadViewState> {
    private readonly emitter: UploadEmitter;

    public constructor(props: {}) {
        super(props);

        this.state = { queue: [] };
        this.emitter = new Emitter<UploadEmissions, UploadEmissions>();
        this.emitter.on('enqueue', (files: UploadedFile[]) => {
            this.setState(
                {
                    ...this.state,
                    queue: this.state.queue.concat(files),
                },
                () => this.uploadNextFile(),
            );
        });

        this.emitter.on('progress', ({}: UploadProgress) => {
            this.forceUpdate(() => this.uploadNextFile());
        });
    }

    public render() {
        return (
            <div className="upload-view">
                <UploadForm emitter={this.emitter}/>
                <UploadQueue queue={this.state.queue}/>
            </div>
        );
    }

    public componentWillUnmount() {
        this.emitter.dispose();
    }

    private isUploading(): boolean {
        return this.state.queue.some((file) => file.status === UploadedFileStatus.STARTED);
    }

    private uploadNextFile(): void {
        if (this.isUploading()) {
            return;
        }

        const file = this.state.queue.find((file: UploadedFile) => file.status === UploadedFileStatus.PENDING);
        console.log(this.state.queue);
        if (file == null) {
            return;
        }

        uploadFile(file, this.emitter);
    }
}

render(<UploadView/>, document.querySelector('#react-content'));
