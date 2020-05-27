import React, { ChangeEvent } from 'react';
import generateRandomId from './generateRandomId';
import { UploadedFile, UploadEmitter } from './UploadTypes';
import { map } from '../util/array';

const FILE_FIELD = 'file';

const UploadForm: React.FC<{ emitter: UploadEmitter }> = ({ emitter }) => {
    function handleChange(e: ChangeEvent<HTMLInputElement>) {
        function toQueuedFile(file: File) {
            const formData = new FormData();
            formData.append(FILE_FIELD, file);

            return new UploadedFile(
                formData,
                generateRandomId(),
                file.name,
                file.size
            );
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
