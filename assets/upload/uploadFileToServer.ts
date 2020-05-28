import $ from 'jquery';
import { UploadedFile, UploadEmitter } from './UploadTypes';
import { generateUrl, Route } from '../common/Routing';

export default function uploadFileToServer(file: UploadedFile, emitter: UploadEmitter) {
    file.started();

    $.ajax({
        url: generateUrl(Route.AJAX_TRACKS_UPLOAD),
        dataType: 'json',
        contentType: false,
        processData: false,
        data: file.formData,
        type: 'post',
        success: (data) => {
            data = String(data.uuid);
            file.success(data);
            emitter.emit('progress', { file });
        },
        error: (error) => {
            console.log(error);
            file.error();
            emitter.emit('progress', { file });
        },
        xhr: () => {
            const xhr = new window.XMLHttpRequest();

            xhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable) {
                    file.bytesUploaded = e.loaded;
                    emitter.emit('progress', { file });
                }
            }, false);

            return xhr;
        },
    });
}
