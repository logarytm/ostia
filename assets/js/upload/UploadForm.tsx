import React, { ChangeEvent } from 'react';
import { generateRandomId } from './generateRandomId';
import { QueuedFile, UploadEmitter } from './UploadTypes';
import { map } from '../util/array';

const FILE_FIELD = 'file';

const UploadForm: React.FC<{ emitter: UploadEmitter }> = ({ emitter }) => {
    function handleChange(e: ChangeEvent<HTMLInputElement>) {
        function toQueuedFile(file: File) {

            const formData = new FormData();
            formData.append(FILE_FIELD, file);

            const fileToQueue: QueuedFile = {
                formData,
                id: generateRandomId(),
                name: file.name,
                size: file.size,
                uploaded: 0,
                percent: 0,
                success: false,
            };

            return fileToQueue;
        }

        const files = map(e.target.files!, toQueuedFile);
        emitter.emit('enqueue', files);
    }

    return (
        <form className="form upload-form">
            <div>
                <label htmlFor="files">Audio file(s)</label>
                <input type="file" name="files" id="files" onChange={handleChange} multiple required/>
            </div>
        </form>
    );
};

export default UploadForm;
