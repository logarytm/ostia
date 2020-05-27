import React from 'react';
import { QueuedFile } from './UploadTypes';

type UploadQueueProps = { queue: QueuedFile[] };

const UploadQueue: React.FC<UploadQueueProps> = ({ queue }) => {
    if (queue.length === 0) {
        return <></>;
    }

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
        </div>
    );
};

export default UploadQueue;
