import React from 'react';
import { render } from 'react-dom';
import { Emitter } from 'event-kit';

import '../css/upload.css';

import UploadQueue from './upload/UploadQueue';
import UploadForm from './upload/UploadForm';
import { QueuedFile, UploadEmissions, UploadEmitter } from './upload/UploadTypes';

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
