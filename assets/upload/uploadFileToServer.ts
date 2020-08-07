import { UploadedFile, UploadEmitter } from './UploadTypes';
import { generateUrl, Route } from '../common/Routing';

export default async function uploadFileToServer(file: UploadedFile, emitter: UploadEmitter) {
    file.started();

    const xhr = new XMLHttpRequest();

    xhr.open('POST', generateUrl(Route.AJAX_TRACKS_UPLOAD), true);

    xhr.upload.addEventListener('progress', function (e) {
        if (e.lengthComputable) {
            file.bytesUploaded = e.loaded;
            emitter.emit('progress', { file });
        }
    }, false);

    xhr.addEventListener('error', () => {
        handleError();
    });

    xhr.addEventListener('readystatechange', () => {
        if (xhr.readyState === /* DONE */ 4) {
            if (xhr.status < 400) {
                handleSuccess(JSON.parse(xhr.responseText));
            } else {
                handleError();
            }
        }
    });

    xhr.send(file.formData);

    function handleSuccess(result: { uuid: string }): void {
        file.success(String(result.uuid));
        emitter.emit('progress', { file });
    }

    function handleError(): void {
        file.error();
        emitter.emit('progress', { file });
    }
}
