import React from 'react';
import { render } from 'react-dom';
import $ from 'jquery';
import { Emitter } from 'event-kit';
import { generateUniqueID } from './util/generateUniqueID';

import '../css/upload.css';

import UploadQueue from './react/UploadQueue';

function toReadableSize(bytes) {
    const precision = 2;

    if (bytes > 1024 * 1024) {
        return (bytes / 1024 / 1024).toFixed(precision) + ' MiB';
    } else if (bytes > 1024) {
        return (bytes / 1024).toFixed(precision) + ' KiB';
    }

    return bytes + ' B';
}

const state = {
    queue: [],
};
const emitter = new Emitter();

const $picker = $('#files');

const STATE_CHANGE = 'state-change';

function addFileToQueue(file) {
    const FILE_FIELD = 'file';

    const formData = new FormData();
    formData.append(FILE_FIELD, file);

    const id = generateUniqueID();

    const queued = {
        id,
        formData,
        name: file.name,
        size: file.size,
        uploaded: 0,
        percent: 0,
        success: false,
    };

    state.queue.push(queued);
    emitter.emit(STATE_CHANGE, state);
}

function addFilesToQueue() {
    $picker.prop('files').forEach(addFileToQueue);
}

$picker.change(function handlePickerChange() {
    addFilesToQueue();
});

class UploadView extends React.Component {
    constructor(props) {
        super(props);

        this.state = { queue: [] };
        this.subscription = props.emitter.on(STATE_CHANGE, (newState) => {
            this.setState(newState);
        });
    }

    render() {
        return (
            <div className="upload-view">
                <UploadQueue queue={this.state.queue}/>
            </div>
        );
    }

    componentWillUnmount() {
        this.subscription.dispose();
    }
}

render(<UploadView emitter={emitter}/>, document.querySelector('#react-content'));
