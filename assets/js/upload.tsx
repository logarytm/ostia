import React from 'react';
import { render } from 'react-dom';
import { Emitter } from 'event-kit';

import '../css/upload.css';

import UploadQueue from './upload/UploadQueue';
import UploadForm from './upload/UploadForm';
import { QueuedFile, UploadEmissions, UploadEmitter } from './upload/UploadTypes';

function toReadableSize(bytes: number) {
    const precision = 2;

    if (bytes > 1024 * 1024) {
        return (bytes / 1024 / 1024).toFixed(precision) + ' MiB';
    } else if (bytes > 1024) {
        return (bytes / 1024).toFixed(precision) + ' KiB';
    }

    return bytes + ' B';
}

type UploadViewState = { queue: QueuedFile[] };

class UploadView extends React.Component<{}, UploadViewState> {
    private readonly emitter: UploadEmitter;

    constructor(props: {}) {
        super(props);

        this.state = { queue: [] };
        this.emitter = new Emitter<UploadEmissions, UploadEmissions>();
        this.emitter.on('enqueue', (files: QueuedFile[]) => {
            this.setState({
                ...this.state,
                queue: this.state.queue.concat(files),
            });
        });
    }

    render() {
        return (
            <div className="upload-view">
                <UploadForm emitter={this.emitter}/>
                <UploadQueue queue={this.state.queue}/>
            </div>
        );
    }

    componentWillUnmount() {
        this.emitter.dispose();
    }
}

render(<UploadView/>, document.querySelector('#react-content'));
