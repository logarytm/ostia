import React from 'react';

export default function UploadQueue({ queue }) {
    return (
        <div className="upload-queue">
            <div className="upload-queue-heading">Upload queue</div>
            <div className="upload-queue-items">
                {queue.map((file) => (
                    <div className="upload-queue-item" key={file.id}>
                        {file.name}
                    </div>
                ))}
            </div>
            {queue.length === 0 ? (
                <div className="upload-queue-empty">
                    Queue is empty.
                </div>
            ) : null}
        </div>
    );
}
