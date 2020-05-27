import $ from 'jquery';
import { UploadedFile, UploadEmitter } from './UploadTypes';

export default function uploadFile(file: UploadedFile, emitter: UploadEmitter) {
    file.started();

    $.ajax({
        url: '/tracks/upload',
        dataType: 'html',
        contentType: false,
        processData: false,
        data: file.formData,
        type: 'post',
        success: (data) => {
            console.log(`file ${file.name} uploaded successfully`);
            file.success();
            emitter.emit('progress', { file });
        },
        error: (error) => {
            console.log(`file ${file.name} upload failed`);
            console.log(error);
            file.error();
            emitter.emit('progress', { file });
        },
        xhr: () => {
            const xhr = new window.XMLHttpRequest();

            xhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable) {
                    file.bytesUploaded = e.loaded;
                    console.log(file.name, file.bytesUploaded);
                    emitter.emit('progress', { file });
                }
            }, false);

            return xhr;
        },
    });
}
