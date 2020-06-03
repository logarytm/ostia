import React, { ReactNode } from 'react';
import { render } from 'react-dom';
import { Emitter } from 'event-kit';

import './css/upload.css';

import UploadQueue from './upload/UploadQueue';
import UploadForm from './upload/UploadForm';
import { UploadedFile, UploadedFileStatus, UploadEmissions, UploadEmitter, UploadProgress } from './upload/UploadTypes';
import uploadFileToServer from './upload/uploadFileToServer';
import { ArrowRight } from 'react-feather';
import { generateUrl, Route } from './common/Routing';

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

    private get canProceed(): boolean {
        const hasPendingUploads = this.state.queue.some((file) => (
            file.status === UploadedFileStatus.PENDING
            || file.status === UploadedFileStatus.STARTED
        ));
        const atLeastOneUploadSucceeded = this.state.queue.some((file) => file.status === UploadedFileStatus.SUCCESS);

        return !hasPendingUploads && atLeastOneUploadSucceeded;
    }

    public render() {
        return (
            <div className="upload-view">
                <UploadForm emitter={this.emitter}/>
                <UploadQueue queue={this.state.queue}/>
                {this.renderProceed()}
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
        if (file == null) {
            return;
        }

        uploadFileToServer(file, this.emitter);
    }

    private handleProceedClick(): void {
        const uuids = this.state.queue
            .filter((track) => track.status === UploadedFileStatus.SUCCESS)
            .map((track) => track.uuid!)
            .join(',');

        window.location.replace(generateUrl(Route.TRACKS_REVIEW, { uuids }));
    }

    private renderProceed(): ReactNode {
        return (
            <div className="upload-proceed">
                <button className="button" type="button" onClick={() => {
                    this.handleProceedClick();
                }} disabled={!this.canProceed}>
                    <span className="button-icon">
                        <ArrowRight/>
                    </span>
                    Next: Tags
                </button>
            </div>
        );
    }
}

render(<UploadView/>, document.querySelector('#react-content'));
