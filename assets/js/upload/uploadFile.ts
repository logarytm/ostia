import $ from 'jquery';
import { UploadedFile, UploadEmitter } from './UploadTypes';

export default function uploadFile(file: UploadedFile, emitter: UploadEmitter) {
    file.started();

    $.ajax({
        url: '/tracks/ajaxUpload',
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
